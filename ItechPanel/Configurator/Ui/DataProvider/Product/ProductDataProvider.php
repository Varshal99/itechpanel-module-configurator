<?php
declare(strict_types=1);

namespace ItechPanel\Configurator\Ui\DataProvider\Product;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;

class ProductDataProvider extends AbstractDataProvider
{
    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        // Explicitly load required attributes so Thumbnail class doesn't crash
        $this->collection->addAttributeToSelect(['name', 'sku', 'price', 'thumbnail', 'status']);
        $this->collection->addAttributeToFilter('type_id', ['in' => ['simple', 'configurable', 'virtual', 'downloadable']]);
    }

    /**
     * @return array
     */
    public function getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->load();
        }

        $items = [];
        foreach ($this->getCollection() as $product) {
            $items[] = $product->getData();
        }

        return [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => $items
        ];
    }
    
    /**
     * Handle search/filtering in the modal grid
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        $field = $filter->getField();
        $conditionType = $filter->getConditionType() ?: 'eq';
        $value = $filter->getValue();

        if ($field === 'entity_id') {
            $this->collection->addFieldToFilter($field, [$conditionType => $value]);
        } else {
            $this->collection->addAttributeToFilter($field, [$conditionType => $value]);
        }
    }
}