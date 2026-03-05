<?php
declare(strict_types=1);

namespace ItechPanel\Configurator\Controller\Adminhtml\Subsection;

use ItechPanel\Configurator\Api\Data\SubsectionInterfaceFactory;
use ItechPanel\Configurator\Api\SubsectionRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\ResourceConnection;

class Save extends Action implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'ItechPanel_Configurator::subsection';
    private $subsectionRepository;
    private $subsectionFactory;
    private $resourceConnection;

    public function __construct(
        Context $context,
        SubsectionRepositoryInterface $subsectionRepository,
        SubsectionInterfaceFactory $subsectionFactory,
        ResourceConnection $resourceConnection
    ) {
        parent::__construct($context);
        $this->subsectionRepository = $subsectionRepository;
        $this->subsectionFactory = $subsectionFactory;
        $this->resourceConnection = $resourceConnection;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if (!$data) return $resultRedirect->setPath('*/*/');
        \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug("------------data 12------------");
		\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r($data, true));
        $id = !empty($data['subsection_id']) ? (int)$data['subsection_id'] : null;

        try {
            $subsection = $id ? $this->subsectionRepository->getById($id) : $this->subsectionFactory->create();

            if (isset($data['title'])) $subsection->setTitle($data['title']);
            if (isset($data['label'])) $subsection->setLabel($data['label']);
            if (isset($data['price'])) $subsection->setPrice((float)$data['price']);
            if (isset($data['is_required'])) $subsection->setIsRequired((bool)$data['is_required']);
            if (isset($data['position'])) $subsection->setPosition((int)$data['position']);
            if (isset($data['is_active'])) $subsection->setIsActive((bool)$data['is_active']);

            $this->subsectionRepository->save($subsection);

            if (isset($data['section_id'])) {
                $this->saveSectionRelationship((int)$subsection->getSubsectionId(), (int)$data['section_id']);
            }

            // --- FIX: Parse JSON from Grid Hidden Input ---
            if (array_key_exists('subsection_products', $data)) {
                $productsJson = $data['subsection_products'];
                $productIds = [];
                \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug("------------productsJson 12------------");
				\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r($productsJson, true));

                if (is_string($productsJson) && !empty($productsJson) && $productsJson !== '{}') {
                    $decodedProducts = json_decode($productsJson, true);
				\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug("------------decodedProducts 12------------");
				\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r($decodedProducts, true));
                    if (is_array($decodedProducts)) {
                        $productIds = array_keys($decodedProducts);
				\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug("------------productIds 12------------");
				\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r($productIds, true));
                    }
                }
                $this->saveProductRelationships((int)$subsection->getSubsectionId(), $productIds);
            }

            $this->messageManager->addSuccessMessage(__('The subsection has been saved.'));
            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', ['subsection_id' => $subsection->getSubsectionId()]);
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving.'));
        }

        return $resultRedirect->setPath('*/*/');
    }

    private function saveSectionRelationship(int $subsectionId, int $sectionId)
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('itechpanel_configurator_section_subsection');
        $connection->delete($tableName, ['subsection_id = ?' => $subsectionId]);

        if ($sectionId > 0) {
            $connection->insert($tableName, ['section_id' => $sectionId, 'subsection_id' => $subsectionId]);
        }
    }

    private function saveProductRelationships(int $subsectionId, array $productIds)
    {
		\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug("------------productIds 00------------");
\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r($productIds, true));

        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('itechpanel_configurator_subsection_product');
        
        $connection->delete($tableName, ['subsection_id = ?' => $subsectionId]);

        if (!empty($productIds)) {
            $insertData = [];
            foreach ($productIds as $productId) {
				\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug("------------productId 123------------");
\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r($productId, true));

                if ((int)$productId > 0) {
                    $insertData[] = ['subsection_id' => $subsectionId, 'product_id' => (int)$productId];
                }
            }
            if (!empty($insertData)) {
				\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug("-------------insertData-----------");
\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r($insertData, true));

                $connection->insertMultiple($tableName, $insertData);
            }
        }
    }
}