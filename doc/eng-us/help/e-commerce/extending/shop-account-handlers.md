---
title: Shop account handlers
audience: ecommerce, app-creator, developer
category: e-commerce
order: 64
summary: Writing a shop account handler for an Exponential store to collect different customer data at checkout - the five methods, how data is stored with the order, the form view and templates, registration in shopaccount.ini, and examples such as company and VAT number fields.
keywords: shop account handler, eZShopAccountHandler, verifyAccountInformation, fetchAccountInformation, email, accountName, accountInformation, shopaccount.ini, shopaccounthandlers, customer data, checkout form, company name, VAT number, delivery address
type: help
---
# Shop account handlers

This page is for developers who need the checkout to ask for different customer data: a company name, a VAT number,
a separate delivery address, a phone number. The shop account handler decides which form the customer fills in and
how the order's customer data is read back. The shipped handlers are described in
[shopaccount.ini walkthrough](../settings/shopaccount-ini-walkthrough.md).

## Contents

1. [The five methods](#the-five-methods)
2. [Where the data lives](#where-the-data-lives)
3. [Writing one](#writing-one)
4. [Registering it](#registering-it)
5. [Checklist](#checklist)

## The five methods

The shipped handlers in `kernel/classes/shopaccounthandlers/` all implement:

| Method | Called | Returns |
|---|---|---|
| `verifyAccountInformation()` | On **Checkout** in the basket | `true` if checkout can go on without a form |
| `fetchAccountInformation( &$module )` | When verification failed | Redirects to the form (or to sign in) |
| `email( $order )` | Order mail, order list, customer list | The customer's e-mail address |
| `accountName( $order )` | Order list, customer list | The customer's name |
| `accountInformation( $order )` | Order view, receipt, mail templates | An array of the customer's data |

`eZShopAccountHandler::instance()` (`kernel/classes/ezshopaccounthandler.php`) loads the handler named in
`[AccountSettings] Handler`, as class `<name>ShopAccountHandler`, falling back to `eZDefaultShopAccountHandler`.

## Where the data lives

The `ezuser`, `ezsimple` and `bcusauser` handlers keep the form's data in the order itself, as XML in
`ezorder.data_text_1`, and parse it back in `email()`, `accountName()` and `accountInformation()`. Keeping the data in
the order means it stays as entered even if the customer's account changes later. `bcusauser` shows how to add fields
on top of an existing handler with almost no code: it extends `eZUserShopAccountHandler`.

## Writing one

1. Copy the form view you start from: `shop/userregister` creates the temporary order and stores the XML
   (`kernel/private/classes/views/shop/userregister.php`). Put your own view in your extension's module, with the
   extra fields, validation and the same order creation.
2. Write the handler: `fetchAccountInformation()` redirects to your view; `email()`, `accountName()` and
   `accountInformation()` read your XML, including the new fields.
3. Templates: the order view and receipts include an account template per handler from
   `design/standard/templates/shop/accounthandlers/` (`html/` and `ascii/`); add yours in your design.
4. If VAT depends on the country, give the form a country field and keep it where `eZVATManager` finds it
   ([Product categories and VAT rules](../catalog/product-categories-and-vat-rules.md#the-customers-country)).

Protect the form like any form ([Forms, CSRF and HTTP input](../../creators/app-creators/forms-csrf-and-http-input.md))
and keep only the data you need ([GDPR requests](../../customers/commerce/gdpr-requests.md)).

## Registering it

```ini
# extension/mystore_shop/settings/shopaccount.ini.append.php
[HandlerSettings]
ExtensionRepositories[]=mystore_shop

[AccountSettings]
Handler=mystore
```

with the class in `extension/mystore_shop/shopaccounthandlers/mystoreshopaccounthandler.php`. The `ezauthorize`
extension ships an example of this layout (`shopaccounthandlers/ezcustomshopaccounthandler.php`).

## Checklist

- [ ] Guest and signed-in checkout both reach the confirmation page.
- [ ] Every new field appears in the order view, receipt, order mail and customer list.
- [ ] Old orders made with the previous handler still display.
- [ ] Invalid input is refused with a clear, accessible message ([Store accessibility](../operations/store-accessibility.md)).

## Related pages

- [Extending the shop](README.md)
- [Customer accounts and guest checkout](../../businesses/stores/customer-accounts-and-guest-checkout.md)
- [Customer accounts (running your store)](../../customers/commerce/customer-accounts.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
