<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Model\ResourceModel\OptionChild;

use ItechPanel\Configurator\Model\ResourceModel\OptionChild\CollectionFactory;
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
        foreach ($items as $child) {
            $childData = $child->getData();
            
            if (isset($childData['thumbnail']) && $childData['thumbnail']) {
                $childData['thumbnail'] = $this->convertImageToArray($childData['thumbnail']);
            }
            
            $childData['option_id'] = $this->getOptionId((int)$child->getId());
            $childData['optiongroup_id'] = $this->getOptionGroupId((int)$child->getId());
            $childData['products'] = $this->getProductIds((int)$child->getId());
            
            $this->loadedData[$child->getId()] = $childData;
        }

        $data = $this->dataPersistor->get('itechpanel_configurator_option_child');
        if (!empty($data)) {
            $child = $this->collection->getNewEmptyItem();
            $child->setData($data);
            
            $childData = $child->getData();
            if (isset($childData['thumbnail']) && $childData['thumbnail']) {
                $childData['thumbnail'] = $this->convertImageToArray($childData['thumbnail']);
            }
            
            $this->loadedData[$child->getId()] = $childData;
            $this->dataPersistor->clear('itechpanel_configurator_option_child');
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

    private function getOptionId(int $childId)
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('itechpanel_configurator_option_child_option');
        
        $select = $connection->select()
            ->from($tableName, ['option_id'])
            ->where('child_id = ?', $childId);
        
        $result = $connection->fetchOne($select);
        return $result ? (int)$result : null;
    }

    private function getOptionGroupId(int $childId)
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('itechpanel_configurator_option_child_group');
        
        $select = $connection->select()
            ->from($tableName, ['group_id'])
            ->where('child_id = ?', $childId);
        
        $result = $connection->fetchOne($select);
        return $result ? (int)$result : null;
    }

    private function getProductIds(int $childId)
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('itechpanel_configurator_option_child_product');
        
        $select = $connection->select()
            ->from($tableName, ['product_id'])
            ->where('child_id = ?', $childId);
        
        return $connection->fetchCol($select);
    }
}
