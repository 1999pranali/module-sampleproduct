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

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class CheckSampleProductInOrder implements ObserverInterface
{
    protected $orderRepository;

    public function __construct(
        OrderRepositoryInterface $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $containsSample = 0;
        foreach ($order->getAllItems() as $item) {
            $product = $item->getProduct();
            if ($product->getData('is_sample')) { 
                $containsSample = 1;
                break;
            }
        }
        $order->setData('contains_sample', $containsSample);
        $this->orderRepository->save($order);
        return $this;
    }
}
