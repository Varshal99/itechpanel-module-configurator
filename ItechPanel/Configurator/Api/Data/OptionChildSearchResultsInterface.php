<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * OptionChild search results interface
 */
interface OptionChildSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get option child list
     *
     * @return \ItechPanel\Configurator\Api\Data\OptionChildInterface[]
     */
    public function getItems();

    /**
     * Set option child list
     *
     * @param \ItechPanel\Configurator\Api\Data\OptionChildInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
