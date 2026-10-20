# Spezifikation: Paketübersicht der Exponential Platform

Diese Seite führt für jedes Repository der auf Symfony basierenden Exponential Platform auf, welches se7enxweb-Composer-Paket
welches Upstream-Paket ersetzt, welche PHP-Versionen es akzeptiert und welches sein neuester Tag ist. Lesen Sie sie, wenn Sie
die `composer.json` eines Platform-Projekts planen oder wissen müssen, woher eine Klasse eines `ibexa/*`- oder
`ezsystems/*`-Pakets tatsächlich stammt. Die Angaben sind aus der `composer.json` und den Tags jedes Repositorys gelesen;
die erste Spalte verlinkt die Chronik jedes Repositorys.

## So funktioniert das Ersetzen

Ein Paket, das ein anderes Paket unter `replace` aufführt, teilt Composer mit, dass es dieses Paket bereitstellt. Composer
installiert dann nur den Fork, und jedes Paket, das den Upstream-Namen verlangt, wird durch ihn erfüllt. Deshalb kann ein
Projekt Pakete von Drittanbietern verwenden, die `ibexa/*`- oder `ezsystems/*`-Namen verlangen, und trotzdem se7enxweb-Code
erhalten. Die Upgrade-Schritte stehen in [Paket-Forks und umbenannte Befehle](../../../en/us/bc/6.0/platform-package-forks-and-command-renames.md).

Ein „-“ bedeutet, dass das Repository für diese Spalte nichts angibt oder keinen Tag hat.

## Distributionen und Starter

| Repository | Composer-Paket | Ersetzt | PHP | Neuester Tag |
|---|---|---|---|---|
| [cjw-exponential-platform-nexus](../../../en/us/history/ecosystem/cjw-exponential-platform-nexus.md) | `se7enxweb/cjw-exponential-platform-nexus` | `ezsystems/ezpublish-community`, `ezsystems/ezpublish-kernel` | `^7.1.3 \|\| ^7.2 \|\| ^7.4 \|\| ^8.0 \|\| ^8....` | 1.0.0.6 (2026-04-22) |
| [exponential-legacy-installer](../../../en/us/history/ecosystem/exponential-legacy-installer.md) | `se7enxweb/exponential-legacy-installer` | `ezsystems/ezpublish-legacy-installer`, `se7enxweb/ezpublish-legacy-installer` | `^7.4 \|\| ^8.1 \|\| ^8.2` | 2.2.3 (2026-06-19) |
| [exponential-platform](../../../en/us/history/ecosystem/exponential-platform.md) | `se7enxweb/exponential-platform` | `paragonie/random_compat`, `symfony/polyfill-ctype`, `symfony/polyfill-iconv` (+3) | `^7.3 \|\| ^8.1 \|\| ^8.2 \|\| ^8.3 \|\| ^8.4` | v3.2.9 (2025-09-28) |
| [exponential-platform-dxp](../../../en/us/history/ecosystem/exponential-platform-dxp.md) | `se7enxweb/exponential-platform-dxp` | - | `>=8.3` | v0.0.0.2 (2026-03-21) |
| [exponential-platform-dxp-skeleton](../../../en/us/history/ecosystem/exponential-platform-dxp-skeleton.md) | `se7enxweb/exponential-platform-dxp-skeleton` | `symfony/polyfill-ctype`, `symfony/polyfill-iconv`, `symfony/polyfill-php72` | `>=8.3` | - |
| [exponential-platform-legacy](../../../en/us/history/ecosystem/exponential-platform-legacy.md) | `se7enxweb/exponential-platform-legacy` | `ezsystems/ezpublish-community`, `ezsystems/ezpublish-kernel` | `^7.1.3 \|\| ^8.1 \|\| ^8.2` | v5.0.2 (2026-04-16) |
| [exponential-platform-nexus](../../../en/us/history/ecosystem/exponential-platform-nexus.md) | `se7enxweb/exponential-platform-nexus` | `ezsystems/ezpublish-community`, `ezsystems/ezpublish-kernel` | `^7.1.3 \|\| ^7.2 \|\| ^7.4 \|\| ^8.0 \|\| ^8....` | v2.5.0.1 (2025-09-14) |
| [exponential-platform-nexus-starter](../../../en/us/history/ecosystem/exponential-platform-nexus-starter.md) | `(none declared)` | `symfony/polyfill-ctype`, `symfony/polyfill-iconv`, `symfony/polyfill-php72` | `>=8.4` | 1.0.0.0 (2026-04-26) |
| [exponential-platform-v4x-dxp-skeleton](../../../en/us/history/ecosystem/exponential-platform-v4x-dxp-skeleton.md) | `se7enxweb/exponential-platform-v4x-dxp-skeleton` | `symfony/polyfill-ctype`, `symfony/polyfill-iconv`, `symfony/polyfill-php72` | `^7.4 \|\| ^8.0 \|\| ^8.1 \|\| ^8.2 \|\| ^8.3 ...` | - |
| [oss](../../../en/us/history/ecosystem/oss.md) | `se7enxweb/oss` | `ibexa/oss` | `^7.3 \|\| ^8.0 \|\| ^8.1 \|\| ^8.2 \|\| ^8.3 ...` | v4.6.0-beta4 (2023-12-21) |
| [oss-skeleton](../../../en/us/history/ecosystem/oss-skeleton.md) | `ibexa/oss-skeleton` | - | `-` | v5.0.3 (2025-10-17) |

