# Specification: the legacy bridge bundle

This page is the reference for the legacy bridge, the Symfony bundle that runs the Exponential 6 kernel inside a
platform request: its layout, the events you can listen to, the settings it maps from the platform into the
legacy kernel, its configuration tree and its branches. Read it if you configure or extend a project that runs
both kernels. For step-by-step setup, use
[Legacy bridge: run Exponential 6 inside the Symfony platform](../../features/6.0/legacy-bridge.md).

| Item | Value |
|---|---|
| Package | `se7enxweb/legacy-bridge` (repository `legacyBridge`) |
| Bundle class | `eZ\Bundle\EzPublishLegacyBundle\EzPublishLegacyBundle` |
| Configuration root | `ez_publish_legacy` |

## What the bridge does

It boots the Exponential 6 kernel inside a Symfony request, shares the database, the siteaccess, the user and the
caches with the platform kernel, and lets each kernel handle the URLs it owns.

## Layout of the bundle

| Path | Responsibility |
|---|---|
| `bundle/DependencyInjection/` | Configuration tree (`ez_publish_legacy`), scope handling, service loading from `bundle/Resources/config/*.yml` |
| `bundle/LegacyMapper/` | `Configuration`, `LegacyBundles`, `Security`, `Session`, `SiteAccess`: map platform state into the legacy kernel's settings before it is built |
| `bundle/Controller/` | `LegacyKernelController` (runs a legacy request), `LegacyRestController`, `LegacySetupController` (setup wizard), `LegacyTreeMenuController`, `PreviewController`, `WebsiteToolbarController` |
| `bundle/EventListener/` | `ConfigScopeListener`, `CsrfTokenResponseListener`, `IndexRequestListener`, `LegacyKernelListener`, `RequestListener`, `RestListener`, `SetupListener` |
| `bundle/Command/` | the six `exponential:legacy:*` commands ([names](platform-console-commands.md)) |
| `bundle/Cache/` | persistence cache purging when the legacy view cache is cleared |
| `bundle/LegacyBundles/`, `bundle/Composer/` | installation of legacy extensions that Symfony bundles define; Composer scripts |
| `bundle/FieldType/`, `bundle/Rest/`, `bundle/Routing/`, `bundle/Security/`, `bundle/SetupWizard/`, `bundle/Collector/` | XML text field type support, REST bridging, legacy-aware routing, login handling, setup wizard integration, debug toolbar data collector |
| `mvc/` | kernel build (`Kernel`, `LegacyEvents`, `LegacyKernelAware`), event classes, image, session, security and signal-slot adapters |

## Events

Constants of `LegacyEvents`. A listener may change the legacy settings while the kernel is built:

| Constant | Event name | When |
|---|---|---|
| `PRE_BUILD_LEGACY_KERNEL_WEB` | `ezpublish_legacy.build_kernel_web_handler` | before the web handler is built |
| `PRE_BUILD_LEGACY_KERNEL` | `ezpublish_legacy.build_kernel` | before the kernel is built; the bridge's `Configuration` mapper listens here (priority 128) |
| `POST_BUILD_LEGACY_KERNEL` | `ezpublish_legacy.post_build_kernel` | after the kernel is built |
| `PRE_RESET_LEGACY_KERNEL` | `ezpublish_legacy.pre_reset_legacy_kernel` | before the kernel is reset |

## Settings mapped from the platform into the legacy kernel

The `Configuration` mapper (when the bridge is enabled) injects these values as legacy INI overrides before the kernel starts.

| Legacy setting | Source |
|---|---|
| `site.ini/DatabaseSettings/Server`, `Port`, `User`, `Password`, `Database`, `Socket` | Doctrine connection parameters `host`, `port`, `user`, `password`, `dbname`, `unix_socket` (`Socket` is `disabled` when no socket is set) |
| `site.ini/DatabaseSettings/DatabaseImplementation` | Doctrine driver through the map below |
| `site.ini/DatabaseSettings/Database` (SQLite) | Doctrine `path` parameter, since `v4.0.0.1` |
| `site.ini/FileSettings/VarDir`, `StorageDir` | platform parameters `var_dir`, `storage_dir` |
| `site.ini/UserSettings/AnonymousUserID` | platform parameter `anonymous_user_id` |
| `site.ini/ContentSettings/ViewCaching` | forced to `enabled` |
| `site.ini/SiteAccessSettings/PathPrefix`, `PathPrefixExclude` | siteaccess root location and path prefix |
| `site.ini/SiteSettings/IndexPage`, `DefaultPage` | configured values, else `/content/view/full/<root location id>/` |
| `image.ini/ImageMagick/*` | platform ImageMagick options and filters |
| `file.ini/ClusteringSettings/FileHandler` and `eZDFSClusteringSettings/*` | DFS clustering parameters when DFS is configured |

Driver map (`LegacyMapper\Configuration`):

| Doctrine driver | Legacy `DatabaseImplementation` |
|---|---|
| `pdo_mysql` | `ezmysqli` |
| `pdo_pgsql` | `ezpostgresql` |
| `oci8` | `ezoracle` |
| `pdo_sqlite` | `sqlite3` (added `v4.0.0.1`, 2026-04-07) |

