<?php
/**
 * Coditron
 *
 * @category  Coditron
 * @package   Coditron_SampleProduct
 * @author    Coditron
 * @copyright Copyright (c) Coditron (https://coditron.com)
 */
namespace Coditron\SampleProduct\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Payment\Model\Config;

class Data extends AbstractHelper
{
    public const XML_PATH_MODULE_ENABLE = 'sampleproduct_config/general/enable';
    public const XML_PATH_LOGINCUSTOMER_ENABLE = 'sampleproduct_config/general/login_customer';
    public const XML_PATH_CUSTOMERGROUP = 'sampleproduct_config/general/customer_group';
    public const XML_PATH_ORDERQTY = 'sampleproduct_config/general/max_quantity';

    /**
     * @var  \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;


    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig     * 
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * Return store configuration value.
     *
     * @param string $path
     * @param int    $storeId
     * @return mixed
     */
    public function getConfigValue($path, $storeId)
    {
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Function to return status of Module
     *
     * @return bool
     */
    public function isModuleEnable()
    {
        return $this->getConfigValue(self::XML_PATH_MODULE_ENABLE, $this->getStore()->getStoreId());
    }

    /**
     * Function to return status of login customer
     *
     * @return bool
     */
    public function isLoginCustomer()
    {
        return $this->getConfigValue(self::XML_PATH_LOGINCUSTOMER_ENABLE, $this->getStore()->getStoreId());
    }

    /**
     * Function to return customer groups
     *
     * @return bool
     */
    public function customerGroup()
    {
        return $this->getConfigValue(self::XML_PATH_CUSTOMERGROUP, $this->getStore()->getStoreId());
    }

    /**
     * Function to return order quantity
     *
     * @return bool
     */
    public function orderQty()
    {
        return $this->getConfigValue(self::XML_PATH_ORDERQTY, $this->getStore()->getStoreId());
    }

    /**
     * Return store.
     *
     * @return \Magento\Store\Model\StoreManagerInterface
     */
    public function getStore()
    {
        return $this->storeManager->getStore();
    }
}
