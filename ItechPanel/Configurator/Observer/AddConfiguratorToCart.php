<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\RequestInterface;
use Psr\Log\LoggerInterface;

/**
 * Observer to add configurator selections to cart
 */
class AddConfiguratorToCart implements ObserverInterface
{
    /**
     * @var Json
     */
    private $jsonSerializer;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Json $jsonSerializer
     * @param RequestInterface $request
     * @param LoggerInterface $logger
     */
    public function __construct(
        Json $jsonSerializer,
        RequestInterface $request,
        LoggerInterface $logger
    ) {
        $this->jsonSerializer = $jsonSerializer;
        $this->request = $request;
        $this->logger = $logger;
    }

    /**
     * Add configurator options to quote item
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        try {
            $quoteItem = $observer->getEvent()->getQuoteItem();
            
            if (!$quoteItem) {
                return;
            }

            $configuratorData = $this->request->getParam('configurator_selections');
            
            if (empty($configuratorData)) {
                return;
            }

            $selections = is_string($configuratorData) 
                ? $this->jsonSerializer->unserialize($configuratorData) 
                : $configuratorData;

            if (empty($selections) || !is_array($selections)) {
                return;
            }

            $additionalOptions = $this->formatAdditionalOptions($selections);
            
            if (empty($additionalOptions)) {
                return;
            }

            $quoteItem->addOption([
                'code' => 'additional_options',
                'value' => $this->jsonSerializer->serialize($additionalOptions)
            ]);

            $quoteItem->addOption([
                'code' => 'configurator_selections',
                'value' => $this->jsonSerializer->serialize($selections)
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Error adding configurator to cart: ' . $e->getMessage());
        }
    }

    /**
     * Format configurator selections as additional options
     *
     * @param array $selections
     * @return array
     */
    private function formatAdditionalOptions(array $selections): array
    {
        $options = [];

        foreach ($selections as $selection) {
            if (!isset($selection['section_name']) || !isset($selection['option_name'])) {
                continue;
            }

            $label = $selection['section_name'];
            
            if (!empty($selection['subsection_name'])) {
                $label .= ' - ' . $selection['subsection_name'];
            }
            
            $label .= ' - ' . $selection['option_name'];

            $value = $selection['child_name'] ?? $selection['value'] ?? '';
            
            if (empty($value)) {
                continue;
            }

            $option = [
                'label' => $label,
                'value' => $value
            ];

            if (isset($selection['price']) && $selection['price'] > 0) {
                $option['price'] = (float)$selection['price'];
            }

            $options[] = $option;
        }

        return $options;
    }
}
