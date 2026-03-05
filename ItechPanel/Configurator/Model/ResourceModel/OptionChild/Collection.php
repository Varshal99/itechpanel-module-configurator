<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Model\ResourceModel\OptionChild;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * OptionChild collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'child_id';

    /**
     * Initialize collection model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \ItechPanel\Configurator\Model\OptionChild::class,
            \ItechPanel\Configurator\Model\ResourceModel\OptionChild::class
        );
    }
}
