# Ecosystem repository: site-legacy-bundle

**Group:** Legacy bridge and site bundles. **Period in the ledger:** 2026-03-16 to 2026-04-17. **Changes:** 10 (10 made by the se7enxweb team, 0 upstream history carried by the fork).

## What it is

Netgen Site Legacy Bundle: glue between the new and the legacy kernel.

## How it relates to Exponential

Ported to eZ Platform 3.3 service names; admin copyright templates carry Exponential branding.

## What a user gets

Legacy admin pages inside Nexus show correct branding and work on platform 3.3.

Install it with Composer (a project that already requires the platform pulls it in by itself):

```bash
composer require se7enxweb/site-legacy-bundle
```

## Counts by kind

| Kind | Changes |
|---|---|
| Features | 1 |
| Fixes | 7 |
| Behaviour and upgrade changes | 1 |
| Releases | 1 |

## Releases made by the se7enxweb team

| Date | Tag | Commit | Change |
|---|---|---|---|
| 2026-03-16 | v2.0.0 | `46a0468` | Update package name and description in composer.json |
| 2026-03-25 | v2.0.2 | `facbd9c` | use se7enxweb/site-bundle ^2.0 and se7enxweb/legacy-bridge ^3.0 for eZ Platform 3.3 compatibility |
| 2026-03-26 | v2.0.3 | `67e6451` | downgrade netgen/metadata-bundle to ^3.0 for eZ Platform 3.3, expand PHP to ^8.5 |
| 2026-03-26 | v2.0.4 | `825e30e` | port all Ibexa namespace/service refs to eZ Platform 3.3 equivalents |
| 2026-03-26 | v2.0.5 | `6cef564` | Fix ImageVariationPass: use named service IDs not FQCN for eZ Platform 3.3 |
| 2026-03-26 | v2.0.6 | `3943714` | Fix search service alias for eZ Platform container |
| 2026-03-26 | v2.0.7 | `3c93610` | Fix XMLText Site API service wiring |
| 2026-03-26 | v2.0.8 | `c46cd8f` | Fix XMLText Site API namespaces |
| 2026-03-27 | v2.0.9 | `a19585e` | replace ibexa_render_field with ez_render_field for eZ Platform 3.x |
| 2026-04-17 | v2.1.0 | `f2deb2e` | rebrand: update admin copyright templates to Exponential / 7x branding |

## Changes made by the se7enxweb team, by theme

### Bug fixes (7)

- 2026-03-26 `67e6451` fix: downgrade netgen/metadata-bundle to ^3.0 for eZ Platform 3.3, expand PHP to ^8.5
- 2026-03-26 `825e30e` fix: port all Ibexa namespace/service refs to eZ Platform 3.3 equivalents
- 2026-03-26 `6cef564` fix: Fix ImageVariationPass: use named service IDs not FQCN for eZ Platform 3.3
- 2026-03-26 `3943714` fix: Fix search service alias for eZ Platform container
- 2026-03-26 `3c93610` fix: Fix XMLText Site API service wiring
- 2026-03-26 `c46cd8f` fix: Fix XMLText Site API namespaces
- 2026-03-27 `a19585e` fix: replace ibexa_render_field with ez_render_field for eZ Platform 3.x

### Composer requirements (1)

- 2026-03-16 `46a0468` release: Update package name and description in composer.json

### Package renamed to the se7enxweb vendor (1)

- 2026-03-25 `facbd9c` bc: use se7enxweb/site-bundle ^2.0 and se7enxweb/legacy-bridge ^3.0 for eZ Platform 3.3 compatibility

### Exponential branding (1)

- 2026-04-17 `f2deb2e` feature: rebrand: update admin copyright templates to Exponential / 7x branding

## Full record

- Every change with date, kind, size and release tag: [ledger of site-legacy-bundle](ledger/site-legacy-bundle.md).
- Overview of all platform repositories: [Ecosystem](../ecosystem.md).
