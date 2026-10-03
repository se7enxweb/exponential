# explayouts_ui_api specification

Reference for the editor API of the `explayouts_ui_api` extension (releases 1.0.0 to
1.3.7). The user-facing description is the
[feature page](../../features/6.0/extensions/explayouts_ui_api.md).

## Module

Module `explayouts_ui_api` (`modules/explayouts_ui_api/module.php`, `variable_params`
on). All views use the policy function `read` of the module, navigation part
`ezsetupnavigationpart`.

| View | Parameters | Purpose |
|---|---|---|
| `app` | none (the rest of the path is read by the dispatcher) | Editor shell at `/explayouts_ui_api/app`, API entry `/app/api/...`, layout preview `/app/preview/<layout_id>`, block edit form fragments |
| `layouts` | `LayoutID` | Read-only JSON view of one layout |
| `rules` | `RuleID` | Read-only JSON view of one mapping rule |
| `blocks` | `ZoneID` | Read-only JSON view of the blocks of a zone |

Settings shipped: `module.ini` (`[ModuleSettings] ExtensionRepositories[]` and
`ModuleList[]` for `explayouts_ui_api`), `design.ini` (`DesignExtensions[]`), `site.ini`
(`[RegionalSettings] TranslationExtensions[]`).

## Dispatcher

`expLayoutsUIApplicationApi::handle( $parts )` in
`classes/explayoutsuiapplicationapi.php`. Every response is `application/json`; list
responses use `{"values": [...], "total": n}`. The dispatcher first checks the form
token, then routes by resource. An uncaught `Throwable` is logged to `error.log` with a
backtrace and answered as HTTP 500 `{"error":"Internal error","details":...}`.

## Authentication and the form token

Same admin session as the rest of the admin. `GET`, `HEAD` and `OPTIONS` need no token.
Every other method needs the session token (`ezxFormToken::getToken()`) as
`X-CSRF-Token` or `ezxform_token`. Refusals: HTTP 403, `Cache-Control: no-store`, one
warning line in the log, body `{"error":{"code":403,"reason":"form_token_missing"}}` or
`"reason":"form_token_wrong"`. With `ezformtoken` not active no token is issued and
nothing is checked. Request bodies may be `application/x-www-form-urlencoded` or
`application/json` (`requestData()` falls back to `php://input` when `$_POST` is empty).

## Endpoints

All paths below are relative to `/explayouts_ui_api/app/api`. `<locale>` is an optional
content locale prefix.

| Method and path | Purpose |
|---|---|
| `GET /config` | `{"csrf_token", "automatic_cache_clear": true, "edition": "Open Source"}` |
| `GET /config/layout_types` | Layout types and their zones from `explayouts.ini` (`LayoutType_*`) |
| `GET /config/block_types` | Block types in `block_types` and `block_type_groups` (grouped by `Category`) |
| `GET /layouts` | List layouts |
| `GET /layouts/shared` | Published shared layouts only |
| `POST /layouts` | Create a draft layout. Body: `layout_type` (required, else 422 `Layout type is required.`), `name`, `identifier` (a slug of the name when missing). Answers 201 |
| `GET /layouts/<id>` | One layout. `?published=false` loads the draft, `?published=true` the published version, no query falls back to `load()` |
| `POST /layouts/<id>/publish` | Publish the draft (404 when there is none) |
| `POST /layouts/<id>/draft` | Create a draft of a published layout (201) |
| `DELETE /layouts/<id>/draft` | Discard the draft |
| `GET [/<locale>]/layouts/<id>/blocks` | Blocks of the layout |
| `GET [/<locale>]/layouts/<id>/zones/<identifier>/blocks` | Blocks of one zone, as a **bare JSON array** (the editor takes ids straight off it) |
| `POST /layouts/<id>/zones/<identifier>/link`, `DELETE ...` | Link and unlink a zone to a zone of a shared layout, on the draft |
| `GET /<locale>/blocks/<id>`, `POST/PUT/PATCH` | Load and update a block: `name`, `view_type`, `position`, `parameters` (object keyed by parameter name) |
| `POST /<locale>/blocks` | Create a block. `parent_position` places it; siblings are renumbered |
| `DELETE /<locale>/blocks/<id>` | Delete; siblings are compacted |
| `POST /<locale>/blocks/<id>/copy`, `/move` | Copy and move, also into containers |
| `GET|POST|PATCH /collections/...` | Collections: `result`, `change_type`, `query`, `items` (`GET`, `POST`, `PUT`, `DELETE`, `remove_all`) |
| `GET /content_browser/...` | Content browser data for adding collection items |
| `GET /rules`, `GET /rules/<id>`, `GET /mappings` | Mapping rules with targets and conditions; rule counts per `layout_id` |
| `/transfer/...` | Import and export through `expLayoutsImporter` and `expLayoutsExporter` |
| `/forms/...`, `/parameters/...` | Form and parameter metadata for the block edit sidebar |
| `GET /versions/<layout_id>` | Draft and published versions |
| `GET|POST|DELETE /share/<layout_id>` | Share tokens (table `explayouts_share`) |

