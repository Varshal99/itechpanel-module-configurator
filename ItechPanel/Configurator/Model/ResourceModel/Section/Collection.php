<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Model\ResourceModel\Section;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Section collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'section_id';

    /**
     * Initialize collection model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \ItechPanel\Configurator\Model\Section::class,
            \ItechPanel\Configurator\Model\ResourceModel\Section::class
        );
    }
}
