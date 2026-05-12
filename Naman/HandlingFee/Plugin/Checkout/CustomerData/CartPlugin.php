<?php
namespace Naman\HandlingFee\Plugin\Checkout\CustomerData;

use Magento\Checkout\CustomerData\Cart;
use Magento\Checkout\Helper\Data as CheckoutHelper;
use Magento\Checkout\Model\Session as CheckoutSession;

class CartPlugin
{
    public function __construct(
        private readonly CheckoutSession $checkoutSession,
        private readonly CheckoutHelper $checkoutHelper
    ) {
    }

    public function afterGetSectionData(Cart $subject, array $result): array
    {
        $handlingFee = (float)$this->checkoutSession->getQuote()->getData('handling_fee');
        if ($handlingFee <= 0) {
            unset($result['handling_fee_amount'], $result['handling_fee']);
            return $result;
        }

        $result['handling_fee_amount'] = $handlingFee;
        $result['handling_fee'] = $this->checkoutHelper->formatPrice($handlingFee);

        return $result;
    }
}
