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
use Magento\Framework\Exception\LocalizedException;

class Delete extends Action
{
    const ADMIN_RESOURCE = 'ItechPanel_Configurator::option_child';

    /**
     * @var OptionChildRepositoryInterface
     */
    private $optionChildRepository;

    /**
     * @param Context $context
     * @param OptionChildRepositoryInterface $optionChildRepository
     */
    public function __construct(
        Context $context,
        OptionChildRepositoryInterface $optionChildRepository
    ) {
        parent::__construct($context);
        $this->optionChildRepository = $optionChildRepository;
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = (int)$this->getRequest()->getParam('child_id');

        if (!$id) {
            $this->messageManager->addErrorMessage(__('We can\'t find an option child to delete.'));
            return $resultRedirect->setPath('*/*/');
        }

        try {
            $optionChild = $this->optionChildRepository->getById($id);
            $this->optionChildRepository->delete($optionChild);
            $this->messageManager->addSuccessMessage(__('The option child has been deleted.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while deleting the option child.'));
        }

        return $resultRedirect->setPath('*/*/');
    }
}
