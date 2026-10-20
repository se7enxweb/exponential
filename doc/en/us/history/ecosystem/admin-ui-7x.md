# admin-ui-7x: platform repository history

The history of `admin-ui-7x`, one of the platform repositories around Exponential (group: Admin user interface). Read it to learn what the repository gives you, how it relates to Exponential and when it changed. The ledger records 1193 changes from 2023-12-11 to 2026-04-17: 11 made by the se7enxweb team and 1182 from the upstream history the fork carries.

## What it is

Fork of the Ibexa admin UI (se7enxweb/admin-ui) for Platform v5.

## How it relates to Exponential

The editor interface of Exponential Platform v5 at /adminui/. History is identical to admin-ui-ibexa (same 1,192 upstream commits plus the same se7enxweb changes).

## What a user gets

Exponential logo, favicons, translated strings and asset paths that resolve inside se7enxweb packages, so the admin builds without the upstream vendor directories.

Install it with Composer (a project that already requires the platform pulls it in by itself):

```bash
composer require se7enxweb/admin-ui
```

## Security, performance and upgrade changes in the history

These changes are classified in the coverage record and are not named elsewhere on this page. Most are upstream history that the fork carries; the date and the commit subject are the ledger entry (see the [full ledger](../ledger/README.md)). Read the subject for what changed; for the exact effect, open the commit in the repository.

### Security (7)

