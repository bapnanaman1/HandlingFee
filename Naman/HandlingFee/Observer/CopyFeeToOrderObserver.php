<?php
namespace Naman\HandlingFee\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;

class CopyFeeToOrderObserver implements ObserverInterface
{
    public function execute(Observer $observer): void
    {
        $quote = $observer->getEvent()->getQuote();
        $order = $observer->getEvent()->getOrder();

        if (!$quote instanceof Quote || !$order instanceof Order) {
            return;
        }

        $handlingFee = (float)$quote->getData('handling_fee');
        $baseHandlingFee = (float)$quote->getData('base_handling_fee');

        if ($handlingFee <= 0 && $baseHandlingFee <= 0) {
            $address = $quote->isVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();
            if ($address) {
                $handlingFee = (float)$address->getData('handling_fee');
                $baseHandlingFee = (float)$address->getData('base_handling_fee');
            }
        }

        $order->setData('handling_fee', max(0, $handlingFee));
        $order->setData('base_handling_fee', max(0, $baseHandlingFee));
    }
}
