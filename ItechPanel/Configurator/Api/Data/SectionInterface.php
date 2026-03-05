<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Section interface
 */
interface SectionInterface extends ExtensibleDataInterface
{
    const TABLE_NAME = 'itechpanel_configurator_section';
    const SECTION_ID = 'section_id';
    const TITLE = 'title';
    const LABEL = 'label';
    const POSITION = 'position';
    const IS_ACTIVE = 'is_active';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * Get section ID
     *
     * @return int|null
     */
    public function getSectionId();

    /**
     * Set section ID
     *
     * @param int $sectionId
     * @return $this
     */
    public function setSectionId($sectionId);

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
     * @return \ItechPanel\Configurator\Api\Data\SectionExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \ItechPanel\Configurator\Api\Data\SectionExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \ItechPanel\Configurator\Api\Data\SectionExtensionInterface $extensionAttributes
    );
}
