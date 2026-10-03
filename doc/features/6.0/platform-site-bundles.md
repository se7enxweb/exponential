# Site bundles, metadata and the Twig tools of the platform

**Repositories:** `se7enxweb/site-bundle`, `se7enxweb/site-legacy-bundle`, `se7enxweb/ngsymfonytools`, `se7enxweb/metadata-bundle`, `se7enxweb/mediata-ezpage-fieldtype-bundle`, `se7enxweb/ez-support-tools`.
**Extension pages:** [ngsymfonytools](extensions/ngsymfonytools.md) · [ez-support-tools](extensions/ez-support-tools.md) · [xrowmetadata](extensions/xrowmetadata.md) (the legacy extension whose data the metadata bundle shares).
**History:** [site-bundle](../../history/ecosystem/site-bundle.md), [site-legacy-bundle](../../history/ecosystem/site-legacy-bundle.md), [ngsymfonytools](../../history/ecosystem/ngsymfonytools.md), [metadata-bundle](../../history/ecosystem/metadata-bundle.md), [mediata-ezpage-fieldtype-bundle-main](../../history/ecosystem/mediata-ezpage-fieldtype-bundle-main.md), [ez-support-tools](../../history/ecosystem/ez-support-tools.md).

## What they are

These are the building blocks that [Nexus](platform-nexus-starter.md) sites are made from. You normally do not install them by hand; the Nexus distribution requires them. Knowing what each does tells you where to look when you extend a Nexus site.

| Package | What it gives a site |
|---|---|
| `se7enxweb/site-bundle` (fork of Netgen Site Bundle) | The common features Netgen builds sites on: `Menu`, `Layouts` integration, `ContentForms`, `InfoCollection`, `OpenGraph` metadata, `Pagerfanta` paging, `QueryType` for content queries, `RichText` rendering, `Relation` and `Topic` helpers, an `Imagine` integration for image variations, a `ContextProvider` (which part of the tree is the site), data collector for the debug toolbar, console commands. Not for standalone use; it is the base bundle of a project. |
| `se7enxweb/site-legacy-bundle` | Glue between the new and the legacy kernel for Netgen style sites running with the [legacy bridge](legacy-bridge.md): legacy mapper, templating, event listeners, and an `ezpublish_legacy` directory with the legacy extension. Admin copyright templates carry the Exponential branding since 2026-04-17. |
| `se7enxweb/ngsymfonytools` | A legacy extension: template operators that let an Exponential `.tpl` template call Twig and Symfony. |
| `se7enxweb/metadata-bundle` | The metadata (SEO) field type for the platform, data compatible with the `xrowmetadata` legacy extension, so the same field and the same stored data work on the legacy kernel and on the new stack. Release `v5.0.0` adds Ibexa 5.x and PHP 8.4 support. |
| `se7enxweb/mediata-ezpage-fieldtype-bundle` | The page (landing page) field type for Ibexa 4, imported on 2026-03-16 as tested for 4.6, with a Twig operator so plain Symfony projects can use it. |
| `se7enxweb/ez-support-tools` | The system information page for administrators (PHP, Composer packages, platform versions). Fixed in April 2026 so a null version from `InstalledVersions::getVersion()` no longer breaks the page. |

## Why you would care

- When something in a Nexus site behaves unexpectedly, the answer is usually in one of these bundles: a menu, a query type, a layout block, a legacy mapping. This page tells you which.
- Replacing the upstream names with se7enxweb ones is done with Composer `replace` declarations. `site-bundle` declares `netgen/site-bundle:*` as replaced (commit of 2026-04-19, release `3.0.6`), so a project cannot end up with both the fork and the upstream copy installed side by side. See [upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md).

## Use the Twig tools from a legacy template

With `ngsymfonytools` active, a legacy `.tpl` can include a Twig template or render a Symfony controller. Activate the extension (admin interface: Setup, Extensions; or in `settings/override/site.ini.append.php`):

```ini
[ExtensionSettings]
ActiveExtensions[]=ngsymfonytools
```

Regenerate the autoload array (admin: Setup, Extensions, "Regenerate autoload arrays"; or run the legacy script `bin/php/ezpgenerateautoloads.php` through [`exponential:legacy:script`](legacy-bridge.md)).

Operators (from the extension's usage guide):

| Operator | Purpose |
|---|---|
| `symfony_include( template, hash )` | include a Twig template; `eZContentObject` and `eZContentObjectTreeNode` parameters are converted to platform `Content` and `Location` objects |
| `symfony_path( route, hash )` | relative URL of a route |
| `symfony_url( route, hash )` | absolute URL of a route |
| `symfony_controller( controller, hash, hash )` | reference to a controller (use inside `symfony_render`) |
| `symfony_render( ... )` | render a controller, path or URL in the legacy template |
| `symfony_render_esi( ... )`, `symfony_render_hinclude( ... )` | emit an ESI or Hinclude tag for a controller or URL |

Example:

```smarty
{symfony_include(
    'AppBundle:Test:test.html.twig',
    hash(
        'theAnswer', 42,
        'homepage', fetch( 'content', 'node', hash( 'node_id', 2 ) )
    )
)}
```

Changes in 2026: the operator no longer uses the `templating` service that Symfony 5 removed (2026-04-05); it asks the container for the `twig` service. On 2026-04-16 it briefly used the `Twig\Environment::class` id and went back to `twig` the same day, because the legacy bridge release `v5.0.9` makes the `twig` service public (its `TwigPass` calls `setPublic(true)`), so legacy code can fetch it again. A `class_alias` at the top of the content converter keeps the old repository interface name working on Ibexa DXP 4.x and 5.0.

## Limits

- `site-bundle` and `site-legacy-bundle` are not standalone: they need a project built on the Nexus pattern.
- The metadata field type needs the platform kernel; on the legacy kernel use the `xrowmetadata` extension, which writes the same data.

## Related

[Nexus starter](platform-nexus-starter.md) · [Legacy bridge](legacy-bridge.md) · [Package map](../../specifications/6.0/platform-package-map.md) · [Legacy bridge specification](../../specifications/6.0/legacy-bridge-bundle.md)

## Platform ecosystem pages

- Features: [Platform administration interface](platform-admin-ui-fork.md); [DXP skeleton](platform-dxp-skeleton.md); [Layouts on the platform](platform-layouts-core-fork.md); [Nexus starter](platform-nexus-starter.md); [PHP 8.5 framework forks](platform-php85-framework-forks.md); [SQLite for Exponential Platform](platform-sqlite-install.md); [Legacy bridge](legacy-bridge.md); [AdminNeo database manager](adminneo-database-manager.md).
- Specifications: [Platform console command names](../../specifications/6.0/platform-console-commands.md); [Platform package map](../../specifications/6.0/platform-package-map.md); [Platform SQLite installer](../../specifications/6.0/platform-sqlite-installer.md); [Legacy bridge bundle specification](../../specifications/6.0/legacy-bridge-bundle.md).
- Upgrade notes: [Package forks and command renames](../../bc/6.0/platform-package-forks-and-command-renames.md).
- Changelog: [Platform changelog](../../changelogs/extensions/exponential-platform.md).
- History: [ecosystem overview](../../history/ecosystem.md), with a page for every month from 2018-11 in [ecosystem months](../../history/ecosystem/months/2026-04.md), and the [change ledger](../../history/ledger/README.md).
