<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Block\Product\View;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Framework\App\ResourceConnection;
use ItechPanel\Configurator\Helper\Config as ConfigHelper;

class Configurator extends Template
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var PricingHelper
     */
    private $pricingHelper;

    /**
     * @var Json
     */
    private $jsonSerializer;

    /**
     * @var ImageHelper
     */
    private $imageHelper;

    /**
     * @var ConfigHelper
     */
    private $configHelper;

    /**
     * @var ResourceConnection
     */
    private $_resource;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param PricingHelper $pricingHelper
     * @param Json $jsonSerializer
     * @param ImageHelper $imageHelper
     * @param ConfigHelper $configHelper
     * @param ResourceConnection $resource
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        PricingHelper $pricingHelper,
        Json $jsonSerializer,
        ImageHelper $imageHelper,
        ConfigHelper $configHelper,
        ResourceConnection $resource,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->pricingHelper = $pricingHelper;
        $this->jsonSerializer = $jsonSerializer;
        $this->imageHelper = $imageHelper;
        $this->configHelper = $configHelper;
        $this->_resource = $resource;
        parent::__construct($context, $data);
    }

    /**
     * Get current product from registry
     *
     * @return Product|null
     */
    public function getProduct(): ?Product
    {
        return $this->registry->registry('current_product');
    }

    /**
     * Check if configurator is enabled for product
     *
     * @return bool
     */
    public function isConfiguratorEnabled(): bool
    {
        $product = $this->getProduct();
        if (!$product || !$product->getId()) {
            return false;
        }

        // Try to load configurator data if not already loaded
        $isEnabled = $product->getData('is_configurator_enabled');
        
        // If not loaded, try to load from database directly
        if ($isEnabled === null) {
            $isEnabled = $this->loadConfiguratorEnabled((int)$product->getId());
            $product->setData('is_configurator_enabled', $isEnabled);
        }

        return (bool)$isEnabled;
    }

    /**
     * Load configurator enabled status from database
     *
     * @param int $productId
     * @return int
     */
    private function loadConfiguratorEnabled(int $productId): int
    {
        try {
            $connection = $this->_resource->getConnection();
            $tableName = $this->_resource->getTableName('itechpanel_configurator_product');
            
            $select = $connection->select()
                ->from($tableName, ['is_configurator_enabled'])
                ->where('product_id = ?', $productId);
            
            $result = $connection->fetchOne($select);
            
            return $result !== false ? (int)$result : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get URL for GetConfiguration AJAX endpoint
     *
     * @return string
     */
    public function getConfigurationUrl(): string
    {
        return $this->getUrl('configurator/ajax/getconfiguration', [
            'product_id' => $this->getProductId()
        ]);
    }

    /**
     * Get URL for CalculatePrice AJAX endpoint
     *
     * @return string
     */
    public function getCalculatePriceUrl(): string
    {
        return $this->getUrl('configurator/ajax/calculateprice');
    }

    /**
     * Get URL for GetImages AJAX endpoint
     *
     * @return string
     */
    public function getImagesUrl(): string
    {
        return $this->getUrl('configurator/ajax/getimages');
    }

    /**
     * Get current product ID
     *
     * @return int|null
     */
    public function getProductId(): ?int
    {
        $product = $this->getProduct();
        return $product ? (int)$product->getId() : null;
    }

    /**
     * Get formatted base price
     *
     * @return string
     */
    public function getBasePrice(): string
    {
        $product = $this->getProduct();
        if (!$product) {
            return '$0.00';
        }

        return $this->pricingHelper->currency($product->getFinalPrice(), true, false);
    }

    /**
     * Get base price as float
     *
     * @return float
     */
    public function getBasePriceValue(): float
    {
        $product = $this->getProduct();
        if (!$product) {
            return 0.0;
        }

        return (float)$product->getFinalPrice();
    }

    /**
     * Get main product image URL
     *
     * @return string
     */
    public function getProductImageUrl(): string
    {
        $product = $this->getProduct();
        if (!$product) {
            return '';
        }

        return $this->imageHelper->init($product, 'product_page_image_large')
            ->setImageFile($product->getImage())
            ->getUrl();
    }

    /**
     * Get serialized JSON for JavaScript
     *
     * @param mixed $data
     * @return string
     */
    public function getJsonData($data): string
    {
        return $this->jsonSerializer->serialize($data);
    }

    protected function _prepareLayout()
	{
		parent::_prepareLayout();
		if ($this->isConfiguratorEnabled()) {
			$this->pageConfig->addBodyClass('is-configurator-product');
		}
		return $this;
	}
}