## Kernel

| Repository | Composer-Paket | Ersetzt | PHP | Neuester Tag |
|---|---|---|---|---|
| [core](../../../en/us/history/ecosystem/core.md) | `ibexa/core` | - | `>=8.3` | v5.0.7 (2026-04-19) |
| [ezplatform-core](../../../en/us/history/ecosystem/ezplatform-core.md) | `se7enxweb/ezplatform-core` | - | `^7.3 \|\| ^8.0` | v4.0.0-alpha2 (2021-10-29) |
| [ezplatform-kernel](../../../en/us/history/ecosystem/ezplatform-kernel.md) | `se7enxweb/ezplatform-kernel` | `ezsystems/ezpublish-kernel`, `ezsystems/ezplatform-kernel`, `ibexa/core` | `^7.3 \|\| ^8.0 \|\| ^8.1 \|\| ^8.2 \|\| ^8.3 ...` | v4.0.0-alpha2 (2021-10-29) |
| [ezpublish-kernel](../../../en/us/history/ecosystem/ezpublish-kernel.md) | `se7enxweb/ezpublish-kernel` | `ezsystems/ezpublish`, `ezsystems/ezpublish-api`, `ezsystems/ezpublish-spi` | `^7.1 \|\| ^8.1 \|\| ^8.2 \|\| ^8.3` | v2014.11.8 (2015-01-23) |

## Administrationsoberfläche

| Repository | Composer-Paket | Ersetzt | PHP | Neuester Tag |
|---|---|---|---|---|
| [admin-ui-7x](../../../en/us/history/ecosystem/admin-ui-7x.md) | `se7enxweb/admin-ui` | `ibexa/admin-ui`, `ezsystems/ezplatform-admin-ui` | `>=8.3` | v5.0.4 (2026-04-17) |
| [admin-ui-assets](../../../en/us/history/ecosystem/admin-ui-assets.md) | `se7enxweb/admin-ui-assets` | `ibexa/admin-ui-assets`, `ezsystems/ezplatform-admin-ui-assets` | `>=8.3` | v5.0.4 (2026-04-14) |
| [ezplatform-admin-ui](../../../en/us/history/ecosystem/ezplatform-admin-ui.md) | `se7enxweb/ezplatform-admin-ui` | `ezsystems/ezplatform-admin-ui` | `^7.3 \|\| ^8.0 \|\| ^8.1 \|\| ^8.2 \|\| ^8.3 ...` | v4.0.0-alpha2 (2021-10-29) |
| [ezplatform-admin-ui-assets](../../../en/us/history/ecosystem/ezplatform-admin-ui-assets.md) | `se7enxweb/ezplatform-admin-ui-assets` | `ezsystems/ezplatform-admin-ui-assets` | `^7.3 \|\| ^8.0` | v6.0.0-alpha3 (2021-10-29) |
| [ezplatform-alloyeditor-element-width](../../../en/us/history/ecosystem/ezplatform-alloyeditor-element-width.md) | `se7enxweb/ezplatform-alloyeditor-element-width` | `contextualcode/ezplatform-alloyeditor-element-width` | `-` | v2.0.1 (2020-05-18) |
| [ezplatform-design-engine](../../../en/us/history/ecosystem/ezplatform-design-engine.md) | `se7enxweb/ezplatform-design-engine` | `ezsystems/ezplatform-design-engine` | `-` | v1.2.0-rc1 (2018-01-22) |
| [ezplatform-standard-design](../../../en/us/history/ecosystem/ezplatform-standard-design.md) | `se7enxweb/ezplatform-standard-design` | `ezsystems/ezplatform-standard-design` | `^7.3 \|\| ^8.1 \|\| ^8.2` | v4.0.0-alpha2 (2021-10-29) |

