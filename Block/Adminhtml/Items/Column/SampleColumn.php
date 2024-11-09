<?php
/**
 * Coditron
 *
 * @category  Coditron
 * @package   Coditron_SampleProduct
 * @author    Coditron
 * @copyright Copyright (c) Coditron (https://coditron.com)
 */
namespace Coditron\SampleProduct\Block\Adminhtml\Items\Column;

use Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn;
use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Model\Product\OptionFactory;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\Framework\Registry;
use Magento\GiftMessage\Helper\Message;
use Magento\Checkout\Helper\Data;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class SampleColumn extends DefaultColumn
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
      * @param \Magento\Backend\Block\Template\Context $context
      * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
      * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
      * @param \Magento\Framework\Registry $registry
      * @param \Magento\Catalog\Model\Product\OptionFactory $optionFactory
      * @param ProductRepositoryInterface $productRepository
      * @param array $data
      */
      public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Product\OptionFactory $optionFactory,
        ProductRepositoryInterface $productRepository,
        array $data = []
    ) {
        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $optionFactory, $data);
        $this->productRepository = $productRepository;
    }

    /**
     * Retrieve 'is_sample' value for the item
     *
     * @return string
     */
    public function getIsSample()
    {
        $item = $this->getItem();
        $productId = $item->getProductId();
        $isSample = 'No';
        $product = $this->productRepository->getById($productId);
        $isSampleAttribute = $product->getCustomAttribute('is_sample');
        if ($isSampleAttribute) {
            $value = $isSampleAttribute->getValue();
            if ($value === true || $value === '1') {
                $isSample = 'Yes';
            }
        }
        return $isSample;
    }

    /**
     * Render the column output
     *
     * @return string
     */
    protected function _toHtml()
    {
        return $this->getIsSample();
    }
}