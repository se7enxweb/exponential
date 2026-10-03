# Ecosystem repository: legacyBridge

**Group:** Legacy bridge and site bundles. **Period in the ledger:** 2025-07-01 to 2026-04-17. **Changes:** 49 (49 made by the se7enxweb team, 0 upstream history carried by the fork).

## What it is

Bridge that runs the Exponential legacy kernel inside the Symfony platform (se7enxweb/legacy-bridge).

## How it relates to Exponential

The piece that lets one installation serve both the legacy admin / templates and the Symfony stack.

## What a user gets

Run an existing Exponential 6 site beside Platform 3.x / 4.6 / 5.x, with PHP 8.x compatible code and exponential:legacy:* commands.

Install it with Composer (a project that already requires the platform pulls it in by itself):

```bash
composer require se7enxweb/legacy-bridge
```

## Counts by kind

| Kind | Changes |
|---|---|
| Features | 8 |
| Fixes | 30 |
| Behaviour and upgrade changes | 3 |
| Tooling | 5 |
| Releases | 3 |

## Releases made by the se7enxweb team

| Date | Tag | Commit | Change |
|---|---|---|---|
| 2025-08-25 | v2.1.10 | `441d49f` | Update LegacyWrapperInstallCommand.php to include bugfix for quoted web dir upon install via composer. Very distressing. Bugfix. |
| 2026-02-25 | v2.1.11 | `9195072` | Updated: Replaced SiteDesign=admin2 with admin3 design for long term support and features. Enhancement. |
| 2026-03-25 | 3.0.0.1 | `54aca93` | create 3.x branch for eZ Platform 3.3 / Symfony 5.4 / PHP 8.x |
| 2026-03-25 | 3.0.0.2 | `3da2a70` | relax se7enxweb/ezplatform-xmltext-fieldtype constraint to ^2.0 for Packagist resolution |
| 2026-03-25 | 3.0.0.3 | `7820f65` | replace ezsystems/ezpublish-legacy with se7enxweb/exponential ^6.0.12 for PHP 8.x compatibility |
| 2026-03-26 | 3.0.0.4 | `2079902` | remove KernelInterface constructor for Symfony 5.4 compatibility; use kernel.bundles param in LegacyBundlesPass |
| 2026-03-26 | 3.0.0.5 | `232f212` | Fix TreeBuilder: pass root name to constructor, use getRootNode() for Symfony 5.4 compat |
| 2026-03-26 | 3.0.0.6 | `531ba3b` | replace removed Exponential.config.resolver.core with Exponential.config.resolver |
| 2026-03-26 | 3.0.0.7 | `4b484e5` | Fix LegacyConfigResolver: add type declarations for PHP 8.x ConfigResolverInterface compat |
| 2026-03-26 | 3.0.0.8 | `3a0d4e8` | replace deprecated Symfony 4 HttpKernel event class names (GetResponseEvent → RequestEvent, FilterResponseEvent → ResponseEvent) |
| 2026-03-26 | 3.0.0.9 | `09fba84` | Fix REST event namespace for eZ Platform REST bundle |
| 2026-03-26 | 3.0.0.10 | `0a59fbf` | Use Twig for legacy response rendering |
| 2026-03-26 | 3.0.0.11 | `c4f1e87` | Use legacy storage connection in config mapper |
| 2026-03-26 | 3.0.0.12 | `9251bef` | Drop obsolete kernel.name usage |
| 2026-03-26 | 3.0.0.13 | `e6af074` | Align HTTP cache purger signatures |
| 2026-03-26 | 3.0.0.14 | `1962ca8` | Fix profiler collector signature |
| 2026-03-26 | 3.0.0.15 | `a903e1e` | Update Twig loader interfaces |
| 2026-03-26 | 3.0.0.16 | `6cde5d5` | Match installed Twig loader API |
| 2026-03-26 | 3.0.0.17 | `5990076` | Update legacy Twig bridge classes |
| 2026-03-26 | 3.0.0.18 | `85c6b7f` | Fix Twig environment typehints |
| 2026-03-26 | 3.0.0.19 | `6cbc9e3` | Stop replacing initialized legacy closures |
| 2026-03-26 | 3.0.0.20 | `bf3952c` | Stop swapping legacy kernel handlers in console |
| 2026-03-26 | 3.0.0.21 | `891de81` | Use Symfony Contracts events |
| 2026-03-26 | 3.0.0.22 | `c8a5224` | Update legacy event dispatching |
| 2026-03-26 | 3.0.0.23 | `b9d41db` | Select CLI legacy handler in console |
| 2026-03-26 | 3.0.0.24 | `05b2c60` | Inject siteaccess into CLI handler factory |
| 2026-03-26 | 3.0.0.25 | `264f981` | Update login cleanup listener for Symfony 5 |
| 2026-03-27 | v3.0.0.26 | `a683980` | Twig 3 compatibility for eZ Platform 3.x |
| 2026-03-27 | v3.0.0.27 | `fc8faa3` | register legacy console commands as DI services; refactor LegacyEmbedScriptCommand to use constructor injection instead of deprecated ContainerAwareCo |
| 2026-03-27 | v3.0.0.28 | `bdae3a5` | refactor all legacy commands removing deprecated ContainerAwareCommand; use constructor injection for Symfony 5 compatibility |
| 2026-04-06 | v4.0.0.0 | `cb08cb4` | Updated: Added extra GUI chrome to legacy_admin siteacessses via ngsite extension. Added for UI Compatibility Improvements. Enhancement. |
| 2026-04-07 | v4.0.0.1 | `b926fc9` | SQLite: map pdo_sqlite driver + inject absolute DB path into legacy INI |
| 2026-04-16 | v4.0.0.2 | `df32ec7` | session.storage removed in Symfony 5.4 factory-based session config |
| 2026-04-17 | v4.0.0.3 | `d61370c` | fall back to sessionStorage when session service is null on Symfony 5.3+ |

