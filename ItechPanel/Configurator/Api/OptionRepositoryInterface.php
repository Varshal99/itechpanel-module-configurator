<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Api;

use ItechPanel\Configurator\Api\Data\OptionInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Option repository interface
 */
interface OptionRepositoryInterface
{
    /**
     * Save option
     *
     * @param OptionInterface $option
     * @return OptionInterface
     * @throws LocalizedException
     */
    public function save(OptionInterface $option);

    /**
     * Get option by ID
     *
     * @param int $optionId
     * @return OptionInterface
     * @throws NoSuchEntityException
     */
    public function getById($optionId);

    /**
     * Delete option
     *
     * @param OptionInterface $option
     * @return bool
     * @throws LocalizedException
     */
    public function delete(OptionInterface $option);

    /**
     * Delete option by ID
     *
     * @param int $optionId
     * @return bool
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($optionId);

    /**
     * Get option list
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \ItechPanel\Configurator\Api\Data\OptionSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
