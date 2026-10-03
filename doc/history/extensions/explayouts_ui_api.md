# explayouts_ui_api (the layout editor): chronicle

The layout editor API and single-page app was imported on 30 July 2026 and grew quickly in August (collections, containers, linked zones, content browser), then hardened in September: block ordering, shared and locked zones, a mobile editor, SQLite and index fixes for the share table, a siteaccess prefix, the form token on every write, writes only on drafts, and German texts. See the [feature page](../../features/6.0/extensions/explayouts_ui_api.md) and the [specification](../../specifications/6.0/explayouts-ui-api.md).

This page lists **every one of the 58 changes** of the repository `explayouts_ui_api` between 2026-07-30 and 2026-09-30, by month, with what kind of change each is. The complete machine-made record, with sizes, is the [change ledger](../ledger/explayouts_ui_api.md); what each release contains is in the [release notes](../../changelogs/extensions/explayouts_ui_api.md); how to use the extension is on its [feature page](../../features/6.0/extensions/explayouts_ui_api.md).

| Kind | Changes |
|---|---|
| feature | 33 |
| fix | 13 |
| security | 2 |
| docs | 3 |
| tooling | 1 |
| release | 5 |
| no user benefit | 1 |

## Releases

| Date | Release | Commit |
|---|---|---|
| 2026-07-30 | v1.0.0 | [`a31f493`](https://github.com/se7enxweb/explayouts_ui_api/commit/a31f493) |
| 2026-09-07 | v1.1.0 | [`390e87b`](https://github.com/se7enxweb/explayouts_ui_api/commit/390e87b) |
| 2026-09-14 | v1.2.0 | [`a292ed0`](https://github.com/se7enxweb/explayouts_ui_api/commit/a292ed0) |
| 2026-09-17 | v1.2.1 | [`f79d59c`](https://github.com/se7enxweb/explayouts_ui_api/commit/f79d59c) |
| 2026-09-17 | v1.2.2 | [`e081f3d`](https://github.com/se7enxweb/explayouts_ui_api/commit/e081f3d) |
| 2026-09-19 | v1.2.3 | [`569a2b7`](https://github.com/se7enxweb/explayouts_ui_api/commit/569a2b7) |
| 2026-09-20 | v1.2.4 | [`2f998a9`](https://github.com/se7enxweb/explayouts_ui_api/commit/2f998a9) |
| 2026-09-22 | v1.3.0 | [`8f53d5d`](https://github.com/se7enxweb/explayouts_ui_api/commit/8f53d5d) |
| 2026-09-27 | v1.3.1 | [`4984275`](https://github.com/se7enxweb/explayouts_ui_api/commit/4984275) |
| 2026-09-27 | v1.3.2 | [`aca9442`](https://github.com/se7enxweb/explayouts_ui_api/commit/aca9442) |
| 2026-09-27 | v1.3.3 | [`df773b8`](https://github.com/se7enxweb/explayouts_ui_api/commit/df773b8) |
| 2026-09-28 | v1.3.4 | [`b26716a`](https://github.com/se7enxweb/explayouts_ui_api/commit/b26716a) |
| 2026-09-28 | v1.3.5 | [`4d03c4b`](https://github.com/se7enxweb/explayouts_ui_api/commit/4d03c4b) |
| 2026-09-29 | v1.3.6 | [`b12f37d`](https://github.com/se7enxweb/explayouts_ui_api/commit/b12f37d) |
| 2026-09-30 | v1.3.7 | [`3b76ac9`](https://github.com/se7enxweb/explayouts_ui_api/commit/3b76ac9) |

## Timeline

### 2026-07

The month across all extensions: [July 2026](months/2026-07.md). [Ledger of this month](../ledger/explayouts_ui_api.md#2026-07-4-changes).

- 2026-07-30 [`f920e6d`](https://github.com/se7enxweb/explayouts_ui_api/commit/f920e6d) (feature) Initial import. Import of explayouts_ui_api extension providing jSON HTTP API and SPA shell for the Exponential Layouts admin UI, served under /explayouts_ui_api/app.
- 2026-07-30 [`b034090`](https://github.com/se7enxweb/explayouts_ui_api/commit/b034090) (docs) Updated README.md to the classic 7x documentation standard with a doc/ index and expanded usage information.
- 2026-07-30 [`0f03c46`](https://github.com/se7enxweb/explayouts_ui_api/commit/0f03c46) (fix) Fixed: Fixed the composer.json license to the SPDX identifier GPL-2.0-or-later required by Packagist.
- 2026-07-30 [`a31f493`](https://github.com/se7enxweb/explayouts_ui_api/commit/a31f493) (tooling) Added .gitattributes with export-ignore rules to keep composer dist archives lean. **Release v1.0.0.**

### 2026-08

The month across all extensions: [August 2026](months/2026-08.md). [Ledger of this month](../ledger/explayouts_ui_api.md#2026-08-25-changes).

- 2026-08-25 [`18ce4c3`](https://github.com/se7enxweb/explayouts_ui_api/commit/18ce4c3) (feature) populate missing block parameters, show manual collection items and improve list block preview in the SPA admin
- 2026-08-25 [`007423b`](https://github.com/se7enxweb/explayouts_ui_api/commit/007423b) (feature) expose default collections in the SPA sidebar with Block/Collection tabs and a working result endpoint.
- 2026-08-25 [`8119189`](https://github.com/se7enxweb/explayouts_ui_api/commit/8119189) (feature) add remove, remove-all and move endpoints for collection items and fix sidebar tab switching.
- 2026-08-25 [`a0a51e8`](https://github.com/se7enxweb/explayouts_ui_api/commit/a0a51e8) (feature) add content browser API endpoint and custom modal for adding collection items.
- 2026-08-25 [`3ebd37a`](https://github.com/se7enxweb/explayouts_ui_api/commit/3ebd37a) (feature) support collection type switching, expose rich parameter metadata, and remove manual save button from block form.
- 2026-08-25 [`74ced49`](https://github.com/se7enxweb/explayouts_ui_api/commit/74ced49) (feature) align SPA sidebar and block preview with Nexus/reference layouts-ui.
- 2026-08-25 [`5f10555`](https://github.com/se7enxweb/explayouts_ui_api/commit/5f10555) (feature) render container blocks with nested placeholders, expose parent_block_id/parent_placeholder, and support DND create/move/copy inside containers.
- 2026-08-25 [`be8acd7`](https://github.com/se7enxweb/explayouts_ui_api/commit/be8acd7) (feature) Render title and rich_text blocks with the inline/CKEditor hooks the SPA expects.
- 2026-08-25 [`3232559`](https://github.com/se7enxweb/explayouts_ui_api/commit/3232559) (feature) render title, text and rich_text blocks with the inline / CKEditor hooks the SPA expects, avoiding empty-content fallback that removes editable targets.
- 2026-08-25 [`0c522f3`](https://github.com/se7enxweb/explayouts_ui_api/commit/0c522f3) (feature) SPA API resolves linked zones and skips placeholder blocks.
- 2026-08-27 [`5c494d0`](https://github.com/se7enxweb/explayouts_ui_api/commit/5c494d0) (feature) route layout publish/draft/discard through core service
- 2026-08-27 [`144f371`](https://github.com/se7enxweb/explayouts_ui_api/commit/144f371) (feature) SPA block edit forms and app template for query options
- 2026-08-27 [`32b8fb2`](https://github.com/se7enxweb/explayouts_ui_api/commit/32b8fb2) (feature) SPA block content, parameter and query edit templates and admin CSS
- 2026-08-27 [`e89ca34`](https://github.com/se7enxweb/explayouts_ui_api/commit/e89ca34) (feature) expose collection offset/limit in the query edit form
- 2026-08-27 [`7acf0c9`](https://github.com/se7enxweb/explayouts_ui_api/commit/7acf0c9) (feature) make offset/limit section collapsible and separate
- 2026-08-27 [`65991ab`](https://github.com/se7enxweb/explayouts_ui_api/commit/65991ab) (docs) rebrand ibexa_content_search query type in the SPA UI
- 2026-08-27 [`f3c7169`](https://github.com/se7enxweb/explayouts_ui_api/commit/f3c7169) (feature) style number inputs in content tab query form
- 2026-08-28 [`2a5ed36`](https://github.com/se7enxweb/explayouts_ui_api/commit/2a5ed36) (feature) Render View type for blocks without item view types and hide the Items panel for non-collection blocks in the layout editor.
- 2026-08-28 [`570e6ab`](https://github.com/se7enxweb/explayouts_ui_api/commit/570e6ab) (feature) adapt SPA block type API and icon assets for tpl_block plus-menu grouping.
- 2026-08-28 [`091a98d`](https://github.com/se7enxweb/explayouts_ui_api/commit/091a98d) (feature) force layout editor icon-* classes to use netgen_layouts font.
- 2026-08-28 [`9d62ea6`](https://github.com/se7enxweb/explayouts_ui_api/commit/9d62ea6) (feature) API GET /layouts/:id respects published=false/true query.
- 2026-08-28 [`e523272`](https://github.com/se7enxweb/explayouts_ui_api/commit/e523272) (feature) API block endpoints resolve to the active draft.
- 2026-08-28 [`11de65f`](https://github.com/se7enxweb/explayouts_ui_api/commit/11de65f) (feature) missing-layout 404 modal now returns to admin instead of creating an empty layout.
- 2026-08-28 [`d3d3488`](https://github.com/se7enxweb/explayouts_ui_api/commit/d3d3488) (feature) SPA title suffix, create_new_draft HTTP method, cache-bust and add missing MaterialIcons fonts
- 2026-08-28 [`52fbcea`](https://github.com/se7enxweb/explayouts_ui_api/commit/52fbcea) (feature) API dispatcher now catches exceptions and returns a JSON 500 response with a logged backtrace.

### 2026-09

The month across all extensions: [September 2026](months/2026-09.md). [Ledger of this month](../ledger/explayouts_ui_api.md#2026-09-29-changes).

- 2026-09-01 [`074ab92`](https://github.com/se7enxweb/explayouts_ui_api/commit/074ab92) (feature) block edit form and SPA shell styling.
- 2026-09-07 [`4c8241f`](https://github.com/se7enxweb/explayouts_ui_api/commit/4c8241f) (fix) Fixed: Fixed block ordering being lost on create, copy, move and delete, caused by every block being stored at position 0 so ordering fell back to insertion order and a block could not be placed before an existing one. Siblings are now renumbered around the requested position and compacted after a delete, and an imported rule...
- 2026-09-07 [`afbab37`](https://github.com/se7enxweb/explayouts_ui_api/commit/afbab37) (feature) Added editor previews that render a collection as a column grid honouring its column count and a component block as its referenced item, with thumbnails, linked names and value types. Blocks are also labelled from their configured definition name rather than their internal identifier.
- 2026-09-07 [`3973dbf`](https://github.com/se7enxweb/explayouts_ui_api/commit/3973dbf) (fix) Fixed: Fixed select parameters in the block design form being discarded, caused by every parameter select carrying the class that suppresses the form's debounced auto submit while the form has no submit button, so column counts and view types now save. The content picker also falls back through remote id when no node is stored.
- 2026-09-07 [`2a95edc`](https://github.com/se7enxweb/explayouts_ui_api/commit/2a95edc) (fix) Fixed: Fixed the block sidebar hanging for a block whose definition has no configuration, caused by method_exists() being called on a false handler which aborts the request under PHP 8, so the editor now shows an empty form instead of loading forever. Also renames the application edition shown in the logo tooltip to Exponenti...
- 2026-09-07 [`390e87b`](https://github.com/se7enxweb/explayouts_ui_api/commit/390e87b) (feature) Updated the layout editor styling to highlight the currently selected block distinctly and to lay out the new collection previews, so the editor shows a block's configuration at a glance. **Release v1.1.0.**
- 2026-09-14 [`a10bfeb`](https://github.com/se7enxweb/explayouts_ui_api/commit/a10bfeb) (feature) Added a mobile layout for the layouts editor, which upstream refuses to draw below 800px, so the canvas takes the full width beside the rail, the properties panel becomes a drawer that opens on tapping a block, and blocks can be dragged by finger.
- 2026-09-14 [`d9d8314`](https://github.com/se7enxweb/explayouts_ui_api/commit/d9d8314) (feature) Updated the mobile layouts editor to open the block options from a toggle in the rail under the add-block button instead of a pill floating over the layout, so the current block's options can always be reached from one fixed place rather than only by tapping the block.
- 2026-09-14 [`a292ed0`](https://github.com/se7enxweb/explayouts_ui_api/commit/a292ed0) (feature) Updated the mobile layouts editor to stop opening the properties drawer when a block is selected, which covered the layout on every tap, so selecting and inspecting are now separate and the rail toggle turns accent coloured to say a block is waiting. **Release v1.2.0.**
- 2026-09-17 [`f79d59c`](https://github.com/se7enxweb/explayouts_ui_api/commit/f79d59c) (fix) Fixed: Fixed the layout editor presenting a shared header and breadcrumb as editable blocks of the layout being edited, so a linked zone is drawn inherited and locked. **Release v1.2.1.**
- 2026-09-17 [`e081f3d`](https://github.com/se7enxweb/explayouts_ui_api/commit/e081f3d) (fix) Fixed: Fixed the block menu drawing the same default icon for most block types, by declaring an icon per type instead of leaving every one of them to a font that has a glyph for fewer than half. **Release v1.2.2.**
- 2026-09-19 [`569a2b7`](https://github.com/se7enxweb/explayouts_ui_api/commit/569a2b7) (fix) Fixed: Fixed the share endpoint reporting success for tokens it never stored, caused by MySQL-only DDL no other engine accepts and three unchecked query results, so a share token now either exists or the caller is told it does not. **Release v1.2.3.**
- 2026-09-20 [`2f998a9`](https://github.com/se7enxweb/explayouts_ui_api/commit/2f998a9) (fix) Fixed: Fixed the share table being created with index names no schema declares, so the system upgrade page no longer reports the database as inconsistent after a layout has been shared. **Release v1.2.4.**
- 2026-09-22 [`8f53d5d`](https://github.com/se7enxweb/explayouts_ui_api/commit/8f53d5d) (fix) Fixed: Fixed the module views declaring functions and classes at the top level, so this extension can be served by a web server that keeps a PHP process alive across requests. **Release v1.3.0.**
- 2026-09-27 [`37f3bf8`](https://github.com/se7enxweb/explayouts_ui_api/commit/37f3bf8) (fix) Fixed: The layout editor works under a siteaccess reached by path, such as /admin
- 2026-09-27 [`4984275`](https://github.com/se7enxweb/explayouts_ui_api/commit/4984275) (fix) Fixed: The editor's View in CMS links carry the siteaccess prefix **Release v1.3.1.**
- 2026-09-27 [`aca9442`](https://github.com/se7enxweb/explayouts_ui_api/commit/aca9442) (fix) Fixed: The ezinfo.php declares its keys as strings, so the about page shows the extension's name, version and license **Release v1.3.2.**
- 2026-09-27 [`c24c800`](https://github.com/se7enxweb/explayouts_ui_api/commit/c24c800) (security) Fixed: The layout editor API refuses a write that does not carry the session's form token
- 2026-09-27 [`82d74d6`](https://github.com/se7enxweb/explayouts_ui_api/commit/82d74d6) (security) The layout editor adds the form token to every write it sends
- 2026-09-27 [`df773b8`](https://github.com/se7enxweb/explayouts_ui_api/commit/df773b8) (release) Version 1.3.3 **Release v1.3.3.**
- 2026-09-28 [`beff303`](https://github.com/se7enxweb/explayouts_ui_api/commit/beff303) (fix) Fixed: The layout editor API changes blocks only on a draft; a published layout's blocks change when its draft is published
- 2026-09-28 [`b26716a`](https://github.com/se7enxweb/explayouts_ui_api/commit/b26716a) (release) Version 1.3.4 **Release v1.3.4.**
- 2026-09-28 [`a738a8e`](https://github.com/se7enxweb/explayouts_ui_api/commit/a738a8e) (feature) Every visible text of the extension is a translation string, with German
- 2026-09-28 [`4d03c4b`](https://github.com/se7enxweb/explayouts_ui_api/commit/4d03c4b) (release) Version 1.3.5 **Release v1.3.5.**
- 2026-09-28 [`fa4cc31`](https://github.com/se7enxweb/explayouts_ui_api/commit/fa4cc31) (no user benefit) GitHub funding metadata, the same as the other se7enxweb packages
- 2026-09-29 [`d4a02cf`](https://github.com/se7enxweb/explayouts_ui_api/commit/d4a02cf) (feature) The layout editor shows its interface texts in the interface language, with German
- 2026-09-29 [`b12f37d`](https://github.com/se7enxweb/explayouts_ui_api/commit/b12f37d) (release) Version 1.3.6 **Release v1.3.6.**
- 2026-09-30 [`4752b22`](https://github.com/se7enxweb/explayouts_ui_api/commit/4752b22) (docs) The about page names the extension Exponential Layouts UI API
- 2026-09-30 [`3b76ac9`](https://github.com/se7enxweb/explayouts_ui_api/commit/3b76ac9) (release) Version 1.3.7 **Release v1.3.7.**

### Added after the ledger was cut (30 September to 1 October 2026)

These commits were pushed after the machine-made ledger of this repository was extracted; they are listed here so the page stays complete.

- 2026-09-30 `54efe41` (fix) Layout share links work on Oracle (v1.3.8)
- 2026-10-01 `8273a03` (feature) The editor runs on ezjscore's jQuery 4 and jQuery UI 1.14 (v1.3.9)
- 2026-10-01 `eeafe10` (fix) The Markdown and HTML blocks have their code editor again (v1.3.9)
- 2026-10-01 `a9dfe30` (fix) The Roboto italic face ships (v1.3.9)
- Releases v1.3.8 (30 September) and v1.3.9 (1 October)

## Related

* [Feature page](../../features/6.0/extensions/explayouts_ui_api.md)
* [Release notes](../../changelogs/extensions/explayouts_ui_api.md)
* [Change ledger](../ledger/explayouts_ui_api.md)
* [Specification](../../specifications/6.0/explayouts-ui-api.md)
