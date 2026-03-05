<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Controller\Adminhtml\Section;

use ItechPanel\Configurator\Api\SectionRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'ItechPanel_Configurator::section';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var SectionRepositoryInterface
     */
    private $sectionRepository;

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param SectionRepositoryInterface $sectionRepository
     * @param Registry $coreRegistry
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        SectionRepositoryInterface $sectionRepository,
        Registry $coreRegistry
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->sectionRepository = $sectionRepository;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('section_id');
        
        if ($id) {
            try {
                $section = $this->sectionRepository->getById($id);
                $this->coreRegistry->register('current_section', $section);
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('This section no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('ItechPanel_Configurator::section');
        $resultPage->getConfig()->getTitle()->prepend(
            $id ? __('Edit Section') : __('New Section')
        );
        $resultPage->addBreadcrumb(__('Configurator'), __('Configurator'));
        $resultPage->addBreadcrumb(__('Sections'), __('Sections'));
        $resultPage->addBreadcrumb(
            $id ? __('Edit Section') : __('New Section'),
            $id ? __('Edit Section') : __('New Section')
        );

        return $resultPage;
    }
}
