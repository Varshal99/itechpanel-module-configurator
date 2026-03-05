<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use ItechPanel\Configurator\Model\ResourceModel\Option\CollectionFactory;

class Option implements OptionSourceInterface
{
    private $collectionFactory;

    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    public function toOptionArray()
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('is_active', ['eq' => 1]);
        $collection->setOrder('title', 'ASC');

        $options = [];
        foreach ($collection as $option) {
            $options[] = [
                'value' => $option->getOptionId(),
                'label' => $option->getTitle()
            ];
        }

        return $options;
    }
}
