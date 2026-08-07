# Exponential Layouts

This document describes the **Exponential Layouts** subsystem in Exponential CMS 6.0, a port of the Netgen Layouts concept to the Exponential legacy stack. It is intended for operators, integrators, and developers who need to understand, configure, or extend the layout engine.

## What it is

Exponential Layouts lets editors compose pages from reusable **blocks** arranged in **zones** inside a **layout**. A **rule resolver** decides which layout is shown for each request path, node, or URL prefix. Content can be bound to blocks manually or pulled dynamically through query handlers.

The implementation is deliberately modular:

- `extension/explayouts` — core persistence, rendering, and resolution
- `extension/explayouts_core` — domain services (layout, block, zone, rule, collection)
- `extension/explayouts_standard` — standard block handlers
- `extension/explayouts_ui` — legacy admin module (form-based editor)
- `extension/explayouts_ui_api` — SPA shell and JSON API
- `extension/explayouts_content_browser` — content picker
- `extension/explayouts_site_api` — Exponential site-API value converters

## Data model

The database layer is expressed through `eZPersistentObject` classes and a matching set of tables.

| Table | Purpose | Persistent object |
|---|---|---|
| `explayouts_layout` | A named layout with identifier, type, and status | `expLayoutsLayout` |
| `explayouts_zone` | A region inside a layout; may link to another layout | `expLayoutsZone` |
| `explayouts_block` | A block placed in a zone, with definition and view type | `expLayoutsBlock` |
| `explayouts_block_parameter` | Key/value settings for a block | `expLayoutsBlockParameter` |
| `explayouts_collection` | A list attached to a block (manual or dynamic) | `expLayoutsCollection` |
| `explayouts_collection_item` | Manually pinned items inside a collection | `expLayoutsCollectionItem` |
| `explayouts_collection_query` | Query parameters for a dynamic collection | `expLayoutsCollectionQuery` |
| `explayouts_rule` | A resolver rule with priority and enabled flag | `expLayoutsRule` |
| `explayouts_rule_target` | Target condition of a rule (path, node, subtree) | `expLayoutsRuleTarget` |
| `explayouts_rule_condition` | Extra conditions (siteaccess, content type) | `expLayoutsRuleCondition` |

Each table has:

- a MySQL schema in `extension/explayouts/sql/mysql/schema.sql`
- a SQLite schema in `extension/explayouts/sql/sqlite/schema.sql`
- a PostgreSQL schema in `extension/explayouts/sql/pgsql/schema.sql`
- an Oracle schema in `extension/explayouts/sql/oracle/schema.sql`

Oracle support uses `NUMBER(11,0)` for integer columns, `CLOB` for long text, plus one sequence and trigger per table to emulate auto-increment behavior.

## Block and query handlers

Blocks are registered in `extension/explayouts/settings/explayouts.ini.append.php`:

```ini
[BlockDefinition_text]
Name=Text
Handler=expLayoutsTextBlockHandler
ViewTypes[]=default

[BlockDefinition_list]
Name=Content list
Handler=expLayoutsListBlockHandler
ViewTypes[]=grid
ViewTypes[]=rows
HasCollection=1
```

The engine ships with the following standard handlers:

- `expLayoutsTextBlockHandler`, `expLayoutsTitleBlockHandler`, `expLayoutsHtmlBlockHandler`
- `expLayoutsImageBlockHandler`, `expLayoutsListBlockHandler`, `expLayoutsSingleBlockHandler`
- `expLayoutsButtonBlockHandler`, `expLayoutsGridBlockHandler`, `expLayoutsGalleryBlockHandler`
- `expLayoutsCardBlockHandler`, `expLayoutsAccordionBlockHandler`, `expLayoutsTabsBlockHandler`
- `expLayoutsCarouselBlockHandler`, `expLayoutsMapBlockHandler`, `expLayoutsVideoBlockHandler`
- and more in `extension/explayouts_standard/`

Dynamic collections are resolved through query handlers:

- `expLayoutsChildrenQueryHandler` — children of a node
- `expLayoutsParentQueryHandler`, `expLayoutsSiblingsQueryHandler`
- `expLayoutsLatestQueryHandler`, `expLayoutsRandomQueryHandler`
- `expLayoutsRelationListQueryHandler`, `expLayoutsReverseRelationListQueryHandler`
- `expLayoutsTagsQueryHandler`
- `expLayoutsManualQueryHandler` for editor-picked items

The dynamic collection executor is `expLayoutsDynamicCollection`. It reads the `explayouts_collection_query` row through the `expLayoutsCollectionQuery` persistent object, decodes the `parameters` JSON, and runs the matching query. Manual items are treated as pinned overrides that fill fixed positions while the query fills the remaining slots.