## Feldtypen

| Repository | Composer-Paket | Ersetzt | PHP | Neuester Tag |
|---|---|---|---|---|
| [ezplatform-matrix-fieldtype](../../../en/us/history/ecosystem/ezplatform-matrix-fieldtype.md) | `se7enxweb/ezplatform-matrix-fieldtype` | `ezsystems/ezplatform-matrix-fieldtype` | `^7.3 \|\| ^8.0 \|\| ^8.1 \|\| ^8.2 \|\| ^8.3 ...` | v4.0.0-alpha2 (2021-10-29) |
| [ezplatform-query-fieldtype](../../../en/us/history/ecosystem/ezplatform-query-fieldtype.md) | `se7enxweb/ezplatform-query-fieldtype` | `ezsystems/ezplatform-query-fieldtype` | `^7.3 \|\| ^8.0 \|\| ^8.1 \|\| ^8.2 \|\| ^8.3 ...` | v4.0.0-alpha2 (2021-10-29) |
| [ezplatform-richtext](../../../en/us/history/ecosystem/ezplatform-richtext.md) | `se7enxweb/ezplatform-richtext` | `ezsystems/ezplatform-richtext` | `^7.3 \|\| ^8.0 \|\| ^8.1 \|\| ^8.2 \|\| ^8.3 ...` | v2.3.28 (2026-03-26) |
| [ezplatform-xmltext-fieldtype](../../../en/us/history/ecosystem/ezplatform-xmltext-fieldtype.md) | `se7enxweb/ezplatform-xmltext-fieldtype` | `ezsystems/ezplatform-xmltext-fieldtype` | `^7.3 \|\| ^8.0 \|\| ^8.1 \|\| ^8.2 \|\| ^8.3 ...` | v5.0.2 (2026-04-14) |
| [fieldtype-richtext-ibexa](../../../en/us/history/ecosystem/fieldtype-richtext-ibexa.md) | `se7enxweb/fieldtype-richtext` | `ibexa/fieldtype-richtext`, `ezsystems/ezplatform-richtext` | `>=8.3` | v5.0.6 (2026-03-05) |
| [ibexa-xmltext-fieldtype](../../../en/us/history/ecosystem/ibexa-xmltext-fieldtype.md) | `se7enxweb/ibexa-xmltext-fieldtype` | - | `^7.4 \|\| ^8.1` | - |
| [mediata-ezpage-fieldtype-bundle-main](../../../en/us/history/ecosystem/mediata-ezpage-fieldtype-bundle-main.md) | `se7enxweb/mediata-ezpage-fieldtype-bundle` | - | `^7.4 \|\| ^8.0 \|\| ^8.1 \|\| ^8.2 \|\| ^8.3 ...` | v4.0.0 (2026-03-26) |
| [metadata-bundle](../../../en/us/history/ecosystem/metadata-bundle.md) | `se7enxweb/metadata-bundle` | `netgen/metadata-bundle` | `^8.4` | v5.0.0 (2026-04-12) |

## Suche, Cache und API

