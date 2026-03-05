<?php
declare(strict_types=1);

namespace ItechPanel\Configurator\Controller\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Helper\Image as ImageHelper;

class GetConfiguration extends Action implements HttpGetActionInterface
{
    private $resultJsonFactory;
    private $productRepository;
    private $storeManager;
    private $resourceConnection;
    private $productCollectionFactory;
    private $imageHelper;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        ProductRepositoryInterface $productRepository,
        StoreManagerInterface $storeManager,
        ResourceConnection $resourceConnection,
        ProductCollectionFactory $productCollectionFactory,
        ImageHelper $imageHelper
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
        $this->resourceConnection = $resourceConnection;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->imageHelper = $imageHelper;
    }

    public function execute()
    {
		\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug("------------GetConfiguration execute------------");
        $result = $this->resultJsonFactory->create();
        
        try {
            $productId = (int)$this->getRequest()->getParam('product_id');
            if (!$productId) return $result->setData(['success' => false, 'data' => []]);

            $product = $this->productRepository->getById($productId);
            $connection = $this->resourceConnection->getConnection();
            
            if (!$this->getConfiguratorStatus($connection, $productId)) {
                return $result->setData([
                    'success' => true,
                    'data' => [
                        'product_id' => $productId,
                        'is_configurator_enabled' => false,
                        'base_price' => (float)$product->getFinalPrice(),
                        'sections' => []
                    ]
                ]);
            }

            return $result->setData([
                'success' => true,
                'data' => [
                    'product_id' => $productId,
                    'is_configurator_enabled' => true,
                    'base_price' => (float)$product->getFinalPrice(),
                    'sections' => $this->loadSections($connection, $productId)
                ]
            ]);
            
        } catch (\Exception $e) {
            return $result->setData(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function getConfiguratorStatus($connection, $productId) {
        $table = $this->resourceConnection->getTableName('itechpanel_configurator_product');
        return (bool)$connection->fetchOne($connection->select()->from($table, ['is_configurator_enabled'])->where('product_id = ?', $productId));
    }

    private function loadSections($connection, $productId) {
        $sectionTable = $this->resourceConnection->getTableName('itechpanel_configurator_section');
        $sectionProductTable = $this->resourceConnection->getTableName('itechpanel_configurator_section_product');
        
        $select = $connection->select()
            ->from(['s' => $sectionTable])
            ->join(['sp' => $sectionProductTable], 's.section_id = sp.section_id', [])
            ->where('sp.product_id = ?', $productId)
            ->where('s.is_active = ?', 1)
            ->order('s.position ASC');
        
        $sections = [];
        foreach ($connection->fetchAll($select) as $row) {
            $sections[] = [
                'section_id' => (int)$row['section_id'],
                'title' => $row['title'],
                'label' => $row['label'] ?: $row['title'],
                'position' => (int)$row['position'],
                'subsections' => $this->loadSubsections($connection, (int)$row['section_id'], $productId)
            ];
        }
        return $sections;
    }

    private function loadSubsections($connection, $sectionId, $productId) {
        $subsectionTable = $this->resourceConnection->getTableName('itechpanel_configurator_subsection');
        $sectionSubsectionTable = $this->resourceConnection->getTableName('itechpanel_configurator_section_subsection');
        $subsectionProductTable = $this->resourceConnection->getTableName('itechpanel_configurator_subsection_product');

        $select = $connection->select()
            ->from(['sub' => $subsectionTable])
            ->join(['ss' => $sectionSubsectionTable], 'sub.subsection_id = ss.subsection_id', [])
            ->join(['sp' => $subsectionProductTable], 'sub.subsection_id = sp.subsection_id', [])
            ->where('ss.section_id = ?', $sectionId)
            ->where('sp.product_id = ?', $productId)
            ->where('sub.is_active = ?', 1)
            ->order('sub.position ASC');
        
        $subsections = [];
        foreach ($connection->fetchAll($select) as $row) {
            // FIX: Load the Options explicitly into the "options" array key
            $options = $this->loadOptions($connection, (int)$row['subsection_id']);

            $subsections[] = [
                'subsection_id' => (int)$row['subsection_id'],
                'label' => $row['label'] ?: $row['title'],
                'title' => $row['title'],
                'price' => (float)$row['price'],
                'is_required' => (bool)$row['is_required'],
                'position' => (int)$row['position'],
                'options' => $options
            ];
        }
        return $subsections;
    }

    private function loadOptions($connection, $subsectionId) {
        $optionTable = $this->resourceConnection->getTableName('itechpanel_configurator_option');
        $subsectionOptionTable = $this->resourceConnection->getTableName('itechpanel_configurator_subsection_option');
        
        $select = $connection->select()
            ->from(['o' => $optionTable])
            ->join(['so' => $subsectionOptionTable], 'o.option_id = so.option_id', [])
            ->where('so.subsection_id = ?', $subsectionId)
            ->where('o.is_active = ?', 1)
            ->order('o.position ASC');
        
        $options = [];
        foreach ($connection->fetchAll($select) as $row) {
            $productOptions = $this->loadAssignedProducts('option', (int)$row['option_id']);
            $manualChildren = $this->loadOptionChildren($connection, (int)$row['option_id']);

            $options[] = [
                'option_id' => (int)$row['option_id'],
                'label' => $row['label'] ?: $row['title'],
                'type' => $row['type'],
                'children' => array_merge($productOptions, $manualChildren)
            ];
        }
        return $options;
    }

    private function loadAssignedProducts($entityType, $entityId) {
        $tableName = $this->resourceConnection->getTableName("itechpanel_configurator_{$entityType}_product");
        $connection = $this->resourceConnection->getConnection();

        $select = $connection->select()->from($tableName, ['product_id'])->where("{$entityType}_id = ?", $entityId);
        $productIds = $connection->fetchCol($select);

        if (empty($productIds)) return [];

        $collection = $this->productCollectionFactory->create()
            ->addAttributeToSelect(['name', 'price', 'thumbnail', 'small_image'])
            ->addIdFilter($productIds)
            ->addStoreFilter($this->storeManager->getStore()->getId());

        $children = [];
        foreach ($collection as $product) {
            $children[] = [
                'child_id' => (int)$product->getId(),
                'label' => $product->getName(),
                'price' => (float)$product->getFinalPrice(),
                'thumbnail' => $this->imageHelper->init($product, 'product_thumbnail_image')->getUrl(),
                'tooltip' => $product->getSku(),
                'is_product' => true
            ];
        }
        return $children;
    }

    private function loadOptionChildren($connection, $optionId) {
        $childTable = $this->resourceConnection->getTableName('itechpanel_configurator_option_child');
        $optionChildTable = $this->resourceConnection->getTableName('itechpanel_configurator_option_child_option');
        
        $select = $connection->select()
            ->from(['oc' => $childTable])
            ->join(['oco' => $optionChildTable], 'oc.child_id = oco.child_id', [])
            ->where('oco.option_id = ?', $optionId)
            ->where('oc.is_active = ?', 1)
            ->order('oc.position ASC');
        
        $children = [];
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        
        foreach ($connection->fetchAll($select) as $row) {
            $children[] = [
                'child_id' => (int)$row['child_id'],
                'label' => $row['label'] ?: $row['title'],
                'price' => (float)$row['price'],
                'thumbnail' => $row['thumbnail'] ? $mediaUrl . 'itechpanel/configurator/' . $row['thumbnail'] : null,
                'tooltip' => $row['tooltip'],
                'is_product' => false
            ];
        }
        return $children;
    }
}