## Changes made by the se7enxweb team, by theme

### Symfony and platform compatibility (9)

- 2026-03-26 `2079902` fix: remove KernelInterface constructor for Symfony 5.4 compatibility; use kernel.bundles param in LegacyBundlesPass
- 2026-03-26 `232f212` fix: Fix TreeBuilder: pass root name to constructor, use getRootNode() for Symfony 5.4 compat
- 2026-03-26 `3a0d4e8` fix: replace deprecated Symfony 4 HttpKernel event class names (GetResponseEvent → RequestEvent, FilterResponseEvent → ResponseEvent)
- 2026-03-27 `a683980` fix: Twig 3 compatibility for eZ Platform 3.x
- 2026-03-27 `bdae3a5` fix: refactor all legacy commands removing deprecated ContainerAwareCommand; use constructor injection for Symfony 5 compatibility
- 2026-04-04 `8317c53` fix: Ibexa 4.6 compat - service/param aliases, session mapper, csrf token, cache purger, boot guard, lazy kernel init
- 2026-04-04 `ed75494` fix: Symfony 5.4 compat - create session.storage alias when storage_factory_id is used
- 2026-04-06 `cb08cb4` fix: Updated: Added extra GUI chrome to legacy_admin siteacessses via ngsite extension. Added for UI Compatibility Improvements. Enhancement.
- 2026-04-16 `df32ec7` fix: session.storage removed in Symfony 5.4 factory-based session config

### Other changes to the fork (9)

- 2026-03-26 `c4f1e87` fix: Use legacy storage connection in config mapper
- 2026-03-26 `9251bef` fix: Drop obsolete kernel.name usage
- 2026-03-26 `e6af074` fix: Align HTTP cache purger signatures
- 2026-03-26 `6cbc9e3` fix: Stop replacing initialized legacy closures
- 2026-03-26 `bf3952c` fix: Stop swapping legacy kernel handlers in console
- 2026-03-26 `891de81` fix: Use Symfony Contracts events
- 2026-03-26 `c8a5224` fix: Update legacy event dispatching
- 2026-03-26 `b9d41db` fix: Select CLI legacy handler in console
- 2026-03-26 `05b2c60` fix: Inject siteaccess into CLI handler factory

### Bug fixes (8)

