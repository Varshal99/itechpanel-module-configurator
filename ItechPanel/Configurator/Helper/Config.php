<?php
declare(strict_types=1);

/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace ItechPanel\Configurator\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

/**
 * Configuration helper
 */
class Config extends AbstractHelper
{
    /**
     * Configuration paths
     */
    private const XML_PATH_ENABLED = 'itechpanel_configurator/general/enabled';
    private const XML_PATH_DISPLAY_ON_PRODUCT_PAGE = 'itechpanel_configurator/general/display_on_product_page';
    private const XML_PATH_SHOW_PRICE_BREAKDOWN = 'itechpanel_configurator/general/show_price_breakdown';
    private const XML_PATH_REQUIRE_VALIDATION = 'itechpanel_configurator/general/require_selection_validation';
    private const XML_PATH_MAX_FILE_SIZE = 'itechpanel_configurator/images/max_file_size';
    private const XML_PATH_ALLOWED_EXTENSIONS = 'itechpanel_configurator/images/allowed_extensions';
    private const XML_PATH_IMAGE_QUALITY = 'itechpanel_configurator/images/image_quality';

    /**
     * Constructor
     *
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * Check if module is enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if configurator should display on product page
     *
     * @param int|null $storeId
     * @return bool
     */
    public function displayOnProductPage(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_DISPLAY_ON_PRODUCT_PAGE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if price breakdown should be shown
     *
     * @param int|null $storeId
     * @return bool
     */
    public function showPriceBreakdown(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SHOW_PRICE_BREAKDOWN,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if selection validation is required
     *
     * @param int|null $storeId
     * @return bool
     */
    public function requireSelectionValidation(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_REQUIRE_VALIDATION,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get maximum file size for image uploads
     *
     * @param int|null $storeId
     * @return int
     */
    public function getMaxFileSize(?int $storeId = null): int
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_MAX_FILE_SIZE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get allowed file extensions
     *
     * @param int|null $storeId
     * @return array
     */
    public function getAllowedExtensions(?int $storeId = null): array
    {
        $extensions = $this->scopeConfig->getValue(
            self::XML_PATH_ALLOWED_EXTENSIONS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        
        return array_map('trim', explode(',', $extensions));
    }

    /**
     * Get image quality setting
     *
     * @param int|null $storeId
     * @return int
     */
    public function getImageQuality(?int $storeId = null): int
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_IMAGE_QUALITY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
