<?php
declare(strict_types=1);

namespace ItechPanel\Configurator\Controller\Adminhtml\Section;

use ItechPanel\Configurator\Api\Data\SectionInterfaceFactory;
use ItechPanel\Configurator\Api\SectionRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\ResourceConnection;

class Save extends Action implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'ItechPanel_Configurator::section';
    private $sectionRepository;
    private $sectionFactory;
    private $resourceConnection;

    public function __construct(
        Context $context,
        SectionRepositoryInterface $sectionRepository,
        SectionInterfaceFactory $sectionFactory,
        ResourceConnection $resourceConnection
    ) {
        parent::__construct($context);
        $this->sectionRepository = $sectionRepository;
        $this->sectionFactory = $sectionFactory;
        $this->resourceConnection = $resourceConnection;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug("-------------data-----------");
		\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r($data, true));
        if (!$data) {
            return $resultRedirect->setPath('*/*/');
        }

        $id = !empty($data['section_id']) ? (int)$data['section_id'] : null;

        try {
            $section = $id ? $this->sectionRepository->getById($id) : $this->sectionFactory->create();
            \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug("-------------section-----------");
			\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r($section, true));
            if (isset($data['title'])) $section->setTitle($data['title']);
            if (isset($data['label'])) $section->setLabel($data['label']);
            if (isset($data['position'])) $section->setPosition((int)$data['position']);
            if (isset($data['is_active'])) $section->setIsActive((bool)$data['is_active']);

            $this->sectionRepository->save($section);

            // Handle the checking/unchecking logic from the grid
            if (array_key_exists('section_products', $data)) {
                $productsJson = $data['section_products'];
                $productIds = [];
                \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug("-------------productsJson-----------");
				\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r($productsJson, true));

                if (is_string($productsJson) && !empty($productsJson)) {
                    $decodedProducts = json_decode($productsJson, true);
                    if (is_array($decodedProducts)) {
                        $productIds = array_keys($decodedProducts);
                    }
                }
                
                $this->saveProductRelationships((int)$section->getSectionId(), $productIds);
            }

            $this->messageManager->addSuccessMessage(__('The section has been saved.'));
            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', ['section_id' => $section->getSectionId()]);
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving.'));
        }

        return $resultRedirect->setPath('*/*/');
    }

    private function saveProductRelationships(int $sectionId, array $productIds)
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('itechpanel_configurator_section_product');
        
        // THIS MUST BE UNCOMMENTED: Always delete old selections first to allow un-checking!
        $connection->delete($tableName, ['section_id = ?' => $sectionId]);

        // Insert new ones if any boxes are checked
        if (!empty($productIds)) {
            $insertData = [];
            foreach ($productIds as $productId) {
                if ((int)$productId > 0) {
                    $insertData[] = ['section_id' => $sectionId, 'product_id' => (int)$productId];
                }
            }
            
            if (!empty($insertData)) {
                $connection->insertMultiple($tableName, $insertData);
            }
        }
    }
}