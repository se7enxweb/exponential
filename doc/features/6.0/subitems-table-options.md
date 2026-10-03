# The sub-items list: 129 columns, presets and CSV export

The list of sub-items in the node view used to offer fifteen fixed columns. In 6.0.15 columns are a registry:
the fifteen old ones, 114 new ones in groups, and every attribute of the content classes in the list, without any
setting. Each user chooses, reorders and saves their columns; administrators define defaults and presets.

Added 2026-10-02. Full catalogue, performance numbers and how to write a column:
[doc/bc/6.0/subitems-table-options.md](../../bc/6.0/subitems-table-options.md).

## Use it

1. Open any node with sub-items, click **Table options**.
2. Filter the grouped list (Basic, Node, Object, Version, Location, URLs, SEO, Dates, People, Relations,
   Translations, Workflow, Users, Media, Technical, Custom), tick columns, drag to reorder.
3. Choose a preset or a page size (10, 25, 50, 100). Export the visible columns as CSV. Click a cell with the
   copy mark to copy its value.

The choice is stored per user on the server. `admin` and `admin4` load the column list from the server;
`admin2`, `admin3` and `sevenx_site_admin` use `admin`'s template. Without the new server functions the table
works as before.

## Add a column in three minutes

Add to `settings/override/subitemscolumns.ini.append.php`, naming a callable, a class or a template:

```ini
[Column_myprefix_nodeid_hex]
Name=Node ID (hex)
Group=Custom
Type=text
Handler=MyColumns::nodeHex
Copy=true
```

```php
class MyColumns
{
    public static function nodeHex( $node, $settings, $column )
    {
        return dechex( (int)$node->attribute( 'node_id' ) );
    }
}
```

Regenerate autoloads (`php bin/php/ezpgenerateautoloads.php -e`) and clear the INI cache
(`php bin/php/ezcache.php --clear-tag=ini --allow-root-user`). Other keys: `Class=` (a subclass of
`expSubitemsColumn`), `Template=design:...tpl`, `SortField=`, `Policy[]`, `Description=`, `Align=right`, `Order=`.

## Settings

| File | Block | Key | Default | Scope |
|---|---|---|---|---|
| `settings/subitems.ini` | `SubitemsSettings` | `DefaultColumns[]` | `name;published;translations;priority` | installation |
| same | `SubitemsSettings` | `AttributeColumns` | enabled | installation |
| same | `SubitemsSettings` | `AttributeColumnsExcludedDataTypes[]` | `ezuser` | installation |
| same | `SubitemsSettings` | `CSVExport` / `CSVLimit` | enabled / 5000 | installation |
| same | `SubitemsSettings` | `PageSizes[]` | 10, 25, 50, 100 | installation |
| same | `Preset_<id>`, `Defaults_<id>` | `Name`, `Columns[]`, `Subtree[]`, `ParentClassIdentifiers[]` | three presets ship: `Preset_seo` (SEO), `Preset_editorial` (Editorial), `Preset_technical` (Technical); no `Defaults_<id>` block | installation |
| `settings/subitemscolumns.ini` | `Column_<key>` | see the guide | 129 columns | installation |

The sub-items table itself is `exp::datatable`; its loader is `ezajaxsubitems_expdatatable.js` (a copy in `design/admin` and
one in `design/admin4`; change both together).

## Speed

Columns load their data once per page, not once per row (`expSubitemsColumn::prefetch()`): the data map, version
rows, readable children per parent, children per class, URL alias rows, locations and relation counts each come
from one query for all rows of the page. The shipped `settings/subitemscolumns.ini` has 129 `[Column_<key>]` blocks (check: `grep -c "^\[Column_" settings/subitemscolumns.ini`) and its header lists every key a block may carry. The guide lists query counts and times for 10, 50 and 100 rows.

Related: [remote services of the same columns](remote-services-expservices.md) (`expsubitems_svc`; the server functions are `expSubitemsServerFunctions`),
[October 2026 chronicle](../../history/2026/2026-10.md).

See also (October 2026): [6.0.15 changelog](../../changelogs/6.0/6.0.15.md), [upgrade checklist of 1-2 October 2026](../../bc/6.0/behaviour-changes-2026-10.md), [October 2026 chronicle](../../history/2026/2026-10.md), [audit trail](audit-trail.md).

## Custom page size

Besides the preset page sizes of `PageSizes[]`, the Table options panel has a **Custom** field (since June 2026; the label
comes from `design/admin/templates/children_detailed.tpl`). Choose **Custom** under items per page, type a whole number from
1 to 10000 and press Enter: the list reloads at once with exactly that many rows. Anything else is refused with a message
and the previous size stays. It helps for bulk work on exactly the rows on screen (select all, then move, copy or hide),
for export checks that need a precise slice, and for testing how a folder behaves at a given size. The page size is a user
preference; see [pagination settings](../../bc/6.0/pagination-settings.md) and the
[June 2026, second half chronicle](../../history/2026/2026-06b.md). The page "Custom items per page" is merged into this one.
