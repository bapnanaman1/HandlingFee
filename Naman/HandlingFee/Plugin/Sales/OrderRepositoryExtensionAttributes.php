<?php
namespace Naman\HandlingFee\Plugin\Sales;

use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Model\OrderRepository;

class OrderRepositoryExtensionAttributes
{
    public function __construct(private readonly OrderExtensionFactory $orderExtensionFactory)
    {
    }

    public function afterGet(OrderRepository $subject, OrderInterface $order): OrderInterface
    {
        return $this->attachExtensionAttributes($order);
    }

    public function afterGetList(OrderRepository $subject, OrderSearchResultInterface $searchResult): OrderSearchResultInterface
    {
        foreach ($searchResult->getItems() as $order) {
            $this->attachExtensionAttributes($order);
        }

        return $searchResult;
    }

    private function attachExtensionAttributes(OrderInterface $order): OrderInterface
    {
        $extensionAttributes = $order->getExtensionAttributes() ?: $this->orderExtensionFactory->create();
        $extensionAttributes->setHandlingFee((float)$order->getData('handling_fee'));
        $extensionAttributes->setBaseHandlingFee((float)$order->getData('base_handling_fee'));
        $order->setExtensionAttributes($extensionAttributes);

        return $order;
    }
}
