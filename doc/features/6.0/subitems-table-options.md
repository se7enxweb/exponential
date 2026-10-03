# The sub-items list: 129 columns, presets and CSV export

This page is for editors who want to see more about the items in a folder, and for administrators and developers who
set defaults or add columns. The sub-items list in the node view used to offer fifteen fixed columns. In 6.0.15
columns are a registry: the fifteen old ones, 114 new ones in groups, and every attribute of the content classes in the
list, without any setting. Each user chooses, reorders and saves their columns; administrators define defaults and
presets. Added 2026-10-02.

The full catalogue, performance numbers and how to write a column are in the guide
[Sub-items table options](../../bc/6.0/subitems-table-options.md).

## Use it

1. Open any node with sub-items and click **Table options**.
2. Filter the grouped list (Basic, Node, Object, Version, Location, URLs, SEO, Dates, People, Relations,
   Translations, Workflow, Users, Media, Technical, Custom), tick columns, and drag them to reorder.
3. Choose a preset or a page size (10, 25, 50, 100).
4. Export the visible columns as CSV if you need them in a spreadsheet. Click a cell with the copy mark to copy its
   value.

The choice is stored per user on the server. `admin` and `admin4` load the column list from the server; `admin2`,
`admin3` and `sevenx_site_admin` use `admin`'s template. Without the new server functions the table works as before.

## Custom page size

Besides the preset page sizes of `PageSizes[]`, the **Table options** panel has a **Custom** field (since June 2026;
the label comes from `design/admin/templates/children_detailed.tpl`).

1. Choose **Custom** under items per page.
2. Type a whole number from 1 to 10000 and press Enter.

The list reloads at once with exactly that many rows. Anything else is refused with a message, and the previous size
stays. Use it for bulk work on exactly the rows on screen (select all, then move, copy or hide), for export checks that
need a precise slice, or to test how a folder behaves at a given size. The page size is a user preference; see
[pagination settings](../../bc/6.0/pagination-settings.md). The page "Custom items per page" is merged into this one.

## Add a column in three minutes

1. Add a block to `settings/override/subitemscolumns.ini.append.php`, naming a callable, a class or a template:

   ```ini
   [Column_myprefix_nodeid_hex]
   Name=Node ID (hex)
   Group=Custom
   Type=text
   Handler=MyColumns::nodeHex
   Copy=true
   ```

2. Write the handler in your extension's `classes/` folder:

   ```php
   class MyColumns
   {
       public static function nodeHex( $node, $settings, $column )
       {
           return dechex( (int)$node->attribute( 'node_id' ) );
       }
   }
   ```

3. Regenerate autoloads and clear the INI cache:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-tag=ini --allow-root-user
   ```

4. Open **Table options**: the column "Node ID (hex)" is listed under **Custom**.

Other keys a column block can carry: `Class=` (a subclass of `expSubitemsColumn`), `Template=design:...tpl`,
`SortField=`, `Policy[]`, `Description=`, `Align=right`, `Order=`. The header of the shipped
`settings/subitemscolumns.ini` lists every key.

## Settings

| File | Block | Key | Default | Scope |
|---|---|---|---|---|
| `settings/subitems.ini` | `SubitemsSettings` | `DefaultColumns[]` | `name;published;translations;priority` | installation |
| `settings/subitems.ini` | `SubitemsSettings` | `AttributeColumns` | enabled | installation |
| `settings/subitems.ini` | `SubitemsSettings` | `AttributeColumnsExcludedDataTypes[]` | `ezuser` | installation |
| `settings/subitems.ini` | `SubitemsSettings` | `CSVExport` / `CSVLimit` | enabled / 5000 | installation |
| `settings/subitems.ini` | `SubitemsSettings` | `PageSizes[]` | 10, 25, 50, 100 | installation |
| `settings/subitems.ini` | `Preset_<id>`, `Defaults_<id>` | `Name`, `Columns[]`, `Subtree[]`, `ParentClassIdentifiers[]` | three presets ship: `Preset_seo` (SEO), `Preset_editorial` (Editorial), `Preset_technical` (Technical); no `Defaults_<id>` block | installation |
| `settings/subitemscolumns.ini` | `Column_<key>` | see the guide | 129 columns | installation |

Count the shipped columns:

```bash
grep -c "^\[Column_" settings/subitemscolumns.ini
```

Expected output: `129`.

## Speed

Columns load their data once per page, not once per row (`expSubitemsColumn::prefetch()`). The data map, version
rows, readable children per parent, children per class, URL alias rows, locations and relation counts each come from
one query for all rows of the page. The guide lists query counts and times for 10, 50 and 100 rows.

## For developers

The sub-items table itself is `exp::datatable`. Its loader is `ezajaxsubitems_expdatatable.js`, with one copy in
`design/admin` and one in `design/admin4`; change both together. The same columns are served as remote services by
`expsubitems_svc`; the server functions are `expSubitemsServerFunctions` (see
[remote services](remote-services-expservices.md)).

## Related pages

- [Sub-items table options (guide)](../../bc/6.0/subitems-table-options.md), [pagination settings](../../bc/6.0/pagination-settings.md)
- [Paging, sorting and page sizes](admin-list-paging.md), [copy selected sub-items](subitems-copy-selected.md), [hide and unhide selected](../../bc/6.0/SUBITEMS_MENU_MORE_ACTIONS_MENU_EXPANSION_HIDE_UNHIDE.md)
- [The responsive admin design (admin3)](admin3-responsive-admin.md), [the admin4 design](admin4-design.md), [left sidebar width and font size](admin3-sidebar-width-and-font-size.md)
- [Audit trail](audit-trail.md)
- [Upgrade checklist of 1-2 October 2026](../../bc/6.0/behaviour-changes-2026-10.md)
- [6.0.15 changelog](../../changelogs/6.0/6.0.15.md)
- History: [October 2026](../../history/2026/2026-10.md), [June 2026, second half](../../history/2026/2026-06b.md), [June 2025](../../history/2025/2025-06.md), [January 2025](../../history/2025/2025-01.md), [November 2024](../../history/2024/2024-11.md), [October 2024](../../history/2024/2024-10.md), [August 2024](../../history/2024/2024-08.md)
