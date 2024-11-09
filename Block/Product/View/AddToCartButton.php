<?php
/**
 * Coditron
 *
 * @category  Coditron
 * @package   Coditron_SampleProduct
 * @author    Coditron
 * @copyright Copyright (c) Coditron (https://coditron.com)
 */
namespace Coditron\SampleProduct\Block\Product\View;

use Magento\Catalog\Block\Product\Context;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Framework\Url\EncoderInterface as UrlEncoderInterface;
use Magento\Framework\Json\EncoderInterface as JsonEncoderInterface;
use Magento\Catalog\Helper\Product;
use Magento\Catalog\Model\ProductTypes\ConfigInterface;
use Magento\Framework\Locale\FormatInterface;
use Magento\Customer\Model\Session;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Coditron\SampleProduct\Helper\Data;
use Magento\Framework\App\Http\Context as AppContext;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Customer\Model\SessionFactory;
use Magento\Checkout\Helper\Cart;

class AddToCartButton extends \Magento\Catalog\Block\Product\View
{
    protected $helperData;
    protected $customerSession;
    protected $context;
    protected $customerRepository;
    protected $groupRepository;
    protected $_cartHelper;

    public function __construct(
        Context $context,
        UrlEncoderInterface $urlEncoder,
        JsonEncoderInterface $jsonEncoder,
        StringUtils $string,
        Product $productHelper,
        ConfigInterface $productTypeConfig,
        FormatInterface $localeFormat,
        Session $customerSession,
        ProductRepositoryInterface $productRepository,
        PriceCurrencyInterface $priceCurrency,
        Data $helperData,
        AppContext $custcontext,
        CustomerRepositoryInterface $customerRepository,
        GroupRepositoryInterface $groupRepository,
        SessionFactory $customer,
        Cart $cartHelper,
        array $data = []
    ) {
        $this->helperData = $helperData;
        $this->custcontext = $custcontext;
        $this->customerRepository = $customerRepository;
        $this->groupRepository = $groupRepository;
        $this->customer = $customer;
        $this->_cartHelper = $cartHelper;
        parent::__construct(
            $context,
            $urlEncoder,
            $jsonEncoder,
            $string,
            $productHelper,
            $productTypeConfig,
            $localeFormat,
            $customerSession,
            $productRepository,
            $priceCurrency,
            $data
        );
    }

    /**
    * Check if the module is enabled
    *
    * @return bool
    */    
    public function isModuleEnabled()
    {
        return $this->helperData->isModuleEnable();
    }

    /**
    * Check if the login is required for the customer
    *
    * @return bool
    */
    public function isAllowCustomerLogin()
    {
        return $this->helperData->isLoginCustomer();
    }

    /**
    * Check if the customer is logged in
    *
    * @return bool
    */
    public function isCustomerLoggedIn()
    {
        $isLoggedIn = $this->custcontext->getValue(CustomerContext::CONTEXT_AUTH);
        return $isLoggedIn;
    }

    /**
    * Get the customer group ID of the logged-in customer
    *
    * @return int|null
    */
    public function loggedInCustomerGroup()
    {
        $isLoggedIn = $this->custcontext->getValue(CustomerContext::CONTEXT_AUTH);
        if($isLoggedIn){
            $customerId = $this->customer->create()->getCustomer()->getId();
            if ($customerId) {
                try {
                    $customer = $this->customerRepository->getById($customerId);
                    $groupId = $customer->getGroupId();
                    return $groupId; 
                } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                    return null;
                }
            }
        }
    }

    /**
    * Get the allowed customer groups for accessing the module
    *
    * @return string
    */
    public function allowCustomerGroup()
    {
        return $this->helperData->customerGroup();
    }

    /**
    * Check if the logged-in customer group is allowed
    *
    * @return bool
    */
    public function isCustomerGroupAllowed()
    {
        $customerGroupId = $this->loggedInCustomerGroup();
        $allowedGroups = explode(',', $this->allowCustomerGroup());
        return in_array($customerGroupId, $allowedGroups);
    }

    /**
     * Get custom attribute value by product ID
     *
     * @param int $productId
     * @param string $attributeCode
     * @return mixed
     */
    public function getCustomAttributeByProductId($productId, $attributeCode)
    {
        try {
            $product = $this->productRepository->getById($productId);
            $customAttribute = $product->getCustomAttribute($attributeCode);
            if ($customAttribute) {
                return $customAttribute->getValue();
            } else {
                return null;
            }
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
    * Format price with the currency symbol
    *
    * @param float $price
    * @return string
    */
    public function formatPriceWithCurrency($price)
    {
        return $this->priceCurrency->format($price, false, 2);
    }

    /**
    * Retrieve the maximum order quantity for samples
    *
    * @return int|null
    */
    public function maxOrderQty()
    {
        $maxOrderQty = $this->helperData->orderQty();
        if(isset($maxOrderQty)){
            return $this->helperData->orderQty();
        }
        return null;
    }

    /**
     * Retrieve sample product Id
     *
     * @param string $sampleProductSku
     * @return string
     */
    public function getSampleProductId($sampleProductSku)
    {
        $product = $this->productRepository->get($sampleProductSku);
        if($product){
            return $product->getId();
        }
        return null; 
    }
}
