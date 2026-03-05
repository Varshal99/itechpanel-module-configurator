<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Model;

use ItechPanel\Configurator\Api\Data\SubsectionInterface;
use ItechPanel\Configurator\Api\Data\SubsectionSearchResultsInterface;
use ItechPanel\Configurator\Api\Data\SubsectionSearchResultsInterfaceFactory;
use ItechPanel\Configurator\Api\SubsectionRepositoryInterface;
use ItechPanel\Configurator\Model\ResourceModel\Subsection as ResourceSubsection;
use ItechPanel\Configurator\Model\ResourceModel\Subsection\CollectionFactory as SubsectionCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Subsection repository
 */
class SubsectionRepository implements SubsectionRepositoryInterface
{
    /**
     * @var ResourceSubsection
     */
    private $resource;

    /**
     * @var SubsectionFactory
     */
    private $subsectionFactory;

    /**
     * @var SubsectionCollectionFactory
     */
    private $subsectionCollectionFactory;

    /**
     * @var SubsectionSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @param ResourceSubsection $resource
     * @param SubsectionFactory $subsectionFactory
     * @param SubsectionCollectionFactory $subsectionCollectionFactory
     * @param SubsectionSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceSubsection $resource,
        SubsectionFactory $subsectionFactory,
        SubsectionCollectionFactory $subsectionCollectionFactory,
        SubsectionSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->subsectionFactory = $subsectionFactory;
        $this->subsectionCollectionFactory = $subsectionCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(SubsectionInterface $subsection)
    {
        try {
            $this->resource->save($subsection);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $subsection;
    }

    /**
     * @inheritDoc
     */
    public function getById($subsectionId)
    {
        $subsection = $this->subsectionFactory->create();
        $this->resource->load($subsection, $subsectionId);
        if (!$subsection->getId()) {
            throw new NoSuchEntityException(__('Subsection with id "%1" does not exist.', $subsectionId));
        }
        return $subsection;
    }

    /**
     * @inheritDoc
     */
    public function delete(SubsectionInterface $subsection)
    {
        try {
            $this->resource->delete($subsection);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($subsectionId)
    {
        return $this->delete($this->getById($subsectionId));
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->subsectionCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }
}
