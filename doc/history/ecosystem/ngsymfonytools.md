# Ecosystem repository: ngsymfonytools

**Group:** Legacy bridge and site bundles. **Period in the ledger:** 2026-03-02 to 2026-04-16. **Changes:** 7 (7 made by the se7enxweb team, 0 upstream history carried by the fork).

## What it is

Legacy extension that includes Twig templates and Symfony sub-requests from legacy templates.

## How it relates to Exponential

Updated to the Twig service id and an Ibexa repository interface alias.

## What a user gets

A legacy .tpl template can embed a Twig template or a Symfony route.

Install it with Composer (a project that already requires the platform pulls it in by itself):

```bash
composer require se7enxweb/ngsymfonytools
```

## Counts by kind

| Kind | Changes |
|---|---|
| Features | 4 |
| Behaviour and upgrade changes | 1 |
| Tooling | 1 |
| No user benefit | 1 |

## Releases made by the se7enxweb team

| Date | Tag | Commit | Change |
|---|---|---|---|
| 2026-04-05 | 4.0.0.0, 4.x | `1e1a0c6` | Updated: Added replace section to ensure clean override with dependencies. Bugfix. |

## Changes made by the se7enxweb team, by theme

### Design and templates (3)

- 2026-04-05 `979547e` feature: replace removed 'templating' service with 'twig' in symfony_include operator
- 2026-04-16 `8a0c480` feature: use Twig\Environment::class instead of 'twig' service ID
- 2026-04-16 `07b2d1c` feature: Revert: restore 'twig' string ID in include operator

### Composer requirements (1)

- 2026-04-05 `6ba9dc1` tooling: Update package name and license in composer.json

### Replace declarations for the upstream package (1)

- 2026-04-05 `1e1a0c6` bc: Updated: Added replace section to ensure clean override with dependencies. Bugfix.

### Exponential branding (1)

- 2026-04-14 `f7e6d9a` feature: class_alias shim for eZ→Ibexa Repository interface (Ibexa DXP 5.0)

Also: 1 merge or funding-metadata commits by the team (no user benefit; see the coverage file notes).

## Full record

- Every change with date, kind, size and release tag: [ledger of ngsymfonytools](ledger/ngsymfonytools.md).
- Overview of all platform repositories: [Ecosystem](../ecosystem.md).
