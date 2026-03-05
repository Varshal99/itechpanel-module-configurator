<?php
declare(strict_types=1);

namespace ItechPanel\Configurator\Ui\DataProvider\Product;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Ui\DataProvider\AbstractDataProvider;

class SectionProductDataProvider extends AbstractDataProvider
{
    protected $collection;
    private $request;
    private $resourceConnection;
    protected $addFieldStrategies;
    protected $addFilterStrategies;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        ResourceConnection $resourceConnection,
        array $addFieldStrategies = [],
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->request = $request;
        $this->resourceConnection = $resourceConnection;
        $this->addFieldStrategies = $addFieldStrategies;
        $this->addFilterStrategies = $addFilterStrategies;
        $this->prepareCollection();
    }

    private function prepareCollection()
    {
        // FIX: Add 'thumbnail' to requested attributes
        $this->collection->addAttributeToSelect(['name', 'sku', 'price', 'status', 'thumbnail']);
        $this->collection->addAttributeToFilter('type_id', ['in' => ['simple', 'configurable', 'virtual', 'downloadable']]);
    }

    public function getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->load();
        }

        $items = [];
        foreach ($this->getCollection()->getItems() as $item) {
            $items[] = $item->getData();
        }

        $sectionId = $this->request->getParam('section_id');
        
        $selectedProducts = [];
        if ($sectionId) {
            $selectedProducts = $this->getSelectedProducts((int)$sectionId);
        }
        
        return [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => $items,
            'selectedProducts' => $selectedProducts
        ];
    }

    private function getSelectedProducts(int $sectionId): array
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('itechpanel_configurator_section_product');
        
        $select = $connection->select()
            ->from($tableName, ['product_id'])
            ->where('section_id = ?', $sectionId);
        
        return $connection->fetchCol($select) ?: [];
    }

    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        $field = $filter->getField();
        $condition = $filter->getConditionType() ?? 'eq';

        if ($field) {
            $this->getCollection()->addFieldToFilter($field, [$condition => $filter->getValue()]);
        }

        return $this;
    }
}
