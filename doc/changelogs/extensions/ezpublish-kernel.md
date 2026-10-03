# ezpublish-kernel: release notes

Read this page before you install or update `ezpublish-kernel`, or to find out which release brought a change.

Releases made by the se7enxweb team, newest first. Each release lists the team's changes since the previous tag, grouped as Added, Updated, Removed and Renamed. Upstream history carried by the fork is not repeated here; it is in the [repository page](../../history/ecosystem/ezpublish-kernel.md).

## v7.5.41 (2026-04-09)

**Added**

- Add SQLite installer support + exponential-oss install type (`23d2b55de`)

**Updated**

- Fix PHP 8.5 compatibility: Replace closure-based Twig functions with named methods (`529b8f3e3`)
- Bugfixes to enable reasonable cache within platform. Tested as working in nexus 1.0.0.2 github. Required to fix symfony/platform based cache problems. (`4be8f2faa`)
- Updated config/cache.yml to patch to provide required missing yaml configuration block. Cache System Bugfix. (`3063e284a`)
- Rebranding Default Installation Name and Doc URL. Rebranding. (`654367b56`)

**Renamed**

- Bugfix for 7x ezpublish-kernel to replace vendor name in configuration yaml. Tested. (`037cc410c`)

## v7.5.36 (2026-01-13)

**Updated**

- Testing composer workaround preventing installations. (`95434bfae`)

## v7.5.35 (2025-09-26)

**Updated**

- Update cleandata.sql to replace product name in sql text strings. Rebranding. (`e1467651a`)
- Update doctrine/doctrine-bundle version constraint to test 3.2 integration (`a7c676028`)
- Update jsrouting-bundle version constraints (`13fd781f3`)
- Update PHP version requirement to include 8.3 (`6b9bb39c1`)
- Update kriswallsmith/buzz version constraints (`89c58e905`)

## v7.5.34 (2025-09-05)

**Updated**

- Update layout.html.twig replaced product name in debug layout template. Rebranding. (`3ce50c5a1`)

## v7.5.33 (2025-08-24)

**Updated**

- Update composer.json replaced package dependency liip for se7enxweb. Forking for changes. (`8d2f36ecb`)
- Update ScriptHandler.php to include php 8.2+ tested support required by ezplatform 2.5 gpl. New PHP 8 Support. (`d297f8e66`)
- Update composer.json to include changes to further fork this package. Bugfixes. (`824576e57`)
- Update composer.json to include undesired package version number. Bugfix. (`49c4ffdc3`)

## v7.5.32 (2025-07-01)

**Updated**

- Update composer.json Reduce twig requirement (`3a5ce7adc`)
- Update composer.json version downgrade for doctrine-dbal-schema. (`5ce71d41f`)
- Update composer.json swap vendor for dependency (`28c99098f`)
- Update composer.json version bump for package (`61141719b`)
- Update composer.json package version bump (`54a42dcde`)
- Update composer.json package version bump for greater php support (`824059c6b`)
- Update composer.json version bump (`66a78a034`)

## v8.0.1 (2025-06-11)

**Updated**

- Update composer.json version bump for twig dependency (`f5f432cbe`)

## v8.0.0 (2025-06-11)

**Updated**

- Update composer.json switched package distro name to 7x. Increased PHP version support for testing. (`75dfdbf5b`)
- Update composer.json version bump (`d0ba8f675`)

## After the last tag

**Added**

- CoreInstaller was changed to substitute SqliteDbPlatform for bare SqlitePlatform during schema import to ensure composite primary keys are generated c (`d50c34bdb`)

## Related pages

- [SQLite for the platform](../../features/6.0/platform-sqlite-install.md)
- [package map](../../specifications/6.0/platform-package-map.md)
- [upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)
