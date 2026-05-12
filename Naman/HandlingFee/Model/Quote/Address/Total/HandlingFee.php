<?php
namespace Naman\HandlingFee\Model\Quote\Address\Total;

use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Naman\HandlingFee\Model\Config;
use Naman\HandlingFee\Model\ExemptionChecker;
use Naman\HandlingFee\Model\FeeCalculator;

class HandlingFee extends AbstractTotal
{
    public const TOTAL_CODE = 'handling_fee';

    public function __construct(
        private readonly Config $config,
        private readonly ExemptionChecker $exemptionChecker,
        private readonly FeeCalculator $feeCalculator
    ) {
        $this->setCode(self::TOTAL_CODE);
    }

    public function collect(Quote $quote, ShippingAssignmentInterface $shippingAssignment, Total $total): self
    {
        parent::collect($quote, $shippingAssignment, $total);
        $this->clearHandlingFee($quote, $shippingAssignment, $total);

        $items = $shippingAssignment->getItems();
        if (!count($items)) {
            return $this;
        }

        $storeId = (int)$quote->getStoreId();
        if (!$this->config->isEnabled($storeId)) {
            return $this;
        }

        $baseSubtotal = (float)$total->getBaseSubtotalWithDiscount();
        $subtotal = (float)$total->getSubtotalWithDiscount();

        if ($baseSubtotal <= 0 && (float)$total->getBaseSubtotal() > 0) {
            $baseSubtotal = (float)$total->getBaseSubtotal();
            $subtotal = (float)$total->getSubtotal();
        }

        if ($this->exemptionChecker->isExempt($quote, $baseSubtotal)) {
            return $this;
        }

        $percent = $this->config->getFeePercent($storeId);
        $baseHandlingFee = $this->feeCalculator->calculate($baseSubtotal, $percent);
        $handlingFee = $this->feeCalculator->calculate($subtotal, $percent);

        if ($baseHandlingFee <= 0 || $handlingFee <= 0) {
            return $this;
        }

        $total->addBaseTotalAmount(self::TOTAL_CODE, $baseHandlingFee);
        $total->addTotalAmount(self::TOTAL_CODE, $handlingFee);
        $total->setData('base_handling_fee', $baseHandlingFee);
        $total->setData('handling_fee', $handlingFee);

        $address = $quote->isVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();
        if ($address) {
            $address->setData('base_handling_fee', $baseHandlingFee);
            $address->setData('handling_fee', $handlingFee);
        }

        $quote->setData('base_handling_fee', $baseHandlingFee);
        $quote->setData('handling_fee', $handlingFee);

        return $this;
    }

    public function fetch(Quote $quote, Total $total): ?array
    {
        $amount = (float)$total->getData('handling_fee');
        if ($amount <= 0) {
            return null;
        }

        return [
            'code' => self::TOTAL_CODE,
            'title' => __('Handling Fee'),
            'value' => $amount,
        ];
    }

    private function clearHandlingFee(Quote $quote, ShippingAssignmentInterface $shippingAssignment, Total $total): void
    {
        $total->addBaseTotalAmount(self::TOTAL_CODE, -1 * (float)$total->getData('base_handling_fee'));
        $total->addTotalAmount(self::TOTAL_CODE, -1 * (float)$total->getData('handling_fee'));
        $total->setData('base_handling_fee', 0);
        $total->setData('handling_fee', 0);

        $address = $quote->isVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();
        if ($shippingAssignment->getShipping() && $shippingAssignment->getShipping()->getAddress()) {
            $address = $shippingAssignment->getShipping()->getAddress();
        }
        if ($address) {
            $address->setData('base_handling_fee', 0);
            $address->setData('handling_fee', 0);
        }

        $quote->setData('base_handling_fee', 0);
        $quote->setData('handling_fee', 0);
    }
}
