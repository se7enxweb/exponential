# Change ledger: explayouts_ui_api

Every change made to `explayouts_ui_api` since the se7enxweb era began, oldest first: 58 changes touching 3313 files. Each line gives the date, the commit, what kind of change it is, its message, and how many files it touched (+ lines added / − lines removed). Release tags are marked. The narrative documentation (features, bc notes, specifications, the history chronicle) explains what these changes deliver; this ledger is the complete record they are built from.

| Kind | Changes |
|---|---|
| Updated | 52 |
| Added | 6 |

## 2026-07 (4 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-07-30 | `f920e6d` | Added | Added: Initial import. Import of explayouts_ui_api extension providing jSON HTTP API and SPA shell for the Exponential Layouts admin UI, served under /explayouts_ui_api/app. | 3153 | +31362 / −0 |  |
| 2026-07-30 | `b034090` | Updated | Updated: Updated README.md to the classic 7x documentation standard with a doc/ index and expanded usage information. | 1 | +152 / −13 |  |
| 2026-07-30 | `0f03c46` | Updated | Fixed: Fixed the composer.json license to the SPDX identifier GPL-2.0-or-later required by Packagist. | 1 | +1 / −1 |  |
| 2026-07-30 | `a31f493` | Added | Added: Added .gitattributes with export-ignore rules to keep composer dist archives lean. | 1 | +5 / −0 | v1.0.0 |

## 2026-08 (25 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-08-25 | `18ce4c3` | Updated | Updated: populate missing block parameters, show manual collection items and improve list block preview in the SPA admin | 5 | +73 / −0 |  |
| 2026-08-25 | `007423b` | Updated | Updated: expose default collections in the SPA sidebar with Block/Collection tabs and a working result endpoint. | 3 | +316 / −39 |  |
| 2026-08-25 | `8119189` | Updated | Updated: add remove, remove-all and move endpoints for collection items and fix sidebar tab switching. | 2 | +90 / −5 |  |
| 2026-08-25 | `a0a51e8` | Updated | Updated: add content browser API endpoint and custom modal for adding collection items. | 3 | +179 / −1 |  |
| 2026-08-25 | `3ebd37a` | Updated | Updated: support collection type switching, expose rich parameter metadata, and remove manual save button from block form. | 4 | +24 / −2 |  |
| 2026-08-25 | `74ced49` | Updated | Updated: align SPA sidebar and block preview with Nexus/reference layouts-ui. | 5 | +96 / −48 |  |
| 2026-08-25 | `5f10555` | Updated | Updated: render container blocks with nested placeholders, expose parent_block_id/parent_placeholder, and support DND create/move/copy inside containers. | 1 | +186 / −13 |  |
| 2026-08-25 | `be8acd7` | Updated | Updated: Render title and rich_text blocks with the inline/CKEditor hooks the SPA expects. | 1 | +8 / −1 |  |
| 2026-08-25 | `3232559` | Updated | Updated: render title, text and rich_text blocks with the inline / CKEditor hooks the SPA expects, avoiding empty-content fallback that removes editable targets. | 1 | +10 / −5 |  |
| 2026-08-25 | `0c522f3` | Updated | Updated: SPA API resolves linked zones and skips placeholder blocks. | 1 | +78 / −6 |  |
| 2026-08-27 | `5c494d0` | Updated | Updated: route layout publish/draft/discard through core service | 1 | +358 / −45 |  |
| 2026-08-27 | `144f371` | Updated | Updated: SPA block edit forms and app template for query options | 4 | +341 / −66 |  |
| 2026-08-27 | `32b8fb2` | Added | Added: SPA block content, parameter and query edit templates and admin CSS | 4 | +152 / −0 |  |
| 2026-08-27 | `e89ca34` | Updated | Updated: expose collection offset/limit in the query edit form | 3 | +25 / −1 |  |
| 2026-08-27 | `7acf0c9` | Updated | Updated: make offset/limit section collapsible and separate | 1 | +10 / −8 |  |
| 2026-08-27 | `65991ab` | Updated | Updated: rebrand ibexa_content_search query type in the SPA UI | 3 | +5 / −5 |  |
| 2026-08-27 | `f3c7169` | Updated | Updated: style number inputs in content tab query form | 1 | +1 / −0 |  |
| 2026-08-28 | `2a5ed36` | Updated | Updated: Render View type for blocks without item view types and hide the Items panel for non-collection blocks in the layout editor. | 2 | +11 / −0 |  |
| 2026-08-28 | `570e6ab` | Updated | Updated: adapt SPA block type API and icon assets for tpl_block plus-menu grouping. | 3 | +53 / −24 |  |
| 2026-08-28 | `091a98d` | Updated | Updated: force layout editor icon-* classes to use netgen_layouts font. | 1 | +1 / −1 |  |
| 2026-08-28 | `9d62ea6` | Updated | Updated: API GET /layouts/:id respects published=false/true query. | 1 | +8 / −1 |  |
| 2026-08-28 | `e523272` | Updated | Updated: API block endpoints resolve to the active draft. | 1 | +23 / −11 |  |
| 2026-08-28 | `11de65f` | Updated | Updated: missing-layout 404 modal now returns to admin instead of creating an empty layout. | 1 | +1 / −1 |  |
| 2026-08-28 | `d3d3488` | Updated | Updated: SPA title suffix, create_new_draft HTTP method, cache-bust and add missing MaterialIcons fonts | 7 | +2375 / −2 |  |
| 2026-08-28 | `52fbcea` | Updated | Updated: API dispatcher now catches exceptions and returns a JSON 500 response with a logged backtrace. | 1 | +48 / −35 |  |

