<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Model;

use ItechPanel\Configurator\Api\Data\ImageInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Image model
 */
class Image extends AbstractModel implements ImageInterface
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\ItechPanel\Configurator\Model\ResourceModel\Image::class);
    }

    /**
     * @inheritDoc
     */
    public function getImageId()
    {
        return $this->getData(self::IMAGE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setImageId($imageId)
    {
        return $this->setData(self::IMAGE_ID, $imageId);
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
    public function getUploadImage()
    {
        return $this->getData(self::UPLOAD_IMAGE);
    }

    /**
     * @inheritDoc
     */
    public function setUploadImage($uploadImage)
    {
        return $this->setData(self::UPLOAD_IMAGE, $uploadImage);
    }

    /**
     * @inheritDoc
     */
    public function getTopImage()
    {
        return $this->getData(self::TOP_IMAGE);
    }

    /**
     * @inheritDoc
     */
    public function setTopImage($topImage)
    {
        return $this->setData(self::TOP_IMAGE, $topImage);
    }

    /**
     * @inheritDoc
     */
    public function getBottomImage()
    {
        return $this->getData(self::BOTTOM_IMAGE);
    }

    /**
     * @inheritDoc
     */
    public function setBottomImage($bottomImage)
    {
        return $this->setData(self::BOTTOM_IMAGE, $bottomImage);
    }

    /**
     * @inheritDoc
     */
    public function getLeftImage()
    {
        return $this->getData(self::LEFT_IMAGE);
    }

    /**
     * @inheritDoc
     */
    public function setLeftImage($leftImage)
    {
        return $this->setData(self::LEFT_IMAGE, $leftImage);
    }

    /**
     * @inheritDoc
     */
    public function getRightImage()
    {
        return $this->getData(self::RIGHT_IMAGE);
    }

    /**
     * @inheritDoc
     */
    public function setRightImage($rightImage)
    {
        return $this->setData(self::RIGHT_IMAGE, $rightImage);
    }

    /**
     * @inheritDoc
     */
    public function getFrontImage()
    {
        return $this->getData(self::FRONT_IMAGE);
    }

    /**
     * @inheritDoc
     */
    public function setFrontImage($frontImage)
    {
        return $this->setData(self::FRONT_IMAGE, $frontImage);
    }

    /**
     * @inheritDoc
     */
    public function getBackImage()
    {
        return $this->getData(self::BACK_IMAGE);
    }

    /**
     * @inheritDoc
     */
    public function setBackImage($backImage)
    {
        return $this->setData(self::BACK_IMAGE, $backImage);
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
        \ItechPanel\Configurator\Api\Data\ImageExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
