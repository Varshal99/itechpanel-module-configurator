<?php
declare(strict_types=1);

namespace ItechPanel\Configurator\Block\Adminhtml\Option\Tab;

use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Registry;
use Magento\Framework\App\ResourceConnection;

class Product extends Extended
{
    protected $productCollectionFactory;
    protected $coreRegistry;
    protected $resourceConnection;
    protected $selectedProducts = [];

    public function __construct(Context $context, Data $backendHelper, CollectionFactory $productCollectionFactory, Registry $coreRegistry, ResourceConnection $resourceConnection, array $data = []) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->coreRegistry = $coreRegistry;
        $this->resourceConnection = $resourceConnection;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct() {
        parent::_construct();
        $this->setId('option_product_grid');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
    }

    public function getOption() { return $this->coreRegistry->registry('current_option'); }
    public function setSelectedProducts($products) { $this->selectedProducts = $products; return $this; }

    public function getSelectedProducts() {
        $products = $this->getRequest()->getPost('option_products');
        if ($products === null) {
            if (!empty($this->selectedProducts)) return array_keys($this->selectedProducts);
            $entity = $this->getOption();
            if ($entity && $entity->getId()) {
                $conn = $this->resourceConnection->getConnection();
                $table = $this->resourceConnection->getTableName('itechpanel_configurator_option_product');
                return $conn->fetchCol($conn->select()->from($table, ['product_id'])->where('option_id = ?', $entity->getId()));
            }
            return [];
        }
        return array_keys(json_decode($products, true) ?: []);
    }

    protected function _prepareCollection() {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect(['name', 'sku', 'price']);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('in_products', ['type' => 'checkbox', 'name' => 'in_products', 'values' => $this->getSelectedProducts(), 'index' => 'entity_id', 'header_css_class' => 'col-select col-massaction', 'column_css_class' => 'col-select col-massaction']);
        $this->addColumn('entity_id', ['header' => __('ID'), 'sortable' => true, 'index' => 'entity_id', 'header_css_class' => 'col-id', 'column_css_class' => 'col-id']);
        $this->addColumn('name', ['header' => __('Name'), 'index' => 'name']);
        $this->addColumn('sku', ['header' => __('SKU'), 'index' => 'sku']);
        $this->addColumn('price', ['header' => __('Price'), 'type' => 'currency', 'currency_code' => (string)$this->_scopeConfig->getValue(\Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE), 'index' => 'price']);
        return parent::_prepareColumns();
    }

    protected function _addColumnFilterToCollection($column) {
        if ($column->getId() == 'in_products') {
            $productIds = $this->getSelectedProducts();
            if (empty($productIds)) $productIds = 0;
            if ($column->getFilter()->getValue()) $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
            elseif (!empty($productIds)) $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
        } else parent::_addColumnFilterToCollection($column);
        return $this;
    }

    public function getGridUrl() { return $this->getUrl('configurator/option/productsgrid', ['_current' => true]); }
}