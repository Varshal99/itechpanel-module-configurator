<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Api;

use ItechPanel\Configurator\Api\Data\ImageInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Image repository interface
 */
interface ImageRepositoryInterface
{
    /**
     * Save image
     *
     * @param ImageInterface $image
     * @return ImageInterface
     * @throws LocalizedException
     */
    public function save(ImageInterface $image);

    /**
     * Get image by ID
     *
     * @param int $imageId
     * @return ImageInterface
     * @throws NoSuchEntityException
     */
    public function getById($imageId);

    /**
     * Delete image
     *
     * @param ImageInterface $image
     * @return bool
     * @throws LocalizedException
     */
    public function delete(ImageInterface $image);

    /**
     * Delete image by ID
     *
     * @param int $imageId
     * @return bool
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($imageId);

    /**
     * Get image list
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \ItechPanel\Configurator\Api\Data\ImageSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
