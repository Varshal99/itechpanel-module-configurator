<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * OptionChild interface
 */
interface OptionChildInterface extends ExtensibleDataInterface
{
    const TABLE_NAME = 'itechpanel_configurator_option_child';
    const CHILD_ID = 'child_id';
    const TITLE = 'title';
    const LABEL = 'label';
    const TOOLTIP = 'tooltip';
    const PRICE = 'price';
    const POSITION = 'position';
    const THUMBNAIL = 'thumbnail';
    const IS_ACTIVE = 'is_active';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * Get child ID
     *
     * @return int|null
     */
    public function getChildId();

    /**
     * Set child ID
     *
     * @param int $childId
     * @return $this
     */
    public function setChildId($childId);

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Set title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title);

    /**
     * Get label
     *
     * @return string|null
     */
    public function getLabel();

    /**
     * Set label
     *
     * @param string|null $label
     * @return $this
     */
    public function setLabel($label);

    /**
     * Get tooltip
     *
     * @return string|null
     */
    public function getTooltip();

    /**
     * Set tooltip
     *
     * @param string|null $tooltip
     * @return $this
     */
    public function setTooltip($tooltip);

    /**
     * Get price
     *
     * @return float|null
     */
    public function getPrice();

    /**
     * Set price
     *
     * @param float|null $price
     * @return $this
     */
    public function setPrice($price);

    /**
     * Get position
     *
     * @return int
     */
    public function getPosition();

    /**
     * Set position
     *
     * @param int $position
     * @return $this
     */
    public function setPosition($position);

    /**
     * Get thumbnail
     *
     * @return string|null
     */
    public function getThumbnail();

    /**
     * Set thumbnail
     *
     * @param string|null $thumbnail
     * @return $this
     */
    public function setThumbnail($thumbnail);

    /**
     * Get is active
     *
     * @return int
     */
    public function getIsActive();

    /**
     * Set is active
     *
     * @param int $isActive
     * @return $this
     */
    public function setIsActive($isActive);

    /**
     * Get created at
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created at
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Get updated at
     *
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set updated at
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \ItechPanel\Configurator\Api\Data\OptionChildExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \ItechPanel\Configurator\Api\Data\OptionChildExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \ItechPanel\Configurator\Api\Data\OptionChildExtensionInterface $extensionAttributes
    );
}
