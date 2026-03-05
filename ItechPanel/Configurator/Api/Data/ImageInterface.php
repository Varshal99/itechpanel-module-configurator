<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Image interface
 */
interface ImageInterface extends ExtensibleDataInterface
{
    const TABLE_NAME = 'itechpanel_configurator_image';
    const IMAGE_ID = 'image_id';
    const TITLE = 'title';
    const UPLOAD_IMAGE = 'upload_image';
    const TOP_IMAGE = 'top_image';
    const BOTTOM_IMAGE = 'bottom_image';
    const LEFT_IMAGE = 'left_image';
    const RIGHT_IMAGE = 'right_image';
    const FRONT_IMAGE = 'front_image';
    const BACK_IMAGE = 'back_image';
    const IS_ACTIVE = 'is_active';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * Get image ID
     *
     * @return int|null
     */
    public function getImageId();

    /**
     * Set image ID
     *
     * @param int $imageId
     * @return $this
     */
    public function setImageId($imageId);

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
     * Get upload image
     *
     * @return string|null
     */
    public function getUploadImage();

    /**
     * Set upload image
     *
     * @param string|null $uploadImage
     * @return $this
     */
    public function setUploadImage($uploadImage);

    /**
     * Get top image
     *
     * @return string|null
     */
    public function getTopImage();

    /**
     * Set top image
     *
     * @param string|null $topImage
     * @return $this
     */
    public function setTopImage($topImage);

    /**
     * Get bottom image
     *
     * @return string|null
     */
    public function getBottomImage();

    /**
     * Set bottom image
     *
     * @param string|null $bottomImage
     * @return $this
     */
    public function setBottomImage($bottomImage);

    /**
     * Get left image
     *
     * @return string|null
     */
    public function getLeftImage();

    /**
     * Set left image
     *
     * @param string|null $leftImage
     * @return $this
     */
    public function setLeftImage($leftImage);

    /**
     * Get right image
     *
     * @return string|null
     */
    public function getRightImage();

    /**
     * Set right image
     *
     * @param string|null $rightImage
     * @return $this
     */
    public function setRightImage($rightImage);

    /**
     * Get front image
     *
     * @return string|null
     */
    public function getFrontImage();

    /**
     * Set front image
     *
     * @param string|null $frontImage
     * @return $this
     */
    public function setFrontImage($frontImage);

    /**
     * Get back image
     *
     * @return string|null
     */
    public function getBackImage();

    /**
     * Set back image
     *
     * @param string|null $backImage
     * @return $this
     */
    public function setBackImage($backImage);

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
     * @return \ItechPanel\Configurator\Api\Data\ImageExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \ItechPanel\Configurator\Api\Data\ImageExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \ItechPanel\Configurator\Api\Data\ImageExtensionInterface $extensionAttributes
    );
}
