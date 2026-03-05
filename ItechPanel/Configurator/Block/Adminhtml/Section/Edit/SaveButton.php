<?php
declare(strict_types=1);

/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace ItechPanel\Configurator\Block\Adminhtml\Section\Edit;

use ItechPanel\Configurator\Block\Adminhtml\GenericButton;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Save button for Section edit form
 */
class SaveButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData(): array
    {
        return [
            'label' => __('Save'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 90,
        ];
    }
}
