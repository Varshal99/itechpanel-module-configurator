<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use ItechPanel\Configurator\Model\ResourceModel\Section\CollectionFactory as SectionCollectionFactory;
use ItechPanel\Configurator\Model\ResourceModel\Subsection\CollectionFactory as SubsectionCollectionFactory;

/**
 * Product configurator form modifier
 */
class Configurator implements ModifierInterface
{
    private const GROUP_CONFIGURATOR = 'configurator';
    private const SORT_ORDER = 100;

    /**
     * @var LocatorInterface
     */
    private $locator;

    /**
     * @var SectionCollectionFactory
     */
    private $sectionCollectionFactory;

    /**
     * @var SubsectionCollectionFactory
     */
    private $subsectionCollectionFactory;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @param LocatorInterface $locator
     * @param SectionCollectionFactory $sectionCollectionFactory
     * @param SubsectionCollectionFactory $subsectionCollectionFactory
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        LocatorInterface $locator,
        SectionCollectionFactory $sectionCollectionFactory,
        SubsectionCollectionFactory $subsectionCollectionFactory,
        ResourceConnection $resourceConnection
    ) {
        $this->locator = $locator;
        $this->sectionCollectionFactory = $sectionCollectionFactory;
        $this->subsectionCollectionFactory = $subsectionCollectionFactory;
        $this->resourceConnection = $resourceConnection;
    }

    /**
	 * @inheritDoc
	 */
	public function modifyMeta(array $meta)
	{
		$meta[self::GROUP_CONFIGURATOR] = [
			'arguments' => [
				'data' => [
					'config' => [
						'label' => __('Product Configurator'),
						'componentType' => 'fieldset',
						'dataScope' => 'data.product', // Fieldset scope
						'collapsible' => true,
						'sortOrder' => self::SORT_ORDER,
					],
				],
			],
			'children' => [
				'is_configurator_enabled' => [
					'arguments' => [
						'data' => [
							'config' => [
								'label' => __('Enable Configurator'),
								'componentType' => 'field',
								'formElement' => 'checkbox',
								// FIX: Remove 'product.' prefix. 
								// It is already inside the 'data.product' fieldset scope.
								'dataScope' => 'is_configurator_enabled', 
								'dataType' => 'number',
								'sortOrder' => 10,
								'value' => '0',
								'valueMap' => [
									'true' => '1',
									'false' => '0'
								],
							],
						],
					],
				],
				'section_ids' => [
					'arguments' => [
						'data' => [
							'config' => [
								'label' => __('Select Sections'),
								'componentType' => 'field',
								'formElement' => 'multiselect',
								'dataScope' => 'section_ids',
								'dataType' => 'text',
								'sortOrder' => 20,
								'options' => $this->getSectionOptions(),
								'visible' => true,
								'imports' => [
									'visible' => 'ns = ${ $.ns }, index = is_configurator_enabled:checked'
								],
							],
						],
					],
				],
				'subsection_ids' => [
					'arguments' => [
						'data' => [
							'config' => [
								'label' => __('Select Subsections'),
								'componentType' => 'field',
								'formElement' => 'multiselect',
								'component' => 'ItechPanel_Configurator/js/form/element/dependent-multiselect',
								'dataScope' => 'subsection_ids',
								'dataType' => 'text',
								'sortOrder' => 30,
								'options' => $this->getSubsectionOptions(),
								'visible' => true,
								'imports' => [
									'visible' => 'ns = ${ $.ns }, index = is_configurator_enabled:checked',
									'sectionIds' => 'ns = ${ $.ns }, index = section_ids:value'
								],
								'filterBy' => null,
							],
						],
					],
				],
			],
		];

		return $meta;
	}

    /**
     * @inheritDoc
     */
    public function modifyData(array $data)
    {
        $product = $this->locator->getProduct();
        $productId = $product->getId();
        
        // Handle null or string ID
        if ($productId === null || $productId === '') {
            return $data;
        }
        
        $productId = (int)$productId;

        if ($productId) {
            $configuratorData = $this->loadConfiguratorData($productId);
            
            if ($configuratorData) {
                $data[$productId]['product']['is_configurator_enabled'] = $configuratorData['is_configurator_enabled'];
            }
            
            $data[$productId]['product']['section_ids'] = $this->loadSectionIds($productId);
            $data[$productId]['product']['subsection_ids'] = $this->loadSubsectionIds($productId);
        }

        return $data;
    }

    /**
     * Get section options for multiselect
     *
     * @return array
     */
    private function getSectionOptions(): array
    {
        $options = [];
        $collection = $this->sectionCollectionFactory->create();
        $collection->addFieldToFilter('is_active', 1);

        foreach ($collection as $section) {
            $options[] = [
                'value' => $section->getId(),
                'label' => $section->getTitle(),
            ];
        }

        return $options;
    }

    /**
     * Get subsection options for multiselect with section relationship
     *
     * @return array
     */
    private function getSubsectionOptions(): array
    {
        $options = [];
        $collection = $this->subsectionCollectionFactory->create();
        $collection->addFieldToFilter('is_active', 1);
        
        $connection = $this->resourceConnection->getConnection();
        $relationTable = $this->resourceConnection->getTableName('itechpanel_configurator_section_subsection');
        
        $collection->getSelect()->joinLeft(
            ['relation' => $relationTable],
            'main_table.subsection_id = relation.subsection_id',
            ['section_id']
        );

        foreach ($collection as $subsection) {
            $options[] = [
                'value' => $subsection->getId(),
                'label' => $subsection->getTitle(),
                'section_id' => $subsection->getData('section_id')
            ];
        }

        return $options;
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

    /**
     * Load subsection IDs for product
     *
     * @param int $productId
     * @return array
     */
    private function loadSubsectionIds(int $productId): array
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('itechpanel_configurator_subsection_product');

        $select = $connection->select()
            ->from($tableName, ['subsection_id'])
            ->where('product_id = ?', $productId);

        $result = $connection->fetchCol($select);

        return $result ?: [];
    }
}
