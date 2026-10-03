# ezplatform-design-engine: platform repository history

The history of `ezplatform-design-engine`, one of the platform repositories around Exponential (group: Admin user interface). Read it to learn what the repository gives you, how it relates to Exponential and when it changed. The ledger records 3 changes from 2026-03-31 to 2026-04-12, all made by the se7enxweb team.

## What it is

Design fallback mechanism (theme chain) for the platform.

## How it relates to Exponential

Replaces ezsystems/ezplatform-design-engine with a wildcard replace declaration.

## What a user gets

Theme fallback keeps working in forked installs.

Install it with Composer (a project that already requires the platform pulls it in by itself):

```bash
composer require se7enxweb/ezplatform-design-engine
```

## Counts by kind

| Kind | Changes |
|---|---|
| Behaviour and upgrade changes | 2 |
| Tooling | 1 |

## Changes made by the se7enxweb team, by theme

### Composer requirements (1)

- 2026-03-31 `0d6416a` tooling: Bugfix for composer validation of composer.json for packagist.org package to update normally. Bugfix.

### Replace declarations for the upstream package (1)

- 2026-04-11 `1f401d1` bc: add replace shim for ezsystems counterpart package

### Package renamed to the se7enxweb vendor (1)

- 2026-04-12 `87c62d1` bc: replace ezsystems/ezplatform-design-engine with wildcard version

## Related pages

- [Platform admin interface](../../features/6.0/platform-admin-ui-fork.md)
- [Package map](../../specifications/6.0/platform-package-map.md)
- [Upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)
- [Ecosystem overview](../ecosystem.md)
- [Complete ledger of this repository](../ledger/ezplatform-design-engine.md)
- Platform ecosystem by month: [2026-03](months/2026-03.md), [2026-04](months/2026-04.md)