## Resolver and caching

`expLayoutsResolver::resolve($path)` is the entry point. It finds the first enabled rule whose targets and conditions match the current request, then loads the corresponding published layout.

Resolver checks, in order:

1. URL path prefix / exact path / regular expression
2. Content node id or object id
3. Subtree membership
4. Conditions such as siteaccess name and content class

To avoid rebuilding the rule list on every request, the resolver writes the resolved rule id and layout id to the file cache under `var/<VarDir>/cache/explayouts/resolver/`. The TTL is configured in `explayouts.ini`:

```ini
[ResolverSettings]
DefaultLayout=homepage
CacheTTL=3600
```

Call `expLayoutsResolver::clearCache()` when rules or layouts are published or unpublished. The resolver already clears its in-process cache per request; the file cache is versioned by expiration time.

## Rendering

Templates live in `extension/explayouts/design/standard/templates/explayouts/`:

- `layout.tpl` — outer shell; selects one of the layout type sub-templates
- `zone.tpl` — renders blocks for a zone in position order
- `block/*.tpl` — one template per block definition / view type
- `layouts/*.tpl` — layout type templates such as `1_column`, `2_column`, `sidebar_left`

A frontend `pagelayout.tpl` typically does:

```smarty
{def $layout=fetch( 'explayouts', 'resolve_layout', hash() )}
{if $layout}
    {include uri='design:explayouts/layout.tpl' layout=$layout}
{else}
    {$module_result.content}
{/if}
```

## Admin interfaces

Two admin paths are available:

1. **Legacy form-based editor** — `extension/explayouts_ui` under `/explayouts_ui/`
   - layout list, layout edit/create, block edit, rule list, rule edit, import/export, setup
2. **Modern SPA** — `extension/explayouts_ui_api` under `/explayouts_ui_api/app`
   - single-page application served by `explayouts_ui_api/modules/sevenx_layouts_ui_api/app.php`
   - JSON API dispatcher `expLayoutsUIApplicationApi` with endpoints for config, layouts, blocks, rules, mappings, collections, parameters, versions, and share
   - bundled CKEditor, Ace Editor, Roboto / Material Icon fonts, and the Netgen layouts CSS/JS

The SPA admin expects a CSRF token and the base API URL to be set in `app.tpl`.

## Asset strategy

The extension ships a self-contained asset bundle in `extension/explayouts_ui_api/design/standard/`. It uses the Netgen Layouts UI stylesheet and Material Icons rather than an external Bootstrap or FontAwesome dependency. This keeps the admin UI independent of the frontend design system. If your public templates need Bootstrap or another framework, include those at the design/site level.

## Module access

Module and policy keys are declared in `extension/explayouts/settings/module.ini.append.php` and `menu.ini.append.php`. Assign the relevant functions to admin roles through the standard Exponential role/policy UI.

## Status and versioning

Layouts, zones, and blocks carry a `status` column. The editor works on draft rows; publishing copies them to published rows. The resolver only reads rows with `status > 0`.

## Quick start

1. Install the tables from the SQL file matching your database.
2. Make sure `explayouts` and related extensions are active.
3. Regenerate autoloads:
   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   ```
4. Clear caches and restart PHP-FPM:
   ```bash
   php bin/php/ezcache.php --clear-all --allow-root-user
   systemctl restart php-fpm
   ```
5. Create a layout and a rule in `/explayouts_ui/dashboard` or `/explayouts_ui_api/app`.
6. Use the layout in `pagelayout.tpl` with the `fetch('explayouts','resolve_layout')` call shown above.

## Files to know

| File | Responsibility |
|---|---|
| `extension/explayouts/classes/explayoutsresolver.php` | Rule matching and layout resolution |
| `extension/explayouts/classes/explayoutsrenderer.php` | Prepares layout/zone/block data for templates |
| `extension/explayouts/classes/explayoutsdynamiccollection.php` | Executes dynamic queries and merges pinned items |
| `extension/explayouts/classes/explayoutscollectionquery.php` | Persistent object for `explayouts_collection_query` |
| `extension/explayouts/settings/explayouts.ini.append.php` | Layout types, block definitions, query types, resolver settings |
| `extension/explayouts/sql/{mysql,sqlite,pgsql,oracle}/schema.sql` | Database schemas |
| `extension/explayouts/design/standard/templates/explayouts/` | Frontend rendering templates |
| `extension/explayouts_ui_api/classes/explayoutsuiapplicationapi.php` | SPA JSON API |

## Migration and parity

The port intentionally follows the Netgen Layouts data shape so that a Netgen XML/JSON export can be imported into these tables with minimal transformation. Differences are limited to Exponential-specific query handlers and the legacy template rendering layer.
