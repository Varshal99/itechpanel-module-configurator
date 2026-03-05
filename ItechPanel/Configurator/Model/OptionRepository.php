<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Model;

use ItechPanel\Configurator\Api\Data\OptionInterface;
use ItechPanel\Configurator\Api\Data\OptionSearchResultsInterface;
use ItechPanel\Configurator\Api\Data\OptionSearchResultsInterfaceFactory;
use ItechPanel\Configurator\Api\OptionRepositoryInterface;
use ItechPanel\Configurator\Model\ResourceModel\Option as ResourceOption;
use ItechPanel\Configurator\Model\ResourceModel\Option\CollectionFactory as OptionCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Option repository
 */
class OptionRepository implements OptionRepositoryInterface
{
    /**
     * @var ResourceOption
     */
    private $resource;

    /**
     * @var OptionFactory
     */
    private $optionFactory;

    /**
     * @var OptionCollectionFactory
     */
    private $optionCollectionFactory;

    /**
     * @var OptionSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @param ResourceOption $resource
     * @param OptionFactory $optionFactory
     * @param OptionCollectionFactory $optionCollectionFactory
     * @param OptionSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceOption $resource,
        OptionFactory $optionFactory,
        OptionCollectionFactory $optionCollectionFactory,
        OptionSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->optionFactory = $optionFactory;
        $this->optionCollectionFactory = $optionCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(OptionInterface $option)
    {
        try {
            $this->resource->save($option);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $option;
    }

    /**
     * @inheritDoc
     */
    public function getById($optionId)
    {
        $option = $this->optionFactory->create();
        $this->resource->load($option, $optionId);
        if (!$option->getId()) {
            throw new NoSuchEntityException(__('Option with id "%1" does not exist.', $optionId));
        }
        return $option;
    }

    /**
     * @inheritDoc
     */
    public function delete(OptionInterface $option)
    {
        try {
            $this->resource->delete($option);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($optionId)
    {
        return $this->delete($this->getById($optionId));
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->optionCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }
}
