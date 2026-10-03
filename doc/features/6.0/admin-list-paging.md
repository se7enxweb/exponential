# Paging, sorting and page sizes in the administration interface

Every long list in the administration interface is paged, and its page size is a
setting. An installation with many sites, thousands of locations or thousands of
roles used to fail to draw some of these pages at all, because the whole list
was read and a query was run for every row before the first byte was sent.

## What is paged now

| List | Page |
|---|---|
| Locations tab of an item | [Locations tab paging and sorting](../../bc/6.0/locations-tab-paging-and-sorting.md) |
| Roles, and the policies of a role | [Role and policy paging](../../bc/6.0/role-policy-paging.md); the role list also shows the role id and sorts |
| Class list, class group list, workflow group list, PDF export list | none had paging before |
| Discount groups, order status, VAT rules, product categories | read their whole list before |
| VAT types, translations, extensions, cronjob scripts, REST applications | read their whole list before |
| Package list | had a page size but no pager to reach page two, and drew every package on every page |
| Section list, collected information overview, state groups, search statistics | moved onto the settings block |
| RSS list | [RSS list](rss-podcast-and-feed-list.md) |
| Layout lists of the layouts editor | paged by `explayouts_ui` entries in the same block |

The extension list also sorts by the extension's own name and its license, and
keeps the sort in the address so paging does not lose it. Item view tabs no
longer load everything they count, and the policy window that actually renders
is the one bounded.

## Change a page size

All sizes are in `admininterface.ini`, block `[PaginationSettings]`, keyed by
`module/view`:

```ini
# settings/override/admininterface.ini.append.php
[PaginationSettings]
DefaultItemsPerPage=25
ItemsPerPage[section/list]=50
ItemsPerPage[class/classlist]=100
```

The full table of keys, the four settings that predate the block, and the one
list that is deliberately not paged are in
[Where the page sizes live](../../bc/6.0/pagination-settings.md).

## A page size of your own in the sub items list

The Table options panel of the sub items list has a **Custom** field (any whole number from 1 to 10000, stored as a
user preference). It is described with the other table options in
[Sub items list: custom page size](subitems-table-options.md#custom-page-size).

## For developers

The modules read their size through one helper rather than a number written in
the module; the helper also gives the lists their paging parameters and the
user's chosen page size. See *Where the reading happens* in
[Where the page sizes live](../../bc/6.0/pagination-settings.md).

## Settings at a glance

| File | Block | Key | Default | Scope |
|---|---|---|---|---|
| `settings/admininterface.ini` | `PaginationSettings` | `DefaultItemsPerPage` | `25` | global |
| `settings/admininterface.ini` | `PaginationSettings` | `ItemsPerPage[<module>/<view>]` | `25` (10 for `workflow/processlist` and `state/groups`, 15 for `shop/orderlist` and `shop/customerlist`, 50 for `shop/archivelist`) | global |
| `settings/content.ini` | `LocationsSettings` | `LocationsPerPage` | `25` | global |
| `settings/site.ini` | `RoleSettings` | `PoliciesPerPage`, `PolicyPreviewPerRole`, `RolesPerPageList[]` | `25`, `10`, `10`/`25`/`50` | global |

Check on your installation: `grep -n -A40 "^\[PaginationSettings\]" settings/admininterface.ini`. Clear the caches after a change: `php bin/php/ezcache.php --clear-all --allow-root-user`.

## See also

- [Where the page sizes live](../../bc/6.0/pagination-settings.md), [Role and policy paging](../../bc/6.0/role-policy-paging.md), [Locations tab paging and sorting](../../bc/6.0/locations-tab-paging-and-sorting.md)
- [Sub items list: columns, presets and CSV export](subitems-table-options.md) and [Roles: policy IDs, sorting and order buttons](role-policy-order.md)
- [June 2026, second half](../../history/2026/2026-06b.md) (where the custom page size arrived)
- [September 2026, first half: 15 September](../../history/2026/2026-09a.md#15-september-paging-everywhere)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)

## Related pages

- [Multi edit (items from the sub items list)](../../bc/6.0/multi-node-edit.md)
