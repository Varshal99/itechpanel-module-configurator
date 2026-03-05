<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Option type source model
 */
class OptionType implements OptionSourceInterface
{
    const TYPE_BUTTON = 'button';
    const TYPE_DROPDOWN = 'dropdown';
    const TYPE_RADIO = 'radio';
    const TYPE_SWATCH = 'swatch';

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::TYPE_BUTTON, 'label' => __('Button')],
            ['value' => self::TYPE_DROPDOWN, 'label' => __('Dropdown')],
            ['value' => self::TYPE_RADIO, 'label' => __('Radio')],
            ['value' => self::TYPE_SWATCH, 'label' => __('Swatch')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            self::TYPE_BUTTON => __('Button'),
            self::TYPE_DROPDOWN => __('Dropdown'),
            self::TYPE_RADIO => __('Radio'),
            self::TYPE_SWATCH => __('Swatch')
        ];
    }
}
