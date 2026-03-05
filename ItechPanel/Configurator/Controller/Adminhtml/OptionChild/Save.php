<?php
declare(strict_types=1);

namespace ItechPanel\Configurator\Controller\Adminhtml\OptionChild;

use ItechPanel\Configurator\Api\Data\OptionChildInterfaceFactory;
use ItechPanel\Configurator\Api\OptionChildRepositoryInterface;
use ItechPanel\Configurator\Model\ImageUploader;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\ResourceConnection;

class Save extends Action implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'ItechPanel_Configurator::option_child';
    private $optionChildRepository;
    private $optionChildFactory;
    private $imageUploader;
    private $resourceConnection;

    public function __construct(
        Context $context, 
        OptionChildRepositoryInterface $optionChildRepository, 
        OptionChildInterfaceFactory $optionChildFactory, 
        ImageUploader $imageUploader, 
        ResourceConnection $resourceConnection
    ) {
        parent::__construct($context);
        $this->optionChildRepository = $optionChildRepository;
        $this->optionChildFactory = $optionChildFactory;
        $this->imageUploader = $imageUploader;
        $this->resourceConnection = $resourceConnection;
    }

    public function execute() {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if (!$data) return $resultRedirect->setPath('*/*/');
        $id = !empty($data['child_id']) ? (int)$data['child_id'] : null;

        try {
            $optionChild = $id ? $this->optionChildRepository->getById($id) : $this->optionChildFactory->create();
            if (isset($data['title'])) $optionChild->setTitle($data['title']);
            if (isset($data['label'])) $optionChild->setLabel($data['label']);
            if (isset($data['tooltip'])) $optionChild->setTooltip($data['tooltip']);
            if (isset($data['price'])) $optionChild->setPrice((float)$data['price']);
            if (isset($data['position'])) $optionChild->setPosition((int)$data['position']);
            if (isset($data['is_active'])) $optionChild->setIsActive((bool)$data['is_active']);

            // --- FIX: Correctly handle thumbnail deletion ---
            if (isset($data['thumbnail']) && !empty($data['thumbnail'])) {
                $imageName = is_array($data['thumbnail']) && isset($data['thumbnail'][0]['name']) ? $data['thumbnail'][0]['name'] : $data['thumbnail'];
                if ($imageName && is_string($imageName)) {
                    try {
                        $imageName = $this->imageUploader->moveFileFromTmp($imageName);
                    } catch (\Exception $e) {}
                    $optionChild->setThumbnail($imageName);
                } else {
                    $optionChild->setThumbnail(null);
                }
            } else {
                // Image was deleted in UI
                $optionChild->setThumbnail(null);
            }

            $this->optionChildRepository->save($optionChild);

            if (isset($data['option_id'])) $this->saveOptionRelationship((int)$optionChild->getChildId(), (int)$data['option_id']);
            if (isset($data['optiongroup_id'])) $this->saveOptionGroupRelationship((int)$optionChild->getChildId(), (int)$data['optiongroup_id']);

            if (array_key_exists('optionchild_products', $data)) {
                $productsJson = $data['optionchild_products'];
                $productIds = [];
                if (is_string($productsJson) && !empty($productsJson) && $productsJson !== '{}') {
                    $decoded = json_decode($productsJson, true);
                    if (is_array($decoded)) $productIds = array_keys($decoded);
                }
                $this->saveProductRelationships((int)$optionChild->getChildId(), $productIds);
            }

            $this->messageManager->addSuccessMessage(__('The option child has been saved.'));
            if ($this->getRequest()->getParam('back')) return $resultRedirect->setPath('*/*/edit', ['child_id' => $optionChild->getChildId()]);
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Error saving option child.'));
        }
        return $resultRedirect->setPath('*/*/');
    }

    private function saveOptionRelationship(int $childId, int $optionId) {
        $conn = $this->resourceConnection->getConnection();
        $table = $this->resourceConnection->getTableName('itechpanel_configurator_option_child_option');
        $conn->delete($table, ['child_id = ?' => $childId]);
        if ($optionId > 0) $conn->insert($table, ['option_id' => $optionId, 'child_id' => $childId]);
    }

    private function saveOptionGroupRelationship(int $childId, int $groupId) {
        $conn = $this->resourceConnection->getConnection();
        $table = $this->resourceConnection->getTableName('itechpanel_configurator_option_child_group');
        $conn->delete($table, ['child_id = ?' => $childId]);
        if ($groupId > 0) $conn->insert($table, ['group_id' => $groupId, 'child_id' => $childId]);
    }

    private function saveProductRelationships(int $childId, array $productIds) {
        $conn = $this->resourceConnection->getConnection();
        $table = $this->resourceConnection->getTableName('itechpanel_configurator_option_child_product');
        $conn->delete($table, ['child_id = ?' => $childId]);
        
        if (!empty($productIds)) {
            $insertData = [];
            foreach ($productIds as $pId) {
                if ((int)$pId > 0) $insertData[] = ['child_id' => $childId, 'product_id' => (int)$pId];
            }
            if (!empty($insertData)) $conn->insertMultiple($table, $insertData);
        }
    }
}