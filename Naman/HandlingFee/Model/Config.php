<?php
namespace Naman\HandlingFee\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    private const XML_PATH_ENABLED = 'naman_handlingfee/general/enabled';
    private const XML_PATH_FEE_PERCENT = 'naman_handlingfee/general/fee_percent';
    private const XML_PATH_SUBTOTAL_THRESHOLD = 'naman_handlingfee/general/subtotal_threshold';
    private const XML_PATH_WHOLESALE_GROUP = 'naman_handlingfee/general/wholesale_group_code';

    public function __construct(private readonly ScopeConfigInterface $scopeConfig)
    {
    }

    public function isEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getFeePercent(?int $storeId = null): float
    {
        return (float)$this->scopeConfig->getValue(self::XML_PATH_FEE_PERCENT, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getSubtotalThreshold(?int $storeId = null): float
    {
        return (float)$this->scopeConfig->getValue(self::XML_PATH_SUBTOTAL_THRESHOLD, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getWholesaleGroupCode(?int $storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_PATH_WHOLESALE_GROUP, ScopeInterface::SCOPE_STORE, $storeId);
    }
}
