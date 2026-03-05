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
use Magento\Framework\Exception\LocalizedException;

class Delete extends Action
{
    const ADMIN_RESOURCE = 'ItechPanel_Configurator::option';

    /**
     * @var OptionRepositoryInterface
     */
    private $optionRepository;

    /**
     * @param Context $context
     * @param OptionRepositoryInterface $optionRepository
     */
    public function __construct(
        Context $context,
        OptionRepositoryInterface $optionRepository
    ) {
        parent::__construct($context);
        $this->optionRepository = $optionRepository;
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = (int)$this->getRequest()->getParam('option_id');

        if (!$id) {
            $this->messageManager->addErrorMessage(__('We can\'t find an option to delete.'));
            return $resultRedirect->setPath('*/*/');
        }

        try {
            $option = $this->optionRepository->getById($id);
            $this->optionRepository->delete($option);
            $this->messageManager->addSuccessMessage(__('The option has been deleted.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while deleting the option.'));
        }

        return $resultRedirect->setPath('*/*/');
    }
}
