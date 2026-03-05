<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Model\ResourceModel\Option;

use ItechPanel\Configurator\Model\ResourceModel\Option\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\App\ResourceConnection;

class DataProvider extends AbstractDataProvider
{
    private $dataPersistor;
    private $loadedData;
    private $resourceConnection;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        ResourceConnection $resourceConnection,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
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
        foreach ($items as $option) {
            $optionData = $option->getData();
            
            $optionData['subsection_id'] = $this->getSubsectionId((int)$option->getId());
            $optionData['products'] = $this->getProductIds((int)$option->getId());
            
            $this->loadedData[$option->getId()] = $optionData;
        }

        $data = $this->dataPersistor->get('itechpanel_configurator_option');
        if (!empty($data)) {
            $option = $this->collection->getNewEmptyItem();
            $option->setData($data);
            $this->loadedData[$option->getId()] = $option->getData();
            $this->dataPersistor->clear('itechpanel_configurator_option');
        }

        return $this->loadedData ?? [];
    }

    private function getSubsectionId(int $optionId)
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('itechpanel_configurator_subsection_option');
        
        $select = $connection->select()
            ->from($tableName, ['subsection_id'])
            ->where('option_id = ?', $optionId);
        
        $result = $connection->fetchOne($select);
        return $result ? (int)$result : null;
    }

    private function getProductIds(int $optionId)
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('itechpanel_configurator_option_product');
        
        $select = $connection->select()
            ->from($tableName, ['product_id'])
            ->where('option_id = ?', $optionId);
        
        return $connection->fetchCol($select);
    }
}
