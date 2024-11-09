<?php
/**
 * Coditron
 *
 * @category  Coditron
 * @package   Coditron_SampleProduct
 * @author    Coditron
 * @copyright Copyright (c) Coditron (https://coditron.com)
 */
namespace Coditron\SampleProduct\Controller\Cart;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Cart;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;

class Add extends Action
{
    protected $cart;
    protected $productRepository;
    protected $resultJsonFactory;

    public function __construct(
        Context $context,
        Cart $cart,
        ProductRepositoryInterface $productRepository,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->cart = $cart;
        $this->productRepository = $productRepository;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $productId = $this->getRequest()->getParam('product_id');
        $qty = $this->getRequest()->getParam('qty');

        try {
            $product = $this->productRepository->getById($productId);
            $this->cart->addProduct($product, ['qty' => $qty]);
            $this->cart->save();
            return $result->setData(['success' => true, 'message' => __('Product added to cart.')]);
        } catch (LocalizedException $e) {
            return $result->setData(['success' => false, 'message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return $result->setData(['success' => false, 'message' => __('Unable to add the product to the cart.')]);
        }
    }
}
