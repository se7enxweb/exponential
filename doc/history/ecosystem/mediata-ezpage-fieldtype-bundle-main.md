# Ecosystem repository: mediata-ezpage-fieldtype-bundle-main

**Group:** Field types. **Period in the ledger:** 2026-03-16 to 2026-03-26. **Changes:** 7 (7 made by the se7enxweb team, 0 upstream history carried by the fork).

## What it is

The page (landing page) field type for Ibexa 4.

## How it relates to Exponential

Imported on 2026-03-16 as tested for Ibexa 4.6 with a Twig operator for plain Symfony use.

## What a user gets

Page field type available on Platform 4.6 / 3.3.

Install it with Composer (a project that already requires the platform pulls it in by itself):

```bash
composer require se7enxweb/mediata-ezpage-fieldtype-bundle
```

## Counts by kind

| Kind | Changes |
|---|---|
| Features | 1 |
| Fixes | 1 |
| Behaviour and upgrade changes | 1 |
| Documentation | 1 |
| Tooling | 1 |
| Releases | 1 |
| No user benefit | 1 |

## Releases made by the se7enxweb team

| Date | Tag | Commit | Change |
|---|---|---|---|
| 2026-03-16 | 1.0.0 | `c3c9150` | Updated: Updated version requirement ibexa/core from 4.5 to 4.6 in composer.json. Upgrade. |
| 2026-03-16 | 1.0.1 | `9c2db8e` | Updated: Added twig operator to bundle to provide for pure symfony / platform access to ezflow / ezpage fieldtype block data for display within twig t |
| 2026-03-26 | 1.0.2 | `4630a40` | replace ibexa/core ~4.6.0 with ezsystems/ezplatform-kernel ^1.3, expand PHP to ^8.5 |

## Changes made by the se7enxweb team, by theme

### Test tooling (1)

- 2026-03-16 `6f03e05` tooling: Added: Initial Import of ibexa4 tested as working bundle. Enhancements.

### Documentation (1)

- 2026-03-16 `0e42de9` docs: Added: Added LICENSE.md Documentation. Doc.

### Bug fixes (1)

- 2026-03-16 `94c8b14` fix: Updated: Bugfix for syntax error (trailing comma).

### Composer requirements (1)

- 2026-03-16 `c3c9150` release: Updated: Updated version requirement ibexa/core from 4.5 to 4.6 in composer.json. Upgrade.

### Design and templates (1)

- 2026-03-16 `9c2db8e` feature: Updated: Added twig operator to bundle to provide for pure symfony / platform access to ezflow / ezpage fieldtype block data for display within twig t

### Package renamed to the se7enxweb vendor (1)

- 2026-03-26 `4630a40` bc: replace ibexa/core ~4.6.0 with ezsystems/ezplatform-kernel ^1.3, expand PHP to ^8.5

Also: 1 merge or funding-metadata commits by the team (no user benefit; see the coverage file notes).

## Full record

- Every change with date, kind, size and release tag: [ledger of mediata-ezpage-fieldtype-bundle-main](ledger/mediata-ezpage-fieldtype-bundle-main.md).
- Overview of all platform repositories: [Ecosystem](../ecosystem.md).
