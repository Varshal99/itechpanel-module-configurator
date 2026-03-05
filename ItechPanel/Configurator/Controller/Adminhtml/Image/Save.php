<?php
declare(strict_types=1);

namespace ItechPanel\Configurator\Controller\Adminhtml\Image;

use ItechPanel\Configurator\Api\Data\ImageInterfaceFactory;
use ItechPanel\Configurator\Api\ImageRepositoryInterface;
use ItechPanel\Configurator\Model\ImageUploader;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\ResourceConnection;

class Save extends Action implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'ItechPanel_Configurator::image_configurator';
    private $imageRepository;
    private $imageFactory;
    private $imageUploader;
    private $resourceConnection;

    public function __construct(
        Context $context, 
        ImageRepositoryInterface $imageRepository, 
        ImageInterfaceFactory $imageFactory, 
        ImageUploader $imageUploader, 
        ResourceConnection $resourceConnection
    ) {
        parent::__construct($context);
        $this->imageRepository = $imageRepository;
        $this->imageFactory = $imageFactory;
        $this->imageUploader = $imageUploader;
        $this->resourceConnection = $resourceConnection;
    }

    public function execute() {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if (!$data) return $resultRedirect->setPath('*/*/');
        $id = !empty($data['image_id']) ? (int)$data['image_id'] : null;

        try {
            $image = $id ? $this->imageRepository->getById($id) : $this->imageFactory->create();
            if (isset($data['title'])) $image->setTitle($data['title']);

            // --- FIX: Correctly handle image deletion ---
            $fields = ['upload_image', 'top_image', 'bottom_image', 'left_image', 'right_image', 'front_image', 'back_image'];
            foreach ($fields as $field) {
                // Check if the field exists AND is not empty
                if (isset($data[$field]) && !empty($data[$field])) {
                    $imageName = is_array($data[$field]) && isset($data[$field][0]['name']) ? $data[$field][0]['name'] : $data[$field];
                    
                    if ($imageName && is_string($imageName)) {
                        try {
                            $imageName = $this->imageUploader->moveFileFromTmp($imageName);
                        } catch (\Exception $e) {
                            // File already exists in destination, safely ignore tmp move error
                        }
                        $image->setData($field, $imageName);
                    } else {
                        $image->setData($field, null);
                    }
                } else {
                    // The field was cleared in the UI (trash icon clicked), wipe it from DB
                    $image->setData($field, null);
                }
            }

            if (isset($data['is_active'])) $image->setIsActive((bool)$data['is_active']);

            $this->imageRepository->save($image);

            if (array_key_exists('image_products', $data)) {
                $productsJson = $data['image_products'];
                $productIds = [];
                if (is_string($productsJson) && !empty($productsJson) && $productsJson !== '{}') {
                    $decoded = json_decode($productsJson, true);
                    if (is_array($decoded)) $productIds = array_keys($decoded);
                }
                $this->saveProductRelationships((int)$image->getImageId(), $productIds);
            }

            $this->messageManager->addSuccessMessage(__('The image has been saved.'));
            if ($this->getRequest()->getParam('back')) return $resultRedirect->setPath('*/*/edit', ['image_id' => $image->getImageId()]);
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Error saving image.'));
        }
        return $resultRedirect->setPath('*/*/');
    }

    private function saveProductRelationships(int $imageId, array $productIds) {
        $conn = $this->resourceConnection->getConnection();
        $table = $this->resourceConnection->getTableName('itechpanel_configurator_image_product');
        $conn->delete($table, ['image_id = ?' => $imageId]);
        
        if (!empty($productIds)) {
            $insertData = [];
            foreach ($productIds as $pId) {
                if ((int)$pId > 0) $insertData[] = ['image_id' => $imageId, 'product_id' => (int)$pId];
            }
            if (!empty($insertData)) $conn->insertMultiple($table, $insertData);
        }
    }
}