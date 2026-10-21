---
title: shopaccount.ini walkthrough
audience: ecommerce, website-creator, admin
category: e-commerce
order: 33
summary: The settings that decide which customer data form the checkout shows and which handler sends the order mail - HandlerSettings, AccountSettings with the ezuser, ezsimple, ezdefault and bcusauser handlers, Alias, and ConfirmOrderSettings.
keywords: shopaccount.ini, shop account handler, AccountSettings, Handler, ezuser, ezsimple, ezdefault, bcusauser, userregister, register, guest checkout, ConfirmOrderSettings, Alias, HandlerSettings, ExtensionRepositories
type: help
---
# shopaccount.ini walkthrough

This page is for developers and administrators who decide how an Exponential store collects customer data at checkout.
`settings/shopaccount.ini` chooses two handlers: the shop account handler (which form the customer fills in and how
name, e-mail and address are read back from an order) and the confirm order handler (which sends the order mail).

## Contents

1. [HandlerSettings](#handlersettings)
2. [AccountSettings](#accountsettings)
3. [The shipped account handlers](#the-shipped-account-handlers)
4. [ConfirmOrderSettings](#confirmordersettings)
5. [Choosing a handler](#choosing-a-handler)

## HandlerSettings

| Setting | Shipped | Meaning |
|---|---|---|
| `Repositories[]` | `kernel/classes` | Where handlers are searched; account handlers in `shopaccounthandlers/`, confirm order handlers in `confirmorderhandlers/` |
| `ExtensionRepositories[]` | empty | Extensions with their own `shopaccounthandlers/` or `confirmorderhandlers/` directory |

## AccountSettings

| Setting | Shipped | Meaning |
|---|---|---|
| `Handler` | `ezuser` | The shop account handler. The class is `<name>ShopAccountHandler` in `<name>shopaccounthandler.php` |
| `Alias[]` | empty | Replaces one handler name by another, for example `Alias[ezdefault]=eztest` |

## The shipped account handlers

All four are in `kernel/classes/shopaccounthandlers/`.

| Handler | The customer | Form | Where the data is kept |
|---|---|---|---|
| `ezuser` (shipped choice) | Enters first and last name, e-mail, two street lines, zip, place, state, country and a comment at every checkout, with or without an account | `shop/userregister` | In the order (`data_text_1`, as XML) |
| `ezsimple` | Enters first and last name, e-mail and one address field | `shop/register` | In the order |
| `ezdefault` | Must be signed in; is sent to `user/login` and back to the basket; nothing extra is asked | none | The user account's name and e-mail |
| `bcusauser` | As `ezuser`, for the US address model: city instead of place, plus a phone number | `shop/userregister` | In the order |

The templates that show the data are in `design/standard/templates/shop/accounthandlers/` (`html/` for screens,
`ascii/` for plain text). Guest checkout is possible with `ezuser` and `ezsimple`, as long as anonymous users have
the `shop/buy` policy. The business view of this choice is
[Customer accounts and guest checkout](../../businesses/stores/customer-accounts-and-guest-checkout.md).

> **Note**
> The `ezauthorize` extension ships its own account handler (`ezcustom`) and an example `shopaccount.ini.append.php`
> with every line commented out. Read it before switching it on: its settings in `ezauthorize.ini` map the account
> fields it expects.

## ConfirmOrderSettings

| Setting | Shipped | Meaning |
|---|---|---|
| `Handler` | `ezdefault` | `eZDefaultConfirmOrderHandler`: sends the order mail to the customer and to `[MailSettings] AdminEmail` when `site.ini [ShopSettings] SendOrderEmail=enabled` |
| `Alias[]` | empty | Replaces one handler by another |

## Choosing a handler

| You need | Handler |
|---|---|
| Guests and customers enter an address each time | `ezuser` |
| A shorter form | `ezsimple` |
| Only registered customers buy; their account holds everything | `ezdefault` |
| US addresses with a phone number | `bcusauser` |
| Extra fields (company, VAT number, delivery address) | A handler of your own ([Shop account handlers](../extending/shop-account-handlers.md)) |

For VAT rules by country, the handler must give the customer a country; check that your form asks for one
([Product categories and VAT rules](../catalog/product-categories-and-vat-rules.md#the-customers-country)).

## Related pages

- [Shop settings walkthroughs](README.md)
- [Customer accounts (running your store)](../../customers/commerce/customer-accounts.md)
- [Basket info and confirm order handlers](../extending/basket-info-and-confirm-order-handlers.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
