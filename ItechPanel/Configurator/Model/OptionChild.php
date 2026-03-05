<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Model;

use ItechPanel\Configurator\Api\Data\OptionChildInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * OptionChild model
 */
class OptionChild extends AbstractModel implements OptionChildInterface
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\ItechPanel\Configurator\Model\ResourceModel\OptionChild::class);
    }

    /**
     * @inheritDoc
     */
    public function getChildId()
    {
        return $this->getData(self::CHILD_ID);
    }

    /**
     * @inheritDoc
     */
    public function setChildId($childId)
    {
        return $this->setData(self::CHILD_ID, $childId);
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * @inheritDoc
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * @inheritDoc
     */
    public function getLabel()
    {
        return $this->getData(self::LABEL);
    }

    /**
     * @inheritDoc
     */
    public function setLabel($label)
    {
        return $this->setData(self::LABEL, $label);
    }

    /**
     * @inheritDoc
     */
    public function getTooltip()
    {
        return $this->getData(self::TOOLTIP);
    }

    /**
     * @inheritDoc
     */
    public function setTooltip($tooltip)
    {
        return $this->setData(self::TOOLTIP, $tooltip);
    }

    /**
     * @inheritDoc
     */
    public function getPrice()
    {
        return $this->getData(self::PRICE);
    }

    /**
     * @inheritDoc
     */
    public function setPrice($price)
    {
        return $this->setData(self::PRICE, $price);
    }

    /**
     * @inheritDoc
     */
    public function getPosition()
    {
        return $this->getData(self::POSITION);
    }

    /**
     * @inheritDoc
     */
    public function setPosition($position)
    {
        return $this->setData(self::POSITION, $position);
    }

    /**
     * @inheritDoc
     */
    public function getThumbnail()
    {
        return $this->getData(self::THUMBNAIL);
    }

    /**
     * @inheritDoc
     */
    public function setThumbnail($thumbnail)
    {
        return $this->setData(self::THUMBNAIL, $thumbnail);
    }

    /**
     * @inheritDoc
     */
    public function getIsActive()
    {
        return $this->getData(self::IS_ACTIVE);
    }

    /**
     * @inheritDoc
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * @inheritDoc
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(
        \ItechPanel\Configurator\Api\Data\OptionChildExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
