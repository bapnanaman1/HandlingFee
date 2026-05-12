<?php
namespace Naman\HandlingFee\Plugin\Quote;

use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\TotalsExtensionFactory;
use Magento\Quote\Api\Data\TotalsInterface;
use Magento\Quote\Model\Cart\CartTotalRepository;

class CartTotalsExtensionAttributes
{
    public function __construct(
        private readonly TotalsExtensionFactory $totalsExtensionFactory,
        private readonly CartRepositoryInterface $cartRepository
    ) {
    }

    public function afterGet(CartTotalRepository $subject, TotalsInterface $result, int $cartId): TotalsInterface
    {
        $quote = $this->cartRepository->get($cartId);
        $extensionAttributes = $result->getExtensionAttributes() ?: $this->totalsExtensionFactory->create();
        $extensionAttributes->setHandlingFee((float)$quote->getData('handling_fee'));
        $extensionAttributes->setBaseHandlingFee((float)$quote->getData('base_handling_fee'));
        $result->setExtensionAttributes($extensionAttributes);

        return $result;
    }
}
