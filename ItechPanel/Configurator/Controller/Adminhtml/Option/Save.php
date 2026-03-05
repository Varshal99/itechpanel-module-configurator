<?php
declare(strict_types=1);

namespace ItechPanel\Configurator\Controller\Adminhtml\Option;

use ItechPanel\Configurator\Api\Data\OptionInterfaceFactory;
use ItechPanel\Configurator\Api\OptionRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\ResourceConnection;

class Save extends Action implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'ItechPanel_Configurator::option';
    private $optionRepository;
    private $optionFactory;
    private $resourceConnection;

    public function __construct(Context $context, OptionRepositoryInterface $optionRepository, OptionInterfaceFactory $optionFactory, ResourceConnection $resourceConnection) {
        parent::__construct($context);
        $this->optionRepository = $optionRepository;
        $this->optionFactory = $optionFactory;
        $this->resourceConnection = $resourceConnection;
    }

    public function execute() {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if (!$data) return $resultRedirect->setPath('*/*/');
        $id = !empty($data['option_id']) ? (int)$data['option_id'] : null;

        try {
            $option = $id ? $this->optionRepository->getById($id) : $this->optionFactory->create();
            if (isset($data['title'])) $option->setTitle($data['title']);
            if (isset($data['label'])) $option->setLabel($data['label']);
            if (isset($data['position'])) $option->setPosition((int)$data['position']);
            if (isset($data['type'])) $option->setType($data['type']);
            if (isset($data['is_active'])) $option->setIsActive((bool)$data['is_active']);

            $this->optionRepository->save($option);

            if (isset($data['subsection_id'])) {
                $this->saveSubsectionRelationship((int)$option->getOptionId(), (int)$data['subsection_id']);
            }

            // CRITICAL: Handle JSON payload
            if (array_key_exists('option_products', $data)) {
                $productsJson = $data['option_products'];
                $productIds = [];
                if (is_string($productsJson) && !empty($productsJson) && $productsJson !== '{}') {
                    $decoded = json_decode($productsJson, true);
                    if (is_array($decoded)) $productIds = array_keys($decoded);
                }
                $this->saveProductRelationships((int)$option->getOptionId(), $productIds);
            }

            $this->messageManager->addSuccessMessage(__('The option has been saved.'));
            if ($this->getRequest()->getParam('back')) return $resultRedirect->setPath('*/*/edit', ['option_id' => $option->getOptionId()]);
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Error saving option.'));
        }
        return $resultRedirect->setPath('*/*/');
    }

    private function saveSubsectionRelationship(int $optionId, int $subsectionId) {
        $conn = $this->resourceConnection->getConnection();
        $table = $this->resourceConnection->getTableName('itechpanel_configurator_subsection_option');
        $conn->delete($table, ['option_id = ?' => $optionId]);
        if ($subsectionId > 0) $conn->insert($table, ['subsection_id' => $subsectionId, 'option_id' => $optionId]);
    }

    private function saveProductRelationships(int $optionId, array $productIds) {
        $conn = $this->resourceConnection->getConnection();
        $table = $this->resourceConnection->getTableName('itechpanel_configurator_option_product');
        $conn->delete($table, ['option_id = ?' => $optionId]);
        if (!empty($productIds)) {
            $insertData = [];
            foreach ($productIds as $pId) {
                if ((int)$pId > 0) $insertData[] = ['option_id' => $optionId, 'product_id' => (int)$pId];
            }
            if (!empty($insertData)) $conn->insertMultiple($table, $insertData);
        }
    }
}