# Ecosystem repository: fieldtype-richtext-ibexa

**Group:** Field types. **Period in the ledger:** 2023-12-14 to 2026-04-19. **Changes:** 244 (2 made by the se7enxweb team, 242 upstream history carried by the fork).

## What it is

Fork of the Ibexa RichText field type for Platform v5 (se7enxweb/fieldtype-richtext).

## How it relates to Exponential

Rich text editing for Exponential Platform v5; fixes hard-coded vendor paths so XSL stylesheets and the webpack alias resolve in the fork.

## What a user gets

Rich text field renders and edits correctly when installed from se7enxweb packages.

Install it with Composer (a project that already requires the platform pulls it in by itself):

```bash
composer require se7enxweb/fieldtype-richtext
```

## Where to read more

- [Platform admin interface](../../features/6.0/platform-admin-ui-fork.md)
- [Release changelog](../../changelogs/extensions/fieldtype-richtext-ibexa.md)
- [Package map](../../specifications/6.0/platform-package-map.md)
- [Upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)

## Counts by kind

| Kind | Changes |
|---|---|
| Features | 51 |
| Fixes | 42 |
| Behaviour and upgrade changes | 13 |
| Security | 4 |
| Performance | 1 |
| Documentation | 1 |
| Tooling | 42 |
| No user benefit | 90 |

## Releases made by the se7enxweb team

| Date | Tag | Commit | Change |
|---|---|---|---|
| 2026-04-19 | 5.0.0 | `1d4b855` | Fork: rename to se7enxweb/fieldtype-richtext, fix  webpack alias for se7enxweb/admin-ui compatibility |

## Changes made by the se7enxweb team, by theme

### Package renamed to the se7enxweb vendor (2)

- 2026-04-19 `1d4b855` bc: Fork: rename to se7enxweb/fieldtype-richtext, fix  webpack alias for se7enxweb/admin-ui compatibility
- 2026-04-19 `95289ad` bc: replace hardcoded vendor/ibexa/fieldtype-richtext paths with se7enxweb

## Upstream history carried by the fork, by month

The fork contains the full upstream history. The table counts it by month and kind; the busiest changes of each month (by files touched) are named.

