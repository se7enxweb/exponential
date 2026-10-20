# ngsymfonytools: release notes

Read this page before you install or update `ngsymfonytools`, or to find out which release brought a change.

Releases made by the se7enxweb team, newest first. Each release lists the team's changes since the previous tag, grouped as Added, Updated, Removed and Renamed. Upstream history carried by the fork is not repeated here; it is in the [repository page](../../eng-us/history/ecosystem/ngsymfonytools.md).

## 4.0.0.0, 4.x (2026-04-05)

**Added**

- Added replace section to ensure clean override with dependencies. Bugfix. (`1e1a0c6`)

**Updated**

- Update package name and license in composer.json (`6ba9dc1`)
- Replace removed 'templating' service with 'twig' in symfony_include operator (`979547e`)

## After the last tag

**Updated**

- class_alias shim for eZ→Ibexa Repository interface (Ibexa DXP 5.0) (`f7e6d9a`)
- Use Twig\Environment::class instead of 'twig' service ID (`8a0c480`)
- Revert: restore 'twig' string ID in include operator (`07b2d1c`)

## Related pages

- [Site bundles](../../eng-us/features/6.0/platform-site-bundles.md)
- [package map](../../eng-us/specifications/6.0/platform-package-map.md)
- [upgrade notes](../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md)
- [Feature page](../../eng-us/features/6.0/extensions/ngsymfonytools.md)
- [behaviour changes of the extensions](../../eng-us/bc/6.0/extensions-behaviour-changes.md)
