<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Api;

use ItechPanel\Configurator\Api\Data\SubsectionInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Subsection repository interface
 */
interface SubsectionRepositoryInterface
{
    /**
     * Save subsection
     *
     * @param SubsectionInterface $subsection
     * @return SubsectionInterface
     * @throws LocalizedException
     */
    public function save(SubsectionInterface $subsection);

    /**
     * Get subsection by ID
     *
     * @param int $subsectionId
     * @return SubsectionInterface
     * @throws NoSuchEntityException
     */
    public function getById($subsectionId);

    /**
     * Delete subsection
     *
     * @param SubsectionInterface $subsection
     * @return bool
     * @throws LocalizedException
     */
    public function delete(SubsectionInterface $subsection);

    /**
     * Delete subsection by ID
     *
     * @param int $subsectionId
     * @return bool
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($subsectionId);

    /**
     * Get subsection list
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \ItechPanel\Configurator\Api\Data\SubsectionSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
