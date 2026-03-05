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
use Magento\Framework\Exception\LocalizedException;

class Delete extends Action
{
    const ADMIN_RESOURCE = 'ItechPanel_Configurator::option_group';

    /**
     * @var OptionGroupRepositoryInterface
     */
    private $optionGroupRepository;

    /**
     * @param Context $context
     * @param OptionGroupRepositoryInterface $optionGroupRepository
     */
    public function __construct(
        Context $context,
        OptionGroupRepositoryInterface $optionGroupRepository
    ) {
        parent::__construct($context);
        $this->optionGroupRepository = $optionGroupRepository;
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = (int)$this->getRequest()->getParam('group_id');

        if (!$id) {
            $this->messageManager->addErrorMessage(__('We can\'t find an option group to delete.'));
            return $resultRedirect->setPath('*/*/');
        }

        try {
            $optionGroup = $this->optionGroupRepository->getById($id);
            $this->optionGroupRepository->delete($optionGroup);
            $this->messageManager->addSuccessMessage(__('The option group has been deleted.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while deleting the option group.'));
        }

        return $resultRedirect->setPath('*/*/');
    }
}
