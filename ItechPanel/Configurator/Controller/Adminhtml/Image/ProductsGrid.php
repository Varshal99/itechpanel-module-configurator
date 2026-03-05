<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Controller\Adminhtml\Image;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class ProductsGrid extends Action
{
    const ADMIN_RESOURCE = 'ItechPanel_Configurator::image_configurator';

    public function __construct(
        Context $context
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Layout $resultLayout */
        $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
        $resultLayout->getLayout()->getBlock('image.product.grid')->setProducts($this->getRequest()->getPost('products', []));
        
        return $resultLayout;
    }
}
