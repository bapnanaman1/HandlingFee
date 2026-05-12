<?php
namespace Naman\HandlingFee\Block\Sales\Order;

use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;

class Totals extends Template
{
    public function initTotals(): self
    {
        $parent = $this->getParentBlock();
        if (!$parent || !method_exists($parent, 'getSource')) {
            return $this;
        }

        $source = $parent->getSource();
        $handlingFee = (float)$source->getData('handling_fee');
        if ($handlingFee <= 0) {
            return $this;
        }

        $total = new DataObject([
            'code' => 'handling_fee',
            'field' => 'handling_fee',
            'strong' => false,
            'value' => $handlingFee,
            'base_value' => (float)$source->getData('base_handling_fee'),
            'label' => __('Handling Fee'),
        ]);

        $parent->addTotalBefore($total, 'grand_total');
        return $this;
    }
}