| Repository | Composer-Paket | Ersetzt | PHP | Neuester Tag |
|---|---|---|---|---|
| [ez-support-tools](../../../en/us/history/ecosystem/ez-support-tools.md) | `se7enxweb/ez-support-tools` | - | `^7.3 \|\| ^8.0 \|\| ^8.1 \|\| ^8.2 \|\| ^8.3 ...` | v4.0.0-alpha2 (2021-10-29) |
| [ezplatform-cron](../../../en/us/history/ecosystem/ezplatform-cron.md) | `se7enxweb/ezplatform-cron` | `ezsystems/ezplatform-cron`, `ezsystems/ezstudio-cron` | `^7.3 \|\| ^8.0` | v4.0.0-alpha2 (2021-10-29) |
| [ezplatform-graphql](../../../en/us/history/ecosystem/ezplatform-graphql.md) | `se7enxweb/ezplatform-graphql` | `bdunogier/ezplatform-graphql-bundle`, `ezsystems/ezplatform-graphql` | `^7.3 \|\| ^8.0` | v4.0.0-alpha2 (2021-10-29) |
| [ezplatform-http-cache](../../../en/us/history/ecosystem/ezplatform-http-cache.md) | `se7enxweb/ezplatform-http-cache` | `ezsystems/ezplatform-http-cache` | `^7.3 \|\| ^8.0 \|\| ^8.1 \|\| ^8.2 \|\| ^8.3 ...` | v2.3.19 (2026-03-26) |
| [ezplatform-search](../../../en/us/history/ecosystem/ezplatform-search.md) | `se7enxweb/ezplatform-search` | `ezsystems/ezplatform-search` | `^7.3 \|\| ^8.0 \|\| ^8.1 \|\| ^8.2 \|\| ^8.3 ...` | v1.2.8 (2025-09-27) |
| [ezplatform-solr-search-engine](../../../en/us/history/ecosystem/ezplatform-solr-search-engine.md) | `se7enxweb/ezplatform-solr-search-engine` | `ezsystems/ezplatform-solr-search-engine` | `^7.3 \|\| ^8.0 \|\| ^8.1 \|\| ^8.2 \|\| ^8.3 ...` | v4.0.0-alpha2 (2021-10-29) |
| [ezplatform-user](../../../en/us/history/ecosystem/ezplatform-user.md) | `se7enxweb/ezplatform-user` | `ezsystems/ezplatform-user` | `^7.3 \|\| ^8.0 \|\| ^8.1 \|\| ^8.2 \|\| ^8.3 ...` | v4.0.0-alpha2 (2021-10-29) |

## Legacy Bridge und Site-Bundles

| Repository | Composer-Paket | Ersetzt | PHP | Neuester Tag |
|---|---|---|---|---|
| [ibexa-legacy-bridge---7x](../../../en/us/history/ecosystem/ibexa-legacy-bridge---7x.md) | `se7enxweb/ibexa-legacy-bridge` | - | `-` | v6.0.0 (2023-09-01) |
| [legacyBridge](../../../en/us/history/ecosystem/legacyBridge.md) | `se7enxweb/legacy-bridge` | - | `^8.0` | v5.0.9 (2026-04-16) |
| [ngsymfonytools](../../../en/us/history/ecosystem/ngsymfonytools.md) | `se7enxweb/ngsymfonytools` | `netgen/ngsymfonytools` | `-` | 4.0.0.0 (2026-04-05) |
| [site-bundle](../../../en/us/history/ecosystem/site-bundle.md) | `se7enxweb/site-bundle` | `netgen/site-bundle` | `^8.2` | v5.0.3 (2026-04-19) |
| [site-legacy-bundle](../../../en/us/history/ecosystem/site-legacy-bundle.md) | `se7enxweb/site-legacy-bundle` | - | `^7.4 \|\| ^8.1 \|\| ^8.2 \|\| ^8.3 \|\| ^8.4 ...` | v5.0.2 (2026-04-12) |

## Layouts und Rezepte

| Repository | Composer-Paket | Ersetzt | PHP | Neuester Tag |
|---|---|---|---|---|
| [layouts-core](../../../en/us/history/ecosystem/layouts-core.md) | `se7enxweb/layouts-core` | `netgen/layouts-core` | `^8.4` | 2.0.0-se7enx.1 (2026-04-19) |

