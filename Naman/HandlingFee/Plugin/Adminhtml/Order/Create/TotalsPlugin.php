<?php
namespace Naman\HandlingFee\Plugin\Adminhtml\Order\Create;

use Magento\Framework\DataObject;
use Magento\Sales\Block\Adminhtml\Order\Create\Totals;

class TotalsPlugin
{
    public function afterGetTotals(Totals $subject, array $totals): array
    {
        if (isset($totals['handling_fee'])) {
            return $totals;
        }

        $quote = $subject->getQuote();
        $address = $quote->isVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();
        if (!$address) {
            return $totals;
        }

        $handlingFee = (float)$address->getData('handling_fee');
        $baseHandlingFee = (float)$address->getData('base_handling_fee');
        if ($handlingFee <= 0 && $baseHandlingFee <= 0) {
            return $totals;
        }

        $handlingFeeTotal = new DataObject([
            'code' => 'handling_fee',
            'title' => __('Handling Fee'),
            'value' => $handlingFee,
            'base_value' => $baseHandlingFee,
        ]);

        if (isset($totals['grand_total'])) {
            $orderedTotals = [];
            foreach ($totals as $code => $total) {
                if ($code === 'grand_total') {
                    $orderedTotals['handling_fee'] = $handlingFeeTotal;
                }
                $orderedTotals[$code] = $total;
            }
            return $orderedTotals;
        }

        $totals['handling_fee'] = $handlingFeeTotal;
        return $totals;
    }
}
