<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Controller\Adminhtml\Image;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use ItechPanel\Configurator\Model\ImageUploader;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

class Upload extends Action implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'ItechPanel_Configurator::image_configurator';

    /**
     * @var ImageUploader
     */
    private $imageUploader;

    /**
     * @param Context $context
     * @param ImageUploader $imageUploader
     */
    public function __construct(
        Context $context,
        ImageUploader $imageUploader
    ) {
        parent::__construct($context);
        $this->imageUploader = $imageUploader;
    }

    /**
     * Upload file controller action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $imageId = $this->getRequest()->getParam('param_name', 'image');
        
        try {
            $result = $this->imageUploader->saveFileToTmpDir($imageId);
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
