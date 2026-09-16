# Where the page sizes live

Every paged list in the administration interface takes its page size from a
setting, so a site can choose its own without editing a template or a module.
This is the index of them.

Most of them live in one block, `admininterface.ini [PaginationSettings]`,
keyed by the module and view that draws the list — so a setting can be found
from the address of the page it governs.

```ini
[PaginationSettings]
DefaultItemsPerPage=25
ItemsPerPage[section/list]=25
ItemsPerPage[shop/archivelist]=50
```

| View | Key |
|---|---|
| `setup/extensions` | `ItemsPerPage[setup/extensions]` |
| `setup/cronjobs` | `ItemsPerPage[setup/cronjobs]` |
| `section/list` | `ItemsPerPage[section/list]` |
| `state/groups` | `ItemsPerPage[state/groups]` |
| `workflow/grouplist` | `ItemsPerPage[workflow/grouplist]` |
| `workflow/processlist` | `ItemsPerPageList_workflow_processlist[]` |
| `oauthadmin/list` | `ItemsPerPage[oauthadmin/list]` |
| `package/list` | `ItemsPerPage[package/list]` |
| `pdf/list` | `ItemsPerPage[pdf/list]` |
| `content/translations` | `ItemsPerPage[content/translations]` |
| `infocollector/overview` | `ItemsPerPage[infocollector/overview]` |
| `class/grouplist` | `ItemsPerPage[class/grouplist]` |
| `class/classlist` | `ItemsPerPage[class/classlist]` |
| `shop/orderlist` | `ItemsPerPage[shop/orderlist]` |
| `shop/customerlist` | `ItemsPerPage[shop/customerlist]` |
| `shop/archivelist` | `ItemsPerPage[shop/archivelist]` |
| `shop/discountgroup` | `ItemsPerPage[shop/discountgroup]` |
| `shop/status` | `ItemsPerPage[shop/status]` |
| `shop/vattype` | `ItemsPerPage[shop/vattype]` |
| `shop/vatrules` | `ItemsPerPage[shop/vatrules]` |
| `shop/productcategories` | `ItemsPerPage[shop/productcategories]` |
| `shop/currencylist` | `ItemsPerPageList_shop_currencylist[]` |
| `shop/productsoverview` | `ItemsPerPageList_shop_productsoverview[]` |
| `explayouts_ui/layout_list` | `ItemsPerPage[explayouts_ui/layout_list]` |
| `explayouts_ui/shared_layouts_list` | `ItemsPerPage[explayouts_ui/shared_layouts_list]` |
| `explayouts_ui/components` | `ItemsPerPage[explayouts_ui/components]` |
| `explayouts_ui/rule_list` | `ItemsPerPage[explayouts_ui/rule_list]` |

The four `explayouts_ui` entries are shipped by that extension's own
`settings/admininterface.ini.append.php`, so using it does not mean adding
entries to the kernel's settings by hand.

These four predate the block and keep settings of their own:

| View | Setting | File | Default |
|---|---|---|---|
| Locations tab of an item | `[LocationsSettings] LocationsPerPage` | `content.ini` | 25 |
| Policies on `role/edit` and `role/view` | `[RoleSettings] PoliciesPerPage` | `site.ini` | 25 |
| Policy preview per role, on a user or user group | `[RoleSettings] PolicyPreviewPerRole` | `site.ini` | 10 |
| `role/list` | `[RoleSettings] RolesPerPageList[]` | `site.ini` | 10, 25, 50 |
| `rss/list` (exports and imports) | `[RSSListSettings] ItemsPerPageList[]` | `content.ini` | 25, 50, 250 |

Override any of them per siteaccess like any other setting —
`settings/siteaccess/<name>/` or `settings/override/`.

## Deliberately not paged

`shop/preferredcurrency` is a drop-down for choosing one currency, not a list
of rows. Paging it would make it unusable.

## Where the reading happens

`expAdminPagination` answers all of it:

```php
expAdminPagination::limit( 'section/list' );          // one size
expAdminPagination::sizes( 'shop/currencylist' );     // the sizes a selector offers
expAdminPagination::chosen( $view, $preferenceName ); // size, choice, sizes
expAdminPagination::offset( $Params );                // the page asked for
expAdminPagination::page( $rows, $offset, $limit );   // one page of a list in memory
```

`offset()` reads the user parameters as well as a declared `Offset`, so a list
can be paged without changing the module definition it belongs to.

`page()` is second best and says so: it bounds what is **drawn**, which is what
takes a page of hundreds of rows down, but not what is **read**. It is used
where the model has no fetch that takes an offset and a limit. Where one exists
it is used instead — `role/list`, `section/list`, `state/groups`,
`oauthadmin/list` and the locations tab all page in the query.

---

## One size, or a list of them

Two shapes appear above, because two kinds of screen exist.

A screen with **no items-per-page selector** takes a single integer:

```ini
[LocationsSettings]
LocationsPerPage=25
```

A screen **with** a selector takes the list of sizes it offers, the first being
what someone who has never chosen gets:

```ini
[RoleSettings]
RolesPerPageList[]
RolesPerPageList[]=10
RolesPerPageList[]=25
RolesPerPageList[]=50
```

The list is also what bounds the query. A size that is not on it cannot be
asked for in the url either, so lengthening it is how you allow a larger page,
and nothing else can.

Both shapes fall back to their default when the setting is emptied or filled
with something that is not a positive number — a list with no usable size in it
would otherwise divide by zero further down.

---

## What the preference stores

The selector remembers a choice per user, in `admin_role_list_limit` and
`admin_rss_list_limit`.

`role/list` stores **the position in the list, counting from one** — not the
size. That is what it has always stored: the values 1, 2 and 3 meant the first,
second and third of a set of sizes written into the module. Keeping the meaning
means a site that changes `RolesPerPageList` keeps everyone's choice rather
than resetting it, and no stored preference has to be migrated.

`rss/list` stores the size itself, and ignores a stored size that is no longer
on the list.

---

## What this replaced

`role/list` had the sizes written into the module:

```php
switch( eZPreferences::value( 'admin_role_list_limit' ) )
{
    case '2': { $limit = 25; } break;
    case '3': { $limit = 50; } break;
    default:  { $limit = 10; } break;
}
```

and **written again into the template**, as `choose( 10, 10, 25, 50 )` for the
selector. Two lists that had to agree by hand, and no way to change either.

`rss/list` had `eZRSSListPager::limits()` returning `array( 25, 50, 250 )`.

---

## Checking it

Changing the settings and reloading is the test:

| Set to | The page then shows |
|---|---|
| `RolesPerPageList = 3, 7, 11, 99` | a selector of `3 7 11 99`, and 3 rows |
| `ItemsPerPageList = 4, 8` | a selector of `4 8` |
| `LocationsPerPage = 7` | 7 rows |

Run after any change to a settings file:

```
php bin/php/ezcache.php --clear-all
```
