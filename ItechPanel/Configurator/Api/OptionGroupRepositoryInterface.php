<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Api;

use ItechPanel\Configurator\Api\Data\OptionGroupInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * OptionGroup repository interface
 */
interface OptionGroupRepositoryInterface
{
    /**
     * Save option group
     *
     * @param OptionGroupInterface $optionGroup
     * @return OptionGroupInterface
     * @throws LocalizedException
     */
    public function save(OptionGroupInterface $optionGroup);

    /**
     * Get option group by ID
     *
     * @param int $groupId
     * @return OptionGroupInterface
     * @throws NoSuchEntityException
     */
    public function getById($groupId);

    /**
     * Delete option group
     *
     * @param OptionGroupInterface $optionGroup
     * @return bool
     * @throws LocalizedException
     */
    public function delete(OptionGroupInterface $optionGroup);

    /**
     * Delete option group by ID
     *
     * @param int $groupId
     * @return bool
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($groupId);

    /**
     * Get option group list
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \ItechPanel\Configurator\Api\Data\OptionGroupSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
