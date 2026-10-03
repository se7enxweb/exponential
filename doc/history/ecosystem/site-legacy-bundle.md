# site-legacy-bundle: platform repository history

The history of `site-legacy-bundle`, one of the platform repositories around Exponential (group: Legacy bridge and site bundles). Read it to learn what the repository gives you, how it relates to Exponential and when it changed. The ledger records 10 changes from 2026-03-16 to 2026-04-17, all made by the se7enxweb team.

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

## Related pages

- [Site bundles](../../features/6.0/platform-site-bundles.md)
- [Release changelog](../../changelogs/extensions/site-legacy-bundle.md)
- [Package map](../../specifications/6.0/platform-package-map.md)
- [Upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)
- [Ecosystem overview](../ecosystem.md)
- [Complete ledger of this repository](../ledger/site-legacy-bundle.md)
- [Platform console commands](../../specifications/6.0/platform-console-commands.md)
- Platform ecosystem by month: [2026-03](months/2026-03.md), [2026-04](months/2026-04.md)
