<?php
declare(strict_types=1);

namespace Coditron\SampleProduct\Plugin\Checkout\Controller\Sidebar;

use Magento\Checkout\Controller\Sidebar\UpdateItemQty as Subject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Checkout\Model\Cart;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Coditron\SampleProduct\Helper\Data;
use Magento\Framework\Message\ManagerInterface;

class UpdateItemQtyPlugin
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var Data
     */
    private $helperData;

    /**
     * @var Cart
     */
    private $cart;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * Constructor
     *
     * @param ProductRepositoryInterface $productRepository
     * @param Data $helperData
     * @param Cart $cart
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        Data $helperData,
        Cart $cart,
        ManagerInterface $messageManager
    ) {
        $this->productRepository = $productRepository;
        $this->helperData = $helperData;
        $this->cart = $cart;
        $this->messageManager = $messageManager;
    }

    /**
     * Around plugin for execute method to restrict qty update in minicart
     *
     * @param Subject $subject
     * @param callable $proceed
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function aroundExecute(Subject $subject, callable $proceed)
    {
        $params = $subject->getRequest()->getParams();
        $itemId = (int)$params['item_id'];
        $qty = (int)$params['item_qty'];
        try {
            $cartItem = $this->cart->getQuote()->getItemById($itemId);
            if (!$cartItem) {
                throw new LocalizedException(__('The item does not exist.'));
            }
            $productId = $cartItem->getProductId();
            $maxOrderQty = $this->helperData->orderQty();
            if ($this->isSampleProduct($productId) && isset($maxOrderQty)) {
                if ($qty > $maxOrderQty) {
                    throw new LocalizedException(
                        __('You cannot add more than %1 of this sample product.', $maxOrderQty)
                    );
                }
            }
            $cartItem->setQty($qty);
            $this->cart->save();
            $this->messageManager->addSuccessMessage(__('Cart item updated successfully.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage(__('You cannot add more than %1 of this sample product.', $maxOrderQty));
            return;
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('You cannot add more than %1 of this sample product.', $maxOrderQty));
            return;
        }
        return $proceed();
    }

    /**
     * Check if the product is a sample product
     *
     * @param int $productId
     * @return bool
     */
    private function isSampleProduct($productId): bool
    {
        try {
            $product = $this->productRepository->getById($productId);
            $isSampleAttribute = $product->getCustomAttribute('is_sample');
            return $isSampleAttribute && (bool)$isSampleAttribute->getValue();
        } catch (\Exception $e) {
            return false;
        }
    }
}
