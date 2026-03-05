<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Controller\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;

/**
 * Calculate price AJAX controller
 */
class CalculatePrice extends Action implements HttpPostActionInterface
{
    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var PricingHelper
     */
    private $pricingHelper;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var JsonSerializer
     */
    private $jsonSerializer;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param ProductRepositoryInterface $productRepository
     * @param PricingHelper $pricingHelper
     * @param ResourceConnection $resourceConnection
     * @param JsonSerializer $jsonSerializer
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        ProductRepositoryInterface $productRepository,
        PricingHelper $pricingHelper,
        ResourceConnection $resourceConnection,
        JsonSerializer $jsonSerializer
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->productRepository = $productRepository;
        $this->pricingHelper = $pricingHelper;
        $this->resourceConnection = $resourceConnection;
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * Execute action
     *
     * @return Json
     */
    public function execute()
    {
		\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug("------------CalculatePrice execute------------");
		
        $result = $this->resultJsonFactory->create();
        
        try {
            $productId = (int)$this->getRequest()->getParam('product_id');
            $selectedOptionsJson = $this->getRequest()->getParam('selected_options');
            
            if (!$productId) {
                return $result->setData([
                    'success' => false,
                    'message' => __('Product ID is required.')
                ]);
            }

            if (!$selectedOptionsJson) {
                return $result->setData([
                    'success' => false,
                    'message' => __('Selected options are required.')
                ]);
            }

            $selectedOptions = $this->jsonSerializer->unserialize($selectedOptionsJson);
            
            if (!is_array($selectedOptions)) {
                return $result->setData([
                    'success' => false,
                    'message' => __('Invalid selected options format.')
                ]);
            }

            $product = $this->productRepository->getById($productId);
            $basePrice = (float)$product->getFinalPrice();
            
            $subsectionPrice = $this->calculateSubsectionPrice($selectedOptions);
            $optionsPrice = $this->calculateOptionsPrice($selectedOptions);
            
            $totalPrice = $basePrice + $subsectionPrice + $optionsPrice;
            
            return $result->setData([
                'success' => true,
                'base_price' => $basePrice,
                'subsection_price' => $subsectionPrice,
                'options_price' => $optionsPrice,
                'total_price' => $totalPrice,
                'formatted_price' => $this->pricingHelper->currency($totalPrice, true, false)
            ]);
            
        } catch (\Exception $e) {
            return $result->setData([
                'success' => false,
                'message' => __('An error occurred: %1', $e->getMessage())
            ]);
        }
    }

    /**
     * Calculate subsection prices
     *
     * @param array $selectedOptions
     * @return float
     */
    private function calculateSubsectionPrice($selectedOptions)
	{
		$connection = $this->resourceConnection->getConnection();
		$subsectionTable = $this->resourceConnection->getTableName('itechpanel_configurator_subsection');

		if (empty($selectedOptions)) {
			return 0.0;
		}

		// Now keys are already numeric subsection IDs
		$subsectionIds = array_map('intval', array_keys($selectedOptions));
		$subsectionIds = array_filter($subsectionIds, function ($id) {
			return $id > 0;
		});

		if (empty($subsectionIds)) {
			return 0.0;
		}

		$select = $connection->select()
			->from($subsectionTable, ['SUM(price) as total_price'])
			->where('subsection_id IN (?)', $subsectionIds)
			->where('is_active = ?', 1);

		$totalPrice = $connection->fetchOne($select);

		return (float)($totalPrice ?: 0.0);
	}

    /**
     * Calculate option child prices
     *
     * @param array $selectedOptions
     * @return float
     */
    private function calculateOptionsPrice($selectedOptions)
	{
		$connection = $this->resourceConnection->getConnection();
		$childTable = $this->resourceConnection->getTableName('itechpanel_configurator_option_child');

		if (empty($selectedOptions)) {
			return 0.0;
		}

		// Values are already child IDs
		$childIds = array_map('intval', array_values($selectedOptions));
		$childIds = array_filter($childIds, function ($id) {
			return $id > 0;
		});

		if (empty($childIds)) {
			return 0.0;
		}

		$select = $connection->select()
			->from($childTable, ['SUM(price) as total_price'])
			->where('child_id IN (?)', $childIds)
			->where('is_active = ?', 1);

		$totalPrice = $connection->fetchOne($select);

		return (float)($totalPrice ?: 0.0);
	}
}
