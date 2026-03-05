<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Block\Cart\Item\Renderer;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;

/**
 * Block for rendering configurator options in cart
 */
class Configurator extends Template
{
    /**
     * @var Json
     */
    private $jsonSerializer;

    /**
     * @var PricingHelper
     */
    private $pricingHelper;

    /**
     * @var array|null
     */
    private $configuratorOptions = null;

    /**
     * @param Context $context
     * @param Json $jsonSerializer
     * @param PricingHelper $pricingHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Json $jsonSerializer,
        PricingHelper $pricingHelper,
        array $data = []
    ) {
        $this->jsonSerializer = $jsonSerializer;
        $this->pricingHelper = $pricingHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get configurator options from quote or order item
     *
     * @return array
     */
    public function getConfiguratorOptions(): array
    {
        if ($this->configuratorOptions !== null) {
            return $this->configuratorOptions;
        }

        $this->configuratorOptions = [];
        
        $item = $this->getItem();
        
        if (!$item) {
            return $this->configuratorOptions;
        }

        try {
            $option = $item->getOptionByCode('additional_options');
            
            if ($option) {
                $value = $option->getValue();
                $options = is_string($value) 
                    ? $this->jsonSerializer->unserialize($value) 
                    : $value;
                
                if (is_array($options)) {
                    $this->configuratorOptions = $options;
                }
            }
        } catch (\Exception $e) {
            $this->_logger->error('Error getting configurator options: ' . $e->getMessage());
        }

        return $this->configuratorOptions;
    }

    /**
     * Format price
     *
     * @param float $price
     * @return string
     */
    public function formatPrice($price): string
    {
        return $this->pricingHelper->currency($price, true, false);
    }

    /**
     * Check if configurator options exist
     *
     * @return bool
     */
    public function hasConfiguratorOptions(): bool
    {
        return !empty($this->getConfiguratorOptions());
    }
}
