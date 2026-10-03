# legacyBridge: release notes

Read this page before you install or update `legacyBridge`, or to find out which release brought a change.

Releases made by the se7enxweb team, newest first. Each release lists the team's changes since the previous tag, grouped as Added, Updated, Removed and Renamed. Upstream history carried by the fork is not repeated here; it is in the [repository page](../../history/ecosystem/legacyBridge.md).

## v4.0.0.3 (2026-04-17)

**Updated**

- Fall back to sessionStorage when session service is null on Symfony 5.3+ (`d61370c`)

## v4.0.0.2 (2026-04-16)

**Updated**

- session.storage removed in Symfony 5.4 factory-based session config (`df32ec7`)

**Renamed**

- Rename legacy commands to exponential:legacy:* prefix, keep ezpublish:* as deprecated aliases (v4/4.x) (`8cea9e8`)

## v4.0.0.1 (2026-04-07)

**Added**

- SQLite: map pdo_sqlite driver + inject absolute DB path into legacy INI (`b926fc9`)

## v4.0.0.0 (2026-04-06)

**Added**

- Added extra GUI chrome to legacy_admin siteacessses via ngsite extension. Added for UI Compatibility Improvements. Enhancement. (`cb08cb4`)

**Updated**

- Ibexa 4.6 compat - service/param aliases, session mapper, csrf token, cache purger, boot guard, lazy kernel init (`8317c53`)
- Symfony 5.4 compat - create session.storage alias when storage_factory_id is used (`ed75494`)
- Register legacy_mode under ibexa.site_access.config namespace (`f269b61`)
- PHP 8 compat - add int return type and return 0 to Command execute() methods (`6ca1508`)
- Version API/Specific/Release Switch for se7enxweb/ezplatform-xmltext-fieldtype package requirements. Bugfix. (`8fc9d52`)
- Altering legacyBridge init_ini settings override installation file site.ini.append.php to test additional configuration items and remove ezmbpaex exte (`0719670`)

**Renamed**

- Require se7enxweb/exponential dev-main instead of ^6.0.12 (`7b57b96`)

## v3.0.0.28 (2026-03-27)

**Updated**

- Refactor all legacy commands removing deprecated ContainerAwareCommand; use constructor injection for Symfony 5 compatibility (`bdae3a5`)

## v3.0.0.27 (2026-03-27)

**Updated**

- Register legacy console commands as DI services; refactor LegacyEmbedScriptCommand to use constructor injection instead of deprecated ContainerAwareCo (`fc8faa3`)

## v3.0.0.26 (2026-03-27)

**Updated**

- Fix getCurrentUser() -> getPermissionResolver()->getCurrentUserReference()->getUserId() for eZ Platform 3 API (`b2957a2`)
- Twig 3 compatibility for eZ Platform 3.x (`a683980`)

## 3.0.0.25 (2026-03-26)

**Updated**

- Update login cleanup listener for Symfony 5 (`264f981`)

## 3.0.0.24 (2026-03-26)

**Updated**

- Inject siteaccess into CLI handler factory (`05b2c60`)

## 3.0.0.23 (2026-03-26)

**Updated**

- Select CLI legacy handler in console (`b9d41db`)

## 3.0.0.22 (2026-03-26)

**Updated**

- Update legacy event dispatching (`c8a5224`)

## 3.0.0.21 (2026-03-26)

**Updated**

- Use Symfony Contracts events (`891de81`)

## 3.0.0.20 (2026-03-26)

**Updated**

- Stop swapping legacy kernel handlers in console (`bf3952c`)

## 3.0.0.19 (2026-03-26)

**Updated**

- Stop replacing initialized legacy closures (`6cbc9e3`)

## 3.0.0.18 (2026-03-26)

**Updated**

- Fix Twig environment typehints (`85c6b7f`)

## 3.0.0.17 (2026-03-26)

**Updated**

- Update legacy Twig bridge classes (`5990076`)

## 3.0.0.16 (2026-03-26)

**Updated**

- Match installed Twig loader API (`6cde5d5`)

## 3.0.0.15 (2026-03-26)

**Updated**

- Update Twig loader interfaces (`a903e1e`)

## 3.0.0.14 (2026-03-26)

**Updated**

- Fix profiler collector signature (`1962ca8`)

## 3.0.0.13 (2026-03-26)

**Updated**

- Align HTTP cache purger signatures (`e6af074`)

## 3.0.0.12 (2026-03-26)

**Updated**

- Drop obsolete kernel.name usage (`9251bef`)

## 3.0.0.11 (2026-03-26)

**Updated**

- Use legacy storage connection in config mapper (`c4f1e87`)

## 3.0.0.10 (2026-03-26)

**Updated**

- Use Twig for legacy response rendering (`0a59fbf`)

## 3.0.0.9 (2026-03-26)

**Updated**

- Fix REST event namespace for eZ Platform REST bundle (`09fba84`)

## 3.0.0.8 (2026-03-26)

**Updated**

- Replace deprecated Symfony 4 HttpKernel event class names (GetResponseEvent → RequestEvent, FilterResponseEvent → ResponseEvent) (`3a0d4e8`)

## 3.0.0.7 (2026-03-26)

**Updated**

- Fix LegacyConfigResolver: add type declarations for PHP 8.x ConfigResolverInterface compat (`4b484e5`)

## 3.0.0.6 (2026-03-26)

**Updated**

- Replace removed Exponential.config.resolver.core with Exponential.config.resolver (`531ba3b`)

## 3.0.0.5 (2026-03-26)

**Updated**

- Fix TreeBuilder: pass root name to constructor, use getRootNode() for Symfony 5.4 compat (`232f212`)

## 3.0.0.4 (2026-03-26)

**Updated**

- Remove KernelInterface constructor for Symfony 5.4 compatibility; use kernel.bundles param in LegacyBundlesPass (`2079902`)

## 3.0.0.3 (2026-03-25)

**Renamed**

- Replace ezsystems/ezpublish-legacy with se7enxweb/exponential ^6.0.12 for PHP 8.x compatibility (`7820f65`)

## 3.0.0.2 (2026-03-25)

**Updated**

- Relax se7enxweb/ezplatform-xmltext-fieldtype constraint to ^2.0 for Packagist resolution (`3da2a70`)

## 3.0.0.1 (2026-03-25)

**Updated**

- Create 3.x branch for eZ Platform 3.3 / Symfony 5.4 / PHP 8.x (`54aca93`)

## v2.1.11 (2026-02-25)

**Updated**

- Replaced SiteDesign=admin2 with admin3 design for long term support and features. Enhancement. (`9195072`)

## v2.1.10 (2025-08-25)

**Updated**

- Update composer.json switched vendor name (`75e0f05`)
- Update composer.json replaced package vendor name (`d9816e9`)
- Update LegacyWrapperInstallCommand.php to include working change to run ezplatform-legacy with this bundle. This change fixed transparently but not co (`7d0dcae`)
- Update composer.json to include replacement package vendor name for twig/twig and sensio/distribution-bundle. Forking for changes. (`4b45ba8`)
- Update composer.json testing switch to dev-main for exponential 6.0.x. Testing. (`8e158d2`)
- Update composer.json replacing pacakge name and version back to tagged releases. Testing. (`3c23639`)
- Update LegacyWrapperInstallCommand.php to include bugfix for quoted web dir upon install via composer. Very distressing. Bugfix. (`441d49f`)

## Related pages

- [Legacy bridge](../../features/6.0/legacy-bridge.md)
- [package map](../../specifications/6.0/platform-package-map.md)
- [upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)