## 2026-09 (29 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-09-01 | `074ab92` | Updated | Updated: block edit form and SPA shell styling. | 4 | +406 / −59 |  |
| 2026-09-07 | `4c8241f` | Updated | Fixed: Fixed block ordering being lost on create, copy, move and delete, caused by every block being stored at position 0 so ordering fell back to insertion order and a block could not be placed before an existing one. Siblings are now renumbered around the requested position and compacted after a delete, and an imported rule keeps its own priority. | 1 | +179 / −8 |  |
| 2026-09-07 | `afbab37` | Added | Added: Added editor previews that render a collection as a column grid honouring its column count and a component block as its referenced item, with thumbnails, linked names and value types. Blocks are also labelled from their configured definition name rather than their internal identifier. | 1 | +244 / −6 |  |
| 2026-09-07 | `3973dbf` | Updated | Fixed: Fixed select parameters in the block design form being discarded, caused by every parameter select carrying the class that suppresses the form's debounced auto submit while the form has no submit button, so column counts and view types now save. The content picker also falls back through remote id when no node is stored. | 1 | +19 / −2 |  |
| 2026-09-07 | `2a95edc` | Updated | Fixed: Fixed the block sidebar hanging for a block whose definition has no configuration, caused by method_exists() being called on a false handler which aborts the request under PHP 8, so the editor now shows an empty form instead of loading forever. Also renames the application edition shown in the logo tooltip to Exponential Layouts. | 1 | +14 / −3 |  |
| 2026-09-07 | `390e87b` | Updated | Updated: Updated the layout editor styling to highlight the currently selected block distinctly and to lay out the new collection previews, so the editor shows a block's configuration at a glance. | 1 | +109 / −1 | v1.1.0 |
| 2026-09-14 | `a10bfeb` | Added | Added: Added a mobile layout for the layouts editor, which upstream refuses to draw below 800px, so the canvas takes the full width beside the rail, the properties panel becomes a drawer that opens on tapping a block, and blocks can be dragged by finger. | 3 | +614 / −0 |  |
| 2026-09-14 | `d9d8314` | Updated | Updated: Updated the mobile layouts editor to open the block options from a toggle in the rail under the add-block button instead of a pill floating over the layout, so the current block's options can always be reached from one fixed place rather than only by tapping the block. | 2 | +74 / −27 |  |
| 2026-09-14 | `a292ed0` | Updated | Updated: Updated the mobile layouts editor to stop opening the properties drawer when a block is selected, which covered the layout on every tap, so selecting and inspecting are now separate and the rail toggle turns accent coloured to say a block is waiting. | 4 | +22 / −79 | v1.2.0 |
| 2026-09-17 | `f79d59c` | Updated | Fixed: Fixed the layout editor presenting a shared header and breadcrumb as editable blocks of the layout being edited, so a linked zone is drawn inherited and locked. | 1 | +192 / −31 | v1.2.1 |
| 2026-09-17 | `e081f3d` | Updated | Fixed: Fixed the block menu drawing the same default icon for most block types, by declaring an icon per type instead of leaving every one of them to a font that has a glyph for fewer than half. | 31 | +410 / −1 | v1.2.2 |
| 2026-09-19 | `569a2b7` | Updated | Fixed: Fixed the share endpoint reporting success for tokens it never stored, caused by MySQL-only DDL no other engine accepts and three unchecked query results, so a share token now either exists or the caller is told it does not. | 1 | +82 / −11 | v1.2.3 |
| 2026-09-20 | `2f998a9` | Updated | Fixed: Fixed the share table being created with index names no schema declares, so the system upgrade page no longer reports the database as inconsistent after a layout has been shared. | 1 | +12 / −4 | v1.2.4 |
| 2026-09-22 | `8f53d5d` | Updated | Fixed: Fixed the module views declaring functions and classes at the top level, so this extension can be served by a web server that keeps a PHP process alive across requests. | 3 | +75 / −63 | v1.3.0 |
| 2026-09-27 | `37f3bf8` | Updated | Fixed: The layout editor works under a siteaccess reached by path, such as /admin | 2 | +67 / −20 |  |
| 2026-09-27 | `4984275` | Updated | Fixed: The editor's View in CMS links carry the siteaccess prefix | 1 | +4 / −0 | v1.3.1 |
| 2026-09-27 | `aca9442` | Updated | Fixed: The ezinfo.php declares its keys as strings, so the about page shows the extension's name, version and license | 2 | +6 / −6 | v1.3.2 |
| 2026-09-27 | `c24c800` | Updated | Fixed: The layout editor API refuses a write that does not carry the session's form token | 1 | +71 / −0 |  |
| 2026-09-27 | `82d74d6` | Updated | Updated: The layout editor adds the form token to every write it sends | 1 | +44 / −2 |  |
| 2026-09-27 | `df773b8` | Updated | Updated: Version 1.3.3 | 2 | +2 / −2 | v1.3.3 |
| 2026-09-28 | `beff303` | Updated | Fixed: The layout editor API changes blocks only on a draft; a published layout's blocks change when its draft is published | 1 | +60 / −21 |  |
| 2026-09-28 | `b26716a` | Updated | Updated: Version 1.3.4 | 2 | +2 / −2 | v1.3.4 |
| 2026-09-28 | `a738a8e` | Updated | Updated: Every visible text of the extension is a translation string, with German | 14 | +916 / −75 |  |
| 2026-09-28 | `4d03c4b` | Updated | Updated: Version 1.3.5 | 2 | +2 / −2 | v1.3.5 |
| 2026-09-28 | `fa4cc31` | Added | Added: GitHub funding metadata, the same as the other se7enxweb packages | 1 | +3 / −0 |  |
| 2026-09-29 | `d4a02cf` | Updated | Updated: The layout editor shows its interface texts in the interface language, with German | 7 | +1631 / −6 |  |
| 2026-09-29 | `b12f37d` | Updated | Updated: Version 1.3.6 | 2 | +2 / −2 | v1.3.6 |
| 2026-09-30 | `4752b22` | Updated | Updated: The about page names the extension Exponential Layouts UI API | 2 | +2 / −2 |  |
| 2026-09-30 | `3b76ac9` | Updated | Updated: Version 1.3.7 | 2 | +2 / −2 | v1.3.7 |
