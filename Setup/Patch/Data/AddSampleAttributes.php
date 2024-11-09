<?php
/**
 * Coditron
 *
 * @category  Coditron
 * @package   Coditron_SampleProduct
 * @author    Coditron
 * @copyright Copyright (c) Coditron (https://coditron.com)
 */
namespace Coditron\SampleProduct\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Psr\Log\LoggerInterface;

class AddSampleAttributes implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private ModuleDataSetupInterface $moduleDataSetup;

    /**
     * @var EavSetupFactory $eavSetupFactory
     */
    private EavSetupFactory $eavSetupFactory;

    /**
     * @var LoggerInterface $logger
     */
    private LoggerInterface $logger;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
        LoggerInterface $logger
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->logger = $logger;
    }
    /**
     * @return void
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->addAttribute(Product::ENTITY, 'enable_sample', [
            'group' => 'Sample',
            'type' => 'int',
            'label' => __('Enable Sample'),
            'input' => 'boolean',
            'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
            'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
            'default' => false,
            'visible' => true,
            'required' => false,
            'filterable' => true,
            'user_defined' => true,
            'visible_on_front' => false,
            'used_in_product_listing' => true,
            'sort_order' => 100
        ]);

        $eavSetup->addAttribute(Product::ENTITY, 'sample_title', [
            'group' => 'Sample',
            'type' => 'varchar',
            'label' => __('Sample Title'),
            'input' => 'text',
            'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'visible_on_front' => false,
            'used_in_product_listing' => true,
            'sort_order' => 110
        ]);

        $eavSetup->addAttribute(Product::ENTITY, 'sample_price', [
            'group' => 'Sample',
            'type' => 'decimal',
            'label' => __('Sample Price'),
            'input' => 'price',
            'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'visible_on_front' => false,
            'used_in_product_listing' => false,
            'sort_order' => 120
        ]);

        $eavSetup->addAttribute(Product::ENTITY, 'sample_qty', [
            'group' => 'Sample',
            'type' => 'int',
            'label' => __('Sample Qty'),
            'input' => 'text',
            'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'visible_on_front' => false,
            'used_in_product_listing' => false,
            'sort_order' => 130
        ]);

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @return void
     */
    public function revert()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->removeAttribute(Product::ENTITY, 'enable_sample');
        $eavSetup->removeAttribute(Product::ENTITY, 'sample_title');
        $eavSetup->removeAttribute(Product::ENTITY, 'sample_price');
        $eavSetup->removeAttribute(Product::ENTITY, 'sample_qty');
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @return array
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * @return string
     */
    public static function getVersion()
    {
        return '1.0.1';
    }
}
