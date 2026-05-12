<?php
namespace Naman\HandlingFee\Block\Checkout\Success;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\View\Element\Template;
use Magento\Sales\Api\Data\OrderInterface;

class HandlingFee extends Template
{
    public function __construct(
        Template\Context $context,
        private readonly CheckoutSession $checkoutSession,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getOrder(): ?OrderInterface
    {
        $order = $this->checkoutSession->getLastRealOrder();
        return $order && $order->getEntityId() ? $order : null;
    }

    public function hasHandlingFee(): bool
    {
        $order = $this->getOrder();
        return $order !== null && (float)$order->getData('handling_fee') > 0;
    }

    public function getFormattedHandlingFee(): string
    {
        $order = $this->getOrder();
        if ($order === null) {
            return '';
        }

        return (string)$order->formatPrice((float)$order->getData('handling_fee'));
    }
}
