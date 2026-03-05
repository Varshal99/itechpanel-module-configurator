<?php
declare(strict_types=1);

/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace ItechPanel\Configurator\Block\Adminhtml;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Generic back button
 */
class GenericButton
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * Get URL
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl(string $route = '', array $params = []): string
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }

    /**
     * Get entity ID from request
     *
     * @param string $paramName
     * @return int|null
     */
    public function getEntityId(string $paramName): ?int
    {
        return (int) $this->context->getRequest()->getParam($paramName) ?: null;
    }
}
