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
use Magento\Framework\Exception\LocalizedException;

class Delete extends Action
{
    const ADMIN_RESOURCE = 'ItechPanel_Configurator::section';

    /**
     * @var SectionRepositoryInterface
     */
    private $sectionRepository;

    /**
     * @param Context $context
     * @param SectionRepositoryInterface $sectionRepository
     */
    public function __construct(
        Context $context,
        SectionRepositoryInterface $sectionRepository
    ) {
        parent::__construct($context);
        $this->sectionRepository = $sectionRepository;
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = (int)$this->getRequest()->getParam('section_id');

        if (!$id) {
            $this->messageManager->addErrorMessage(__('We can\'t find a section to delete.'));
            return $resultRedirect->setPath('*/*/');
        }

        try {
            $section = $this->sectionRepository->getById($id);
            $this->sectionRepository->delete($section);
            $this->messageManager->addSuccessMessage(__('The section has been deleted.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while deleting the section.'));
        }

        return $resultRedirect->setPath('*/*/');
    }
}
