<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Model;

use ItechPanel\Configurator\Api\Data\OptionGroupInterface;
use ItechPanel\Configurator\Api\Data\OptionGroupSearchResultsInterface;
use ItechPanel\Configurator\Api\Data\OptionGroupSearchResultsInterfaceFactory;
use ItechPanel\Configurator\Api\OptionGroupRepositoryInterface;
use ItechPanel\Configurator\Model\ResourceModel\OptionGroup as ResourceOptionGroup;
use ItechPanel\Configurator\Model\ResourceModel\OptionGroup\CollectionFactory as OptionGroupCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * OptionGroup repository
 */
class OptionGroupRepository implements OptionGroupRepositoryInterface
{
    /**
     * @var ResourceOptionGroup
     */
    private $resource;

    /**
     * @var OptionGroupFactory
     */
    private $optionGroupFactory;

    /**
     * @var OptionGroupCollectionFactory
     */
    private $optionGroupCollectionFactory;

    /**
     * @var OptionGroupSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @param ResourceOptionGroup $resource
     * @param OptionGroupFactory $optionGroupFactory
     * @param OptionGroupCollectionFactory $optionGroupCollectionFactory
     * @param OptionGroupSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceOptionGroup $resource,
        OptionGroupFactory $optionGroupFactory,
        OptionGroupCollectionFactory $optionGroupCollectionFactory,
        OptionGroupSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->optionGroupFactory = $optionGroupFactory;
        $this->optionGroupCollectionFactory = $optionGroupCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(OptionGroupInterface $optionGroup)
    {
        try {
            $this->resource->save($optionGroup);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $optionGroup;
    }

    /**
     * @inheritDoc
     */
    public function getById($groupId)
    {
        $optionGroup = $this->optionGroupFactory->create();
        $this->resource->load($optionGroup, $groupId);
        if (!$optionGroup->getId()) {
            throw new NoSuchEntityException(__('OptionGroup with id "%1" does not exist.', $groupId));
        }
        return $optionGroup;
    }

    /**
     * @inheritDoc
     */
    public function delete(OptionGroupInterface $optionGroup)
    {
        try {
            $this->resource->delete($optionGroup);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($groupId)
    {
        return $this->delete($this->getById($groupId));
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->optionGroupCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }
}
