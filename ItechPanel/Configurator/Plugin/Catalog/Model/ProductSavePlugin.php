<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Plugin\Catalog\Model;

use Magento\Catalog\Controller\Adminhtml\Product\Save as SaveController;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface;

class ProductSavePlugin
{
    /** @var RequestInterface */
    private $request;

    /** @var ResourceConnection */
    private $resourceConnection;

    /** @var LoggerInterface */
    private $logger;

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
     * @param SaveController $subject
     * @param mixed $result
     * @return mixed
     */
    public function afterExecute(SaveController $subject, $result)
    {
        // Use 'id' for existing products, or check the 'product' param for new ones
        $productId = (int)$this->request->getParam('id');
        $productData = $this->request->getParam('product');

        // If it's a new product, we need to find the ID after it was created
        if (!$productId && $productData) {
            // Note: In a real scenario, you might need to fetch the last inserted ID 
            // if the request doesn't contain it yet.
        }

        if ($productId && is_array($productData)) {
            $this->logger->debug("Configurator Save Plugin: Processing Product $productId");

            // 1. Save Configurator Toggle Status
            if (isset($productData['is_configurator_enabled'])) {
                $this->saveConfiguratorData($productId, (int)$productData['is_configurator_enabled']);
            }

            // 2. Save Multiselect Sections
            if (isset($productData['section_ids'])) {
                $this->saveSectionAssignments($productId, $productData['section_ids']);
            }

            // 3. Save Multiselect Subsections
            if (isset($productData['subsection_ids'])) {
                $this->saveSubsectionAssignments($productId, $productData['subsection_ids']);
            }
        }

        return $result;
    }

    /**
     * Direct SQL to save configurator toggle
     */
    private function saveConfiguratorData(int $productId, int $isEnabled): void
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('itechpanel_configurator_product');

        $data = [
            'product_id' => $productId,
            'is_configurator_enabled' => $isEnabled
        ];

        $connection->insertOnDuplicate($tableName, $data, ['is_configurator_enabled']);
    }

    /**
     * Direct SQL to save section assignments
     */
    private function saveSectionAssignments(int $productId, $sectionIds): void
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('itechpanel_configurator_section_product');

        // Remove old links
        $connection->delete($tableName, ['product_id = ?' => $productId]);

        if (is_string($sectionIds)) {
            $sectionIds = explode(',', $sectionIds);
        }

        if (is_array($sectionIds)) {
            $insertData = [];
            foreach (array_filter($sectionIds) as $sectionId) {
                $insertData[] = [
                    'product_id' => $productId,
                    'section_id' => (int)$sectionId
                ];
            }

            if (!empty($insertData)) {
                $connection->insertMultiple($tableName, $insertData);
            }
        }
    }

    /**
     * Direct SQL to save subsection assignments
     */
    private function saveSubsectionAssignments(int $productId, $subsectionIds): void
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('itechpanel_configurator_subsection_product');

        // Remove old links
        $connection->delete($tableName, ['product_id = ?' => $productId]);

        if (is_string($subsectionIds)) {
            $subsectionIds = explode(',', $subsectionIds);
        }

        if (is_array($subsectionIds)) {
            $insertData = [];
            foreach (array_filter($subsectionIds) as $subsectionId) {
                $insertData[] = [
                    'product_id' => $productId,
                    'subsection_id' => (int)$subsectionId
                ];
            }

            if (!empty($insertData)) {
                $connection->insertMultiple($tableName, $insertData);
            }
        }
    }
}
