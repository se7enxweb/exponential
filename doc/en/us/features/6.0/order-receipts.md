# Permanent order receipts

This page is for shop owners who want customers to reach their order receipt at any time. Every order can have a
receipt with an address that never expires. A customer can bookmark it, print it, download it, or open it from the
link in the order e-mail on any device, with or without an account.

Before, `shop/orderview/<id>` was the only link. For an order placed without an account it stopped working once the
browser session expired, and its address said nothing about who may see it.

## Turn it on

1. Put this in `settings/override/shop.ini.append.php`:

   ```ini
   <?php /* #?ini charset="utf-8"?

   [OrderViewSettings]
   OrderLinkView=orderreceipt

   */ ?>
   ```

2. Clear the INI and template caches:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini --allow-root-user
   php bin/php/ezcache.php --clear-tag=template --allow-root-user
   ```

3. Place a test order. The page after checkout, the order lists and customer order view in the standard design, and
   the order confirmation e-mail now link to `shop/orderreceipt/<token>`.

These places take their link from the order (`eZOrder` `link_url`), which is the receipt or `shop/orderview` as the
setting says. The administration keeps linking orders to `shop/orderview`, where orders are managed.

## The address

```
https://example.com/shop/orderreceipt/<token>
```

The token is signed: it carries the order id, its creation time and its customer, signed with bcrypt at a low cost
over a SHA-256 of a secret and the payload. The salt is derived from the same secret, so each order has exactly one
receipt address, forever, and nothing is stored in the database. The misspelled `shop/orderreciept/<token>` redirects
permanently to the canonical spelling. `eZOrder` gains `receipt_url`.

## Who can open a receipt

| Visitor | Opens the receipt? |
|---|---|
| Anyone holding the link | Yes, from any browser or device, with or without an account (since 26 September 2026). The signed token is the key, so the link is as private as the receipt. |
| The signed-in customer who placed the order | Yes, without the link |
| A user with `shop / administrate` | Yes, any receipt |
| Anyone with a wrong token | No: the same answer as for a receipt that is not theirs, the login form |

The page offers **Print** (the receipt alone, whatever the site design around it) and **Download** (a self-contained
`receipt-<number>.html`). The footer says the link should be kept private.

The first version (24 September) required signing in, so a customer who clicked the link in the e-mail landed on a
sign-in page. On 26 September the token itself became the key, and the setting `AnonymousOrderLinkView` was removed,
because every order now links to its receipt when you ask for it.

## Settings

| File | Block | Key | Default | Scope | Meaning |
|---|---|---|---|---|---|
| `settings/shop.ini` | `OrderViewSettings` | `OrderLinkView` | `orderview` | global | What the system links a placed order to: after checkout, in the customer's order list and history, and in the order confirmation e-mail. `orderview` keeps the classic `shop/orderview/<id>`; `orderreceipt` links `shop/orderreceipt/<token>`, and the e-mail then carries the receipt's full address. |
| `settings/shop.ini` | `OrderReceiptSettings` | `Secret` | empty | global | Secret that signs receipt addresses. Empty means one is generated on first use and kept in `<VarDir>/secrets/orderreceipt.key` (mode 0600). Set it (32 characters or more) in an override file, never in the shipped file, to share receipts between installations that share a database. **Changing it changes every receipt address.** |
| `settings/shop.ini` | `OrderReceiptSettings` | `BcryptCost` | `5` | global | bcrypt cost of the signature, 4 to 6. Receipts made at an earlier cost keep working. |

## A checkout fix made on the way

**Cancel** on the confirm order step used to come back with "You have no products in your basket", because checkout
attaches the basket to its temporary order and the basket lookup could then not find it. `eZBasket::currentBasket()`
now:

- takes the session's basket while its order is still temporary;
- gives a basket back (items and all) once its temporary order has been cancelled away;
- never returns a basket whose order is complete;
- matches no stored basket for a request without a session id.

The confirm order view no longer runs the confirm operation again on an order that was already placed (Back button,
bookmark, second tab). It goes to the order view, or to the basket when no order waits.

## Related pages

- [Store dashboard and order statuses](store-dashboard.md), [order list sorting](order-list-sorting.md)
- [Shop basket view name](../../bc/6.0/shop-basket-view-name.md)
- [Behaviour changes, 16 to 30 September 2026](../../bc/6.0/behaviour-changes-2026-09b.md)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
- [Chronicle, 16 to 30 September 2026](../../history/2026/2026-09b.md)