- 2026-03-25 `3da2a70` fix: relax se7enxweb/ezplatform-xmltext-fieldtype constraint to ^2.0 for Packagist resolution
- 2026-03-26 `531ba3b` fix: replace removed Exponential.config.resolver.core with Exponential.config.resolver
- 2026-03-26 `09fba84` fix: Fix REST event namespace for eZ Platform REST bundle
- 2026-03-26 `1962ca8` fix: Fix profiler collector signature
- 2026-03-26 `b2957a2` fix: Fix getCurrentUser() -> getPermissionResolver()->getCurrentUserReference()->getUserId() for eZ Platform 3 API
- 2026-03-27 `fc8faa3` fix: register legacy console commands as DI services; refactor LegacyEmbedScriptCommand to use constructor injection instead of deprecated ContainerAwareCo
- 2026-04-04 `f269b61` fix: register legacy_mode under ibexa.site_access.config namespace
- 2026-04-17 `d61370c` fix: fall back to sessionStorage when session service is null on Symfony 5.3+

### Composer requirements (6)

- 2025-07-01 `75e0f05` tooling: Update composer.json switched vendor name
- 2025-07-01 `d9816e9` tooling: Update composer.json replaced package vendor name
- 2025-08-24 `4b45ba8` tooling: Update composer.json to include replacement package vendor name for twig/twig and sensio/distribution-bundle. Forking for changes.
- 2025-08-25 `8e158d2` tooling: Update composer.json testing switch to dev-main for exponential 6.0.x. Testing.
- 2025-08-25 `3c23639` tooling: Update composer.json replacing pacakge name and version back to tagged releases. Testing.
- 2025-08-25 `441d49f` release: Update LegacyWrapperInstallCommand.php to include bugfix for quoted web dir upon install via composer. Very distressing. Bugfix.

### Design and templates (5)

- 2026-03-26 `0a59fbf` feature: Use Twig for legacy response rendering
- 2026-03-26 `a903e1e` feature: Update Twig loader interfaces
- 2026-03-26 `6cde5d5` feature: Match installed Twig loader API
- 2026-03-26 `5990076` feature: Update legacy Twig bridge classes
- 2026-03-26 `85c6b7f` feature: Fix Twig environment typehints

### PHP 8.x compatibility (4)

- 2025-08-24 `7d0dcae` fix: Update LegacyWrapperInstallCommand.php to include working change to run ezplatform-legacy with this bundle. This change fixed transparently but not co
- 2026-03-25 `54aca93` fix: create 3.x branch for eZ Platform 3.3 / Symfony 5.4 / PHP 8.x
- 2026-03-26 `4b484e5` fix: Fix LegacyConfigResolver: add type declarations for PHP 8.x ConfigResolverInterface compat
- 2026-04-05 `6ca1508` fix: PHP 8 compat - add int return type and return 0 to Command execute() methods

### Package renamed to the se7enxweb vendor (2)

- 2026-03-25 `7820f65` bc: replace ezsystems/ezpublish-legacy with se7enxweb/exponential ^6.0.12 for PHP 8.x compatibility
- 2026-04-04 `7b57b96` bc: Fixed: require se7enxweb/exponential dev-main instead of ^6.0.12

### Responsive admin design (admin3) (1)

- 2026-02-25 `9195072` feature: Updated: Replaced SiteDesign=admin2 with admin3 design for long term support and features. Enhancement.

### Cleanup (1)

- 2026-03-26 `264f981` release: Update login cleanup listener for Symfony 5

### Version numbers (1)

- 2026-04-06 `8fc9d52` release: Updated: Version API/Specific/Release Switch for se7enxweb/ezplatform-xmltext-fieldtype package requirements. Bugfix.

### Extensions bundled with the distribution (1)

- 2026-04-06 `0719670` feature: Updated: Altering legacyBridge init_ini settings override installation file site.ini.append.php to test additional configuration items and remove ezmb

### SQLite support (1)

- 2026-04-07 `b926fc9` feature: SQLite: map pdo_sqlite driver + inject absolute DB path into legacy INI

### Console command names (1)

- 2026-04-07 `8cea9e8` bc: rename legacy commands to exponential:legacy:* prefix, keep ezpublish:* as deprecated aliases (v4/4.x)

## Full record

- Every change with date, kind, size and release tag: [ledger of legacyBridge](ledger/legacyBridge.md).
- Overview of all platform repositories: [Ecosystem](../ecosystem.md).
