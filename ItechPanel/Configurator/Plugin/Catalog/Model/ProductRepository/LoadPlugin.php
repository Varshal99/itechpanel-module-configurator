<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Plugin\Catalog\Model\ProductRepository;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface;

/**
 * Product repository load plugin
 */
class LoadPlugin
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ResourceConnection $resourceConnection
     * @param LoggerInterface $logger
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        LoggerInterface $logger
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->logger = $logger;
    }

    /**
     * After get plugin for product repository
     *
     * @param ProductRepositoryInterface $subject
     * @param ProductInterface $result
     * @return ProductInterface
     */
    public function afterGet(
        ProductRepositoryInterface $subject,
        ProductInterface $result
    ): ProductInterface {
        try {
            $productId = (int)$result->getId();

            if ($productId) {
                $configuratorData = $this->loadConfiguratorData($productId);
                
                if ($configuratorData) {
                    $result->setData('is_configurator_enabled', $configuratorData['is_configurator_enabled']);
                    
                    $sectionIds = $this->loadSectionIds($productId);
                    $result->setData('section_ids', $sectionIds);
                }
            }
        } catch (\Exception $e) {
            $this->logger->error(
                'Error loading configurator data: ' . $e->getMessage(),
                ['exception' => $e]
            );
        }

        return $result;
    }

    /**
     * Load configurator data from database
     *
     * @param int $productId
     * @return array|null
     */
    private function loadConfiguratorData(int $productId): ?array
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('itechpanel_configurator_product');

        $select = $connection->select()
            ->from($tableName)
            ->where('product_id = ?', $productId);

        $result = $connection->fetchRow($select);

        return $result ?: null;
    }

    /**
     * Load section IDs for product
     *
     * @param int $productId
     * @return array
     */
    private function loadSectionIds(int $productId): array
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('itechpanel_configurator_section_product');

        $select = $connection->select()
            ->from($tableName, ['section_id'])
            ->where('product_id = ?', $productId);

        $result = $connection->fetchCol($select);

        return $result ?: [];
    }
}
