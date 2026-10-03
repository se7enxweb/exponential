# Platform package map

Which se7enxweb package replaces which upstream package, what PHP it accepts and its newest tag. Facts are read from each repository's `composer.json` and tags; the history of each repository is linked in the first column.

How replacement works: a package that lists another package under `replace` tells Composer that it provides that package. Composer then installs only the fork, and every other package that requires the upstream name is satisfied by it. This is why a project can require `ibexa/*` or `ezsystems/*` names in third-party packages and still get se7enxweb code. See [upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md).

## Distributions and starters

| Repository | Composer package | Replaces | PHP | Newest tag |
|---|---|---|---|---|
| [cjw-exponential-platform-nexus](../../history/ecosystem/cjw-exponential-platform-nexus.md) | `se7enxweb/cjw-exponential-platform-nexus` | `ezsystems/ezpublish-community`, `ezsystems/ezpublish-kernel` | `^7.1.3 || ^7.2 || ^7.4 || ^8.0 || ^8....` | 1.0.0.6 (2026-04-22) |
| [exponential-legacy-installer](../../history/ecosystem/exponential-legacy-installer.md) | `se7enxweb/exponential-legacy-installer` | `ezsystems/ezpublish-legacy-installer`, `se7enxweb/ezpublish-legacy-installer` | `^7.4 || ^8.1 || ^8.2` | 2.2.3 (2026-06-19) |
| [exponential-platform](../../history/ecosystem/exponential-platform.md) | `se7enxweb/exponential-platform` | `paragonie/random_compat`, `symfony/polyfill-ctype`, `symfony/polyfill-iconv` (+3) | `^7.3 || ^8.1 || ^8.2 || ^8.3 || ^8.4` | v3.2.9 (2025-09-28) |
| [exponential-platform-dxp](../../history/ecosystem/exponential-platform-dxp.md) | `se7enxweb/exponential-platform-dxp` | - | `>=8.3` | v0.0.0.2 (2026-03-21) |
| [exponential-platform-dxp-skeleton](../../history/ecosystem/exponential-platform-dxp-skeleton.md) | `se7enxweb/exponential-platform-dxp-skeleton` | `symfony/polyfill-ctype`, `symfony/polyfill-iconv`, `symfony/polyfill-php72` | `>=8.3` | - |
| [exponential-platform-legacy](../../history/ecosystem/exponential-platform-legacy.md) | `se7enxweb/exponential-platform-legacy` | `ezsystems/ezpublish-community`, `ezsystems/ezpublish-kernel` | `^7.1.3 || ^8.1 || ^8.2` | v5.0.2 (2026-04-16) |
| [exponential-platform-nexus](../../history/ecosystem/exponential-platform-nexus.md) | `se7enxweb/exponential-platform-nexus` | `ezsystems/ezpublish-community`, `ezsystems/ezpublish-kernel` | `^7.1.3 || ^7.2 || ^7.4 || ^8.0 || ^8....` | v2.5.0.1 (2025-09-14) |
| [exponential-platform-nexus-starter](../../history/ecosystem/exponential-platform-nexus-starter.md) | `(none declared)` | `symfony/polyfill-ctype`, `symfony/polyfill-iconv`, `symfony/polyfill-php72` | `>=8.4` | 1.0.0.0 (2026-04-26) |
| [exponential-platform-v4x-dxp-skeleton](../../history/ecosystem/exponential-platform-v4x-dxp-skeleton.md) | `se7enxweb/exponential-platform-v4x-dxp-skeleton` | `symfony/polyfill-ctype`, `symfony/polyfill-iconv`, `symfony/polyfill-php72` | `^7.4 || ^8.0 || ^8.1 || ^8.2 || ^8.3 ...` | - |
| [oss](../../history/ecosystem/oss.md) | `se7enxweb/oss` | `ibexa/oss` | `^7.3 || ^8.0 || ^8.1 || ^8.2 || ^8.3 ...` | v4.6.0-beta4 (2023-12-21) |
| [oss-skeleton](../../history/ecosystem/oss-skeleton.md) | `ibexa/oss-skeleton` | - | `-` | v5.0.3 (2025-10-17) |

