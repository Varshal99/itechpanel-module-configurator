<?php
declare(strict_types=1);

/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace ItechPanel\Configurator\Block\Adminhtml\Image\Edit;

use ItechPanel\Configurator\Block\Adminhtml\GenericButton;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Delete button for Image edit form
 */
class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData(): array
    {
        $data = [];
        if ($this->getEntityId('image_id')) {
            $data = [
                'label' => __('Delete Image'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to delete this image?'
                ) . '\', \'' . $this->getDeleteUrl() . '\', {"data": {}})',
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * Get URL for delete button
     *
     * @return string
     */
    public function getDeleteUrl(): string
    {
        return $this->getUrl('*/*/delete', ['image_id' => $this->getEntityId('image_id')]);
    }
}
