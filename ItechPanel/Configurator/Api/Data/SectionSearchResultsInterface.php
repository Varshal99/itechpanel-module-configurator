<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Section search results interface
 */
interface SectionSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get section list
     *
     * @return \ItechPanel\Configurator\Api\Data\SectionInterface[]
     */
    public function getItems();

    /**
     * Set section list
     *
     * @param \ItechPanel\Configurator\Api\Data\SectionInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
