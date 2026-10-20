---
title: Discounts
audience: customer, admin, businesses
category: customer-success
order: 70
summary: Running discounts in an Exponential store - group discounts for members, resellers and staff, running a sale by hand, testing a discount, and keeping a record - with what the shop cannot do, such as coupon codes.
keywords: discounts, discount groups, discount rules, sale, member prices, reseller prices, staff discount, promotion, coupon, voucher, shop/discountgroup
type: help
---
# Discounts

This page is for owners and marketers who run promotions in the store. It gives the routines for the discounts the
shop has, and an honest list of what needs an extension. How discount groups and rules work, and which discount wins,
is explained on [Discounts and wishlists for businesses](../../businesses/stores/discounts-and-wishlists.md).

## What the shop offers

A **discount group** has members (single customers or whole user groups) and one or more **discount rules**, each a
percentage, optionally limited by content class, section or specific products. Discount groups are managed under
**Store > Discounts** (`shop/discountgroup`, `shop/discountruleedit`), which needs `shop/setup`
(`kernel/shop/module.php`). For each product the customer gets the **highest** matching percentage; discounts do not
add up (`kernel/classes/ezdiscount.php`).

Because discounts follow the customer's account and user groups, they reach signed-in customers only.

## Typical uses, and who runs them

| Promotion | How | Usually run by |
|---|---|---|
| Member prices | A discount group whose member is the members' user group | Owner |
| Reseller prices | A reseller user group with its discount | Owner, with sales |
| Staff discount | The staff user group in a discount group | Owner |
| A sale on some products | A rule limited to those products, or to a "Sale" section, for a group everyone signed in belongs to | Marketing, with the owner |
| A personal arrangement | One customer as a single member | Owner |

Prefer user groups over single customers: people who join the group later get the discount without anyone
remembering to add them.

## Running a sale by hand

The shop has no discounts that start and end on their own. A sale is run by hand:

1. **Prepare** the rule and its limitation in advance, at 0 percent or with no members, so it is ready.
2. **Start**: set the percentage, or add the members, at the agreed time.
3. **Check** (below) right after the start.
4. **End**: remove the members or the rule at the agreed end time.
5. **Record** what ran, from when to when, in your promotion log.

Moving many products into or out of a "Sale" section can run in the background as a content job
([Content jobs](../../../features/6.0/content-jobs.md)).

> **Tip**
> Put the start and end in the team calendar with a named person for each. A sale that nobody ends is a price cut.

## Checking a discount

- [ ] Sign in as a test customer who is in the group: the basket shows the reduced price.
- [ ] Sign in as a test customer who is not: the basket shows the normal price.
- [ ] A product outside the rule's limitation is not reduced.
- [ ] If the customer is in two groups, the higher percentage applies, not the sum.
- [ ] The order view shows the discount on the order line (each order line stores its discount, table
  `ezproductcollection_item` in `kernel/sql/mysql/kernel_schema.sql`).

## Keeping a record

Discount changes are recorded in the audit trail's `commerce` channel ([The audit trail](../../../features/6.0/audit-trail.md)).
Keep your own short log as well, because the reason for a promotion is not in the audit trail:

```text
PROMOTION LOG
Name | Discount group / rule | Percentage | Who gets it | Start | End | Started by | Ended by | Notes
-----|-----------------------|------------|-------------|-------|-----|------------|----------|------
```

## What the shop cannot do without an extension

From the kernel code, as summarized in [Discounts and wishlists for businesses](../../businesses/stores/discounts-and-wishlists.md):

- coupon or voucher codes typed at checkout;
- gift cards;
- quantity rules such as "buy two, get one free";
- discounts that start and end on their own;
- discounts for guests who are not signed in (other than giving one to everyone).

If you need one of these, ask your partner for a quote. A common approach is a workflow event that adds a negative
order line before the confirmation page ([Workflows](../../../guides/workflows.md)).

## Related pages

- [Discounts and wishlists for businesses](../../businesses/stores/discounts-and-wishlists.md)
- [Promoting your store](../../businesses/stores/promoting-your-store.md)
- [Customer accounts](customer-accounts.md)
- [Wishlists and baskets](wishlists-and-baskets.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
