<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Model;

use ItechPanel\Configurator\Api\Data\SectionInterface;
use ItechPanel\Configurator\Api\Data\SectionSearchResultsInterface;
use ItechPanel\Configurator\Api\Data\SectionSearchResultsInterfaceFactory;
use ItechPanel\Configurator\Api\SectionRepositoryInterface;
use ItechPanel\Configurator\Model\ResourceModel\Section as ResourceSection;
use ItechPanel\Configurator\Model\ResourceModel\Section\CollectionFactory as SectionCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Section repository
 */
class SectionRepository implements SectionRepositoryInterface
{
    /**
     * @var ResourceSection
     */
    private $resource;

    /**
     * @var SectionFactory
     */
    private $sectionFactory;

    /**
     * @var SectionCollectionFactory
     */
    private $sectionCollectionFactory;

    /**
     * @var SectionSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @param ResourceSection $resource
     * @param SectionFactory $sectionFactory
     * @param SectionCollectionFactory $sectionCollectionFactory
     * @param SectionSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceSection $resource,
        SectionFactory $sectionFactory,
        SectionCollectionFactory $sectionCollectionFactory,
        SectionSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->sectionFactory = $sectionFactory;
        $this->sectionCollectionFactory = $sectionCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(SectionInterface $section)
    {
        try {
            $this->resource->save($section);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $section;
    }

    /**
     * @inheritDoc
     */
    public function getById($sectionId)
    {
        $section = $this->sectionFactory->create();
        $this->resource->load($section, $sectionId);
        if (!$section->getId()) {
            throw new NoSuchEntityException(__('Section with id "%1" does not exist.', $sectionId));
        }
        return $section;
    }

    /**
     * @inheritDoc
     */
    public function delete(SectionInterface $section)
    {
        try {
            $this->resource->delete($section);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($sectionId)
    {
        return $this->delete($this->getById($sectionId));
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->sectionCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }
}
