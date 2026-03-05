<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Controller\Adminhtml\Option;

use ItechPanel\Configurator\Api\OptionRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'ItechPanel_Configurator::option';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var OptionRepositoryInterface
     */
    private $optionRepository;

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param OptionRepositoryInterface $optionRepository
     * @param Registry $coreRegistry
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        OptionRepositoryInterface $optionRepository,
        Registry $coreRegistry
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->optionRepository = $optionRepository;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('option_id');
        
        if ($id) {
            try {
                $option = $this->optionRepository->getById($id);
                $this->coreRegistry->register('current_option', $option);
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('This option no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('ItechPanel_Configurator::option');
        $resultPage->getConfig()->getTitle()->prepend(
            $id ? __('Edit Option') : __('New Option')
        );
        $resultPage->addBreadcrumb(__('Configurator'), __('Configurator'));
        $resultPage->addBreadcrumb(__('Options'), __('Options'));
        $resultPage->addBreadcrumb(
            $id ? __('Edit Option') : __('New Option'),
            $id ? __('Edit Option') : __('New Option')
        );

        return $resultPage;
    }
}
