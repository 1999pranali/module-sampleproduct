<?php
/**
 * Coditron
 *
 * @category  Coditron
 * @package   Coditron_SampleProduct
 * @author    Coditron
 * @copyright Copyright (c) Coditron (https://coditron.com)
 */
namespace Coditron\SampleProduct\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface; 

class CreateSampleProduct implements ObserverInterface
{
    protected $productFactory;
    protected $productRepository;
    protected $messageManager;

    // To prevent recursion
    private static $isObserverExecuting = false;

    public function __construct(
        ProductFactory $productFactory,
        ProductRepositoryInterface $productRepository,
        ManagerInterface $messageManager 
    ) {
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->messageManager = $messageManager;
    }

    public function execute(Observer $observer)
    {
        if (self::$isObserverExecuting) {
            return;
        }        
        self::$isObserverExecuting = true;
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $observer->getEvent()->getProduct();        
        try {
            $product = $this->productRepository->getById($product->getId());

            $isSample = $product->getCustomAttribute('enable_sample');
            if(!$isSample || !$isSample->getValue()) 
            {
                return; 
            }
            $sampleSku = $product->getSku() . '-sample';
            // Check if a sample product already exists
            $existingSampleProduct = $this->getSampleProductBySku($sampleSku);

            if ($existingSampleProduct) {
                $this->updateSampleProduct($existingSampleProduct, $product);
                $this->messageManager->addSuccessMessage(__('You saved the sample product'));
            } else {
                $this->createNewSampleProduct($product, $sampleSku);
                $this->messageManager->addSuccessMessage(__('You saved the sample product'));
            }                  
        } catch (LocalizedException $e) {
            $this->messageManager->addSErrorMessage(__('Cannot saved smple product'));

        } catch (\Exception $e) {
            $this->messageManager->addSErrorMessage(__('Cannot saved smple product'));

        } finally {
            self::$isObserverExecuting = false;
        }
    }

    /**
     * Get a sample product by SKU.
     *
     * @param string $sampleSku
     * @return \Magento\Catalog\Model\Product|null
     */
    protected function getSampleProductBySku($sampleSku)
    {
        try {
            return $this->productRepository->get($sampleSku);
        } catch (LocalizedException $e) {
            return null; 
        }
    }

    /**
     * Create a new sample product based on the original product.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $sampleSku
     */
    protected function createNewSampleProduct($product, $sampleSku)
    {
        $sampleTitle = $product->getCustomAttribute('sample_title') 
            ? $product->getCustomAttribute('sample_title')->getValue() 
            : null;

        $samplePrice = $product->getCustomAttribute('sample_price') 
            ? $product->getCustomAttribute('sample_price')->getValue() 
            : null;
        
        $sampleQty = $product->getCustomAttribute('sample_qty') 
            ? $product->getCustomAttribute('sample_qty')->getValue() 
            : null;

        $sampleProduct = $this->productFactory->create();
        $sampleProduct->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
        $sampleProduct->setAttributeSetId($product->getAttributeSetId());
        $sampleProduct->setWebsiteIds($product->getWebsiteIds());
        $sampleProduct->setName($sampleTitle ?: $product->getName() . ' - Sample');
        $sampleProduct->setSku($sampleSku);
        $sampleProduct->setPrice($samplePrice ?: 0);
        $sampleProduct->setWeight($product->getWeight()); 
        $sampleProduct->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE);
        $sampleProduct->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        $sampleProduct->setCustomAttribute('is_sample', 1);
        $sampleProduct->setStockData([
            'use_config_manage_stock' => 1,
            'qty' => $sampleQty ?: 100,
            'is_qty_decimal' => 0,
            'is_in_stock' => 1,
        ]);

        $this->productRepository->save($sampleProduct);
    }

    /**
     * Update an existing sample product based on the original product.
     *
     * @param \Magento\Catalog\Model\Product $sampleProduct
     * @param \Magento\Catalog\Model\Product $product
     */
    protected function updateSampleProduct($sampleProduct, $product)
    {
        $sampleTitle = $product->getCustomAttribute('sample_title') 
            ? $product->getCustomAttribute('sample_title')->getValue() 
            : null;

        $samplePrice = $product->getCustomAttribute('sample_price') 
            ? $product->getCustomAttribute('sample_price')->getValue() 
            : null;
        
        $sampleQty = $product->getCustomAttribute('sample_qty') 
            ? $product->getCustomAttribute('sample_qty')->getValue() 
            : null;        

        // Update sample product details
        $sampleProduct->setName($sampleTitle ?: $product->getName() . ' - Sample');
        $sampleProduct->setPrice($samplePrice ?: 0);
        $sampleProduct->setWeight($product->getWeight()); // Set the weight of the sample product
        $sampleProduct->setStockData([
            'use_config_manage_stock' => 1,
            'qty' => $sampleQty ?: 100,
            'is_qty_decimal' => 0,
            'is_in_stock' => 1,
        ]);

        $this->productRepository->save($sampleProduct);
    }
}