- 2024-05-23 `a340f4099` IBX-8140: Enabled authenticator manager-based security (#1264)
- 2025-05-30 `ffe911133` IBX-9944: Add support for stateless CSRF protection in admin-ui (#1556)
- 2025-06-05 `3581728bd` Moved ibexa_get_rest_csrf_token_intention twig function to ibexa/rest package (#1576)
- 2025-06-11 `acaa620d4` IBX-9793: Fixed XSS issues in several places
- 2025-06-11 `72a64d90d` IBX-9793: Fixed XSS issues in several places (see commit description)
- 2025-10-16 `da3bfbfbc` [Security] IBX-10200: Fix XSS in reschedule/cancel-schedule modal
- 2025-10-16 `2016a6933` IBX-10286: Fix Multilevel Popup Menu  XSS

### Performance (2)

- 2024-12-13 `843ac719e` IBX-9314: UDW's suggestions query performance optimization (#1406)
- 2025-07-16 `10871954f` IBX-10331: Fixed memory leak while compiling assets (#1624)

### Behaviour and upgrade (12)

- 2024-06-27 `6eaf79f5b` IBX-8224: Dropped BackwardCompatibleCommand (#1277)
- 2024-11-07 `fa233476e` IBX-8534: Dropped deprecated Relation related methods usage (#1379)
- 2024-12-12 `8bd3e61b2` Enhanced @deprecated phpdoc tag usage (#1398)
- 2025-02-09 `5ac9bf647` [CLI] Replaced deprecated Command::{$defaultName, $defaultDescription} with the AsCommand attribute
- 2025-02-24 `79f6dfc45` Removed usage of deprecated SearchResult::$count property (#1467)
- 2025-02-25 `c0bcfc9f4` [Twig] Removed usage of deprecated spaceless filter (#1468)
- 2025-02-25 `99b09a60f` Allowed symfony/deprecation-contracts ^3.0 installation (#1471)
- 2025-05-17 `4078a4c6b` [twig/twig] Removed usage of deprecated spaceless filter
- 2025-05-28 `84cd19fd0` Removed twig deprecation (#1564)
- 2025-06-03 `c99327e74` Removed deprecated usage of Ibexa\Core\Repository\Values\User\User::$content property (#1575)
- 2025-07-17 `04e5be630` Update deprecation removal version from 5.0 to 6.0 in admin-ui components (#1642)
- 2025-07-28 `75cc63661` Adapted to deprecated `null` value for content structs in `BaseContentType` (#1646)

<!-- rev2-listed-rows:end -->

## Counts by kind

| Kind | Changes |
|---|---|
| Features | 372 |
| Fixes | 220 |
| Behaviour and upgrade changes | 21 |
| Security | 7 |
| Performance | 3 |
| Documentation | 20 |
| Tooling | 127 |
| No user benefit | 423 |

## Releases made by the se7enxweb team

| Date | Tag | Commit | Change |
|---|---|---|---|
| 2026-04-17 | v5.0.4 | `fece63895` | rebrand: update favicons to se7enxweb branding |

## Changes made by the se7enxweb team, by theme

### Exponential branding (3)

- 2026-03-21 `68c431f3e` feature: Added Exponential Platform DXP Logo SVG Image File for admin-ui. Rebranding.
- 2026-04-16 `69fc077c3` feature: translations: rebrand Ibexa DXP -> Exponential Platform DXP in messages.en.xliff  Updated 3 translation strings in messages.en.xliff to use Exponential Platform DXP (...)
- 2026-04-17 `fece63895` feature: rebrand: update favicons to se7enxweb branding

### Package renamed to the se7enxweb vendor (3)

- 2026-03-21 `4deba2de5` bc: Replacing package vendor. Rebrading.
- 2026-03-22 `582cbedbe` bc: Replacing key encore build process internal path resolution strings to point to forked vendor storage. Bugfix.
- 2026-04-16 `8ea7f259c` bc: replace vendor/ibexa/admin-ui-assets with vendor/se7enxweb/admin-ui-assets in encore configs

### Composer requirements (1)

- 2026-03-21 `1412184b6` tooling: Added package replacement statement to package composer.json. Bugfix.

### Bug fixes (1)

- 2026-03-25 `7dc60cada` fix: Bugfix for missing text field label translation strings specificly within sub items view page controls. Bugfix.

Also: 3 merge or funding-metadata commits by the team (no user benefit; see the coverage file notes).

## Upstream history carried by the fork, by month

The fork contains the full upstream history. The table counts it by month and kind; the busiest changes of each month (by files touched) are named.

| Month | Changes | Features | Fixes | BC | Security | Perf | Docs | Tooling | Release | No benefit | Busiest changes |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2023-12 | 35 | 18 | 4 | 0 | 0 | 0 | 0 | 5 | 0 | 8 | `15704ef6f` IBX-6398: UDW as standalone as GH package (#1010); `06ec78689` IBX-7329: Enhancing UX: Name Updates for Product 4.6 LTS+ (#1040) |
| 2024-01 | 77 | 42 | 18 | 0 | 0 | 0 | 2 | 6 | 0 | 9 | `e518224eb` IBX-7409: Changed Content Type to content type (#1087); `925b98844` IBX-7525: UDW as npm package (#1062) |
| 2024-02 | 43 | 22 | 15 | 0 | 0 | 0 | 0 | 4 | 0 | 2 | `293b8bcfc` Updated PHPUnit to 9.5; `5053ee9b0` IBX-7107: Tabs validation updated (#1048) |
| 2024-03 | 63 | 18 | 11 | 0 | 0 | 0 | 2 | 3 | 0 | 29 | `79ce1a1c7` IBX-7901: [UDW] Removing from bookmarks content is still visible on bookmark list (#1209); `28a9fb8f6` IBX-7085: Missing text for empty screens (#1185) |
| 2024-04 | 48 | 17 | 5 | 0 | 0 | 0 | 0 | 1 | 0 | 25 | `ba368d6a3` IBX-7816: ALW files upload (#1184); `3f23e9173` IBX-7717: [REST] Implemented extended-info endpoint for UDW (#1197) |
| 2024-05 | 24 | 7 | 3 | 0 | 1 | 0 | 0 | 3 | 0 | 10 | `da76e0066` IBX-8121: Fixed code style for 5.0; `38d971c96` IBX-8142: removed depreacated code (#1248) |
| 2024-06 | 37 | 12 | 3 | 2 | 0 | 0 | 1 | 3 | 0 | 16 | `26f130a90` IBX-8139: Dropped class_alias BC layer statements from all classes (#1267); `200eb6c6d` IBX-7198: Updated ibexa label margin, form styling in edit and create (#1220) |
| 2024-07 | 44 | 10 | 8 | 1 | 0 | 0 | 0 | 3 | 0 | 22 | `a3b152e1b` IBX-8110: ALW images edit (#1282); `67f1aa4f9` IBX-8593: Remove deprecation warnings from SCSS build after upgrading sass package (#1293) |
| 2024-08 | 49 | 15 | 4 | 1 | 0 | 0 | 1 | 6 | 0 | 22 | `1d4f94aec` IBX-8138: [Rector] Applied rules from Symfony 5 Rector set lists (#1294); `dd3a7fcbc` IBX-8533: Dropped deprecated code (#1313) |
| 2024-09 | 35 | 12 | 3 | 0 | 0 | 0 | 0 | 6 | 0 | 14 | `e3594d019` Extracted menu builder from ProductCatalog package (#1344); `d01569c0a` IBX-8626: Create action menu component foundations (#1318) |
| 2024-10 | 40 | 7 | 10 | 0 | 0 | 0 | 2 | 2 | 0 | 19 | `c80979b98` IBX-8525: Added language code validation (#1356); `974dfdc3e` IBX-8968: Fixed translation selector in search and dashboard (#1362) |
| 2024-11 | 30 | 8 | 6 | 2 | 0 | 0 | 0 | 0 | 0 | 14 | `ef7f07bbc` IBX-8805: Dropped deprecated Twig Functions&Filters (#1390); `56724eefe` IBX-9227: Restore multipleItemsLimit cehcks in UDW (#1396) |
| 2024-12 | 33 | 10 | 3 | 1 | 0 | 2 | 1 | 5 | 0 | 11 | `c890067c0` IBX-9069: Initial Product Tab (#1397); `06d1b32b0` IBX-9289: `NodeFactory` optimization (#1405) |
| 2025-01 | 30 | 11 | 5 | 0 | 0 | 0 | 0 | 0 | 0 | 14 | `d6d83c769` IBX-9002: Added endpoint for list users with permission info (#1372); `11d3fdea0` IBX-9322: ezobjectrelationlist field allows selecting the same content multiple times (#1409) |
| 2025-02 | 70 | 19 | 14 | 4 | 0 | 0 | 1 | 12 | 0 | 20 | `b9575cf69` IBX-8470: Upgraded codebase to Symfony 6 (#1415); `d24630a63` Added missing return types and removed redundant PhpDocs in Ibexa\AdminUi\Form\Type\**  (#1455) |
| 2025-03 | 47 | 12 | 11 | 1 | 0 | 0 | 0 | 2 | 0 | 21 | `106ac8948` Removed deprecated ContentType::isContainer property (#1472); `b6c6fd35f` IBX-9628: ellipsized details content (#1466) |
| 2025-04 | 27 | 7 | 3 | 0 | 0 | 0 | 0 | 6 | 0 | 11 | `f5b1aedf3` IBX-9727: Added missing type hints (#1504); `036964bc0` IBX-9722: Replaced graphql subitems fetching with dedicated REST endpoint (#1503) |
| 2025-05 | 54 | 12 | 10 | 3 | 1 | 0 | 0 | 12 | 0 | 16 | `c0adbf5b1` IBX-9940: Removed Sass deprecations (#1548); `3eb6d1b14` IBX-10066: Change lint config to 2.0 (#1558) |
| 2025-06 | 46 | 8 | 16 | 1 | 3 | 0 | 0 | 4 | 0 | 14 | `1026f2c42` IBX-7845: Added icon mapping configuration and resolver method (#1559); `abba9125c` IBX-9947: Rebranded field type identifiers (#1560) |
| 2025-07 | 82 | 18 | 25 | 2 | 0 | 1 | 1 | 9 | 0 | 26 | `1cf067374` IBX-9807: Introduce date range single component (#1582); `ad273306c` IBX-10299: Moved users-with-permission-info endpoint to ibexa/share (#1631) |
| 2025-08 | 49 | 23 | 5 | 0 | 0 | 0 | 1 | 1 | 0 | 19 | `540f0a101` IBX-9727: Added type-hints and adapted codebase to PHP8+ for Behat-related code (#1674); `7c931cf75` IBX-9727: Added type-hints and adapted codebase to PHP8+ within `src/b |
| 2025-09 | 44 | 13 | 7 | 0 | 0 | 0 | 0 | 8 | 0 | 16 | `d922ce6e5` IBX-9727: Aligned library code with PHP8+ and added missing strict types (#1697); `a3ddff418` IBX-9727: Added typehints and adapted forms-related code to PHP8 (#1692) |
| 2025-10 | 39 | 8 | 7 | 0 | 2 | 0 | 2 | 5 | 0 | 15 | `1cc922812` IBX-10429: [Collaboration] UI improvements for the header (#1682); `de085b476` IBX-9840: Implemented service fetching content fields from expression and validating if field is within expression (#1500) |
| 2025-11 | 48 | 14 | 9 | 0 | 0 | 0 | 1 | 8 | 0 | 16 | `1117cc03e` [CI] Fixed missing closure return types (#1780); `f0b0128ac` IBX-10936: Implemented fetching field definitions from an expression (#1765) |
| 2025-12 | 33 | 7 | 8 | 0 | 0 | 0 | 0 | 6 | 0 | 12 | `afd71af83` IBX-9266: Added REST endpoint loading available site accesses for location (#1759); `abb1d869f` IBX-11029: Fixed tests (#1790) |
| 2026-01 | 12 | 2 | 4 | 0 | 0 | 0 | 0 | 2 | 0 | 4 | `1f29214cd` IBX: 11010 improve anchor links switcher (#1805); `9654b2183` IBX-9519: [Behat] Added Behat content tree coverage (#1685) |
| 2026-02 | 27 | 9 | 2 | 0 | 0 | 0 | 2 | 3 | 0 | 11 | `1ae827db4` IBX-9976: Fixed styles for invalid fields labels (#1647); `b376bc7d4` IBX-10186: Added limits to repository child count queries and subtree operations (#1815) |
| 2026-03 | 16 | 8 | 0 | 0 | 0 | 0 | 3 | 1 | 0 | 4 | `3455b88fc` IBX-10827: Added extensibility point to richtext config (#1816); `4e7e48634` [Behat] IBX-11133: Added enabled option for Help center (#1808) |

## Related pages

- [Platform admin interface](../../features/6.0/platform-admin-ui-fork.md)
- [Release changelog](../../changelogs/extensions/admin-ui.md)
- [Package map](../../specifications/6.0/platform-package-map.md)
- [Upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)
- [Ecosystem overview](../ecosystem.md)
- [Complete ledger of this repository](../ledger/admin-ui-7x.md)
- Platform ecosystem by month: [2023-12](months/2023-12.md), [2024-01](months/2024-01.md), [2024-02](months/2024-02.md), [2024-03](months/2024-03.md), [2024-04](months/2024-04.md), [2024-05](months/2024-05.md), [2024-06](months/2024-06.md), [2024-07](months/2024-07.md), [2024-08](months/2024-08.md), [2024-09](months/2024-09.md), [2024-10](months/2024-10.md), [2024-11](months/2024-11.md), [2024-12](months/2024-12.md), [2025-01](months/2025-01.md), [2025-02](months/2025-02.md), [2025-03](months/2025-03.md), [2025-04](months/2025-04.md), [2025-05](months/2025-05.md), [2025-06](months/2025-06.md), [2025-07](months/2025-07.md), [2025-08](months/2025-08.md), [2025-09](months/2025-09.md), [2025-10](months/2025-10.md), [2025-11](months/2025-11.md), [2025-12](months/2025-12.md), [2026-01](months/2026-01.md), [2026-02](months/2026-02.md), [2026-03](months/2026-03.md), [2026-04](months/2026-04.md)
