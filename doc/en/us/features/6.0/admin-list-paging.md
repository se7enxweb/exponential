# Paging, sorting and page sizes in the administration interface

This page is for administrators of large installations, and for anyone who wants longer or shorter lists in the admin.
Every long list in the administration interface is now paged, and its page size is a setting. Before, an installation
with many sites, thousands of locations or thousands of roles could fail to draw some of these pages at all: the whole
list was read, and a query ran for every row, before the first byte was sent.

## Change a page size

All sizes are in `admininterface.ini`, block `[PaginationSettings]`, keyed by `module/view`:

```ini
# settings/override/admininterface.ini.append.php
[PaginationSettings]
DefaultItemsPerPage=25
ItemsPerPage[section/list]=50
ItemsPerPage[class/classlist]=100
```

Clear the caches after the change:

```bash
php bin/php/ezcache.php --clear-all --allow-root-user
```

To see the keys your installation ships:

```bash
grep -n -A40 "^\[PaginationSettings\]" settings/admininterface.ini
```

The full table of keys, the four settings that predate the block, and the one list that is deliberately not paged are
in [Where the page sizes live](../../bc/6.0/pagination-settings.md).

## Pick your own size in the sub-items list

The **Table options** panel of the sub-items list has a **Custom** field: any whole number from 1 to 10000, stored as a
user preference. See [Sub-items list: custom page size](subitems-table-options.md#custom-page-size).

## What is paged now

| List | Notes |
|---|---|
| Locations tab of an item | [Locations tab paging and sorting](../../bc/6.0/locations-tab-paging-and-sorting.md) |
| Roles, and the policies of a role | [Role and policy paging](../../bc/6.0/role-policy-paging.md); the role list also shows the role id and sorts |
| The users and groups of a role | [Role assignment paging](role-assignment-paging.md): sorted by name, with a name filter; the role list counts them per role |
| Class list, class group list, workflow group list, PDF export list | none had paging before |
| Discount groups, order status, VAT rules, product categories | read their whole list before |
| VAT types, translations, extensions, cronjob scripts, REST applications | read their whole list before |
| Package list | had a page size but no pager to reach page two, and drew every package on every page |
| Section list, collected information overview, state groups, search statistics | moved onto the settings block |
| RSS list | [RSS list](rss-podcast-and-feed-list.md) |
| Layout lists of the layouts editor | paged by `explayouts_ui` entries in the same block |

Also:

- The extension list sorts by the extension's own name and its license, and keeps the sort in the address, so paging
  does not lose it.
- Item view tabs no longer load everything they count.
- In the role view, the policy window that actually renders is the one that is bounded.

## Settings

| File | Block | Key | Default | Scope |
|---|---|---|---|---|
| `settings/admininterface.ini` | `PaginationSettings` | `DefaultItemsPerPage` | `25` | global |
| `settings/admininterface.ini` | `PaginationSettings` | `ItemsPerPage[<module>/<view>]` | `25` (10 for `workflow/processlist` and `state/groups`, 15 for `shop/orderlist` and `shop/customerlist`, 50 for `shop/archivelist`) | global |
| `settings/content.ini` | `LocationsSettings` | `LocationsPerPage` | `25` | global |
| `settings/site.ini` | `RoleSettings` | `PoliciesPerPage`, `PolicyPreviewPerRole`, `RolesPerPageList[]` | `25`, `10`, `10`/`25`/`50` | global |

## For developers

The modules read their size through one helper instead of a number written in the module. The helper also gives the
lists their paging parameters and the user's chosen page size. See "Where the reading happens" in
[Where the page sizes live](../../bc/6.0/pagination-settings.md).

## Related pages

- [Where the page sizes live](../../bc/6.0/pagination-settings.md), [role and policy paging](../../bc/6.0/role-policy-paging.md), [locations tab paging and sorting](../../bc/6.0/locations-tab-paging-and-sorting.md)
- [Sub-items list: columns, presets and CSV export](subitems-table-options.md), [roles: policy IDs, sorting and order buttons](role-policy-order.md)
- [Multi edit (items from the sub-items list)](../../bc/6.0/multi-node-edit.md)
- [Sections](../../guides/sections.md): the section list, its page size and the other section pages
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
- History: [June 2026, second half](../../history/2026/2026-06b.md) (custom page size), [September 2026, first half: paging everywhere](../../history/2026/2026-09a.md#15-september-paging-everywhere)
