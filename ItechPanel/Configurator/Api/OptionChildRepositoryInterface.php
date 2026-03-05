<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Api;

use ItechPanel\Configurator\Api\Data\OptionChildInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * OptionChild repository interface
 */
interface OptionChildRepositoryInterface
{
    /**
     * Save option child
     *
     * @param OptionChildInterface $optionChild
     * @return OptionChildInterface
     * @throws LocalizedException
     */
    public function save(OptionChildInterface $optionChild);

    /**
     * Get option child by ID
     *
     * @param int $childId
     * @return OptionChildInterface
     * @throws NoSuchEntityException
     */
    public function getById($childId);

    /**
     * Delete option child
     *
     * @param OptionChildInterface $optionChild
     * @return bool
     * @throws LocalizedException
     */
    public function delete(OptionChildInterface $optionChild);

    /**
     * Delete option child by ID
     *
     * @param int $childId
     * @return bool
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($childId);

    /**
     * Get option child list
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \ItechPanel\Configurator\Api\Data\OptionChildSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
