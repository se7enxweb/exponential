# Site bundles, metadata and the Twig tools of the platform

This page is for developers who build or extend a [Nexus](platform-nexus-starter.md) site on Exponential Platform.
These packages are the building blocks Nexus sites are made from. You normally do not install them by hand; the Nexus
distribution requires them. Knowing what each does tells you where to look when a site behaves unexpectedly: a menu, a
query type, a layout block or a legacy mapping is usually in one of them.

**Repositories:** `se7enxweb/site-bundle`, `se7enxweb/site-legacy-bundle`, `se7enxweb/ngsymfonytools`,
`se7enxweb/metadata-bundle`, `se7enxweb/mediata-ezpage-fieldtype-bundle`, `se7enxweb/ez-support-tools`.

## What each package gives a site

| Package | What it gives a site |
|---|---|
| `se7enxweb/site-bundle` (fork of Netgen Site Bundle) | The common features Netgen builds sites on: `Menu`, `Layouts` integration, `ContentForms`, `InfoCollection`, `OpenGraph` metadata, `Pagerfanta` paging, `QueryType` for content queries, `RichText` rendering, `Relation` and `Topic` helpers, an `Imagine` integration for image variations, a `ContextProvider` (which part of the tree is the site), a data collector for the debug toolbar, console commands. Not for standalone use; it is the base bundle of a project. |
| `se7enxweb/site-legacy-bundle` | Glue between the new and the legacy kernel for Netgen style sites running with the [legacy bridge](legacy-bridge.md): legacy mapper, templating, event listeners, and an `ezpublish_legacy` directory with the legacy extension. Admin copyright templates carry the Exponential branding since 2026-04-17. |
| `se7enxweb/ngsymfonytools` | A legacy extension: template operators that let an Exponential `.tpl` template call Twig and Symfony |
| `se7enxweb/metadata-bundle` | The metadata (SEO) field type for the platform, data compatible with the `xrowmetadata` legacy extension, so the same field and the same stored data work on the legacy kernel and on the new stack. Release `v5.0.0` adds Ibexa 5.x and PHP 8.4 support. |
| `se7enxweb/mediata-ezpage-fieldtype-bundle` | The page (landing page) field type for Ibexa 4, imported on 2026-03-16 as tested for 4.6, with a Twig operator so plain Symfony projects can use it |
| `se7enxweb/ez-support-tools` | The system information page for administrators (PHP, Composer packages, platform versions). Fixed in April 2026 so a null version from `InstalledVersions::getVersion()` no longer breaks the page. |

The se7enxweb packages replace the upstream names through Composer `replace` declarations. `site-bundle` declares
`netgen/site-bundle:*` as replaced (commit of 2026-04-19, release `3.0.6`), so a project cannot end up with both the
fork and the upstream copy installed side by side. See the
[upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md).

## Call Twig from a legacy template

With `ngsymfonytools` active, a legacy `.tpl` can include a Twig template or render a Symfony controller.

1. Activate the extension (admin: Setup, Extensions; or in `settings/override/site.ini.append.php`):

   ```ini
   [ExtensionSettings]
   ActiveExtensions[]=ngsymfonytools
   ```

2. Regenerate the autoload array (admin: Setup, Extensions, "Regenerate autoload arrays"; or run the legacy script
   `bin/php/ezpgenerateautoloads.php` through [`exponential:legacy:script`](legacy-bridge.md)).

3. Include a Twig template:

   ```smarty
   {symfony_include(
       'AppBundle:Test:test.html.twig',
       hash(
           'theAnswer', 42,
           'homepage', fetch( 'content', 'node', hash( 'node_id', 2 ) )
       )
   )}
   ```

Operators (from the extension's usage guide):

| Operator | Purpose |
|---|---|
| `symfony_include( template, hash )` | Include a Twig template; `eZContentObject` and `eZContentObjectTreeNode` parameters are converted to platform `Content` and `Location` objects |
| `symfony_path( route, hash )` | Relative URL of a route |
| `symfony_url( route, hash )` | Absolute URL of a route |
| `symfony_controller( controller, hash, hash )` | Reference to a controller (use inside `symfony_render`) |
| `symfony_render( ... )` | Render a controller, path or URL in the legacy template |
| `symfony_render_esi( ... )`, `symfony_render_hinclude( ... )` | Emit an ESI or Hinclude tag for a controller or URL |

Changes in 2026: the operator no longer uses the `templating` service that Symfony 5 removed (2026-04-05); it asks the
container for the `twig` service. On 2026-04-16 it briefly used the `Twig\Environment::class` id and went back to
`twig` the same day, because the legacy bridge release `v5.0.9` makes the `twig` service public (its `TwigPass` calls
`setPublic(true)`), so legacy code can fetch it again. A `class_alias` at the top of the content converter keeps the
old repository interface name working on Ibexa DXP 4.x and 5.0.

## Limits

- `site-bundle` and `site-legacy-bundle` are not standalone: they need a project built on the Nexus pattern.
- The metadata field type needs the platform kernel. On the legacy kernel use the `xrowmetadata` extension, which
  writes the same data.

## Related pages

- Extension pages: [ngsymfonytools](extensions/ngsymfonytools.md), [ez-support-tools](extensions/ez-support-tools.md), [xrowmetadata](extensions/xrowmetadata.md)
- Platform features: [Nexus starter](platform-nexus-starter.md), [legacy bridge](legacy-bridge.md), [platform administration interface](platform-admin-ui-fork.md), [DXP skeleton](platform-dxp-skeleton.md), [Layouts on the platform](platform-layouts-core-fork.md), [PHP 8.5 framework forks](platform-php85-framework-forks.md), [SQLite for Exponential Platform](platform-sqlite-install.md), [AdminNeo database manager](adminneo-database-manager.md)
- Specifications: [platform package map](../../specifications/6.0/platform-package-map.md), [legacy bridge bundle](../../specifications/6.0/legacy-bridge-bundle.md), [platform console command names](../../specifications/6.0/platform-console-commands.md), [platform SQLite installer](../../specifications/6.0/platform-sqlite-installer.md)
- Upgrade notes: [package forks and command renames](../../bc/6.0/platform-package-forks-and-command-renames.md)
- Changelog: [platform changelog](../../changelogs/extensions/exponential-platform.md)
- History: [site-bundle](../../history/ecosystem/site-bundle.md), [site-legacy-bundle](../../history/ecosystem/site-legacy-bundle.md), [ngsymfonytools](../../history/ecosystem/ngsymfonytools.md), [metadata-bundle](../../history/ecosystem/metadata-bundle.md), [mediata-ezpage-fieldtype-bundle-main](../../history/ecosystem/mediata-ezpage-fieldtype-bundle-main.md), [ez-support-tools](../../history/ecosystem/ez-support-tools.md), [ecosystem overview](../../history/ecosystem.md), [ecosystem months](../../history/ecosystem/months/2026-04.md), [change ledger](../../history/ledger/README.md)
