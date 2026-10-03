# core: platform repository history

The history of `core`, one of the platform repositories around Exponential (group: Kernel). Read it to learn what the repository gives you, how it relates to Exponential and when it changed. The ledger records 655 changes from 2023-12-07 to 2026-04-29: 4 made by the se7enxweb team and 651 from the upstream history the fork carries.

## What it is

Fork of the Ibexa core 5.0 content repository (the Symfony-based new stack).

## How it relates to Exponential

The Exponential Platform v5 kernel. The se7enxweb changes make the installer run on SQLite; everything else is upstream history carried along.

## What a user gets

Install and run Exponential Platform v5 with no database server at all (SQLite), alongside MySQL, MariaDB and PostgreSQL.

## Security, performance and upgrade changes in the history

These changes are classified in the coverage record and are not named elsewhere on this page. Most are upstream history that the fork carries; the date and the commit subject are the ledger entry (see the [full ledger](../ledger/README.md)). Read the subject for what changed; for the exact effect, open the commit in the repository.

### Security (5)

- 2024-05-23 `eeb08b493` IBX-8140: Enabled authenticator manager-based security (#368)
- 2024-06-19 `d369ebe6e` IBX-8356: Deprecated `Ibexa\Core\MVC\Symfony\Security\Authentication\AuthenticatorInterface` to be replaced with Symfony-based authorization in 5.0 (#
- 2024-07-01 `62e04b2fc` IBX-8356: Removed `Ibexa\Core\MVC\Symfony\Security\Authentication\AuthenticatorInterface` to be replaced with Symfony-based authentication
- 2024-08-01 `b4a44ccc2` IBX-8558: Removed `GuardRepositoryAuthenticationProvider` due to Symfony security deprecations (#405)
- 2025-06-03 `13b64dc4c` Removed deprecated getName method from RandomSortClauseHandlerFactory (#570)

### Performance (2)

- 2024-06-07 `c88c39759` IBX-8019: Added performance consideration notice to `LocationService::loadLocationChildren` (#407)
- 2024-10-31 `623bbc332` Optimised content thumbnail resolving (#441)

### Behaviour and upgrade (24)

- 2024-04-25 `669c3002c` [BC break] Added `implements \Stringable` to `Translation` Value Object (#344)
- 2024-06-27 `2c706aef2` IBX-8224: Dropped BackwardCompatibleCommand usage (#386)
- 2024-07-05 `f44f605b2` Deprecated `CONSTANT_AUTH_TIME_SETTING` (#401)
- 2024-07-23 `da23f87c5` IBX-8138: Refactored deprecated `loadUserByUsername` method (#400)
- 2024-09-16 `b9f1582de` Aligned PHPStan baseline and updated Symfony deprecations helper setting (#426)
- 2024-11-07 `78236ffc3` IBX-8805: Dropped deprecated Twig Functions&Filters (#450)
- 2025-02-15 `3738528c5` [PHPUnit] Bumped number of expected direct deprecations (#482)
- 2025-02-16 `dd928f579` [CLI] Replaced deprecated Command::{$defaultName, $defaultDescription} with the AsCommand attribute
- 2025-02-16 `b4d33e044` [Serializer] Replaced usage of deprecated Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface
- 2025-02-16 `b16d98681` [HTTP] Replaced usage of deprecated Symfony\Component\HttpKernel\UriSigner
- 2025-02-24 `7048240e6` Removed deprecated SearchResult::$count property (#489)
- 2025-02-25 `cdfba458b` [Twig] Removed usage of deprecated spaceless filter (#490)
- 2025-03-01 `e4d79cf58` Removed deprecated ContentType::isContainer property (#491)
- 2025-03-05 `2ae80f9b3` Removed deprecated MaskGenerator::generateLanguageMask method  (#503)
- 2025-03-07 `a874f9810` Removed deprecated PermissionSubtree::createFromQueryBuilder method (#506)
- 2025-03-07 `4dfa29f4a` Replaced deprecated FieldNameResolver::getFieldNames method with FieldNameResolver::getFieldTypes (#507)
- 2025-03-15 `ed27a78f9` Removed deprecated buildSPIFieldDefinitionUpdate and buildSPIFieldDefinitionCreate methodd from ContentTypeDomainMapper (#504)
- 2025-03-15 `049e9c386` Removed deprecated timestamp property from DataAndTimeConverter and DateConverter (#509)
- 2025-05-03 `50191dcbc` [doctrine/dbal] Replaced usage of deprecated fetchColumn method with fetchOne
- 2025-05-16 `969de8188` [symfony/dependency-injection] Replaced deprecated !tagged YAML tag to !tagged_iterator
- 2025-05-22 `1af6f9d98` IBX-8226: Removed deprecated PHP deprecations handler (#542)
- 2025-05-26 `9edc9f795` Removed deprecated getName method from GatewayFactory (#544)
- 2025-05-28 `aed11eab8` Removed deprecated mergeGlobals method (#545)
- 2025-06-03 `752e91892` Replaced deprecated DBAL count expression and other minor deprecations (#571)

<!-- rev2-listed-rows:end -->

## Counts by kind

| Kind | Changes |
|---|---|
| Features | 116 |
| Fixes | 84 |
| Behaviour and upgrade changes | 32 |
| Security | 5 |
| Performance | 3 |
| Documentation | 18 |
| Tooling | 149 |
| Releases | 38 |
| No user benefit | 210 |

## Changes made by the se7enxweb team, by theme

### SQLite support (3)

- 2026-04-29 `d8c0db034` feature: skip doctrine:database:create for SQLite in checkCreateDatabase()
- 2026-04-29 `5f861df99` feature: substitute SqliteDbPlatform in importSchema() to fix composite-PK AUTOINCREMENT on SQLite
- 2026-04-29 `93f175106` feature: data/sqlite/cleandata.sql — SQLite-compatible seed data for ibexa:install ibexa-oss

### Other changes to the fork (1)

- 2026-04-29 `0b979991e` fix: use instanceof to resolve DBMS platform name in getKernelSQLFileForDBMS()

## Upstream history carried by the fork, by month

The fork contains the full upstream history. The table counts it by month and kind; the busiest changes of each month (by files touched) are named.

| Month | Changes | Features | Fixes | BC | Security | Perf | Docs | Tooling | Release | No benefit | Busiest changes |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2023-12 | 18 | 6 | 0 | 0 | 0 | 0 | 0 | 4 | 0 | 8 | `9111b0873` IBX-6827: Aggregation API improvements  (#287); `3fd56c68c` IBX-6856: Added mime types limitation for ezimage field type (#300) |
| 2024-01 | 16 | 6 | 1 | 0 | 0 | 0 | 1 | 2 | 0 | 6 | `2ac0538e7` IBX-7409: Changed Content Type to content type (#316); `fb6e4500a` IBX-6937: Changed expected min and max value types to numeric instead of int (#308) |
| 2024-02 | 25 | 5 | 3 | 0 | 0 | 0 | 2 | 3 | 2 | 10 | `2c8459dfa` IBX-6906: [DX] Introduced identifier-based view matchers (#322); `12b3b45a4` Specified return type for PermissionResolver |
| 2024-03 | 27 | 4 | 1 | 0 | 0 | 0 | 0 | 2 | 4 | 16 | `1fd57b27f` IBX-7149: Refactored content type-based indexing to rely on a dedicated strategy (#296); `1b543804f` IBX-7959: Added `ContentInfo::getSectionId` strict getter (#348) |
| 2024-04 | 28 | 6 | 2 | 1 | 0 | 0 | 1 | 5 | 1 | 12 | `038e5ea7b` IBX-8121: Fixed code style for 5.0 - Run code style fixer; `4ccc0fad6` IBX-7833: [PAPI] Implemented loading paginated relation list (#343) |
| 2024-05 | 28 | 3 | 3 | 1 | 1 | 1 | 1 | 4 | 3 | 11 | `5d4ffc6f7` IBX-8139: Dropped `class_alias` BC layer statements from all classes (; `98b7b50e6` IBX-5388: Fixed performance issues of content updates after field changes |
| 2024-06 | 24 | 3 | 2 | 1 | 1 | 1 | 1 | 5 | 1 | 9 | `5848b1e80` IBX-7911: Used strict getters for content tree loading code paths (#347); `5daf2a100` IBX-8399: Moved RepositoryConfigurationProvider to Repository layer (#383) |
| 2024-07 | 22 | 1 | 4 | 2 | 1 | 0 | 1 | 4 | 1 | 8 | `e710a6e2d` [PHPDoc] Fixed erroneous annotations for PHP API reference (#397); `fe4c34ac6` IBX-8323: Reworked RepositoryAuthenticationProvider and moved its logic to a dedicated subscriber (#396) |
| 2024-08 | 24 | 5 | 3 | 0 | 1 | 0 | 0 | 6 | 1 | 8 | `7e4312d81` IBX-8138: [Rector] Applied rules from Symfony 5 Rector set lists (#385); `7c398f219` Refactored Float and Integer field types to use external validators (#425) |
| 2024-09 | 10 | 4 | 0 | 1 | 0 | 0 | 0 | 1 | 1 | 3 | `0ba8cd18d` IBX-8726: Added IsBookmarked criterion (#417); `81784d04d` change to account credential handling (wording omitted; see the ledger |
| 2024-10 | 29 | 2 | 4 | 0 | 0 | 1 | 0 | 8 | 2 | 12 | `ac4e57c91` IBX-8957: Fixed deserializing SiteAccess Matchers for ESI (#430); `9328655cc` IBX-8811: Rebranded SiteAccess session prefix (#420) |
| 2024-11 | 19 | 2 | 4 | 2 | 0 | 0 | 1 | 2 | 1 | 7 | `aba61e909` IBX-8534: Dropped core deprecations (#435); `2e22cebee` Fixed issues related to twig/twig 3.15 in tests |
| 2024-12 | 12 | 0 | 1 | 2 | 0 | 0 | 0 | 3 | 1 | 5 | `625f7b740` IBX-8534: Cleaned up deprecations (#456); `54014140a` [PHPDoc] Enhanced `` usage (#458) |
| 2025-01 | 9 | 2 | 1 | 0 | 0 | 0 | 0 | 1 | 1 | 4 | `7a5d9eb7c` Updated copyright year to 2025; `8481e63cb` Updated copyright year to 2025 |
| 2025-02 | 36 | 5 | 3 | 8 | 0 | 0 | 2 | 10 | 0 | 8 | `9571e9304` IBX-8532: Removed deprecated Facets API (#484); `1dd257aca` Removed deprecated SearchResult::$spellSuggestion property (#488) |
| 2025-03 | 28 | 2 | 1 | 7 | 0 | 0 | 0 | 6 | 2 | 10 | `e33b2ff04` Removed deprecated Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter\*Converter::create method (#500); `fdeeb4fd6` Bump phpstan/phpstan to ^2.0 (#493) |
| 2025-04 | 16 | 4 | 2 | 1 | 0 | 0 | 0 | 3 | 1 | 5 | `f6e3294b3` Remove usage of deprecated ContainerAwareTrait (#514); `d177f134e` IBX-9379: Added Grace Period for archived versions (#515) |
| 2025-05 | 52 | 5 | 10 | 5 | 0 | 0 | 1 | 25 | 1 | 5 | `132c789a5` IBX-9727: Add missing type hints to PAPI events (#557); `55463f8fa` IBX-9941: Renamed core database schema (#541) |
| 2025-06 | 39 | 11 | 12 | 1 | 1 | 0 | 1 | 6 | 1 | 6 | `12abb87c8` IBX-9947: Rebranded field type identifiers (#543); `8358297d8` [Tests] Added `Case` suffix to abstract integration test cases (#567) |
| 2025-07 | 37 | 15 | 5 | 0 | 0 | 0 | 2 | 7 | 0 | 8 | `6b7ba98e9` IBX-9727: Added missing type hints to content related VO (#569); `7ea74c5bd` IBX-7801: Added constraint for new content to only be created inside content with container content type (#598) |
| 2025-08 | 29 | 5 | 3 | 0 | 0 | 0 | 0 | 10 | 3 | 8 | `8608e1e83` IBX-10507: Updated method signatures to use nullable type hints for improved code readability and consistency. (#636); `8f1f61e39` IBX-9727: Fixed strict types of IO layer (#613) |
| 2025-09 | 16 | 1 | 3 | 0 | 0 | 0 | 0 | 5 | 2 | 5 | `79649c3d7` Fixed issues uncovered by PHPStan v2.1.23 (#649); `db39fd774` Fixed the incorrect type hint for `CreateStruct::$mainLocationId` (#64 |
| 2025-10 | 27 | 1 | 3 | 0 | 0 | 0 | 1 | 11 | 2 | 9 | `62929022f` IBX-10458: Implemented new Content Type search PHP API (#633); `fd2c23a2d` Enforced PHPStan rule against dynamic properties |
| 2025-11 | 10 | 3 | 1 | 0 | 0 | 0 | 0 | 2 | 0 | 4 | `fb60a60d6` IBX-10936: Made `ProxyDomainMapperFactory` lazy (#676); `9de4817ac` IBX-10627: Fixed transformation ForbiddenException to AccessDeniedHttpException (#673) |
| 2025-12 | 20 | 2 | 3 | 0 | 0 | 0 | 2 | 4 | 2 | 7 | `c6385a697` Defined TParent of Symfony semantic configuration builders; `6e92721b7` IBX-9846: Added search using embeddings (#536) |
| 2026-01 | 17 | 2 | 4 | 0 | 0 | 0 | 1 | 1 | 4 | 5 | `8931fd055` IBX-11116: Added EmbeddingProviderException and an interface for embedding providers' error handling (#697); `98253e9f0` IBX-11130: Fixed proxy initializer returning `null` (#693) |
| 2026-02 | 14 | 5 | 0 | 0 | 0 | 0 | 0 | 5 | 0 | 4 | `42ff60ae6` IBX-10186: Added limits to Repository Filtering count and subtree queries (#696); `b99a8a24a` IBX-11179: Resolved PHP 8.4 lazy proxy incompatibility (#707) |
| 2026-03 | 19 | 3 | 4 | 0 | 0 | 0 | 0 | 4 | 1 | 7 | `06eb4ae9d` IBX-11179: Updated PHP versions in CI configuration with 8.3 and 8.4 (#724); `355d88540` Adjust phpunit and phpstan setup after merge to main |

## Related pages

- [SQLite for the platform](../../features/6.0/platform-sqlite-install.md)
- [SQLite installer specification](../../specifications/6.0/platform-sqlite-installer.md)
- [Console command names](../../specifications/6.0/platform-console-commands.md)
- [Package map](../../specifications/6.0/platform-package-map.md)
- [Upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)
- [Ecosystem overview](../ecosystem.md)
- [Complete ledger of this repository](../ledger/core.md)
- Platform ecosystem by month: [2023-12](months/2023-12.md), [2024-01](months/2024-01.md), [2024-02](months/2024-02.md), [2024-03](months/2024-03.md), [2024-04](months/2024-04.md), [2024-05](months/2024-05.md), [2024-06](months/2024-06.md), [2024-07](months/2024-07.md), [2024-08](months/2024-08.md), [2024-09](months/2024-09.md), [2024-10](months/2024-10.md), [2024-11](months/2024-11.md), [2024-12](months/2024-12.md), [2025-01](months/2025-01.md), [2025-02](months/2025-02.md), [2025-03](months/2025-03.md), [2025-04](months/2025-04.md), [2025-05](months/2025-05.md), [2025-06](months/2025-06.md), [2025-07](months/2025-07.md), [2025-08](months/2025-08.md), [2025-09](months/2025-09.md), [2025-10](months/2025-10.md), [2025-11](months/2025-11.md), [2025-12](months/2025-12.md), [2026-01](months/2026-01.md), [2026-02](months/2026-02.md), [2026-03](months/2026-03.md), [2026-04](months/2026-04.md)
