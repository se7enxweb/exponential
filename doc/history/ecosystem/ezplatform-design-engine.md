# Ecosystem repository: ezplatform-design-engine

**Group:** Admin user interface. **Period in the ledger:** 2026-03-31 to 2026-04-12. **Changes:** 3 (3 made by the se7enxweb team, 0 upstream history carried by the fork).

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

- 2026-03-31 `0d6416a` tooling: Updated: Bugfix for composer validation of composer.json for packagist.org package to update normally. Bugfix.

### Replace declarations for the upstream package (1)

- 2026-04-11 `1f401d1` bc: add replace shim for ezsystems counterpart package

### Package renamed to the se7enxweb vendor (1)

- 2026-04-12 `87c62d1` bc: replace ezsystems/ezplatform-design-engine with wildcard version

## Full record

- Every change with date, kind, size and release tag: [ledger of ezplatform-design-engine](ledger/ezplatform-design-engine.md).
- Overview of all platform repositories: [Ecosystem](../ecosystem.md).
