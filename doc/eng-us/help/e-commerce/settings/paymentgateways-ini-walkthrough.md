---
title: paymentgateways.ini walkthrough
audience: ecommerce, website-creator, developer
category: e-commerce
order: 35
summary: How Exponential finds payment gateways - paymentgateways.ini AvailableGateways and GatewaysDirectories, the automatic loading of a gateway class from an active extension, registerGateway, and the provider files paypal.ini and ezauthorize.ini.
keywords: paymentgateways.ini, GatewaysSettings, AvailableGateways, GatewaysDirectories, GatewaysDerictories, registerGateway, eZPaymentGatewayType, paypal.ini, ezauthorize.ini, TestMode, Business, PaymentMadeOrderStatusID, gateway loading
type: help
---
# paymentgateways.ini walkthrough

This page is for developers who connect or write a payment gateway. It explains the two ways the payment gateway
event finds gateway classes, the settings of `settings/paymentgateways.ini`, and the provider files of the two payment
extensions in the repository.

## Contents

1. [GatewaysSettings](#gatewayssettings)
2. [How gateways are loaded](#how-gateways-are-loaded)
3. [The provider files](#the-provider-files)
4. [Checking that a gateway is available](#checking-that-a-gateway-is-available)

## GatewaysSettings

| Setting | Shipped | Meaning |
|---|---|---|
| `AvailableGateways[]` | empty | Names of gateways to load from `GatewaysDirectories[]` |
| `GatewaysDirectories[]` | `kernel/shop/paymentgateways` | Directories to search; this kernel directory is not shipped, so nothing is loaded from it |

The old misspelling `GatewaysDerictories` is still read when present (`eZPaymentGatewayType::loadAndRegisterBuiltInGateways()`).

## How gateways are loaded

When the payment gateway event type is constructed it loads gateways in two ways
(`kernel/classes/workflowtypes/event/ezpaymentgateway/ezpaymentgatewaytype.php`):

| Way | File it includes | Used by |
|---|---|---|
| Listed gateways | `<GatewaysDirectories>/<gateway>/classes/<gateway>gateway.php` for every name in `AvailableGateways[]` | Gateways kept outside the extension convention |
| Active extensions | `extension/<extension>/classes/<extension>gateway.php` for every active extension | `ezpaypal` (`classes/ezpaypalgateway.php`), `ezauthorize` (`classes/ezauthorizegateway.php`) |

Including the file is not enough: at the foot of its file every gateway calls

```php
eZPaymentGatewayType::registerGateway( <type>, '<class name>', '<description>' );
```

and only registered gateways appear in the payment gateway event. Registering the same type twice logs an error and
keeps the first. The simplest way for a gateway of your own is the extension convention: an extension `mypay` with
`classes/mypaygateway.php` ([Payment gateway development](../extending/payment-gateway-development.md)).

## The provider files

### ezpaypal: paypal.ini

| Block and setting | Shipped | Note |
|---|---|---|
| `[ServerSettings] ServerName`, `ServerPort`, `RequestURI` | `https://www.paypal.com`, `443`, `/cgi-bin/webscr` | Point to PayPal's sandbox while testing |
| `[PaypalSettings] Business` | empty | Your PayPal business account; required |
| `[PaypalSettings] MaxDescriptionLength` | `127` | Longest order description sent |
| `[PaypalSettings] NoNote`, `NoteLabel`, `PageStyle`, `BackgroundColor`, `LogoURI` | `0`, empty, empty, `0`, empty | Look of the PayPal page |
| `[OrderSettings] PaymentMadeOrderStatusID` | `1000` | Status set when the payment is confirmed; see the note in [PayPal payments](../../businesses/stores/paypal-payments.md) |

The extension also registers the module view `paypal/notify_url` for PayPal's callback, exempts it from policy checks
(`[RoleSettings] PolicyOmitList[]=paypal/notify_url`) and allows the redirect host
(`[SiteSettings] AllowedRedirectHosts[]=www.paypal.com`). The full table is in
[ezpaypal](../../../features/6.0/extensions/ezpaypal.md).

### ezauthorize: ezauthorize.ini

`ezauthorize` is a transparent gateway: `eZAuthorizeGateway` extends `eZCurlGateway`, which extends
`eZPaymentGateway`, so card details are entered on your site and sent to Authorize.Net from your server. Its
`settings/ezauthorize.ini.append.php` holds, among others:

| Setting | Shipped | Note |
|---|---|---|
| `MerchantLogin`, `TransactionKey` | `false` | Your Authorize.Net credentials; keep them in `settings/override/` |
| `TestMode` | `true` | Leave on until the go-live test |
| `SSLVerify` | `true` | Keep on |
| `CustomerCVV2Check` | `false` | Ask for the card security code |
| `MD5HashVerification`, `MD5HashSecretWord` | `true`, `publish` | Change the secret word from the shipped value |
| `OrderStatusRefundCode`, `ShowAuthorizeDotNetRefundButton` | `1000`, `true` | The extension adds a refund view (`refund` module) |
| `ShopAdminEmailAddress` | `info@example.com` | Replace with your own address |

Because card data passes through your server with this gateway, read
[Card payments and PCI basics](../../businesses/stores/card-payments-and-pci-basics.md) before choosing it.

## Checking that a gateway is available

1. Activate the extension and clear the INI cache.
2. Open **Setup > Workflows**, create or edit the checkout workflow and add the event **Payment Gateway**.
3. The gateway's description ("Paypal", "Authorize.Net", or yours) is listed for selection. If it is missing, the file
   was not found or `registerGateway()` was not called.
4. The payment gateway event writes a log to `var/log/eZPaymentGatewayType.log`; redirect gateways also write
   `var/log/eZRedirectGateway.log` ([Troubleshooting checkout](../operations/troubleshooting-checkout.md)).

## Related pages

- [Shop settings walkthroughs](README.md)
- [Payment gateway event](../checkout/payment-gateway-event.md)
- [Payment gateways (website creators)](../../creators/website-creators/payment-gateways.md)
- [Payment gateways (running your store)](../../customers/commerce/payment-gateways.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
