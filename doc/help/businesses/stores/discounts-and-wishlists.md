---
title: Discounts and wishlists
audience: businesses, admin, sales-marketing
category: business-solutions
order: 160
summary: The two customer-loyalty tools of the built-in shop - percentage discount groups for customers and customer groups, with rules by class, section or product, and the wishlist for signed-in customers - plus what is not included, such as coupon codes.
keywords: discounts, discount groups, discount rules, customer groups, wholesale prices, member prices, wishlist, wish list, coupon, voucher, promotion, loyalty
type: help
---
# Discounts and wishlists

This page is for business owners and marketers who want to reward customers. It explains the discount groups of the
shop, how the shop decides which discount applies, the wishlist, and what is not included, such as coupon codes. The
settings side is in [Products, prices and VAT](../../creators/website-creators/products-prices-and-vat.md#discounts).

## Contents

1. [Discount groups in short](#discount-groups-in-short)
2. [Who gets a discount](#who-gets-a-discount)
3. [What the discount applies to](#what-the-discount-applies-to)
4. [Which discount wins](#which-discount-wins)
5. [Uses for a small business](#uses-for-a-small-business)
6. [The wishlist](#the-wishlist)
7. [What is not included](#what-is-not-included)
8. [Checklist](#checklist)

## Discount groups in short

A **discount group** (`shop/discountgroup`, policy `shop/setup`) has members and one or more **discount rules**. A
rule is a percentage, optionally limited to certain products (`shop/discountruleedit`). The basket applies the
percentage to the line's price with and without VAT (`kernel/classes/ezbasket.php`).

## Who gets a discount

Members of a discount group are added from the user list (`shop/discountgroupview`). The shop then looks up discount
rules for the customer **and for every user group the customer belongs to** (`eZDiscount::discountPercent()` in
`kernel/classes/ezdiscount.php`). In practice:

- add a single customer, for a personal arrangement;
- add a user group, such as "Members" or "Resellers", so everyone in it gets the discount, including people who join
  later.

Discounts therefore need customers who are signed in. A guest is the anonymous user and gets no group discount unless
you give one to everyone.

## What the discount applies to

A rule can be limited by any combination of (same file):

| Limitation | Example |
|---|---|
| Content class | 10 percent on everything of the class "Book" |
| Section | 15 percent on products in the "Outlet" section |
| Specific products | 20 percent on three named products |

A rule with no limitation applies to every product. When several kinds of limitation are set, a product must match all
of them.

## Which discount wins

Discounts do not add up. For each product the shop takes the **highest** percentage among the rules that match the
customer and the product (`kernel/classes/ezdiscount.php` sorts the rules by percentage and keeps the best match). A
customer in "Members" (5 percent on everything) and "Resellers" (20 percent on books) gets 20 percent on books and 5
percent on everything else.

## Uses for a small business

| Goal | How |
|---|---|
| Member prices for a club or association | A group discount for the members' user group (Members) |
| Trade prices for resellers | A reseller user group with its discount, plus account-only checkout ([Customer accounts and guest checkout](customer-accounts-and-guest-checkout.md)) |
| Staff discount | A group discount for the staff user group |
| A clearance sale | A rule limited to an "Outlet" section, given to everyone |
| Thanks to a key customer | That customer as a single member |

> **Tip**
> Show the reduced price on the product page for signed-in customers, not only in the basket. That is template work for
> your designer, and it makes the discount visible where the buying decision is made.

## The wishlist

The shop has a wishlist (`shop/wishlist`, policy `shop/buy`). Customers add products with an **Add to wishlist**
button on the product page, change quantities and remove items, and later put them in the basket.

- The wishlist belongs to the user account (`eZWishList::currentWishList()` in `kernel/classes/ezwishlist.php`), and
  adding to it requires a registered, signed-in user (`kernel/private/classes/views/content/action.php` refuses
  anonymous visitors).
- Templates ship in the standard, admin and admin4 designs (`shop/wishlist.tpl`); your site design needs the button
  and a link to the wishlist.

The wishlist is a good reason to offer customer accounts if your products are considered purchases, such as furniture
or gifts.

## What is not included

We checked the kernel for these and did not find them:

- **coupon or voucher codes** typed at checkout;
- **gift cards**;
- **"buy two, get one free"** or other quantity rules;
- **time-limited discounts** that start and end on their own;
- **shared wishlists** or wishlists for guests.

Ways to get them: a time-limited sale can be run by hand: create the rule when the sale starts and remove it when it
ends, or assign the sale products to an "Outlet" section and back again (a section assignment for many items can run
in the background as a content job, [Content jobs](../../../features/6.0/content-jobs.md)). Coupon codes,
gift cards and quantity rules need an extension, for example a workflow event on `before_shop_confirmorder` that adds a
negative order line; that is developer or partner work ([Writing a workflow event type](../../creators/app-creators/writing-a-workflow-event-type.md),
Choosing a partner).

## Checklist

- [ ] Discount groups named after who they are for.
- [ ] Members are user groups wherever possible, not single users.
- [ ] Each rule's limitation tested with a test customer in the basket.
- [ ] The highest-wins behavior is understood by whoever plans promotions.
- [ ] The wishlist button appears only for signed-in customers.
- [ ] Discount changes are recorded: the audit trail logs discounts in the `commerce` category ([Audit trail](../../../features/6.0/audit-trail.md)).

## Related pages

- [Products, prices and VAT](../../creators/website-creators/products-prices-and-vat.md)
- [Promoting your store](promoting-your-store.md)
- [Customer accounts and guest checkout](customer-accounts-and-guest-checkout.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
