<?php
declare(strict_types=1);

/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace ItechPanel\Configurator\Block\Adminhtml\OptionGroup\Edit;

use ItechPanel\Configurator\Block\Adminhtml\GenericButton;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Delete button for OptionGroup edit form
 */
class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData(): array
    {
        $data = [];
        if ($this->getEntityId('group_id')) {
            $data = [
                'label' => __('Delete Option Group'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to delete this option group?'
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
        return $this->getUrl('*/*/delete', ['group_id' => $this->getEntityId('group_id')]);
    }
}
