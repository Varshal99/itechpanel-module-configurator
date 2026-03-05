<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Model;

use ItechPanel\Configurator\Api\Data\OptionChildInterface;
use ItechPanel\Configurator\Api\Data\OptionChildSearchResultsInterface;
use ItechPanel\Configurator\Api\Data\OptionChildSearchResultsInterfaceFactory;
use ItechPanel\Configurator\Api\OptionChildRepositoryInterface;
use ItechPanel\Configurator\Model\ResourceModel\OptionChild as ResourceOptionChild;
use ItechPanel\Configurator\Model\ResourceModel\OptionChild\CollectionFactory as OptionChildCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * OptionChild repository
 */
class OptionChildRepository implements OptionChildRepositoryInterface
{
    /**
     * @var ResourceOptionChild
     */
    private $resource;

    /**
     * @var OptionChildFactory
     */
    private $optionChildFactory;

    /**
     * @var OptionChildCollectionFactory
     */
    private $optionChildCollectionFactory;

    /**
     * @var OptionChildSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @param ResourceOptionChild $resource
     * @param OptionChildFactory $optionChildFactory
     * @param OptionChildCollectionFactory $optionChildCollectionFactory
     * @param OptionChildSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceOptionChild $resource,
        OptionChildFactory $optionChildFactory,
        OptionChildCollectionFactory $optionChildCollectionFactory,
        OptionChildSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->optionChildFactory = $optionChildFactory;
        $this->optionChildCollectionFactory = $optionChildCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(OptionChildInterface $optionChild)
    {
        try {
            $this->resource->save($optionChild);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $optionChild;
    }

    /**
     * @inheritDoc
     */
    public function getById($childId)
    {
        $optionChild = $this->optionChildFactory->create();
        $this->resource->load($optionChild, $childId);
        if (!$optionChild->getId()) {
            throw new NoSuchEntityException(__('OptionChild with id "%1" does not exist.', $childId));
        }
        return $optionChild;
    }

    /**
     * @inheritDoc
     */
    public function delete(OptionChildInterface $optionChild)
    {
        try {
            $this->resource->delete($optionChild);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($childId)
    {
        return $this->delete($this->getById($childId));
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->optionChildCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }
}
