<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Model\ResourceModel\Image;

use ItechPanel\Configurator\Model\ResourceModel\Image\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\App\ResourceConnection;

class DataProvider extends AbstractDataProvider
{
    private $dataPersistor;
    private $loadedData;
    private $storeManager;
    private $resourceConnection;

    private const IMAGE_FIELDS = [
        'upload_image',
        'top_image',
        'bottom_image',
        'left_image',
        'right_image',
        'front_image',
        'back_image'
    ];

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        StoreManagerInterface $storeManager,
        ResourceConnection $resourceConnection,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->storeManager = $storeManager;
        $this->resourceConnection = $resourceConnection;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $items = $this->collection->getItems();
        foreach ($items as $image) {
            $imageData = $image->getData();
            
            foreach (self::IMAGE_FIELDS as $field) {
                if (isset($imageData[$field]) && $imageData[$field]) {
                    $imageData[$field] = $this->convertImageToArray($imageData[$field]);
                }
            }
            
            $imageData['products'] = $this->getProductIds((int)$image->getId());
            
            $this->loadedData[$image->getId()] = $imageData;
        }

        $data = $this->dataPersistor->get('itechpanel_configurator_image');
        if (!empty($data)) {
            $image = $this->collection->getNewEmptyItem();
            $image->setData($data);
            
            $imageData = $image->getData();
            foreach (self::IMAGE_FIELDS as $field) {
                if (isset($imageData[$field]) && $imageData[$field]) {
                    $imageData[$field] = $this->convertImageToArray($imageData[$field]);
                }
            }
            
            $this->loadedData[$image->getId()] = $imageData;
            $this->dataPersistor->clear('itechpanel_configurator_image');
        }

        return $this->loadedData ?? [];
    }

    /**
     * Convert image to array format
     *
     * @param string $image
     * @return array
     */
    private function convertImageToArray($image)
    {
        if (!$image) {
            return [];
        }

        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        $imagePath = ltrim($image, '/');
        $fullPath = BP . '/pub/media/itechpanel/configurator/' . $imagePath;
        
        return [
            [
                'name' => basename($imagePath),
                'url' => $mediaUrl . 'itechpanel/configurator/' . $imagePath,
                'size' => file_exists($fullPath) ? filesize($fullPath) : 0,
                'type' => file_exists($fullPath) ? mime_content_type($fullPath) : 'image/jpeg'
            ]
        ];
    }

    private function getProductIds(int $imageId)
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('itechpanel_configurator_image_product');
        
        $select = $connection->select()
            ->from($tableName, ['product_id'])
            ->where('image_id = ?', $imageId);
        
        return $connection->fetchCol($select);
    }
}
