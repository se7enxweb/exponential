# explayouts_ui_api: the layout editor

`explayouts_ui_api` is the visual layout editor of Exponential Layouts. It serves
the editor application (a single-page app) and the JSON API behind it, as plain
Exponential module views on top of the `explayouts_core` services. You open it from
the **Layouts** admin tab (see [explayouts_ui](explayouts_ui.md)) or directly at
`/explayouts_ui_api/app`.

It was imported on 30 July 2026 (1.0.0) and by 1.3.7 (30 September 2026) can be
used on a phone, under a siteaccess reached by path (`/admin`), with German texts,
with shared header and footer layouts shown as locked, and with write protection
that makes sure a published layout is only changed through its draft.

Version note: the installed copy in `extension/explayouts_ui_api` reads 1.3.10 in `ezinfo.php`; the clone of the repository has the tags v1.3.8 and v1.3.9 on commits that
are not reachable from its main branch, so this page itemises changes up to 1.3.7 only. Check what your copy has with `grep Version extension/explayouts_ui_api/ezinfo.php`.

## Open and use the editor

1. Log in to the admin and open **Layouts** (`/explayouts_ui/dashboard`).
2. Choose **New layout**, or **Edit layout** on a layout card, a mapping rule or
   the dashboard. The editor opens at `/explayouts_ui_api/app#layout/<id>`.
3. Drag a block from the **+** menu into a zone, or inside a container block.
   Blocks can be copied, moved and deleted; their order is kept.
4. Select a block to edit it in the sidebar. The block form opens on its **Design**
   tab (view type, column count and the other design parameters; the block's name is
   a field of the form), the sidebar has **Block** and **Collection** tabs for blocks
   that hold a collection (manual items or a dynamic query), and an **Advanced
   options** section. The query form has a collapsible, separate **Offset and
   number of items** panel.
5. **Publish** makes the draft live; **Discard** throws it away; **Create draft**
   opens a published layout for changing.

The editor always edits the **draft**. A published layout is never changed directly:
every write to a block of a published layout is refused with HTTP 403, and its
blocks change only when its draft is published.

### What the preview shows

* Blocks are labelled with the name of their definition, not an internal
  identifier. Title and rich text blocks render with the inline-edit hooks the editor
  expects; a template block shows `Template block: <name>`.
* A list block shows readable item titles. A collection renders as a column grid that
  honours its column count; a component block shows the item it references, with
  thumbnail, linked name and value type.
* Manual collection items show node id, parent node id and name. Items can be added
  from a content browser, removed, removed all, and moved. The collection type can be
  switched between manual and dynamic.
* The selected block is highlighted distinctly.

### Shared layouts and linked zones

A zone can inherit its blocks from a zone of a shared layout (for example the site
header and footer). The editor draws such a zone as **inherited and locked**: you see
the shared blocks, but cannot edit them from the layout that links to them. To change
them, open the shared layout itself and draft it. Zone link and unlink are available
in the editor (the link icon at the bottom left of a zone), and write requests into a
linked zone are refused. This replaced the behaviour before 1.2.1, in which the editor
listed the shared blocks as the layout's own and an edit wrote straight to the
published rows of the shared layout, site wide.

### Use it on a phone

From 1.2.0 the editor has a mobile layout (upstream refuses to draw the editor below
800 px). The canvas takes the full width beside a rail; blocks can be dragged by
finger; the block options open from a toggle in the rail under the add-block button,
and selecting a block no longer covers the layout with the properties drawer. The
toggle turns accent coloured to say a block is waiting to be inspected.

### Languages

The editor shows its buttons, menus, dialogs, notices, empty states, tooltips and
date picker texts in the interface language. English, German (complete) and the
untranslated catalogue ship, 123 messages. The texts are looked up in the context
`design/standard/explayouts_ui_api/spa` through the template
`explayouts_ui_api/spa_strings.tpl`; add a language by adding a translation file for
that context.

### Under a siteaccess reached by path

From 1.3.1 the editor works when the admin lives at a path such as `/admin/` rather
than on its own host. All editor URLs go through `eZURI::transformURI()`, the router
accepts a siteaccess prefix, and "View in CMS" and the links that leave the editor
carry the prefix. Leaving the editor (Discard, Cancel, close) returns to: the
`return_to` parameter, else the page that opened the editor, else a kept value inside
this siteaccess, else the siteaccess root.

## Security

Requests run in the authenticated admin session. Since 1.3.3 every API request other
than `GET`, `HEAD` and `OPTIONS` must carry the session's form token, as an
`X-CSRF-Token` header or an `ezxform_token` form field. Before this the form token
check of the `ezformtoken` extension looked only at `POST`, so `PUT`, `PATCH` and
`DELETE` (discard a draft, change or delete a block, link or unlink a zone) were not
checked. Without a token, or with a wrong one, the API answers before it reads or
changes anything: HTTP 403, `Cache-Control: no-store`, and the body

```json
{"error":{"code":403,"reason":"form_token_missing"}}
```

(`form_token_wrong` for a wrong token). The editor adds the token itself, including to
same-site `fetch()` and `XMLHttpRequest` calls that change something, so nothing it
sends is refused. When `ezformtoken` is not active nothing is checked. Block writes
are additionally refused unless the block belongs to a draft (1.3.4).

## Call the API yourself

Any HTTP client works with an authenticated admin session cookie and the form token.
Get the token from the config endpoint, then create a layout and a block:

```bash
BASE=https://admin.example.com/explayouts_ui_api/app/api
# cookie.txt holds the admin session cookie
TOKEN=$(curl -s -b cookie.txt "$BASE/config" | sed -n 's/.*"csrf_token":"\([^"]*\)".*/\1/p')
curl -s -b cookie.txt -H "X-CSRF-Token: $TOKEN" -H 'Content-Type: application/json' \
  -d '{"name":"My layout","layout_type":"2_column"}' "$BASE/layouts"
```

The full endpoint list, request and response shapes and error contract are in the
[explayouts_ui_api specification](../../../specifications/6.0/explayouts-ui-api.md).

## Requirements

* Exponential 6 (PHP 8.1 or later).
* Sibling extensions `explayouts` (value objects, tables, `explayouts.ini` layout and
  block types), `explayouts_core` (services) and `ezformtoken` (form token).
* Activate it for the admin siteaccess, after its dependencies, then regenerate
  autoloads and clear caches. Verify with `/explayouts_ui_api/app/api/config`, which
  returns JSON including the current form token.

## Behaviour changes to know when upgrading

* **1.3.4**: block writes on a published layout are refused with 403. If a script
  changed published blocks directly, make it create a draft and publish.
* **1.3.3**: non-GET requests need the form token (see above).
* **1.2.1**: shared and linked blocks are locked in the layout that links to them.
* **1.2.4**: the share table indexes are named `idx_share_layout` and
  `idx_share_token`. An installation that shared a layout before this keeps the old
  index names; the system upgrade page names the four statements that correct it, or
  you may drop `explayouts_share` (it holds only share tokens) and it is created
  again on the next share. Needs `explayouts` 1.3.7 or later.
* **1.3.0**: module views can be served by a persistent PHP worker (Velocity); see
  [Velocity engines](../../../bc/6.0/velocity-engines.md).

## Related

* [explayouts_ui](explayouts_ui.md): the Layouts admin screens that link into the editor
* [Specification](../../../specifications/6.0/explayouts-ui-api.md)
* [Chronicle](../../../history/extensions/explayouts_ui_api.md) and [release notes](../../../changelogs/extensions/explayouts_ui_api.md)
