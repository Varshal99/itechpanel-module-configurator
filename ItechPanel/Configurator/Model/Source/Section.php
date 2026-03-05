<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use ItechPanel\Configurator\Model\ResourceModel\Section\CollectionFactory;

/**
 * Section Source Model for dropdown
 */
class Section implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('is_active', 1);
        $collection->setOrder('title', 'ASC');

        $options = [];
        foreach ($collection as $section) {
            $options[] = [
                'value' => $section->getSectionId(),
                'label' => $section->getTitle()
            ];
        }

        return $options;
    }
}
