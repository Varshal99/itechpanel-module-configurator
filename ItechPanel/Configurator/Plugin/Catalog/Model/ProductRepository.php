<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Plugin\Catalog\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface;

/**
 * Product repository save plugin
 */
class ProductRepository
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param RequestInterface $request
     * @param ResourceConnection $resourceConnection
     * @param LoggerInterface $logger
     */
    public function __construct(
        RequestInterface $request,
        ResourceConnection $resourceConnection,
        LoggerInterface $logger
    ) {
        $this->request = $request;
        $this->resourceConnection = $resourceConnection;
        $this->logger = $logger;
    }

    /**
     * Before save plugin for product repository
     *
     * @param ProductRepositoryInterface $subject
     * @param ProductInterface $product
     * @param bool $saveOptions
     * @return array
     */
    public function beforeSave(
        ProductRepositoryInterface $subject,
        ProductInterface $product,
        $saveOptions = false
    ): array {
        try {
            // Try to get data from custom attributes first
            $isConfiguratorEnabled = $product->getData('is_configurator_enabled');
            $sectionIds = $product->getData('section_ids');
            
            // If not in product data, try request
            if ($isConfiguratorEnabled === null && $sectionIds === null) {
                $productData = $this->request->getParam('product');
                
                if (is_array($productData)) {
                    $isConfiguratorEnabled = $productData['is_configurator_enabled'] ?? null;
                    $sectionIds = $productData['section_ids'] ?? null;
                }
            }
            
            // Log what we receive for debugging
            $this->logger->debug('Product Configurator - beforeSave data', [
                'product_id' => $product->getId(),
                'is_configurator_enabled' => $isConfiguratorEnabled,
                'section_ids' => $sectionIds,
                'product_data_keys' => array_keys($product->getData())
            ]);
            
            // Store in temporary property for use in afterSave
            if ($isConfiguratorEnabled !== null) {
                $product->setData('_temp_is_configurator_enabled', $isConfiguratorEnabled);
            }
            if ($sectionIds !== null) {
                $product->setData('_temp_section_ids', $sectionIds);
            }
            
        } catch (\Exception $e) {
            $this->logger->error(
                'Error in beforeSave configurator data: ' . $e->getMessage(),
                ['exception' => $e]
            );
        }

        return [$product, $saveOptions];
    }

    /**
     * After save plugin for product repository
     *
     * @param ProductRepositoryInterface $subject
     * @param ProductInterface $result
     * @return ProductInterface
     */
    public function afterSave(
        ProductRepositoryInterface $subject,
        ProductInterface $result
    ): ProductInterface {
        try {
            $productId = (int)$result->getId();
            
            // Get data from temporary storage set in beforeSave
            $isConfiguratorEnabled = $result->getData('_temp_is_configurator_enabled');
            $sectionIds = $result->getData('_temp_section_ids');
            
            $this->logger->debug('Product Configurator - afterSave processing', [
                'product_id' => $productId,
                'is_configurator_enabled' => $isConfiguratorEnabled,
                'section_ids' => $sectionIds
            ]);

            // Save configurator enabled/disabled status
            if ($isConfiguratorEnabled !== null) {
                $this->saveConfiguratorData($productId, ['is_configurator_enabled' => $isConfiguratorEnabled]);
            }

            // Save section assignments
            if ($sectionIds !== null) {
                $this->saveSectionAssignments($productId, $sectionIds);
            }
            
            // Clean up temporary data
            $result->unsetData('_temp_is_configurator_enabled');
            $result->unsetData('_temp_section_ids');
            
        } catch (\Exception $e) {
            $this->logger->error(
                'Error saving configurator data: ' . $e->getMessage(),
                ['exception' => $e]
            );
        }

        return $result;
    }

    /**
     * Save configurator data
     *
     * @param int $productId
     * @param array $productData
     * @return void
     */
    private function saveConfiguratorData(int $productId, array $productData): void
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('itechpanel_configurator_product');

        $isEnabled = isset($productData['is_configurator_enabled']) 
            ? (int)$productData['is_configurator_enabled'] 
            : 0;

        $select = $connection->select()
            ->from($tableName)
            ->where('product_id = ?', $productId);

        $exists = $connection->fetchRow($select);

        if ($exists) {
            $connection->update(
                $tableName,
                ['is_configurator_enabled' => $isEnabled],
                ['product_id = ?' => $productId]
            );
            $this->logger->debug('Configurator data updated', [
                'productId' => $productId,
                'isEnabled' => $isEnabled
            ]);
        } else {
            $connection->insert(
                $tableName,
                [
                    'product_id' => $productId,
                    'is_configurator_enabled' => $isEnabled
                ]
            );
            $this->logger->debug('Configurator data inserted', [
                'productId' => $productId,
                'isEnabled' => $isEnabled
            ]);
        }
    }

    /**
     * Save section assignments
     *
     * @param int $productId
     * @param mixed $sectionIds
     * @return void
     */
    private function saveSectionAssignments(int $productId, $sectionIds): void
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('itechpanel_configurator_section_product');

        // Delete existing assignments
        $connection->delete(
            $tableName,
            ['product_id = ?' => $productId]
        );

        // Handle different input formats
        if (is_string($sectionIds)) {
            $sectionIds = explode(',', $sectionIds);
            $sectionIds = array_filter($sectionIds); // Remove empty values
        }

        if (!empty($sectionIds) && is_array($sectionIds)) {
            $data = [];
            foreach ($sectionIds as $sectionId) {
                if (!empty($sectionId)) {
                    $data[] = [
                        'section_id' => (int)$sectionId,
                        'product_id' => $productId
                    ];
                }
            }

            if (!empty($data)) {
                $connection->insertMultiple($tableName, $data);
                $this->logger->debug('Section assignments saved', [
                    'productId' => $productId,
                    'data' => $data
                ]);
            }
        }
    }
}
