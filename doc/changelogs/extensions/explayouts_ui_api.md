# explayouts_ui_api (the layout editor): release notes

Read this page before you install or update `explayouts_ui_api`, or to find out which release brought a change.

What each release of `explayouts_ui_api` contains, assembled from its commits. Each line links its commit. Version bumps, merge commits and funding or package metadata that deliver nothing to a user are counted, not listed. The extension is described on its [feature page](../../features/6.0/extensions/explayouts_ui_api.md); the story is in the [chronicle](../../history/extensions/explayouts_ui_api.md).

## v1.3.9 (2026-10-01)

**Updated**

- The layout editor runs on ezjscore's jQuery 4 and jQuery UI 1.14 instead of the jQuery 2.2.4 and jQuery UI 1.10.4 built into its bundle, and its page tags, globals and texts carry `explayouts` names, so the whole admin runs on one jQuery 4 (`8273a03`)
- The Markdown and HTML blocks in the editor have a code editor again: their preview now carries the `ace-editor` element the editor is built on, so opening such a block no longer fails and its text can be edited and saved (`eeafe10`)
- The Roboto italic face the editor's stylesheet points to ships, so italic text shows in Roboto and three font requests no longer fail (`a9dfe30`)

## v1.3.8 (2026-09-30)

**Updated**

- Layout share links work on Oracle: the `explayouts_share` table (with its sequence, trigger and the indexes `idx_share_layout` and `idx_share_token`) is created with Oracle's own statements when missing (`54efe41`)

## v1.3.7 (2026-09-30)

**Updated**

