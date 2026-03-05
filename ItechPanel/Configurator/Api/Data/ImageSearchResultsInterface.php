<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Image search results interface
 */
interface ImageSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get image list
     *
     * @return \ItechPanel\Configurator\Api\Data\ImageInterface[]
     */
    public function getItems();

    /**
     * Set image list
     *
     * @param \ItechPanel\Configurator\Api\Data\ImageInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