## Kernel

| Repository | Composer package | Replaces | PHP | Newest tag |
|---|---|---|---|---|
| [core](../../history/ecosystem/core.md) | `ibexa/core` | - | `>=8.3` | v5.0.7 (2026-04-19) |
| [ezplatform-core](../../history/ecosystem/ezplatform-core.md) | `se7enxweb/ezplatform-core` | - | `^7.3 || ^8.0` | v4.0.0-alpha2 (2021-10-29) |
| [ezplatform-kernel](../../history/ecosystem/ezplatform-kernel.md) | `se7enxweb/ezplatform-kernel` | `ezsystems/ezpublish-kernel`, `ezsystems/ezplatform-kernel`, `ibexa/core` | `^7.3 || ^8.0 || ^8.1 || ^8.2 || ^8.3 ...` | v4.0.0-alpha2 (2021-10-29) |
| [ezpublish-kernel](../../history/ecosystem/ezpublish-kernel.md) | `se7enxweb/ezpublish-kernel` | `ezsystems/ezpublish`, `ezsystems/ezpublish-api`, `ezsystems/ezpublish-spi` | `^7.1 || ^8.1 || ^8.2 || ^8.3` | v2014.11.8 (2015-01-23) |

## Admin user interface

| Repository | Composer package | Replaces | PHP | Newest tag |
|---|---|---|---|---|
| [admin-ui-7x](../../history/ecosystem/admin-ui-7x.md) | `se7enxweb/admin-ui` | `ibexa/admin-ui`, `ezsystems/ezplatform-admin-ui` | `>=8.3` | v5.0.4 (2026-04-17) |
| [admin-ui-assets](../../history/ecosystem/admin-ui-assets.md) | `se7enxweb/admin-ui-assets` | `ibexa/admin-ui-assets`, `ezsystems/ezplatform-admin-ui-assets` | `>=8.3` | v5.0.4 (2026-04-14) |
| [ezplatform-admin-ui](../../history/ecosystem/ezplatform-admin-ui.md) | `se7enxweb/ezplatform-admin-ui` | `ezsystems/ezplatform-admin-ui` | `^7.3 || ^8.0 || ^8.1 || ^8.2 || ^8.3 ...` | v4.0.0-alpha2 (2021-10-29) |
| [ezplatform-admin-ui-assets](../../history/ecosystem/ezplatform-admin-ui-assets.md) | `se7enxweb/ezplatform-admin-ui-assets` | `ezsystems/ezplatform-admin-ui-assets` | `^7.3 || ^8.0` | v6.0.0-alpha3 (2021-10-29) |
| [ezplatform-alloyeditor-element-width](../../history/ecosystem/ezplatform-alloyeditor-element-width.md) | `se7enxweb/ezplatform-alloyeditor-element-width` | `contextualcode/ezplatform-alloyeditor-element-width` | `-` | v2.0.1 (2020-05-18) |
| [ezplatform-design-engine](../../history/ecosystem/ezplatform-design-engine.md) | `se7enxweb/ezplatform-design-engine` | `ezsystems/ezplatform-design-engine` | `-` | v1.2.0-rc1 (2018-01-22) |
| [ezplatform-standard-design](../../history/ecosystem/ezplatform-standard-design.md) | `se7enxweb/ezplatform-standard-design` | `ezsystems/ezplatform-standard-design` | `^7.3 || ^8.1 || ^8.2` | v4.0.0-alpha2 (2021-10-29) |

## Field types