| Month | Changes | Features | Fixes | BC | Security | Perf | Docs | Tooling | Release | No benefit | Busiest changes |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2023-12 | 2 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `2ba22d5` IBX-7329: Enhancing UX: Name Updates for Product 4.6 LTS+ (#138); `97c1b76` IBX-7441: Fixed custom attributes in link (#140) |
| 2024-01 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `cba4967` Added event to manipulate CKEditor config before initialization  (#141 |
| 2024-02 | 11 | 1 | 6 | 0 | 0 | 0 | 0 | 0 | 0 | 4 | `058d349` IBX-6932: Fixed adding custom attrs to list (#139); `ef78778` IBX-6379: Fixed anchor in formatted (#144) |
| 2024-03 | 17 | 2 | 4 | 0 | 0 | 0 | 0 | 3 | 0 | 8 | `f3cb1bc` Added test for translation extraction (#151); `f315398` IBX-7503: Fixed custom attributes (#152) |
| 2024-04 | 5 | 3 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 2 | `158be52` IBX-8119: Upgraded minimum PHP version to 8.3; `1e5ed43` Made 'Link to' field in link module required (#157) |
| 2024-05 | 3 | 1 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 1 | `8568d2e` IBX-8121: Fixed code style for 5.0; `e005bb6` Updated copyright year to 2024 |
| 2024-06 | 7 | 1 | 0 | 1 | 0 | 0 | 1 | 1 | 0 | 3 | `ddc118f` IBX-8139: Dropped class_alias BC layer statements from all classes (#1; `b1ec1be` Added translation extractor for custom tags (#162) |
| 2024-07 | 13 | 4 | 1 | 0 | 0 | 0 | 0 | 1 | 0 | 7 | `1d29e0c` IBX-8434: Added ProviderConfiguratorInterface to allow configuration p; `5ebad1e` IBX-8150: Added option to enable attributes types (#167) |
| 2024-08 | 18 | 3 | 1 | 1 | 0 | 0 | 0 | 5 | 0 | 8 | `c5d18f3` [Rector] Applied all Symfony 5.x rectors to the production codebase (#; `dac171d` IBX-8150: Added option to enable attributes types (#179) |
| 2024-09 | 10 | 1 | 1 | 1 | 0 | 0 | 0 | 2 | 0 | 5 | `1d7a8da` IBX-8875: Dropped deprecated code (#185); `2ef456b` IBX-8824: The popup for custom tags with multiple fields is not scroll |
| 2024-10 | 4 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 2 | `e9bbcaf` IBX-8845: Added alignments for custom tags (#187); `9e9924f` IBX-9097: [PB] Error with Richtext validator in nested_attribute (#191 |
| 2024-11 | 3 | 0 | 1 | 2 | 0 | 0 | 0 | 0 | 0 | 0 | `c268ebf` IBX-8534: Dropped deprecated context and getIndexData from Storage (#1; `ef174e2` IBX-8805: Dropped deprecated Twig Functions&Filters (#192) |
| 2024-12 | 5 | 3 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 2 | `f6b1f85` IBX-9154: The dropdown for custom class is illegible (#193); `fe4db77` IBX-9319-TS-support (#198) |
| 2025-01 | 6 | 2 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 3 | `21f1dbc` IBX-9297: Custom CSS can not be removed from link element (#199); `ef64b0a` IBX-9296: Custom CSS classes not working for "embedimage" (#197) |
| 2025-02 | 23 | 2 | 6 | 3 | 0 | 0 | 0 | 8 | 0 | 4 | `ab8c8af` IBX-9298: Fixed visual indication of selected custom CSS classes (#200; `ce80180` Fixed obsolete `ibexa_content` controller references (#205) |
| 2025-03 | 10 | 2 | 2 | 0 | 0 | 0 | 0 | 2 | 0 | 4 | `be60c4a` Fixed PHP 8.1 PHPstan issue report; `cb92634` [PHPStan] Set `treatPhpDocTypesAsCertain` to `false` (#219) |
| 2025-04 | 12 | 1 | 2 | 0 | 2 | 0 | 0 | 0 | 0 | 7 | `b5a8382` IBX-9727: Added missing type hints (#222); `53d1e60` Backported fix for backward-compatibility issue on XMLSanitizer |
| 2025-05 | 16 | 5 | 1 | 3 | 0 | 0 | 0 | 4 | 0 | 3 | `c20af5d` IBX-9916: Upgrade frontend dependencies (#229); `82d9e36` IBX-8471: Upgraded codebase to Symfony 7 (#217) |
| 2025-06 | 7 | 3 | 0 | 0 | 1 | 0 | 0 | 2 | 0 | 1 | `a8ca1c5` IBX-9947: Rebranded field type identifiers (#238); `4a4a170` IBX-9867: Fixed XSS in embed in rich text |
| 2025-07 | 17 | 4 | 3 | 0 | 0 | 1 | 0 | 4 | 0 | 5 | `63136d4` IBX-9727: Applied missing strict types (#252); `372c366` IBX-10262: Renamed and refactored `CustomTagsValidator` to support bot |
| 2025-08 | 3 | 0 | 1 | 0 | 0 | 0 | 0 | 1 | 0 | 1 | `adce2b9` Fixed PHPStan issue (#260); `1f07cef` IBX-10316: Fixed editing linked embed image (#261) |
| 2025-09 | 11 | 5 | 2 | 0 | 0 | 0 | 0 | 0 | 0 | 4 | `e0ecc27` IBX-10507: Allowed nullable type declarations for method parameters ac; `52ab56d` IBX-9981: Added go to and edit image (#264) |
| 2025-10 | 10 | 0 | 3 | 0 | 1 | 0 | 0 | 3 | 0 | 3 | `00554a9` [CI] Adjusted gh actions and PHPStan (#268); `33590a1` IBX-10288: Fix XSS in custom tags |
| 2025-11 | 10 | 0 | 3 | 0 | 0 | 0 | 0 | 1 | 0 | 6 | `b58bebc` IBX-9657: Added option to set shouldFireInputEvent in updateInput via ; `5c5d806` IBX-10725: Fixed image filter dropdown on first selection (#271) |
| 2025-12 | 4 | 1 | 0 | 0 | 0 | 0 | 0 | 2 | 0 | 1 | `2091241` IBX-9266: Allowed the selection of site access when creating a RTE lin; `f2903e6` [Composer] Fixed license information |
| 2026-01 | 2 | 0 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `80e31c1` IBX-9266: Fixed siteaccess handling for image embed links (#280) |
| 2026-02 | 6 | 1 | 1 | 0 | 0 | 0 | 0 | 1 | 0 | 3 | `f6647c6` IBX-11179: Updated PHP versions in CI configuration with 8.3 and 8.4 (; `c701a4a` IBX-10897: Fixed links on embed image (#282) |
| 2026-03 | 4 | 3 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `84ae86e` IBX-10827: Added option to extend Exponential.rng schema (#281); `0efe2d9` IBX-11247: Bumped `symfony/*` to 7.4 LTS (#286) |
| 2026-04 | 2 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 1 | `a84e349` Refactor PHP code to fix phpstan and added error baselines. (#291) |

## Full record

- Every change with date, kind, size and release tag: [ledger of fieldtype-richtext-ibexa](../ledger/fieldtype-richtext-ibexa.md).
- Overview of all platform repositories: [Ecosystem](../ecosystem.md).
