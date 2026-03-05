<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Controller\Adminhtml\Section;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;

class NewAction extends Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'ItechPanel_Configurator::section';

    /**
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    /**
     * New action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultForward = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_FORWARD);
        return $resultForward->forward('edit');
    }
}
