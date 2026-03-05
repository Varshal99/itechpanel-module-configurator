<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Option search results interface
 */
interface OptionSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get option list
     *
     * @return \ItechPanel\Configurator\Api\Data\OptionInterface[]
     */
    public function getItems();

    /**
     * Set option list
     *
     * @param \ItechPanel\Configurator\Api\Data\OptionInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