- The about page names the extension Exponential Layouts UI API ([`4752b22`](https://github.com/se7enxweb/explayouts_ui_api/commit/4752b22))

1 version, merge or metadata commit not listed.

## v1.3.6 (2026-09-29)

**Updated**

- The layout editor shows its interface texts in the interface language, with German ([`d4a02cf`](https://github.com/se7enxweb/explayouts_ui_api/commit/d4a02cf))

2 version, merge or metadata commits not listed.

## v1.3.5 (2026-09-28)

**Updated**

- Every visible text of the extension is a translation string, with German ([`a738a8e`](https://github.com/se7enxweb/explayouts_ui_api/commit/a738a8e))

1 version, merge or metadata commit not listed.

## v1.3.4 (2026-09-28)

**Updated**

- Fixed: The layout editor API changes blocks only on a draft; a published layout's blocks change when its draft is published ([`beff303`](https://github.com/se7enxweb/explayouts_ui_api/commit/beff303))

1 version, merge or metadata commit not listed.

## v1.3.3 (2026-09-27)

**Updated**

- Fixed: The layout editor API refuses a write that does not carry the session's form token ([`c24c800`](https://github.com/se7enxweb/explayouts_ui_api/commit/c24c800))
- The layout editor adds the form token to every write it sends ([`82d74d6`](https://github.com/se7enxweb/explayouts_ui_api/commit/82d74d6))

1 version, merge or metadata commit not listed.

## v1.3.2 (2026-09-27)

**Updated**

- Fixed: The ezinfo.php declares its keys as strings, so the about page shows the extension's name, version and license ([`aca9442`](https://github.com/se7enxweb/explayouts_ui_api/commit/aca9442))

## v1.3.1 (2026-09-27)

**Updated**

- Fixed: The layout editor works under a siteaccess reached by path, such as /admin ([`37f3bf8`](https://github.com/se7enxweb/explayouts_ui_api/commit/37f3bf8))
- Fixed: The editor's View in CMS links carry the siteaccess prefix ([`4984275`](https://github.com/se7enxweb/explayouts_ui_api/commit/4984275))

## v1.3.0 (2026-09-22)

**Updated**

- Fixed the module views declaring functions and classes at the top level, so this extension can be served by a web server that keeps a PHP process alive across requests. ([`8f53d5d`](https://github.com/se7enxweb/explayouts_ui_api/commit/8f53d5d))

## v1.2.4 (2026-09-20)

**Updated**

- Fixed the share table being created with index names no schema declares, so the system upgrade page no longer reports the database as inconsistent after a layout has been shared. ([`2f998a9`](https://github.com/se7enxweb/explayouts_ui_api/commit/2f998a9))

## v1.2.3 (2026-09-19)

**Updated**

- Fixed the share endpoint reporting success for tokens it never stored, caused by MySQL-only DDL no other engine accepts and three unchecked query results, so a share token now either exists or the caller is told it does not. ([`569a2b7`](https://github.com/se7enxweb/explayouts_ui_api/commit/569a2b7))

## v1.2.2 (2026-09-17)

**Updated**

- Fixed the block menu drawing the same default icon for most block types, by declaring an icon per type instead of leaving every one of them to a font that has a glyph for fewer than half. ([`e081f3d`](https://github.com/se7enxweb/explayouts_ui_api/commit/e081f3d))

## v1.2.1 (2026-09-17)

**Updated**

- Fixed the layout editor presenting a shared header and breadcrumb as editable blocks of the layout being edited, so a linked zone is drawn inherited and locked. ([`f79d59c`](https://github.com/se7enxweb/explayouts_ui_api/commit/f79d59c))

## v1.2.0 (2026-09-14)

**Added**

- Added a mobile layout for the layouts editor, which upstream refuses to draw below 800px, so the canvas takes the full width beside the rail, the properties panel becomes a drawer that opens on tapping a block, and blocks can be dragged by finger. ([`a10bfeb`](https://github.com/se7enxweb/explayouts_ui_api/commit/a10bfeb))

**Updated**

- Updated the mobile layouts editor to open the block options from a toggle in the rail under the add-block button instead of a pill floating over the layout, so the current block's options can always be reached from one fixed place rather than only by tapping the block. ([`d9d8314`](https://github.com/se7enxweb/explayouts_ui_api/commit/d9d8314))
- Updated the mobile layouts editor to stop opening the properties drawer when a block is selected, which covered the layout on every tap, so selecting and inspecting are now separate and the rail toggle turns accent coloured to say a block is waiting. ([`a292ed0`](https://github.com/se7enxweb/explayouts_ui_api/commit/a292ed0))

## v1.1.0 (2026-09-07)

**Added**

- SPA block content, parameter and query edit templates and admin CSS ([`32b8fb2`](https://github.com/se7enxweb/explayouts_ui_api/commit/32b8fb2))
- Added editor previews that render a collection as a column grid honouring its column count and a component block as its referenced item, with thumbnails, linked names and value types. Blocks are also labelled from their configured definition name rather than their internal identifier. ([`afbab37`](https://github.com/se7enxweb/explayouts_ui_api/commit/afbab37))

**Updated**

- Populate missing block parameters, show manual collection items and improve list block preview in the SPA admin ([`18ce4c3`](https://github.com/se7enxweb/explayouts_ui_api/commit/18ce4c3))
- Expose default collections in the SPA sidebar with Block/Collection tabs and a working result endpoint. ([`007423b`](https://github.com/se7enxweb/explayouts_ui_api/commit/007423b))
- Add remove, remove-all and move endpoints for collection items and fix sidebar tab switching. ([`8119189`](https://github.com/se7enxweb/explayouts_ui_api/commit/8119189))
- Add content browser API endpoint and custom modal for adding collection items. ([`a0a51e8`](https://github.com/se7enxweb/explayouts_ui_api/commit/a0a51e8))
- Support collection type switching, expose rich parameter metadata, and remove manual save button from block form. ([`3ebd37a`](https://github.com/se7enxweb/explayouts_ui_api/commit/3ebd37a))
- Align SPA sidebar and block preview with Nexus/reference layouts-ui. ([`74ced49`](https://github.com/se7enxweb/explayouts_ui_api/commit/74ced49))
- Render container blocks with nested placeholders, expose parent_block_id/parent_placeholder, and support DND create/move/copy inside containers. ([`5f10555`](https://github.com/se7enxweb/explayouts_ui_api/commit/5f10555))
- Render title and rich_text blocks with the inline/CKEditor hooks the SPA expects. ([`be8acd7`](https://github.com/se7enxweb/explayouts_ui_api/commit/be8acd7))
- Render title, text and rich_text blocks with the inline / CKEditor hooks the SPA expects, avoiding empty-content fallback that removes editable targets. ([`3232559`](https://github.com/se7enxweb/explayouts_ui_api/commit/3232559))
- SPA API resolves linked zones and skips placeholder blocks. ([`0c522f3`](https://github.com/se7enxweb/explayouts_ui_api/commit/0c522f3))
- Route layout publish/draft/discard through core service ([`5c494d0`](https://github.com/se7enxweb/explayouts_ui_api/commit/5c494d0))
- SPA block edit forms and app template for query options ([`144f371`](https://github.com/se7enxweb/explayouts_ui_api/commit/144f371))
- Expose collection offset/limit in the query edit form ([`e89ca34`](https://github.com/se7enxweb/explayouts_ui_api/commit/e89ca34))
- Make offset/limit section collapsible and separate ([`7acf0c9`](https://github.com/se7enxweb/explayouts_ui_api/commit/7acf0c9))
- Style number inputs in content tab query form ([`f3c7169`](https://github.com/se7enxweb/explayouts_ui_api/commit/f3c7169))
- Render View type for blocks without item view types and hide the Items panel for non-collection blocks in the layout editor. ([`2a5ed36`](https://github.com/se7enxweb/explayouts_ui_api/commit/2a5ed36))
- Adapt SPA block type API and icon assets for tpl_block plus-menu grouping. ([`570e6ab`](https://github.com/se7enxweb/explayouts_ui_api/commit/570e6ab))
- Force layout editor icon-* classes to use netgen_layouts font. ([`091a98d`](https://github.com/se7enxweb/explayouts_ui_api/commit/091a98d))
- API GET /layouts/:id respects published=false/true query. ([`9d62ea6`](https://github.com/se7enxweb/explayouts_ui_api/commit/9d62ea6))
- API block endpoints resolve to the active draft. ([`e523272`](https://github.com/se7enxweb/explayouts_ui_api/commit/e523272))
- missing-layout 404 modal now returns to admin instead of creating an empty layout. ([`11de65f`](https://github.com/se7enxweb/explayouts_ui_api/commit/11de65f))
- SPA title suffix, create_new_draft HTTP method, cache-bust and add missing MaterialIcons fonts ([`d3d3488`](https://github.com/se7enxweb/explayouts_ui_api/commit/d3d3488))
- API dispatcher now catches exceptions and returns a JSON 500 response with a logged backtrace. ([`52fbcea`](https://github.com/se7enxweb/explayouts_ui_api/commit/52fbcea))
- Block edit form and SPA shell styling. ([`074ab92`](https://github.com/se7enxweb/explayouts_ui_api/commit/074ab92))
- Fixed block ordering being lost on create, copy, move and delete, caused by every block being stored at position 0 so ordering fell back to insertion order and a block could not be placed before an existing one. Siblings are now renumbered around the requested position and compacted after a delete, and an imported rule... ([`4c8241f`](https://github.com/se7enxweb/explayouts_ui_api/commit/4c8241f))
- Fixed select parameters in the block design form being discarded, caused by every parameter select carrying the class that suppresses the form's debounced auto submit while the form has no submit button, so column counts and view types now save. The content picker also falls back through remote id when no node is stored. ([`3973dbf`](https://github.com/se7enxweb/explayouts_ui_api/commit/3973dbf))
- Fixed the block sidebar hanging for a block whose definition has no configuration, caused by method_exists() being called on a false handler which aborts the request under PHP 8, so the editor now shows an empty form instead of loading forever. Also renames the application edition shown in the logo tooltip to Exponenti... ([`2a95edc`](https://github.com/se7enxweb/explayouts_ui_api/commit/2a95edc))
- Updated the layout editor styling to highlight the currently selected block distinctly and to lay out the new collection previews, so the editor shows a block's configuration at a glance. ([`390e87b`](https://github.com/se7enxweb/explayouts_ui_api/commit/390e87b))
- Rebrand ibexa_content_search query type in the SPA UI ([`65991ab`](https://github.com/se7enxweb/explayouts_ui_api/commit/65991ab))

## v1.0.0 (2026-07-30)

**Added**

- Initial import. Import of explayouts_ui_api extension providing jSON HTTP API and SPA shell for the Exponential Layouts admin UI, served under /explayouts_ui_api/app. ([`f920e6d`](https://github.com/se7enxweb/explayouts_ui_api/commit/f920e6d))

**Updated**

- Fixed the composer.json license to the SPDX identifier GPL-2.0-or-later required by Packagist. ([`0f03c46`](https://github.com/se7enxweb/explayouts_ui_api/commit/0f03c46))
- Updated README.md to the classic 7x documentation standard with a doc/ index and expanded usage information. ([`b034090`](https://github.com/se7enxweb/explayouts_ui_api/commit/b034090))
- Added .gitattributes with export-ignore rules to keep composer dist archives lean. ([`a31f493`](https://github.com/se7enxweb/explayouts_ui_api/commit/a31f493))

## Related pages

- [Feature page](../../features/6.0/extensions/explayouts_ui_api.md)
- [Chronicle](../../history/extensions/explayouts_ui_api.md)
- [Change ledger](../../history/ledger/explayouts_ui_api.md)
- [Specification](../../specifications/6.0/explayouts-ui-api.md)
- [Behaviour changes of the extensions](../../bc/6.0/extensions-behaviour-changes.md)
- Months: [2026-07](../../history/extensions/months/2026-07.md), [2026-08](../../history/extensions/months/2026-08.md), [2026-09](../../history/extensions/months/2026-09.md)
