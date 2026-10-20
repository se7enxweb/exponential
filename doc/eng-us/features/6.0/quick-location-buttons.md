# Quick location buttons

Some pages ask for a place in the content tree: the content checks' start location
([content-checks.md](content-checks.md), "Where a check starts") and the upload page of ezmultiupload (its target
location). Next to the node picker they offer one-click buttons for the well-known places, **Websites** and **Media**,
and a row with the places right below one of them (the sites under Websites, Images, Files and Multimedia under
Media). The buttons are found from the content tree and the settings, never from fixed node ids, so they are right on
every installation.

## The kernel part: `expNodeQuickPick`

`kernel/classes/expnodequickpick.php` finds the places and builds the rows; each page decides what a button does (a
link, or a button that sets a field) and which places it may offer.

| Method | |
|---|---|
| `source( $key )` | how a key is found: `tree` (node 1), `content`, `media`, `users`, `setup`, `design` (content.ini `[NodeSettings] RootNode`, `MediaRootNode` ...), a node id (`58`), `remote:<node remote id>`, a URL alias path (`media/images`); `null` for anything else |
| `nodeID( $source )` | the node id of a source, 0 when it is not there |
| `label( $key, $nodeName )` | "Whole tree", "Websites", "Media", "Users", "Setup", "Design" for the named keys, else the node's name |
| `childRows( $parent )` | the children of a node in its own sort order (read access applies), each with its node |
| `row( $children, $currentPath, $currentID, $limit, $accept )` | the visible children `$accept` takes (a permission check, say), the first `$limit`, `more` when there are more; each item has `current` (the current place) and `within` (the current place lies below it) |
| `state( $nodeID, $currentID, $currentPath )` | `current` and `within` of one button |

`source()`, `label()`, `row()` and `state()` read no database and are tested without one
(`tests/tests/kernel/expNodeQuickPickTest.php`).

## The look: `.a4-quickpick`

A shared component of `stylesheets/components.css` (design/admin and design/admin4):

```html
<div class="a4-toolbar a4-quickpick">
  <div class="a4-row" role="group" aria-labelledby="x-presets">
    <span class="a4-quickpick-label" id="x-presets">Quick locations</span>
    <button type="button" class="a4-btn a4-btn-small" aria-pressed="true">Media</button>
    ...
  </div>
</div>
```

The current place is filled: `aria-pressed="true"` on a button, `aria-current="true"` on a link (or `.a4-btn-primary`).
The place the current one lies in has `.is-within` (outlined; say it in an `.a4-sr-only` text too). Rows wrap; on a
phone the label takes a line of its own. Tokens only, so dark mode and forced colors follow.

## Users

- `expContentCheckScope` (content checks): presets `contentcheck.ini [ScopeSettings] PresetList[]`, its methods
  `presetSource()`, `presetNodeID()`, `presetLabel()` and `siteRow()` are now `expNodeQuickPick`'s.
- ezmultiupload 6.1.8 (upload page, target location): `ezmultiupload.ini [QuickLocationSettings]`, see the
  extension's `doc/upload-page.md`.
