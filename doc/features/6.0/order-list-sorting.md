# Sort the order list by any column

The shop's order list (Store > Orders) could only be sorted by time or customer, chosen with a pair of
preference links above the table. Every heading now sorts it. Added 2026-10-01.

## Use it

Click a heading: ID, customer, either total, time or status. A second click on the sorted column turns the
direction round. The column and direction are part of the address, like the other admin lists:

    /shop/orderlist/(sort)/<column>/(dir)/<asc|desc>

with the columns `id`, `customer`, `total_ex_vat`, `total_inc_vat`, `created` and `status`, for example
`/shop/orderlist/(sort)/total_inc_vat/(dir)/desc`.

Paging, reloading, saving a status change and a copied link keep them. Without those parameters the stored
preferences still decide, and the `sort_field` template variable keeps its former values for site designs that
show their own sort selector.

## How it works

`eZOrder::activeSorted()` sorts the whole list in the database, in one query on every engine; the totals use the
arithmetic of the order view, and ties are broken by the order id, so no order appears twice across pages. The
columns are checked against `eZOrder::sortColumnsForList()`. The headings use the shared
`parts/sortheader.tpl`, as the role list does; in `admin3` the sort arrow takes the heading's own colour, so it
shows on the dark heading bar, and in `admin4` the sorted column is readable.

The view is `Exponential\View\Kernel\Shop\Orderlist` (`kernel/private/classes/views/shop/orderlist.php`); the
sort and direction arrive as the view's user parameters, which is why they are written `(sort)/...` and not as plain
module parameters. Check without opening the browser: `grep -n "sort" kernel/private/classes/views/shop/orderlist.php`.

Related: [admin list paging](admin-list-paging.md), [the admin4 design](admin4-design.md), [October 2026 chronicle](../../history/2026/2026-10.md).

See also (October 2026): [6.0.15 changelog](../../changelogs/6.0/6.0.15.md), [upgrade checklist of 1-2 October 2026](../../bc/6.0/behaviour-changes-2026-10.md), [October 2026 chronicle](../../history/2026/2026-10.md), [trash: who and where](trash-who-and-where.md).