HTML fragments, relative to `/explayouts_ui_api/app`: `GET /<locale>/blocks/<id>/edit`
(wrapper, `form_block_edit.tpl`) and `GET /<locale>/blocks/<id>/form` (the `<form>`,
`form_block_fields.tpl`). The form submits to `POST /app/api/<locale>/blocks/<id>`.

### Write rules

* A block is changed, copied or moved only when it belongs to a **draft**; otherwise
  HTTP 403. A draft block cannot be created, copied or moved under a published parent
  block. New and moved blocks take the zone of the draft.
* A block of a published **shared** layout, and any write into a **linked** zone, is
  refused for every non-`GET` block route.
* Block endpoints resolve to the active draft when no explicit `published` query is
  supplied.
* A linked zone reports `block_ids: []`; its blocks come from the zone blocks route.
  The layout payload reports the real `shared` flag and, per zone, the link normalised
  to the published layout id.
* `publish()` publishes a draft only. It loads the draft (`loadDraft()`), so it returns
  false instead of copying the published layout onto itself.

### Share endpoint

`explayouts_share` is created on demand by `ensureShareTable()` and must match the
declaration in `explayouts`' `share/db_schema.dba` (indexes `idx_share_layout` and
`idx_share_token`). MySQL and MongoDB use the MySQL statement (the MongoDB driver reads
the key clauses to build its indexes); SQLite has its own spelling. Each result is
checked: a failure answers 500, `POST` answers 201 only with a stored token, `GET`
always returns the documented list, and `DELETE` reports what happened.

## Templates and assets

| File | Role |
|---|---|
| `design/standard/templates/explayouts_ui_api/app.tpl` | Bootstrap page. Meta tags read by the JavaScript: `nglayouts-route-prefix` (`/explayouts_ui_api`), `nglayouts-base-path`, `ngcb-base-path`, `ezxform-token` |
| `.../spa_strings.tpl` | Defines `window.nglayoutsSpaI18n` from `\|i18n( 'design/standard/explayouts_ui_api/spa' )` calls, keyed by the English text |
| `.../form_block_edit.tpl`, `form_block_fields.tpl`, `form_query_edit.tpl` | Block and query edit forms |
| `design/standard/javascript/netgen-layouts.js` | The editor bundle. Its `l.sync` maps `options.method` (`POST`, `PATCH`, `PUT`, `GET`, `DELETE`) to the CRUD verb so that `create_new_draft` sends `POST` |
| `design/standard/javascript/explayouts-mobile.js`, `stylesheets/explayouts-mobile.css` | Mobile layout |

Override templates through the design cascade. Layout types and block types come from
`explayouts.ini` and take effect after a cache clear; no code change is needed.

## Translations

Context `design/standard/explayouts_ui_api/spa` in `untranslated`, `eng-US` and `ger-DE`
(123 messages, German complete). Sentences built from pieces are single messages with
placeholders (`%kind`, `%language`, `%name`, `%count`) and have singular and plural forms.

## Related

* [Feature page](../../features/6.0/extensions/explayouts_ui_api.md)
* [explayouts_ui](../../features/6.0/extensions/explayouts_ui.md)
* [Chronicle](../../history/extensions/explayouts_ui_api.md) and [release notes](../../changelogs/extensions/explayouts_ui_api.md)
