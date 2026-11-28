# My orders: a customer's own order list

This page is for shop owners who want signed-in customers to see the orders they placed. `shop/myorders` lists the
current user's orders, newest first: order number, date, status and total, each linked to the order.

Before, the kernel listed a customer's orders only in `shop/customerorderview`, an administration view that needs
`shop/administrate`. A customer could open one order from its link, but had no list of them.

## What the customer sees

- Only their own orders. The user comes from the session, never from the address, so there is no way to ask for
  someone else's list.
- Completed orders only. An order still in checkout (temporary) is not listed. Archived orders are listed, because
  archiving tidies the shop's own lists and the order still belongs to the customer.
- Each order links to the same place as everywhere else in the shop: the order view, or the permanent receipt when
  `shop.ini [OrderViewSettings] OrderLinkView=orderreceipt` ([Permanent order receipts](order-receipts.md)).
- 20 orders per page, with the standard page navigator (`/(offset)/20`).
- A visitor who is not signed in gets the login form. After signing in they come back to the list.

## Turn it on

The view has its own policy function, `shop/myorders`, so you decide which users get the list.

1. In the administration, open **Roles and policies**, edit the role your customers have (for example *Member*),
   and add the policy **shop / myorders**. It has no limitations.
2. Link the page from the site design, for example on the account page:

   ```
   <a href={'shop/myorders'|ezurl}>My orders</a>
   ```

3. Optional: set the page size in `settings/override/shop.ini.append.php`:

   ```ini
   [MyOrdersSettings]
   # Orders per page, 1 to 100 (default 20)
   Limit=20
   ```

4. Clear the INI and template caches:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini --allow-root-user
   php bin/php/ezcache.php --clear-tag=template --allow-root-user
   ```

## Restyle it

The page is `design/standard/templates/shop/myorders.tpl`. Copy it to your site design under the same path to change
it. It receives `$order_list`, `$order_count`, `$offset`, `$limit` and `$view_parameters`. Each order has
`link_url`, `status_name`, `order_nr`, `created` and `total_inc_vat`.

In PHP, `eZOrder::fetchListByUserID( $userID, $offset, $limit )` and `eZOrder::countByUserID( $userID )` return the
same orders.
