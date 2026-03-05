<?php
declare(strict_types=1);

namespace ItechPanel\Configurator\Block\Adminhtml\Subsection;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;

class AssignProducts extends Template
{
    protected $_template = 'ItechPanel_Configurator::subsection/assign_products.phtml';
    protected $coreRegistry;
    protected $blockGrid;

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                \ItechPanel\Configurator\Block\Adminhtml\Subsection\Tab\Product::class,
                'subsection.product.grid'
            );
        }
        return $this->blockGrid;
    }

    public function getGridHtml()
    {
        return $this->getBlockGrid()->toHtml();
    }

    public function getProductsJson()
    {
        $products = $this->getBlockGrid()->getSelectedProducts();
        if (!empty($products)) {
            $result = [];
            foreach ($products as $productId) {
                $result[$productId] = '0';
            }
            return json_encode($result);
        }
        return '{}';
    }
}