<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Subsection interface
 */
interface SubsectionInterface extends ExtensibleDataInterface
{
    const TABLE_NAME = 'itechpanel_configurator_subsection';
    const SUBSECTION_ID = 'subsection_id';
    const TITLE = 'title';
    const LABEL = 'label';
    const PRICE = 'price';
    const IS_REQUIRED = 'is_required';
    const POSITION = 'position';
    const IS_ACTIVE = 'is_active';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * Get subsection ID
     *
     * @return int|null
     */
    public function getSubsectionId();

    /**
     * Set subsection ID
     *
     * @param int $subsectionId
     * @return $this
     */
    public function setSubsectionId($subsectionId);

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
     * Get is required
     *
     * @return int
     */
    public function getIsRequired();

    /**
     * Set is required
     *
     * @param int $isRequired
     * @return $this
     */
    public function setIsRequired($isRequired);

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
     * @return \ItechPanel\Configurator\Api\Data\SubsectionExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \ItechPanel\Configurator\Api\Data\SubsectionExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \ItechPanel\Configurator\Api\Data\SubsectionExtensionInterface $extensionAttributes
    );
}
