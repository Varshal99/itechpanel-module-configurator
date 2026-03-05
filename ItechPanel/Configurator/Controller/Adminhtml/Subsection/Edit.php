<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Controller\Adminhtml\Subsection;

use ItechPanel\Configurator\Api\SubsectionRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'ItechPanel_Configurator::subsection';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var SubsectionRepositoryInterface
     */
    private $subsectionRepository;

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param SubsectionRepositoryInterface $subsectionRepository
     * @param Registry $coreRegistry
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        SubsectionRepositoryInterface $subsectionRepository,
        Registry $coreRegistry
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->subsectionRepository = $subsectionRepository;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('subsection_id');
        
        if ($id) {
            try {
                // FIX: Actually assign the variable and register it!
                $subsection = $this->subsectionRepository->getById($id);
                $this->coreRegistry->register('current_subsection', $subsection);
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('This subsection no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('ItechPanel_Configurator::subsection');
        $resultPage->getConfig()->getTitle()->prepend(
            $id ? __('Edit Subsection') : __('New Subsection')
        );
        $resultPage->addBreadcrumb(__('Configurator'), __('Configurator'));
        $resultPage->addBreadcrumb(__('Subsections'), __('Subsections'));
        $resultPage->addBreadcrumb(
            $id ? __('Edit Subsection') : __('New Subsection'),
            $id ? __('Edit Subsection') : __('New Subsection')
        );

        return $resultPage;
    }
}