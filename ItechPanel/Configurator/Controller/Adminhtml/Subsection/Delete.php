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
use Magento\Framework\Exception\LocalizedException;

class Delete extends Action
{
    const ADMIN_RESOURCE = 'ItechPanel_Configurator::subsection';

    /**
     * @var SubsectionRepositoryInterface
     */
    private $subsectionRepository;

    /**
     * @param Context $context
     * @param SubsectionRepositoryInterface $subsectionRepository
     */
    public function __construct(
        Context $context,
        SubsectionRepositoryInterface $subsectionRepository
    ) {
        parent::__construct($context);
        $this->subsectionRepository = $subsectionRepository;
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = (int)$this->getRequest()->getParam('subsection_id');

        if (!$id) {
            $this->messageManager->addErrorMessage(__('We can\'t find a subsection to delete.'));
            return $resultRedirect->setPath('*/*/');
        }

        try {
            $subsection = $this->subsectionRepository->getById($id);
            $this->subsectionRepository->delete($subsection);
            $this->messageManager->addSuccessMessage(__('The subsection has been deleted.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while deleting the subsection.'));
        }

        return $resultRedirect->setPath('*/*/');
    }
}
