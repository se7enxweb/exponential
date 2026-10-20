# Sort the order list by any column

This page is for shop administrators who work with **Store > Orders**, and for designers who build their own
order list. Every column heading of the order list now sorts it. Before, the list could only be sorted by time or
customer, through a pair of preference links above the table. Added 2026-10-01.

## Use it

1. Open **Store > Orders**.
2. Click a heading: ID, customer, either total, time or status.
3. Click the same heading again to reverse the direction.

The column and the direction are part of the address, as in the other admin lists:

    /shop/orderlist/(sort)/<column>/(dir)/<asc|desc>

| Column | Sorts by |
|---|---|
| `id` | order id |
| `customer` | customer |
| `total_ex_vat` | total without VAT |
| `total_inc_vat` | total with VAT |
| `created` | time |
| `status` | status |

Example: `/shop/orderlist/(sort)/total_inc_vat/(dir)/desc` lists the largest orders first.

Paging, reloading, saving a status change and a copied link all keep the sort. Without these parameters the
stored preferences decide, as before. The `sort_field` template variable keeps its former values, so site designs
that show their own sort selector keep working.

## How it works

- `eZOrder::activeSorted()` sorts the whole list in the database, in one query on every database engine.
- The totals use the same arithmetic as the order view.
- Ties are broken by the order id, so no order appears twice across pages.
- The columns are checked against `eZOrder::sortColumnsForList()`.
- The headings use the shared `parts/sortheader.tpl`, like the role list. In `admin3` the sort arrow takes the
  heading's colour, so it shows on the dark heading bar; in `admin4` the sorted column is readable.
- The view is `Exponential\View\Kernel\Shop\Orderlist` (`kernel/private/classes/views/shop/orderlist.php`). Sort
  and direction arrive as the view's user parameters, which is why they are written `(sort)/...` and not as plain
  module parameters.

Check the code without a browser:

```bash
grep -n "sort" kernel/private/classes/views/shop/orderlist.php
```

## Related pages

- [Admin list paging](admin-list-paging.md)
- [The admin4 design](admin4-design.md)
- [Store dashboard](store-dashboard.md)
- [Upgrade checklist of 1-2 October 2026](../../bc/6.0/behaviour-changes-2026-10.md)
- [6.0.15 changelog](../../changelogs/6.0/6.0.15.md)
- [October 2026 chronicle](../../history/2026/2026-10.md)
