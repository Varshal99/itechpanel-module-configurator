<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Model\ResourceModel\Subsection;

use ItechPanel\Configurator\Model\ResourceModel\Subsection\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\App\ResourceConnection;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var array
     */
    private $loadedData;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param ResourceConnection $resourceConnection
     * @param array $meta
     * @param array $data
     */
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
        foreach ($items as $subsection) {
            $subsectionData = $subsection->getData();
            
            // Load section relationship
            $subsectionData['section_id'] = $this->getSectionId((int)$subsection->getId());
            
            // Load product relationships
            $subsectionData['products'] = $this->getProductIds((int)$subsection->getId());
            
            $this->loadedData[$subsection->getId()] = $subsectionData;
        }

        $data = $this->dataPersistor->get('itechpanel_configurator_subsection');
        if (!empty($data)) {
            $subsection = $this->collection->getNewEmptyItem();
            $subsection->setData($data);
            $this->loadedData[$subsection->getId()] = $subsection->getData();
            $this->dataPersistor->clear('itechpanel_configurator_subsection');
        }

        return $this->loadedData ?? [];
    }

    /**
     * Get section ID for subsection
     *
     * @param int $subsectionId
     * @return int|null
     */
    private function getSectionId(int $subsectionId)
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('itechpanel_configurator_section_subsection');
        
        $select = $connection->select()
            ->from($tableName, ['section_id'])
            ->where('subsection_id = ?', $subsectionId);
        
        $result = $connection->fetchOne($select);
        return $result ? (int)$result : null;
    }

    /**
     * Get product IDs for subsection
     *
     * @param int $subsectionId
     * @return array
     */
    private function getProductIds(int $subsectionId)
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('itechpanel_configurator_subsection_product');
        
        $select = $connection->select()
            ->from($tableName, ['product_id'])
            ->where('subsection_id = ?', $subsectionId);
        
        return $connection->fetchCol($select);
    }
}
