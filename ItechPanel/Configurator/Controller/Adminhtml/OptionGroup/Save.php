<?php
declare(strict_types=1);

namespace ItechPanel\Configurator\Controller\Adminhtml\OptionGroup;

use ItechPanel\Configurator\Api\Data\OptionGroupInterfaceFactory;
use ItechPanel\Configurator\Api\OptionGroupRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\ResourceConnection;

class Save extends Action implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'ItechPanel_Configurator::option_group';
    private $optionGroupRepository;
    private $optionGroupFactory;
    private $resourceConnection;

    public function __construct(Context $context, OptionGroupRepositoryInterface $optionGroupRepository, OptionGroupInterfaceFactory $optionGroupFactory, ResourceConnection $resourceConnection) {
        parent::__construct($context);
        $this->optionGroupRepository = $optionGroupRepository;
        $this->optionGroupFactory = $optionGroupFactory;
        $this->resourceConnection = $resourceConnection;
    }

    public function execute() {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if (!$data) return $resultRedirect->setPath('*/*/');
        $id = !empty($data['group_id']) ? (int)$data['group_id'] : null;

        try {
            $optionGroup = $id ? $this->optionGroupRepository->getById($id) : $this->optionGroupFactory->create();
            if (isset($data['title'])) $optionGroup->setTitle($data['title']);
            if (isset($data['label'])) $optionGroup->setLabel($data['label']);
            if (isset($data['position'])) $optionGroup->setPosition((int)$data['position']);
            if (isset($data['is_active'])) $optionGroup->setIsActive((bool)$data['is_active']);

            $this->optionGroupRepository->save($optionGroup);

            // CRITICAL: Handle JSON payload
            if (array_key_exists('optiongroup_products', $data)) {
                $productsJson = $data['optiongroup_products'];
                $productIds = [];
                if (is_string($productsJson) && !empty($productsJson) && $productsJson !== '{}') {
                    $decoded = json_decode($productsJson, true);
                    if (is_array($decoded)) $productIds = array_keys($decoded);
                }
                $this->saveProductRelationships((int)$optionGroup->getGroupId(), $productIds);
            }

            $this->messageManager->addSuccessMessage(__('The option group has been saved.'));
            if ($this->getRequest()->getParam('back')) return $resultRedirect->setPath('*/*/edit', ['group_id' => $optionGroup->getGroupId()]);
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Error saving option group.'));
        }
        return $resultRedirect->setPath('*/*/');
    }

    private function saveProductRelationships(int $groupId, array $productIds) {
        $conn = $this->resourceConnection->getConnection();
        $table = $this->resourceConnection->getTableName('itechpanel_configurator_option_group_product');
        $conn->delete($table, ['group_id = ?' => $groupId]);
        if (!empty($productIds)) {
            $insertData = [];
            foreach ($productIds as $pId) {
                if ((int)$pId > 0) $insertData[] = ['group_id' => $groupId, 'product_id' => (int)$pId];
            }
            if (!empty($insertData)) $conn->insertMultiple($table, $insertData);
        }
    }
}