Any other driver raises `RuntimeException`: "Could not map database driver to Legacy Stack database implementation."

## Configuration tree

Root `ez_publish_legacy`, in a platform configuration file such as `config/packages/ezplatform.yaml` or `app/config/ezplatform.yml`:

| Key | Type | Default | Scope | Meaning |
|---|---|---|---|---|
| `enabled` | bool | `true` | global | switch the bridge on or off |
| `clear_all_spi_cache_on_symfony_clear_cache` | bool | `true` | global | `cache:clear` also clears the persistence cache |
| `clear_all_spi_cache_from_legacy` | bool | `true` | global | clearing the legacy content view cache clears the persistence cache |
| `root_dir` | string | none | global | legacy root directory; validation fails when the path does not exist |
| `legacy_aware_routes` | list of strings | `[]` | global | routes or route prefixes still reachable when `legacy_mode` is true |
| `templating.view_layout` | string | none | siteaccess | Twig pagelayout used when a content view is rendered in legacy |
| `templating.module_layout` | string | none | siteaccess | Twig pagelayout for legacy modules |
| `legacy_mode` | bool | none | siteaccess | the legacy kernel handles URL aliases for the siteaccess |

Example for the `legacy_admin` siteaccess:

```yaml
ez_publish_legacy:
    clear_all_spi_cache_from_legacy: true
    legacy_aware_routes: [ 'my_legacy_route_' ]
    system:
        legacy_admin:
            legacy_mode: true
```

## Branches and requirements

| Branch | `php` | Key requirements (from `composer.json`) |
|---|---|---|
| `3.x` | `^8.0` | `se7enxweb/exponential ^6.0.12`, `se7enxweb/ezpublish-legacy-installer ^2.0.4`, `ezsystems/ezplatform-kernel ~1.3`, `se7enxweb/ezplatform-xmltext-fieldtype ^2.0`, `symfony/framework-bundle ^5.4`, `se7enxweb/ngsymfonytools ^4.0`, `se7enxweb/richtext-datatype-bundle 2.0.x-dev` |
| `4.x` | `^8.0` | as `3.x` with `se7enxweb/exponential dev-main` and `se7enxweb/ezplatform-xmltext-fieldtype ^4.0` |
| `5.x` | `^8.4` | `se7enxweb/exponential dev-main`, `se7enxweb/ezplatform-xmltext-fieldtype 5.x-dev`, `se7enxweb/ngsymfonytools v5.0.x-dev`, `se7enxweb/richtext-datatype-bundle *`, `se7enxweb/site-bundle 5.0.x-dev`, `se7enxweb/site-legacy-bundle`, a database translator package |

## Related bundles

- `site-legacy-bundle`: glue between the new and the legacy kernel for Netgen style sites (ported to eZ Platform 3.3 service ids in March 2026).
- `ngsymfonytools`: legacy extension with a `symfony_include` template operator to render a Twig template or a Symfony sub-request from a legacy `.tpl`. The operator fetches the `twig` service from the container; this works on Symfony 5 and later because the bridge's `TwigPass` makes the service public (`v5.0.9`, April 2026). It also carries a class alias from the old repository interface to the Ibexa one.
- `ibexa-legacy-bridge` (Platform 4): port of the bridge to the Ibexa 4 APIs, with `exponential:legacy:script`.

## Related pages

- Features: [Legacy bridge](../../features/6.0/legacy-bridge.md), [Site bundles](../../features/6.0/platform-site-bundles.md), [Platform administration interface](../../features/6.0/platform-admin-ui-fork.md), [DXP skeleton](../../features/6.0/platform-dxp-skeleton.md), [Layouts on the platform](../../features/6.0/platform-layouts-core-fork.md), [Nexus starter](../../features/6.0/platform-nexus-starter.md), [PHP 8.5 framework forks](../../features/6.0/platform-php85-framework-forks.md), [SQLite for Exponential Platform](../../features/6.0/platform-sqlite-install.md), [AdminNeo database manager](../../features/6.0/adminneo-database-manager.md)
- Specifications: [Platform console command names](platform-console-commands.md), [Platform package map](platform-package-map.md), [Platform SQLite installer](platform-sqlite-installer.md)
- Upgrade notes: [Package forks and command renames](../../bc/6.0/platform-package-forks-and-command-renames.md)
- Changelog: [Platform changelog](../../changelogs/extensions/exponential-platform.md)
- History: [ecosystem overview](../../history/ecosystem.md), [ecosystem months](../../history/ecosystem/months/2026-04.md), [change ledger](../../history/ledger/README.md); repositories [legacyBridge](../../history/ecosystem/legacyBridge.md), [ibexa-legacy-bridge---7x](../../history/ecosystem/ibexa-legacy-bridge---7x.md), [site-legacy-bundle](../../history/ecosystem/site-legacy-bundle.md), [ngsymfonytools](../../history/ecosystem/ngsymfonytools.md)
