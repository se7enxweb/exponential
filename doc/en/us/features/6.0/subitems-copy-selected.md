# Copy selected sub-items

This page is for editors who copy several items at once. **Copy selected** copies several items, with everything
below them, to a new place in the tree in one step. Before January 2025 the sub-items list could remove or move the
selected items, but not copy them.

## Use it

1. Open a node in the admin. In the **Sub items** list, tick the check boxes of the items to copy.
2. Open the **More actions** menu above the list and choose **Copy selected**.
3. A browse dialog asks for the new parent. Choose it and confirm.
4. Exponential copies each selected node and its subtree below the new parent and, if notifications are on, shows a
   summary of what was copied.

The same action is in the pop-up menu of a single item and in the `content/copysubtree` view.

## What is checked first

- The user must be able to read every selected node, and must have **create** permission for the class of each copy
  under the chosen parent.
- A node cannot be copied into itself or one of its own descendants.
- When the copy is larger than `MaxNodesCopySubtree`, current releases do not refuse it: the copy view offers to run it
  as a background content job, which cannot hit a request time limit (see [content jobs](content-jobs.md)). The first
  version of the feature (January 2025) copied at most that many nodes.

## Settings

All keys are in `settings/content.ini`, block `CopySettings`. Scope: override or siteaccess.

| Key | Default | Meaning |
|---|---|---|
| `MaxNodesCopySubtree` | `30` | Maximum number of nodes a copy-subtree request handles |
| `VersionHandling` | `all` | `user-defined`, `last-published` or `all`: which versions of each object are copied |
| `CreatorHandling` | `current` | `user-defined`, `keep-unchanged` or `current`: who is recorded as the creator of the copies |
| `TimeHandling` | `current` | `user-defined`, `keep-unchanged` or `current`: which timestamps the copies get |
| `ShowCopySubtreeNotification` | `enabled` | Show the result page after copying |

## Where it lives in the code

| Piece | File |
|---|---|
| Menu entry (value 2, form field `CopyButton`, label "Copy selected") | Label in `design/admin/templates/children_detailed.tpl` and `design/admin4/templates/children_detailed.tpl`; the menu and its submit in `design/admin4/javascript/ezajaxsubitems_expdatatable.js` (the 2025 script `ezajaxsubitems_datatable.js` no longer exists) |
| Actions `CopyButton` and `CopyNode` (browse for the target) | `kernel/private/classes/views/content/action.php` |
| Operation | `eZContentOperationCollection::copyNode()` in `kernel/content/ezcontentoperationcollection.php` and `eZContentObjectTreeNodeOperations::copySubtree()` in `kernel/classes/ezcontentobjecttreenodeoperations.php` |
| Result page | `design/standard/templates/content/copy_subtrees_notification.tpl` |
| View | `kernel/content/module.php`, view `copysubtree` (needs the `create` function) |

## Related pages

- [Content jobs](content-jobs.md) for large copies
- [Hide and unhide selected](../../bc/6.0/SUBITEMS_MENU_MORE_ACTIONS_MENU_EXPANSION_HIDE_UNHIDE.md)
- [Sub-items table options](subitems-table-options.md)
- [The responsive admin design (admin3)](admin3-responsive-admin.md), [the admin4 design](admin4-design.md), [left sidebar width and font size](admin3-sidebar-width-and-font-size.md)
- [Changelog 6.0.7](../../changelogs/6.0/6.0.7.md)
- History: [August 2024](../../history/2024/2024-08.md), [October 2024](../../history/2024/2024-10.md), [November 2024](../../history/2024/2024-11.md), [January 2025](../../history/2025/2025-01.md), [June 2025](../../history/2025/2025-06.md), [June 2026, second half](../../history/2026/2026-06b.md)
