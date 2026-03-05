<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Model;

use ItechPanel\Configurator\Api\Data\ImageInterface;
use ItechPanel\Configurator\Api\Data\ImageSearchResultsInterface;
use ItechPanel\Configurator\Api\Data\ImageSearchResultsInterfaceFactory;
use ItechPanel\Configurator\Api\ImageRepositoryInterface;
use ItechPanel\Configurator\Model\ResourceModel\Image as ResourceImage;
use ItechPanel\Configurator\Model\ResourceModel\Image\CollectionFactory as ImageCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Image repository
 */
class ImageRepository implements ImageRepositoryInterface
{
    /**
     * @var ResourceImage
     */
    private $resource;

    /**
     * @var ImageFactory
     */
    private $imageFactory;

    /**
     * @var ImageCollectionFactory
     */
    private $imageCollectionFactory;

    /**
     * @var ImageSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @param ResourceImage $resource
     * @param ImageFactory $imageFactory
     * @param ImageCollectionFactory $imageCollectionFactory
     * @param ImageSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceImage $resource,
        ImageFactory $imageFactory,
        ImageCollectionFactory $imageCollectionFactory,
        ImageSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->imageFactory = $imageFactory;
        $this->imageCollectionFactory = $imageCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(ImageInterface $image)
    {
        try {
            $this->resource->save($image);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $image;
    }

    /**
     * @inheritDoc
     */
    public function getById($imageId)
    {
        $image = $this->imageFactory->create();
        $this->resource->load($image, $imageId);
        if (!$image->getId()) {
            throw new NoSuchEntityException(__('Image with id "%1" does not exist.', $imageId));
        }
        return $image;
    }

    /**
     * @inheritDoc
     */
    public function delete(ImageInterface $image)
    {
        try {
            $this->resource->delete($image);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($imageId)
    {
        return $this->delete($this->getById($imageId));
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->imageCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }
}
