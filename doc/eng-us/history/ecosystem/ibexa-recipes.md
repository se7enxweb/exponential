# ibexa-recipes: platform repository history

The history of `ibexa-recipes`, one of the platform repositories around Exponential (group: Layouts and recipes). Read it to learn what the repository gives you, how it relates to Exponential and when it changed. The ledger records 142 changes from 2023-12-14 to 2026-03-02: 1 made by the se7enxweb team and 141 from the upstream history the fork carries.

## What it is

Symfony Flex recipes of the platform packages.

## How it relates to Exponential

Mirror of the upstream recipes repository; the se7enxweb endpoint is sevenx-recipes.

## What a user gets

Flex configures bundles on composer require.

## Security, performance and upgrade changes in the history

These changes are classified in the coverage record and are not named elsewhere on this page. Most are upstream history that the fork carries; the date and the commit subject are the ledger entry (see the [full ledger](../ledger/README.md)). Read the subject for what changed; for the exact effect, open the commit in the repository.

### Security (5)

- 2024-05-23 `cf3ce7f` IBX-8140: Enabled authenticator manager-based security (#118)
- 2024-08-14 `8a7c7ef` IBX-8656: Reworked OAuth2 client authentication to comply with new Symfony security (#133)
- 2024-08-20 `1a4333f` IBX-8657: Reworked CDP security layer to not rely on deprecated guard authenticators (#136)
- 2024-08-21 `8b9ed28` IBX-8691: Simplified security config for OAuth2 server usages (#135)
- 2025-05-29 `3ec1063` IBX-9944: Add support for stateless CSRF protection in admin-ui (#183)

<!-- rev2-listed-rows:end -->

## Counts by kind

| Kind | Changes |
|---|---|
| Features | 67 |
| Fixes | 12 |
| Behaviour and upgrade changes | 2 |
| Security | 5 |
| Performance | 1 |
| Documentation | 1 |
| Tooling | 20 |
| No user benefit | 34 |

Also: 1 merge or funding-metadata commits by the team (no user benefit; see the coverage file notes).

## Upstream history carried by the fork, by month

The fork contains the full upstream history. The table counts it by month and kind; the busiest changes of each month (by files touched) are named.

| Month | Changes | Features | Fixes | BC | Security | Perf | Docs | Tooling | Release | No benefit | Busiest changes |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2023-12 | 5 | 3 | 0 | 0 | 0 | 0 | 0 | 2 | 0 | 0 | `678cde6` IBX-7332: Updated graphic request in Welcome Page: Content and Commerce (#101); `12d5fae` IBX-6645: As the User I want to change my data and avatar in User profile (#99) |
| 2024-01 | 5 | 2 | 1 | 0 | 0 | 0 | 0 | 1 | 0 | 1 | `c601a78` IBX-7525: UDW as npm package (#106); `e77012d` IBX-7242: Excluded "Product categories", "Tags" and "Corporate account" locations from site context (#105) |
| 2024-02 | 4 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 3 | `bc4c912` IBX-7611: Bump version for webpack and sass-loader (#108); `e8417d6` [ibexa/docker] Set the default image to php:8.2-node18 (#109) |
| 2024-03 | 2 | 0 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `841db1b` [ALL] Added recipes for 5.0 (#111); `5cd134c` IBX-3740: Fixed resolving `ELASTICSEARCH_DSN` variable for Platform.sh |
| 2024-04 | 2 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `29c5275` IBX-7687: Add editor content icon to configuration above OSS (#116) |
| 2024-05 | 8 | 4 | 0 | 0 | 1 | 0 | 0 | 2 | 0 | 1 | `e2314fb` [All] Added app-switcher and headless-assets bundles (#115); `bbc8fa4` IBX-8144: [Rebranding] Remove compatibility layer from webpack encore (#117) |
| 2024-06 | 3 | 2 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `53e9df8` IBX-8290: Reworked REST authentication to comply with the new Symfony authenticator mechanism under separate firewall (#121); `bf4c8ff` IBX-8356: Reworked JWT firewall to be in-tact with the new Symfony auth (#122) |
| 2024-07 | 13 | 7 | 1 | 0 | 0 | 0 | 0 | 1 | 0 | 4 | `3fd2560` IBX-8150: Added enabled attributes types configuration (#123); `aa3a512` IBX-8356: Reworked JWT GraphQL firewall configuration to comply with Symfony-based authentication (#124) |
| 2024-08 | 7 | 3 | 0 | 0 | 3 | 0 | 0 | 0 | 0 | 1 | `564c4ef` IBX-8137: [ibexa/*] Dropped Swiftmailer bundle (#139); `f28dc8c` IBX-8817: Documented `http_basic` authentication config (#138) |
| 2024-09 | 4 | 3 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `fbfc208` IBX-8775: Added recipe for ibexa/product-catalog-symbol-attribute (#137); `8c7ee96` IBX-8784: Added ibexa/connector-qualifio REST routes |
| 2024-10 | 5 | 2 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 2 | `36e2220` Enabled ibexa/core-search (#143); `3460a12` Added recipe for ibexa/discounts package (#144) |
| 2024-11 | 2 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `c136fb8` IBX-9094: Added recipe for ibexa/product-catalog-date-time-attribute (#147) |
| 2024-12 | 3 | 0 | 1 | 0 | 0 | 0 | 0 | 1 | 0 | 1 | `a317c26` IBX-9109: Enabling TypeScript (ts-loader) with Webpack Encore (#145); `c22e2bd` IBX-8732: Fixed ibexa/collaboration recipe (#148) |
| 2025-01 | 3 | 3 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `0516c1b` IBX-9419: Updated ts-config tag (#151); `148032b` Updated copyright year to 2025 |
| 2025-02 | 6 | 3 | 1 | 0 | 0 | 0 | 0 | 2 | 0 | 0 | `6b54c91` IBX-8470: Upgraded codebase to Symfony 6 (#150); `dee2f4d` [ibexa/test-fixtures] Fixed configuration recipes (#160) |
| 2025-03 | 13 | 4 | 1 | 1 | 0 | 0 | 0 | 2 | 0 | 5 | `0e7fcc3` IBX-9584: Resolved Symfony 6.x deprecations vol. 2 (#159); `a281298` Added `IbexaDiscountsCodes` bundle to recipes |
| 2025-04 | 4 | 3 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `d5dac94` IBX-9339: Added recipe for ibexa/fieldtype-richtext-rte (#149); `19ebaae` IBX-8552: Changed customer registration group id to remote id (#165) |
| 2025-05 | 11 | 3 | 0 | 1 | 1 | 0 | 0 | 5 | 0 | 1 | `c88b6e8` IBX-9940: Removed Sass deprecations (#177); `a32d816` IBX-9939: TypeScript aliases for bundles (#171) |
| 2025-06 | 7 | 6 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `d811f15` IBX-7845: Changed icons set to IDS Icons (#182); `1538eba` Allowed to install `ibexa/personalization` as opt-in package for 5.0 ( |
| 2025-07 | 23 | 12 | 4 | 0 | 0 | 1 | 1 | 2 | 0 | 3 | `678d3e2` IBX-10173: Extract webpack files from root dir (#191); `339cbc9` IBX-10331: Fixed memory leak while compiling assets (#200) |
| 2025-08 | 4 | 1 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 2 | `9ff3d90` [CS] Fixed indent in webpack.config.js (#213); `5eb7667` IBX-9353: Added default mailer configuration with global sender (4.6) (#214) |
| 2025-09 | 6 | 4 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `a00b6c4` IBX-10640: Changed order of assets compilation (#218); `af0aaea` IBX-10525: Added shareable_link firewall (#215) |
| 2025-10 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 |  |

## Related pages

- [Package map](../../specifications/6.0/platform-package-map.md)
- [Upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)
- [Ecosystem overview](../ecosystem.md)
- [Complete ledger of this repository](../ledger/ibexa-recipes.md)
- [Layouts core fork](../../features/6.0/platform-layouts-core-fork.md)
- Platform ecosystem by month: [2023-12](months/2023-12.md), [2024-01](months/2024-01.md), [2024-02](months/2024-02.md), [2024-03](months/2024-03.md), [2024-04](months/2024-04.md), [2024-05](months/2024-05.md), [2024-06](months/2024-06.md), [2024-07](months/2024-07.md), [2024-08](months/2024-08.md), [2024-09](months/2024-09.md), [2024-10](months/2024-10.md), [2024-11](months/2024-11.md), [2024-12](months/2024-12.md), [2025-01](months/2025-01.md), [2025-02](months/2025-02.md), [2025-03](months/2025-03.md), [2025-04](months/2025-04.md), [2025-05](months/2025-05.md), [2025-06](months/2025-06.md), [2025-07](months/2025-07.md), [2025-08](months/2025-08.md), [2025-09](months/2025-09.md), [2025-10](months/2025-10.md), [2026-03](months/2026-03.md)
