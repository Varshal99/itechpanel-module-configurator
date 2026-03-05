<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * OptionGroup search results interface
 */
interface OptionGroupSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get option group list
     *
     * @return \ItechPanel\Configurator\Api\Data\OptionGroupInterface[]
     */
    public function getItems();

    /**
     * Set option group list
     *
     * @param \ItechPanel\Configurator\Api\Data\OptionGroupInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