| Repository | Composer package | Replaces | PHP | Newest tag |
|---|---|---|---|---|
| [ezplatform-matrix-fieldtype](../../history/ecosystem/ezplatform-matrix-fieldtype.md) | `se7enxweb/ezplatform-matrix-fieldtype` | `ezsystems/ezplatform-matrix-fieldtype` | `^7.3 || ^8.0 || ^8.1 || ^8.2 || ^8.3 ...` | v4.0.0-alpha2 (2021-10-29) |
| [ezplatform-query-fieldtype](../../history/ecosystem/ezplatform-query-fieldtype.md) | `se7enxweb/ezplatform-query-fieldtype` | `ezsystems/ezplatform-query-fieldtype` | `^7.3 || ^8.0 || ^8.1 || ^8.2 || ^8.3 ...` | v4.0.0-alpha2 (2021-10-29) |
| [ezplatform-richtext](../../history/ecosystem/ezplatform-richtext.md) | `se7enxweb/ezplatform-richtext` | `ezsystems/ezplatform-richtext` | `^7.3 || ^8.0 || ^8.1 || ^8.2 || ^8.3 ...` | v2.3.28 (2026-03-26) |
| [ezplatform-xmltext-fieldtype](../../history/ecosystem/ezplatform-xmltext-fieldtype.md) | `se7enxweb/ezplatform-xmltext-fieldtype` | `ezsystems/ezplatform-xmltext-fieldtype` | `^7.3 || ^8.0 || ^8.1 || ^8.2 || ^8.3 ...` | v5.0.2 (2026-04-14) |
| [fieldtype-richtext-ibexa](../../history/ecosystem/fieldtype-richtext-ibexa.md) | `se7enxweb/fieldtype-richtext` | `ibexa/fieldtype-richtext`, `ezsystems/ezplatform-richtext` | `>=8.3` | v5.0.6 (2026-03-05) |
| [ibexa-xmltext-fieldtype](../../history/ecosystem/ibexa-xmltext-fieldtype.md) | `se7enxweb/ibexa-xmltext-fieldtype` | - | `^7.4 || ^8.1` | - |
| [mediata-ezpage-fieldtype-bundle-main](../../history/ecosystem/mediata-ezpage-fieldtype-bundle-main.md) | `se7enxweb/mediata-ezpage-fieldtype-bundle` | - | `^7.4 || ^8.0 || ^8.1 || ^8.2 || ^8.3 ...` | v4.0.0 (2026-03-26) |
| [metadata-bundle](../../history/ecosystem/metadata-bundle.md) | `se7enxweb/metadata-bundle` | `netgen/metadata-bundle` | `^8.4` | v5.0.0 (2026-04-12) |

## Search, cache and API

| Repository | Composer package | Replaces | PHP | Newest tag |
|---|---|---|---|---|
| [ez-support-tools](../../history/ecosystem/ez-support-tools.md) | `se7enxweb/ez-support-tools` | - | `^7.3 || ^8.0 || ^8.1 || ^8.2 || ^8.3 ...` | v4.0.0-alpha2 (2021-10-29) |
| [ezplatform-cron](../../history/ecosystem/ezplatform-cron.md) | `se7enxweb/ezplatform-cron` | `ezsystems/ezplatform-cron`, `ezsystems/ezstudio-cron` | `^7.3 || ^8.0` | v4.0.0-alpha2 (2021-10-29) |
| [ezplatform-graphql](../../history/ecosystem/ezplatform-graphql.md) | `se7enxweb/ezplatform-graphql` | `bdunogier/ezplatform-graphql-bundle`, `ezsystems/ezplatform-graphql` | `^7.3 || ^8.0` | v4.0.0-alpha2 (2021-10-29) |
| [ezplatform-http-cache](../../history/ecosystem/ezplatform-http-cache.md) | `se7enxweb/ezplatform-http-cache` | `ezsystems/ezplatform-http-cache` | `^7.3 || ^8.0 || ^8.1 || ^8.2 || ^8.3 ...` | v2.3.19 (2026-03-26) |
| [ezplatform-search](../../history/ecosystem/ezplatform-search.md) | `se7enxweb/ezplatform-search` | `ezsystems/ezplatform-search` | `^7.3 || ^8.0 || ^8.1 || ^8.2 || ^8.3 ...` | v1.2.8 (2025-09-27) |
| [ezplatform-solr-search-engine](../../history/ecosystem/ezplatform-solr-search-engine.md) | `se7enxweb/ezplatform-solr-search-engine` | `ezsystems/ezplatform-solr-search-engine` | `^7.3 || ^8.0 || ^8.1 || ^8.2 || ^8.3 ...` | v4.0.0-alpha2 (2021-10-29) |
| [ezplatform-user](../../history/ecosystem/ezplatform-user.md) | `se7enxweb/ezplatform-user` | `ezsystems/ezplatform-user` | `^7.3 || ^8.0 || ^8.1 || ^8.2 || ^8.3 ...` | v4.0.0-alpha2 (2021-10-29) |

