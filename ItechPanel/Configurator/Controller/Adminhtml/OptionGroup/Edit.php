<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Controller\Adminhtml\OptionGroup;

use ItechPanel\Configurator\Api\OptionGroupRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'ItechPanel_Configurator::option_group';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var OptionGroupRepositoryInterface
     */
    private $optionGroupRepository;

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param OptionGroupRepositoryInterface $optionGroupRepository
     * @param Registry $coreRegistry
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        OptionGroupRepositoryInterface $optionGroupRepository,
        Registry $coreRegistry
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->optionGroupRepository = $optionGroupRepository;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('group_id');
        
        if ($id) {
            try {
                $optionGroup = $this->optionGroupRepository->getById($id);
                $this->coreRegistry->register('current_optiongroup', $optionGroup);
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('This option group no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('ItechPanel_Configurator::option_group');
        $resultPage->getConfig()->getTitle()->prepend(
            $id ? __('Edit Option Group') : __('New Option Group')
        );
        $resultPage->addBreadcrumb(__('Configurator'), __('Configurator'));
        $resultPage->addBreadcrumb(__('Option Groups'), __('Option Groups'));
        $resultPage->addBreadcrumb(
            $id ? __('Edit Option Group') : __('New Option Group'),
            $id ? __('Edit Option Group') : __('New Option Group')
        );

        return $resultPage;
    }
}
