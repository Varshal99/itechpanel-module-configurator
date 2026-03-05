<?php
declare(strict_types=1);

namespace ItechPanel\Configurator\Model\ResourceModel\Section;

use ItechPanel\Configurator\Model\ResourceModel\Section\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\App\ResourceConnection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Helper\Image as ImageHelper;

class DataProvider extends AbstractDataProvider
{
    private $dataPersistor;
    private $loadedData;
    private $resourceConnection;
    private $productCollectionFactory;
    private $imageHelper;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        ResourceConnection $resourceConnection,
        ProductCollectionFactory $productCollectionFactory,
        ImageHelper $imageHelper,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->resourceConnection = $resourceConnection;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->imageHelper = $imageHelper;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $items = $this->collection->getItems();
        foreach ($items as $section) {
            $sectionData = $section->getData();
            // Injects into the 'products' node directly mapped in the XML above
            $sectionData['products'] = $this->getSelectedProductsData((int)$section->getId());
            $this->loadedData[$section->getId()] = $sectionData;
        }

        $data = $this->dataPersistor->get('itechpanel_configurator_section');
        if (!empty($data)) {
            $section = $this->collection->getNewEmptyItem();
            $section->setData($data);
            $this->loadedData[$section->getId()] = $section->getData();
            $this->dataPersistor->clear('itechpanel_configurator_section');
        }

        return $this->loadedData ?? [];
    }

    private function getSelectedProductsData(int $sectionId): array
    {
        $productIds = $this->getProductIds($sectionId);
        $result = [];
        
        if (!empty($productIds)) {
            $collection = $this->productCollectionFactory->create();
            $collection->addAttributeToSelect(['name', 'sku', 'price', 'thumbnail']);
            $collection->addIdFilter($productIds);

            $position = 0;
            foreach ($collection as $product) {
                $thumbnailUrl = $this->imageHelper->init($product, 'product_listing_thumbnail')->getUrl();
                $result[] = [
                    'entity_id'     => $product->getId(),
                    'name'          => $product->getName(),
                    'sku'           => $product->getSku(),
                    'price'         => $product->getFinalPrice(),
                    'thumbnail'     => $thumbnailUrl,
                    'thumbnail_src' => $thumbnailUrl,
                    'position'      => $position++
                ];
            }
        }
        
        return $result;
    }

    private function getProductIds(int $sectionId): array
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('itechpanel_configurator_section_product');
        
        $select = $connection->select()
            ->from($tableName, ['product_id'])
            ->where('section_id = ?', $sectionId);
        
        return $connection->fetchCol($select);
    }
}