## Legacy bridge and site bundles

| Repository | Composer package | Replaces | PHP | Newest tag |
|---|---|---|---|---|
| [ibexa-legacy-bridge---7x](../../history/ecosystem/ibexa-legacy-bridge---7x.md) | `se7enxweb/ibexa-legacy-bridge` | - | `-` | v6.0.0 (2023-09-01) |
| [legacyBridge](../../history/ecosystem/legacyBridge.md) | `se7enxweb/legacy-bridge` | - | `^8.0` | v5.0.9 (2026-04-16) |
| [ngsymfonytools](../../history/ecosystem/ngsymfonytools.md) | `se7enxweb/ngsymfonytools` | `netgen/ngsymfonytools` | `-` | 4.0.0.0 (2026-04-05) |
| [site-bundle](../../history/ecosystem/site-bundle.md) | `se7enxweb/site-bundle` | `netgen/site-bundle` | `^8.2` | v5.0.3 (2026-04-19) |
| [site-legacy-bundle](../../history/ecosystem/site-legacy-bundle.md) | `se7enxweb/site-legacy-bundle` | - | `^7.4 || ^8.1 || ^8.2 || ^8.3 || ^8.4 ...` | v5.0.2 (2026-04-12) |

## Layouts and recipes

| Repository | Composer package | Replaces | PHP | Newest tag |
|---|---|---|---|---|
| [layouts-core](../../history/ecosystem/layouts-core.md) | `se7enxweb/layouts-core` | `netgen/layouts-core` | `^8.4` | 2.0.0-se7enx.1 (2026-04-19) |

## Framework forks

| Repository | Composer package | Replaces | PHP | Newest tag |
|---|---|---|---|---|
| [doctrine-dbal-schema](../../history/ecosystem/doctrine-dbal-schema.md) | `se7enxweb/doctrine-dbal-schema` | `ezsystems/doctrine-dbal-schema` | `^7.1 || ^8.1 || ^8.2` | v4.0.0-alpha2 (2021-10-13) |
| [DoctrineBundle](../../history/ecosystem/DoctrineBundle.md) | `se7enxweb/doctrine-bundle` | `doctrine/doctrine-bundle` | `^7.1 || ^8.0` | v1.5.2 (2015-08-31) |
| [PHP-Parser](../../history/ecosystem/PHP-Parser.md) | `se7enxweb/php-parser` | `nikic/php-parser` | `>=5.2` | v5.7.0 (2025-12-06) |
| [symfony](../../history/ecosystem/symfony.md) | `se7enxweb/symfony` | `symfony/asset`, `symfony/browser-kit`, `symfony/cache` (+49) | `^5.5.9|>=7.0.8` | vPR12 (2011-04-19) |
| [twig](../../history/ecosystem/twig.md) | `se7enxweb/twig` | `twig/twig` | `>=7.1.3` | v3.21.1 (2025-05-03) |

## Database administration

| Repository | Composer package | Replaces | PHP | Newest tag |
|---|---|---|---|---|
| [adminneo](../../history/ecosystem/adminneo.md) | `adminneo-org/adminneo` | - | `7.1 - 8.5` | v5.2.1 (2025-12-07) |

Not every package keeps its upstream name: `oss-skeleton` still declares the name `ibexa/oss-skeleton` (its fork is published as `se7enxweb/exponential-platform-dxp-skeleton`), and `admin-ui-ibexa` is the same repository as `admin-ui-7x`.
