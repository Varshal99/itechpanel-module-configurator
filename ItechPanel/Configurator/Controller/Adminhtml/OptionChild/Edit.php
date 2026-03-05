<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Controller\Adminhtml\OptionChild;

use ItechPanel\Configurator\Api\OptionChildRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'ItechPanel_Configurator::option_child';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var OptionChildRepositoryInterface
     */
    private $optionChildRepository;

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param OptionChildRepositoryInterface $optionChildRepository
     * @param Registry $coreRegistry
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        OptionChildRepositoryInterface $optionChildRepository,
        Registry $coreRegistry
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->optionChildRepository = $optionChildRepository;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('child_id');
        
        if ($id) {
            try {
                $optionChild = $this->optionChildRepository->getById($id);
                $this->coreRegistry->register('current_optionchild', $optionChild);
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('This option child no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('ItechPanel_Configurator::option_child');
        $resultPage->getConfig()->getTitle()->prepend(
            $id ? __('Edit Option Child') : __('New Option Child')
        );
        $resultPage->addBreadcrumb(__('Configurator'), __('Configurator'));
        $resultPage->addBreadcrumb(__('Option Children'), __('Option Children'));
        $resultPage->addBreadcrumb(
            $id ? __('Edit Option Child') : __('New Option Child'),
            $id ? __('Edit Option Child') : __('New Option Child')
        );

        return $resultPage;
    }
}
