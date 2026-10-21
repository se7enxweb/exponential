---
title: Extending the shop
audience: ecommerce, app-creator, developer
category: e-commerce
order: 60
summary: The extension points of the Exponential shop in one place - shipping, VAT, basket info, shop account, confirm order and exchange rate handlers, payment gateways, price datatypes and workflow events - with how each is selected, where its file goes, and how to package and test it.
keywords: extending the shop, handler, shipping handler, VAT handler, basket info handler, shop account handler, confirm order handler, exchange rate handler, payment gateway, price datatype, workflow event, extension, RAD wizard
type: help
---
# Extending the shop

This part is for developers who need the shop to do something it does not do out of the box. The Exponential shop is
built around replaceable **handlers**: a setting names a handler, the kernel finds its file by a naming convention and
calls it in place of its own. Everything on these pages goes into an extension of your own, never into `kernel/`.

## Contents

1. [All extension points](#all-extension-points)
2. [The handler pattern](#the-handler-pattern)
3. [Packaging](#packaging)
4. [Testing an extension of the shop](#testing-an-extension-of-the-shop)

## All extension points

| Extension point | Selected by | File in your extension | Page |
|---|---|---|---|
| Shipping handler | `shop.ini [ShippingSettings] Handler`, `ExtensionDirectories[]` | `shippinghandlers/<name>shippinghandler.php` | [Custom shipping handler](custom-shipping-handler.md) |
| VAT handler | `shop.ini [VATSettings] Handler`, `ExtensionDirectories[]` | `vathandlers/<name>vathandler.php` | [Custom VAT handler](custom-vat-handler.md) |
| Payment gateway | an active extension, or `paymentgateways.ini` | `classes/<extension>gateway.php` | [Payment gateway development](payment-gateway-development.md) |
| Shop account handler | `shopaccount.ini [AccountSettings] Handler`, `[HandlerSettings] ExtensionRepositories[]` | `shopaccounthandlers/<name>shopaccounthandler.php` | [Shop account handlers](shop-account-handlers.md) |
| Basket info handler | `shop.ini [BasketInfoSettings] Handler`, `ExtensionDirectories[]` | `basketinfohandlers/<name>basketinfohandler.php` | [Basket info and confirm order handlers](basket-info-and-confirm-order-handlers.md) |
| Confirm order handler | `shopaccount.ini [ConfirmOrderSettings] Handler` | `confirmorderhandlers/<name>confirmorderhandler.php` | same |
| Exchange rates handler | `shop.ini [ExchangeRatesSettings] ExchangeRatesUpdateHandler`, `ExtensionDirectories[]` | `exchangeratehandlers/<name>/<name>handler.php` | [Exchange rate handlers](exchange-rate-handlers.md) |
| Price datatype | `site.ini [ShopSettings] ProductDatatypeStringList[]` | `datatypes/<name>/<name>type.php` | [Custom price datatypes](custom-price-datatypes.md) |
| Workflow event | `workflow.ini [EventSettings]` | `workflowtypes/event/<name>/<name>type.php` | [Workflows on shop triggers](../checkout/workflows-on-shop-triggers.md#events-of-your-own) |

## The handler pattern

1. A setting names the handler, for example `Handler=mystore`.
2. The kernel searches the repository directories and the listed extensions for `<name><suffix>.php`.
3. It includes the file and instantiates `<name><Suffix>`, for example `mystoreShippingHandler` (PHP class names are
   not case sensitive, so `MyStoreShippingHandler` works too).
4. If the file is missing, an error is written to the debug log and the kernel falls back or does nothing; it does not
   stop the request. Check the log after every change.

The RAD tool **Setup > RAD** has a handler wizard (`setup/handlerextension/<kind>`) with recipes for payment gateways,
VAT, shipping, basket info and exchange rate handlers; it writes the class, the setting and an extension around them
([Extensions](../../../guides/extensions.md#63-the-handler-wizard-step-by-step)).

## Packaging

- One extension per store or per integration, for example `mystore_shop`, with its own `settings/*.ini.append.php`
  ([Packaging as an extension](../../creators/app-creators/packaging-as-an-extension.md)).
- Regenerate autoloads after adding classes (`php bin/php/ezpgenerateautoloads.php -e`) and check that every class
  loads (`php bin/php/checkclasses.php --quiet-ok`).
- Keep provider credentials out of the extension: read them from `settings/override/`
  ([Environment settings and secrets](../../creators/app-creators/environment-settings-and-secrets.md)).
- If the extension is useful to others, consider [contributing it back](../../contribute-extensions-back.md).

## Testing an extension of the shop

- Unit tests for calculations (shipping, VAT) without a database: [DB-less PHPUnit tests](../../creators/app-creators/db-less-phpunit-tests.md).
- A full test order for each path the extension changes: [Testing a store](../operations/testing-a-store.md).
- The Store dashboard's "How your shop works" after deployment: it names the handlers and workflows in effect.

## Related pages

- [Developer path](../roles/developer-path.md)
- [Extension anatomy](../../creators/app-creators/extension-anatomy.md)
- [Settings and ini files](../../creators/app-creators/settings-and-ini-files.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
