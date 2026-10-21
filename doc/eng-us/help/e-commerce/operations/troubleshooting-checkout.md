---
title: Troubleshooting checkout
audience: ecommerce, website-creator, admin
category: e-commerce
order: 82
summary: Symptoms, causes and fixes for an Exponential checkout that goes wrong - paid but no order, NO_CALLBACK, empty basket after cancel, items removed at checkout, missing shipping, unknown or zero VAT, gateway missing from the event, no order mail, status cannot be set - with the logs and screens to check.
keywords: troubleshooting, checkout problem, payment callback, NO_CALLBACK, checkoutagain, cancelcheckout, payment not confirmed, order missing, VAT unknown, VAT zero, shipping missing, gateway not listed, order mail missing, eZPaymentGatewayType.log, workflow process
type: help
---
# Troubleshooting checkout

This page is for administrators and developers who have to find out why a checkout went wrong. Each section starts
from what someone reports, lists the likely causes in the order to check them, and names the log or screen that
proves it.

## Contents

1. [Where to look first](#where-to-look-first)
2. [Customer paid but no order](#customer-paid-but-no-order)
3. [Items disappear at checkout](#items-disappear-at-checkout)
4. [Basket empty after cancelling](#basket-empty-after-cancelling)
5. [Shipping missing or doubled](#shipping-missing-or-doubled)
6. [VAT unknown or zero](#vat-unknown-or-zero)
7. [Gateway not offered](#gateway-not-offered)
8. [No order mail](#no-order-mail)
9. [A status cannot be set](#a-status-cannot-be-set)

## Where to look first

| Source | Shows |
|---|---|
| Store dashboard, "How your shop works" and "Next steps" | The configuration in effect and known problems |
| **Setup > Workflow processes** | Workflow processes still waiting, for example a payment |
| `var/log/eZPaymentGatewayType.log`, `var/log/eZRedirectGateway.log` | Each step of the payment event and redirect gateways |
| The debug log | Handler files not found, VAT and category errors |
| **Audit** tab, `commerce` channel | Checkouts started, orders created, payments approved ([Commerce audit trail](commerce-audit-trail.md)) |

## Customer paid but no order

The provider took the money, but the order list has no order. The order is still temporary because the provider's
callback never approved the payment ([Temporary orders and activation](../checkout/temporary-orders-and-activation.md)).
The customer may have seen "check out again" pages and then an error with the code `NO_CALLBACK`
([Checkout operations step by step](../checkout/checkout-operations-step-by-step.md#step-6-back-from-the-provider)).

1. Is the callback address reachable from the internet over HTTPS, without login, maintenance page or IP filter?
2. Is the callback view exempt from policy checks (for PayPal `PolicyOmitList[]=paypal/notify_url`)?
3. Do the provider's settings point to the live address, not a test or old domain?
4. Does the gateway log show the callback and why it was refused (amount, currency, validation)?
5. Is the provider in the same mode (test or live) as the site's settings?

Fix the cause first; then settle the affected customers' orders by hand with the provider's records.

## Items disappear at checkout

When **Checkout** is pressed, the basket is verified: items whose product was unpublished, or whose price or VAT
changed since they were added, are removed and listed on the basket page. Usually a price was changed during the day.
Tell the customer to add the item again.

## Basket empty after cancelling

| Cause | Fix |
|---|---|
| `ClearBasketOnCheckout=enabled` | Expected behavior with that setting; switch it off unless the gateway requires it |
| `ClearBasketOnLogout=enabled` and the customer signed out | Expected |
| An installation older than 6.0.15 | Cancel on the confirmation page used to lose the basket; update ([Permanent order receipts](../../../features/6.0/order-receipts.md#a-checkout-fix-made-on-the-way)) |

## Shipping missing or doubled

| Symptom | Cause |
|---|---|
| No shipping line | The shipping workflow is not on `shop` `confirmorder` before, or has no Simple shipping event |
| Wrong amount | `workflow.ini [SimpleShippingWorkflow] ShippingCost` in another override file wins ([Shop settings walkthroughs](../settings/README.md#checking-the-value-in-effect)) |
| Two shipping charges | Both a shipping handler and the simple shipping workflow are active |
| Handler ignored | Debug log: "Shipping handler ... not found"; check `ExtensionDirectories[]` and the file name |

## VAT unknown or zero

| Symptom | Cause |
|---|---|
| "Determined by VAT charging rules" not offered | `[VATSettings] Handler` not set |
| VAT unknown | Dynamic VAT type without a handler, or the handler returned nothing |
| 0 percent for some customers | No rule matched; add a catch-all rule (`*`, no categories) |
| Error about the product category attribute | `ProductCategoryAttribute` empty or not on the class |
| Notice about a missing user country | The account handler's form has no country, or `UserCountryAttribute` is wrong |

Details: [Product categories and VAT rules](../catalog/product-categories-and-vat-rules.md).

## Gateway not offered

The Payment Gateway event lists only registered gateways. Check that the extension is active, the file is
`classes/<extension>gateway.php`, it calls `eZPaymentGatewayType::registerGateway()`, and the INI cache was cleared
([paymentgateways.ini walkthrough](../settings/paymentgateways-ini-walkthrough.md#checking-that-a-gateway-is-available)).

## No order mail

See [Order mail and receipts for operators](../orders/order-mail-and-receipts-for-operators.md#when-a-mail-does-not-arrive).
The mail is sent only when the order is activated, so a missing order explains a missing mail.

## A status cannot be set

The choices offered are those the user's `shop/setstatus` policies allow for the current status
([Status transitions and permissions](../orders/status-transitions-and-permissions.md#how-the-check-works)).

## Related pages

- [Operating a store](README.md)
- [When something goes wrong](../../when-something-goes-wrong.md)
- [Workflows: troubleshooting](../../../guides/workflows.md#9-troubleshooting)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
