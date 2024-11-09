<?php
declare(strict_types=1);

namespace Coditron\SampleProduct\Rewrite\Magento\Checkout\Controller\Cart;

use Magento\Checkout\Model\Cart\RequestQuantityProcessor;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Coditron\SampleProduct\Helper\Data;
use Magento\Catalog\Api\ProductRepositoryInterface;

class UpdatePost extends \Magento\Checkout\Controller\Cart\UpdatePost
{
    /**
     * @var RequestQuantityProcessor
     */
    private $quantityProcessor;

    /**
     * @var \Coditron\SampleProduct\Helper\Data
     */
    private $helperData;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Checkout\Model\Cart $cart
     * @param RequestQuantityProcessor $quantityProcessor
     * @param \Coditron\SampleProduct\Helper\Data $helperData
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Checkout\Model\Cart $cart,
        RequestQuantityProcessor $quantityProcessor = null,
        Data $helperData,
        ProductRepositoryInterface $productRepository
    ) {
        $this->helperData = $helperData;
        $this->productRepository = $productRepository;
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart
        );
        $this->quantityProcessor = $quantityProcessor ?: $this->_objectManager->get(RequestQuantityProcessor::class);
    }

    /**
     * Check if the product is a sample product.
     *
     * @param int $productId
     * @return bool
     */
    protected function isSampleProduct($productId): bool
    {
        $product = $this->productRepository->getById($productId);
        $isSampleAttribute = $product->getCustomAttribute('is_sample');
        return $isSampleAttribute && (bool)$isSampleAttribute->getValue();
    }

    protected function _updateShoppingCart()
    {
        try {
            $cartData = $this->getRequest()->getParam('cart');
            $maxOrderQty = $this->helperData->orderQty();           
            if (is_array($cartData)) {
                foreach ($cartData as $itemId => $item) {
                    if (isset($item['qty'])) {
                        $qty = $item['qty'];
                        $cartItem = $this->cart->getQuote()->getItemById($itemId);
                        $productId = $cartItem->getProductId();
                        // Check if product is a sample product
                        if ($this->isSampleProduct($productId) && isset($maxOrderQty)) {
                            if ($qty > $maxOrderQty) {
                                throw new \Magento\Framework\Exception\LocalizedException(
                                    __('You cannot add more than %1 of this sample product.', $maxOrderQty)
                                );
                            }
                        }
                    }
                }
                // If all validations pass, update the cart
                $cartData = $this->quantityProcessor->process($cartData);
                $cartData = $this->cart->suggestItemsQty($cartData);
                $this->cart->updateItems($cartData)->save();
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage(
                $this->_objectManager->get(\Magento\Framework\Escaper::class)->escapeHtml($e->getMessage())
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e, __('Something went wrong while updating the shopping cart.'));
        }
    }
}