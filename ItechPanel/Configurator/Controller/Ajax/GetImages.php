<?php
declare(strict_types=1);

namespace ItechPanel\Configurator\Controller\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Psr\Log\LoggerInterface;

// FIX 1: Added the missing "GetImages" class name
class GetImages extends Action implements HttpPostActionInterface, CsrfAwareActionInterface
{
    private $resultJsonFactory;
    private $storeManager;
    private $resourceConnection;
    private $jsonSerializer;
    private $logger;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        StoreManagerInterface $storeManager,
        ResourceConnection $resourceConnection,
        JsonSerializer $jsonSerializer,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->storeManager = $storeManager;
        $this->resourceConnection = $resourceConnection;
        $this->jsonSerializer = $jsonSerializer;
        $this->logger = $logger;
    }

    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool {
        return true;
    }

    public function execute()
    {
        $this->logger->debug("--- GetImages AJAX Hit ---");
        $result = $this->resultJsonFactory->create();
        
        try {
            $productId = (int)$this->getRequest()->getParam('product_id');
            $selectedChildrenJson = $this->getRequest()->getParam('selected_children');

            if (!$productId || !$selectedChildrenJson) {
                return $result->setData(['success' => false, 'message' => 'Missing parameters.']);
            }

            $selectedChildren = $this->jsonSerializer->unserialize($selectedChildrenJson);

            $selectedChildIds = array_filter(
                array_map('intval', array_values($selectedChildren))
            );

            $this->logger->debug("Filtered Child IDs to check images for:", $selectedChildIds);

            $images = $this->getImagesForProducts($selectedChildIds);

            return $result->setData([
                'success' => true,
                'images' => $images
            ]);
            
        } catch (\Exception $e) {
            $this->logger->error("GetImages Error: " . $e->getMessage());
            return $result->setData(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function getImagesForProducts($productIds)
    {
        $mergedImages = $this->getEmptyImagesArray();
        
        if (empty($productIds)) {
            return $mergedImages;
        }

        $connection = $this->resourceConnection->getConnection();
        $imageTable = $this->resourceConnection->getTableName('itechpanel_configurator_image');
        
        // FIX 2: Query the correct image_product table
        $imageProductTable = $this->resourceConnection->getTableName('itechpanel_configurator_image_product');

        $select = $connection->select()
            ->from(['img' => $imageTable])
            ->join(
                ['ip' => $imageProductTable],
                'img.image_id = ip.image_id',
                []
            )
            ->where('ip.product_id IN (?)', $productIds)
            ->where('img.is_active = ?', 1)
            ->order('img.image_id ASC');
        
        $imageRows = $connection->fetchAll($select);
        
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $imageBasePath = $mediaUrl . 'itechpanel/configurator/';
        
        foreach ($imageRows as $imageData) {
            foreach (array_keys($mergedImages) as $field) {
                if (!empty($imageData[$field])) {
                    $mergedImages[$field] = $imageBasePath . ltrim($imageData[$field], '/');
                }
            }
        }
        
        return $mergedImages;
    }

    private function getEmptyImagesArray()
    {
        return [
            'upload_image' => null, 'top_image' => null, 'bottom_image' => null,
            'left_image' => null, 'right_image' => null, 'front_image' => null, 'back_image' => null
        ];
    }
}