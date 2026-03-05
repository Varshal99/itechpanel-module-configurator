<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Subsection search results interface
 */
interface SubsectionSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get subsection list
     *
     * @return \ItechPanel\Configurator\Api\Data\SubsectionInterface[]
     */
    public function getItems();

    /**
     * Set subsection list
     *
     * @param \ItechPanel\Configurator\Api\Data\SubsectionInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
