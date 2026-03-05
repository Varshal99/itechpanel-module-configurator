<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Api;

use ItechPanel\Configurator\Api\Data\SectionInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Section repository interface
 */
interface SectionRepositoryInterface
{
    /**
     * Save section
     *
     * @param SectionInterface $section
     * @return SectionInterface
     * @throws LocalizedException
     */
    public function save(SectionInterface $section);

    /**
     * Get section by ID
     *
     * @param int $sectionId
     * @return SectionInterface
     * @throws NoSuchEntityException
     */
    public function getById($sectionId);

    /**
     * Delete section
     *
     * @param SectionInterface $section
     * @return bool
     * @throws LocalizedException
     */
    public function delete(SectionInterface $section);

    /**
     * Delete section by ID
     *
     * @param int $sectionId
     * @return bool
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($sectionId);

    /**
     * Get section list
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \ItechPanel\Configurator\Api\Data\SectionSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
