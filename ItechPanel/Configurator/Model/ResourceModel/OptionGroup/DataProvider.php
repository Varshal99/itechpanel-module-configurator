<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Model\ResourceModel\OptionGroup;

use ItechPanel\Configurator\Model\ResourceModel\OptionGroup\CollectionFactory;
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
        foreach ($items as $group) {
            $groupData = $group->getData();
            $groupData['products'] = $this->getProductIds((int)$group->getId());
            $this->loadedData[$group->getId()] = $groupData;
        }

        $data = $this->dataPersistor->get('itechpanel_configurator_option_group');
        if (!empty($data)) {
            $group = $this->collection->getNewEmptyItem();
            $group->setData($data);
            $this->loadedData[$group->getId()] = $group->getData();
            $this->dataPersistor->clear('itechpanel_configurator_option_group');
        }

        return $this->loadedData ?? [];
    }

    private function getProductIds(int $groupId)
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('itechpanel_configurator_option_group_product');
        
        $select = $connection->select()
            ->from($tableName, ['product_id'])
            ->where('group_id = ?', $groupId);
        
        return $connection->fetchCol($select);
    }
}