## Framework-Forks

| Repository | Composer-Paket | Ersetzt | PHP | Neuester Tag |
|---|---|---|---|---|
| [doctrine-dbal-schema](../../../en/us/history/ecosystem/doctrine-dbal-schema.md) | `se7enxweb/doctrine-dbal-schema` | `ezsystems/doctrine-dbal-schema` | `^7.1 \|\| ^8.1 \|\| ^8.2` | v4.0.0-alpha2 (2021-10-13) |
| [DoctrineBundle](../../../en/us/history/ecosystem/DoctrineBundle.md) | `se7enxweb/doctrine-bundle` | `doctrine/doctrine-bundle` | `^7.1 \|\| ^8.0` | v1.5.2 (2015-08-31) |
| [PHP-Parser](../../../en/us/history/ecosystem/PHP-Parser.md) | `se7enxweb/php-parser` | `nikic/php-parser` | `>=5.2` | v5.7.0 (2025-12-06) |
| [symfony](../../../en/us/history/ecosystem/symfony.md) | `se7enxweb/symfony` | `symfony/asset`, `symfony/browser-kit`, `symfony/cache` (+49) | `^5.5.9\|>=7.0.8` | vPR12 (2011-04-19) |
| [twig](../../../en/us/history/ecosystem/twig.md) | `se7enxweb/twig` | `twig/twig` | `>=7.1.3` | v3.21.1 (2025-05-03) |

## Datenbankverwaltung

| Repository | Composer-Paket | Ersetzt | PHP | Neuester Tag |
|---|---|---|---|---|
| [adminneo](../../../en/us/history/ecosystem/adminneo.md) | `adminneo-org/adminneo` | - | `7.1 - 8.5` | v5.2.1 (2025-12-07) |

## Abweichende Namen

Nicht jedes Paket trägt einen se7enxweb-Namen:

- `oss-skeleton` gibt weiterhin den Namen `ibexa/oss-skeleton` an; sein Fork wird als
  `se7enxweb/exponential-platform-dxp-skeleton` veröffentlicht.
- `exponential-platform-nexus-starter` gibt keinen Paketnamen an.
- `core` gibt `ibexa/core` an.
- `admin-ui-ibexa` ist dasselbe Repository wie `admin-ui-7x`.

## Verwandte Seiten

- Spezifikationen: [Namen der Platform-Konsolenbefehle](platform-konsolenbefehle.md), [SQLite-Installer der Platform](platform-sqlite-installer.md), [Legacy-Bridge-Bundle](legacy-bridge-bundle.md)
- Funktionen: [Administrationsoberfläche der Platform](../../../en/us/features/6.0/platform-admin-ui-fork.md), [DXP-Skeleton](../../../en/us/features/6.0/platform-dxp-skeleton.md), [Layouts auf der Platform](../../../en/us/features/6.0/platform-layouts-core-fork.md), [Nexus-Starter](../../../en/us/features/6.0/platform-nexus-starter.md), [Framework-Forks für PHP 8.5](../../../en/us/features/6.0/platform-php85-framework-forks.md), [Site-Bundles](../../../en/us/features/6.0/platform-site-bundles.md), [SQLite für Exponential Platform](../../../en/us/features/6.0/platform-sqlite-install.md), [Legacy Bridge](../../../en/us/features/6.0/legacy-bridge.md), [Datenbankverwaltung AdminNeo](../../../en/us/features/6.0/adminneo-database-manager.md)
- Upgrade-Hinweise: [Paket-Forks und umbenannte Befehle](../../../en/us/bc/6.0/platform-package-forks-and-command-renames.md)
- Changelog: [Platform-Changelog](../../../changelogs/extensions/exponential-platform.md)
- Chronik: [Überblick über das Ökosystem](../../../en/us/history/ecosystem.md), [Monate des Ökosystems](../../../en/us/history/ecosystem/months/2026-04.md) (eine Seite für jeden Monat ab 2018-11), [Änderungsjournal](../../../en/us/history/ledger/README.md)
