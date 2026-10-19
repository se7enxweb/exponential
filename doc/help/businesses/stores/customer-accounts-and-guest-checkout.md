---
title: Customer accounts and guest checkout
audience: businesses, admin, website-creator
category: business-solutions
order: 90
summary: Whether customers buy as guests or with an account in an Exponential store - the shipped shop account handlers, what each asks of the customer, what is stored, and how to choose.
keywords: guest checkout, customer account, registration, sign in, shop account handler, shopaccount.ini, ezuser, ezdefault, customer data, returning customers, personal data
type: help
---
# Customer accounts and guest checkout

This page is for business owners who decide whether customers must create an account to buy. It explains the options
the shop ships with, what each one asks of the customer, what is stored with the order, and how the choice affects
receipts, wishlists and personal data.

## Contents

1. [Why it matters](#why-it-matters)
2. [The shop account handlers](#the-shop-account-handlers)
3. [Guest checkout with the shipped handler](#guest-checkout-with-the-shipped-handler)
4. [Account-only checkout](#account-only-checkout)
5. [What accounts give your customers](#what-accounts-give-your-customers)
6. [Customer data and privacy](#customer-data-and-privacy)
7. [Choosing](#choosing)

## Why it matters

A required account adds a step before the first purchase; no accounts at all means returning customers type their
address every time. Exponential lets you choose, and the choice is one setting.

## The shop account handlers

How the shop collects customer data is decided by the **shop account handler**, set in `settings/shopaccount.ini`,
`[AccountSettings] Handler` ([Basket, checkout and orders](../../creators/website-creators/basket-checkout-and-orders.md#checkout)).
The kernel ships these, in `kernel/classes/shopaccounthandlers/`:

| Handler | What the customer does | Fits |
|---|---|---|
| `ezuser` (the shipped setting) | Fills a form with name, e-mail and address at checkout (`shop/userregister`), with or without an account | Most stores: guests and account holders both welcome |
| `ezdefault` | Must sign in; a visitor is sent to the login page and back to the basket afterwards | Stores for registered customers only, such as wholesale |
| `ezsimple` | Is sent to `shop/register` for the customer data | Sites with their own registration form |
| `bcusauser` | The `ezuser` form with the US address model (city and phone) | Stores with US addresses |

Developers can add a handler of their own in an extension (`shopaccount.ini [HandlerSettings] ExtensionRepositories[]`).
The `ezauthorize` extension, for example, ships its own account handler templates.

## Guest checkout with the shipped handler

With `ezuser`, every customer sees one form at checkout. The fields its view reads are first name, last name, e-mail,
two address lines, postal code, city, state, country, a comment, and a phone number when the design's form offers one
(`kernel/private/classes/views/shop/userregister.php`).

- A **guest** fills the form and buys. No account is created.
- A **signed-in customer** finds name and e-mail filled in from their account, and the address of their latest order
  copied in (same file).

The data is stored with the order, so the shop staff see it in the order view whatever kind of customer it was.

> **Tip**
> Guests should get the permanent receipt link in their confirmation mail. Without it, a guest's order view stops
> working once their browser session ends ([Order receipts and confirmation mail](order-receipts-and-confirmation-mail.md)).

## Account-only checkout

With `ezdefault`, the shop checks that the customer is a registered user and otherwise sends them to `user/login`,
then back to the basket to continue the checkout automatically
(`kernel/classes/shopaccounthandlers/ezdefaultshopaccounthandler.php`). The customer's e-mail and name then come from
their user account.

Customer accounts are ordinary Exponential users. Registration, activation, approval and member areas are described in
[Member registration and areas](../../creators/website-creators/member-registration-and-areas.md) and, for business
use, in Registration and account approval. Account-only checkout
pairs well with a **dealer or wholesale** catalog that only signed-in customers may see
(Dealer and supplier portals).

## What accounts give your customers

| Feature | Guest | Signed-in customer |
|---|---|---|
| Buy | yes (with `ezuser`) | yes |
| Address filled in next time | no | yes |
| Wishlist | not meaningful: the wishlist is kept per user account | yes ([Discounts and wishlists](discounts-and-wishlists.md)) |
| Discount groups | no | yes, discounts follow the user's group |
| Receipt without the link | no | yes, the customer who placed the order can open it signed in |
| E-mail preferences page | via the link in a mail | yes, in the profile ([Mail preferences](../../../features/6.0/mail-preferences.md)) |
| Two-factor sign-in | not applicable | available with the 2FA extension ([sevenx_authentication_2fa](../../../features/6.0/extensions/sevenx_authentication_2fa.md)) |

## Customer data and privacy

Whatever the handler, the order keeps the customer's name, e-mail and address. That is personal data. The mail
preferences feature treats order mail as essential e-mail that is always sent and never carries advertising
([Mail preferences](../../../features/6.0/mail-preferences.md)); marketing to customers needs their opt-in
([Opt-in and consent](../newsletters/opt-in-and-consent.md)).

> **Note**
> These are basics, not legal advice. For your duties under data protection law, read the regulation that applies to
> you and ask your lawyer or data protection officer. Start with
> GDPR basics and Personal data in Exponential.

## Choosing

- [ ] Do most customers buy once (guest checkout) or come back often (accounts)?
- [ ] Do some products or prices belong only to registered customers (account-only, or a protected section)?
- [ ] Do you want discount groups or wishlists (accounts)?
- [ ] Which address fields does your carrier need, and does the form ask for them?
- [ ] Is the permanent receipt turned on for guests?
- [ ] Who may see the customer list (`shop/administrate`)?

## Related pages

- [Basket and checkout flow](basket-and-checkout-flow.md)
- [Member registration and areas](../../creators/website-creators/member-registration-and-areas.md)
- User accounts and passwords
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
