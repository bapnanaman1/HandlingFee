# Naman_HandlingFee

Magento 2 module that adds a handling fee total with exemption rules and API exposure.

## Compatibility

- Magento 2.4.x
- PHP 8.1+

## Business Rules

- Handling Fee = 7% of cart subtotal (after discount).
- Exempt when subtotal is greater than 50000.
- Exempt when customer group code is Wholesale.
- Guests are treated as regular retail customers.

Default values are preconfigured and can be changed at:

Stores > Configuration > Sales > Handling Fee

## Installation

1. Place module under app/code/Naman/HandlingFee.
2. Run setup commands:

```bash
php bin/magento module:enable Naman_HandlingFee
php bin/magento setup:upgrade
php bin/magento cache:flush
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy -f
```

## What This Module Implements

- Custom total collector based on Magento Quote Address AbstractTotal.
- Exemption logic service separated from calculation logic.
- Persistence in quote, quote_address, and sales_order.
- Quote-to-order conversion mapping via fieldset.
- Total line display in:
  - Mini-cart
  - Cart page
  - Checkout summary
  - Order success page
  - Customer account order view
  - Admin order view
  - Admin create order totals
- REST extension attributes for:
  - Magento\Quote\Api\Data\TotalsInterface
  - Magento\Sales\Api\Data\OrderInterface

## Data Model

Columns added:

- quote.handling_fee
- quote.base_handling_fee
- quote_address.handling_fee
- quote_address.base_handling_fee
- sales_order.handling_fee
- sales_order.base_handling_fee

## API Validation Targets

Use provided Postman collection in postman/Naman-HandlingFee.postman_collection.json and validate:

- GET /rest/V1/carts/mine/totals
- POST /rest/V1/carts/mine/payment-information
- GET /rest/V1/orders/{id}

## Assumptions

- Subtotal for fee calculation uses subtotal after discount when available.
- Handling fee is non-taxable and not discountable.
- Wholesale exemption is determined by customer group code, not hardcoded group ID.
