<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Plugin\Quote\Model\Quote\Item;

use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Quote\Model\Quote\Item\ToOrderItem as QuoteToOrderItem;
use Magento\Sales\Model\Order\Item as OrderItem;

/**
 * Plugin to transfer configurator options from quote to order
 */
class ToOrderItem
{
    /**
     * Transfer configurator options from quote item to order item
     *
     * @param QuoteToOrderItem $subject
     * @param callable $proceed
     * @param AbstractItem $item
     * @param array $additional
     * @return OrderItem
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundConvert(
        QuoteToOrderItem $subject,
        callable $proceed,
        AbstractItem $item,
        $additional = []
    ) {
        /** @var OrderItem $orderItem */
        $orderItem = $proceed($item, $additional);

        $options = $item->getOptions();
        
        if (!$options) {
            return $orderItem;
        }

        foreach ($options as $option) {
            if (in_array($option->getCode(), ['additional_options', 'configurator_selections'])) {
                $orderItem->addOption([
                    'code' => $option->getCode(),
                    'value' => $option->getValue(),
                    'product_id' => $orderItem->getProductId()
                ]);
            }
        }

        return $orderItem;
    }
}
