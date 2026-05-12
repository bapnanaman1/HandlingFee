<?php
namespace Naman\HandlingFee\Model;

use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote;

class ExemptionChecker
{
    public function __construct(
        private readonly Config $config,
        private readonly GroupRepositoryInterface $groupRepository
    ) {
    }

    public function isExempt(Quote $quote, float $baseSubtotal): bool
    {
        $storeId = (int)$quote->getStoreId();
        if ($baseSubtotal > $this->config->getSubtotalThreshold($storeId)) {
            return true;
        }

        $groupId = (int)$quote->getCustomerGroupId();
        if ($groupId <= 0) {
            return false;
        }

        try {
            $groupCode = (string)$this->groupRepository->getById($groupId)->getCode();
        } catch (NoSuchEntityException) {
            return false;
        }

        return strcasecmp(trim($groupCode), trim($this->config->getWholesaleGroupCode($storeId))) === 0;
    }
}
