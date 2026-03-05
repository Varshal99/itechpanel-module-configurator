<?php
declare(strict_types=1);

namespace ItechPanel\Configurator\Controller\Adminhtml\Section;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class ProductsGrid extends Action
{
    const ADMIN_RESOURCE = 'ItechPanel_Configurator::section';

    public function execute()
    {
        $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
        $products = $this->getRequest()->getPost('section_products', null);
        
        $gridBlock = $resultLayout->getLayout()->getBlock('section.product.grid');
        if ($gridBlock) {
            $gridBlock->setSelectedProducts(is_string($products) ? json_decode($products, true) : []);
        }

        return $resultLayout;
    }
}