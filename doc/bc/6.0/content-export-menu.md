# The Export menu of the content tree

Exponential 6.0.15 adds an **Export** submenu to the **Advanced** submenu of the administration's context menus:
the content tree's menu and the "…" menu of each row in the sub-items list. Both use the same `Advanced` menu
(`popupmenu/popup_sub_advance_menu.tpl`), so the entries are the same in both places. The menu exists in
design/admin4 (and admin4l, which falls back to it), design/admin (an identical copy), and the adminui design of
exp_adminui (which uses admin4's templates).

Every entry opens the form of its tool **filled in** for the item. Nothing is exported from the menu: no entry
submits a form, and no entry URL writes content or starts a job. The user checks the form and starts the export
there.

## The entries

| Entry | Opens |
|---|---|
| This item as package… | the content object package wizard with the item selected (node only) |
| This item and its subtree as package… | the wizard with the item's subtree selected |
| Subtree as package, without class definitions… | the subtree; "Include class definitions" unticked on the second step |
| Subtree as package, with all versions… | the subtree; "All versions" chosen on the second step |
| Open the package wizard… | `package/create`, the choice of wizard |
| Children as CSV… | xrowextract's CSV form: the item's direct children |
| Subtree as CSV… | the CSV form: the item's whole subtree |
| Subtree of class ▸ | one entry per class with objects below the item (the count in brackets), each opening the CSV form for the subtree and that class; "More classes…" after the first 12 |
| Subtree as CSV, every attribute… | the CSV form with every attribute of the class as the columns |
| Subtree as CSV, migration columns… | the CSV form with the column set "Migration" (identity, parent, languages, dates, every attribute) |

The wizard's "Related objects" choice is not offered as an entry: the content object package handler does not
read it (the relations of an object are exported with its attributes either way).

## Entry parameters

### package/create

```
package/create/(creator)/ezcontentobject/(node)/<node id>[/(subtree)/1][/(classes)/0|1][/(versions)/current|all]
```

- `creator` must be `ezcontentobject` (the only wizard with an entry); `node` a node id, 1 to 2147483647 in plain
  digits; `subtree` `0` or `1` (default `0`: the node alone); `classes` and `versions` preset the second step.
- Any other value answers "not available". A node that does not exist answers "not available", one the user cannot
  read "access denied", and so does a user without the policies below.
- The wizard starts again (its session data is replaced) with the node or subtree on its first step. The package is
  made only when the user goes through the steps. The checks are in
  `eZContentObjectPackageCreator::entryParameters()` and `::currentUserCanExportContent()`; the view is
  `kernel/private/classes/views/package/create.php`.

### xrowextract/csv (xrowextract 2.5.11)

```
xrowextract/csv/(node)/<node id>[/(scope)/list|tree][/(class)/<class id>][/(columns)/all|migration]
```

- `scope` defaults to `tree` (the subtree); without `class` the form chooses as before (export.ini DefaultClassID,
  else the class with the most objects); without `columns` the user's saved columns for the class stay.
- The form's saved settings are filled in (node, scope, class; filters and offset cleared; output settings kept) and
  the view redirects to `xrowextract/csv`. Invalid values and a class export.ini ExportClasses does not offer
  answer "not available", a node the user cannot read "access denied". Code: `XrowExtractEntry`.
- `xrowextract/subtree_classes/<node id>` (policy xrowextract/csv) answers JSON for the "Subtree of class" list:
  `{ node, classes: [ { id, identifier, name, count } ], more, total }`, counted with the user's read access; a node
  that is not there or not readable answers 404.

## Who sees what

- The package entries: `fetch( 'package', 'content_export_access' )`, true for a user with the policies
  package/create (a CreatorType limitation must include `ezcontentobject`, a Type limitation `contentobject`) and
  package/export (to download the package; a Type limitation must include `contentobject`).
- The CSV entries: the policy xrowextract/csv.
- **Export** is shown only when it has an entry for the user; an Editor (no package or xrowextract policies) sees
  no change. The tree and the sub-items list show only items the user can read, and every entry URL checks the node
  again.
- package.ini `[ExtensionExportSettings]` (exporting *extensions* as packages, off by default, #198) does not apply:
  content packages are made by the content object wizard, which it does not switch off.

## Adding entries from an extension

admininterface.ini `[AdditionalMenuSettings]`:

```
ExportMenuTemplateArray[]=popupmenu/myextension_export_menu.tpl      # items inside the Export menu
ExportSubMenuTemplateArray[]=popupmenu/myextension_export_submenus.tpl # their submenus (depth 3), after Export
```

An item template checks its own policy and outputs nothing for a user without it, adds its URLs to
`menuArray['ExportMenu']['elements']` (with `%nodeID%`), and opens a form, never an export. A submenu whose items are
known only when it opens sets `menuArray[id]['onshow'] = function( menu, values, refresh )`: ezpopupmenu.js calls it
when the submenu opens; the function fills the menu (as text, never markup from data) and calls `refresh()`, which
gives the new items their roles and places the menu again. xrowextract's `xrowextract-exportmenu.js` is the example.

## Keyboard and placement

The submenus follow the menu pattern of #165: Right, Enter or Space open a submenu and move the focus into it, Left
or Escape close it and return the focus to its item; items with a submenu have `aria-haspopup="menu"` and
`aria-expanded`. A nested submenu opens beside its parent on the side with room and is moved inside the window when
neither side has room (960px at 200 % zoom, phones); it moves up from the bottom edge and scrolls when it is taller
than the window (`_horizontalSubPlace()`, `_subVerticalPlace()` in ezpopupmenu.js).

Tests: `tests/tests/kernel/classes/expContentExportEntryTest.php`,
`tests/tests/design/AdminContextMenuNestedSubmenuTest.php`, xrowextract's `tests/unit/EntryTest.php`.
