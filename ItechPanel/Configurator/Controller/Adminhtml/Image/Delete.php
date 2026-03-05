<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Controller\Adminhtml\Image;

use ItechPanel\Configurator\Api\ImageRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;

class Delete extends Action
{
    const ADMIN_RESOURCE = 'ItechPanel_Configurator::image_configurator';

    /**
     * @var ImageRepositoryInterface
     */
    private $imageRepository;

    /**
     * @param Context $context
     * @param ImageRepositoryInterface $imageRepository
     */
    public function __construct(
        Context $context,
        ImageRepositoryInterface $imageRepository
    ) {
        parent::__construct($context);
        $this->imageRepository = $imageRepository;
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = (int)$this->getRequest()->getParam('image_id');

        if (!$id) {
            $this->messageManager->addErrorMessage(__('We can\'t find an image to delete.'));
            return $resultRedirect->setPath('*/*/');
        }

        try {
            $image = $this->imageRepository->getById($id);
            $this->imageRepository->delete($image);
            $this->messageManager->addSuccessMessage(__('The image has been deleted.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while deleting the image.'));
        }

        return $resultRedirect->setPath('*/*/');
    }
}
