# Subitems "More actions": Hide selected and Unhide selected

Read this page if you maintain the admin3 sub-items list, its templates or the `content/action` module. It
describes two new bulk actions in the **More actions** menu. The change is backward compatible: nothing breaks,
and there is nothing to migrate.

## In short

| | |
|---|---|
| What changed | The admin3 sub-items list has **Hide selected** and **Unhide selected** in the **More actions** menu. |
| Who is affected | Editors (new actions). Developers who override the sub-items list or the `content/action` view. |
| How to check | Select a few sub-items, choose **More actions > Hide selected**, and confirm the nodes are hidden. |
| How to fix | Nothing to fix. If you override the pieces listed under "Keep these pieces aligned", add the new actions there. |

## What changed

The **More actions** menu of the admin3 sub-items list has two new entries:

- **Hide selected**
- **Unhide selected**

They work like the other bulk actions in that menu. They post the selected node IDs through the `content/action`
module view and redirect back to the parent node when the operation is done.

## How it works

- The live path is the `content` module override in `extension/nxc_powercontent`
  (`extension/nxc_powercontent/modules/content/action.php`). The kernel `content/action.php` has the same
  hide/unhide branch, but the extension override is the one that runs.
- The handler accepts `HideButton` or `UnhideButton`, reads `SelectedIDArray`, and changes the hidden state through
  the content operation layer.
- The menu entries and the posted button come from `design/admin/javascript/ezajaxsubitems_expdatatable.js`
  (admin3 uses it through design fallback; admin4 has its own copy in `design/admin4/javascript/`).

## How to check

1. In the admin, open a folder with sub-items.
2. Select two or three sub-items.
3. Choose **More actions > Hide selected**. The page reloads on the parent node and the selected nodes show as
   hidden.
4. Select them again and choose **More actions > Unhide selected**.

A regression test covers the active action path:
`tests/tests/kernel/content/ezcontentaction_hide_unhide_regression.php` (group `database`). It creates a real node,
posts the hide and unhide actions, and checks both the redirect and the node's hidden state. It needs a test
database, so run it in a development copy, not on a live site.

## Keep these pieces aligned

If you change this menu again, change all of these together:

- the admin3 labels and template wiring of the menu entries;
- the JavaScript that posts the bulk action buttons;
- the active `content/action` handler in `extension/nxc_powercontent`;
- the kernel `content/action` branch, for installations without that extension.

## Related pages

- [The responsive admin design (admin3)](../../features/6.0/admin3-responsive-admin.md)
- [The sub-items list: 129 columns, presets and CSV export](../../features/6.0/subitems-table-options.md)
- [Copy selected subitems](../../features/6.0/subitems-copy-selected.md)
- [Left sidebar width and font size](../../features/6.0/admin3-sidebar-width-and-font-size.md)
- [The admin4 design](../../features/6.0/admin4-design.md)
- History: [August 2024](../../history/2024/2024-08.md), [October 2024](../../history/2024/2024-10.md),
  [November 2024](../../history/2024/2024-11.md), [January 2025](../../history/2025/2025-01.md),
  [June 2025](../../history/2025/2025-06.md), [June 2026, 16 to 30](../../history/2026/2026-06b.md)
