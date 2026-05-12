<?php
namespace Naman\HandlingFee\Model;

class FeeCalculator
{
    public function calculate(float $subtotal, float $percent): float
    {
        if ($subtotal <= 0 || $percent <= 0) {
            return 0.0;
        }

        return (float)round(($subtotal * $percent) / 100, 4);
    }
}
