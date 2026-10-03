# The Exponential platform ecosystem

Exponential 6 is the legacy kernel that this installation runs. Around it the se7enxweb team maintains a family of repositories that put the same product on the Symfony based platform (Exponential Platform 3.x, 4.6 and v5), keep the old Symfony / Twig / Doctrine stack running on PHP 8.4 and 8.5, and ship ready-made sites. This page maps them, says what each gives a user and links the history of every one.

The history covers 6556 ledger changes in 53 repositories since December 2023. 1037 of them were made by the se7enxweb team; the rest is upstream history that a fork carries along (for example the 1,192 upstream changes inside the admin UI fork). The complete line-by-line record is in the [ledgers](ledger/README.md).

## How to choose

| You want | Use | Read |
|---|---|---|
| The Exponential 6 legacy CMS (this installation) | `se7enxweb/exponential` | [exponential](ecosystem/exponential.md) |
| A Symfony 7.4 / PHP 8.4+ site with layouts, REST and GraphQL | `se7enxweb/exponential-platform-nexus-starter` or `se7enxweb/exponential-platform-dxp-skeleton` | [Nexus starter](../features/6.0/platform-nexus-starter.md), [DXP skeleton](../features/6.0/platform-dxp-skeleton.md) |
| To keep an existing Exponential 6 site and add the Symfony platform next to it | `se7enxweb/legacy-bridge` | [Legacy bridge](../features/6.0/legacy-bridge.md) |
| A database without a server | SQLite install of the platform | [SQLite for the platform](../features/6.0/platform-sqlite-install.md) |
| To know which package replaces which upstream package | the package map | [Package map](../specifications/6.0/platform-package-map.md) |
| To know what changed when you upgrade a fork | the upgrade note | [Package forks and command renames](../bc/6.0/platform-package-forks-and-command-renames.md) |

## Distributions and starters

| Repository | What it is | What a user gets | Changes (own / total) |
|---|---|---|---|
| [exponential-platform-nexus-starter](ecosystem/exponential-platform-nexus-starter.md) | Nexus for Platform v5 (Symfony 7.4, PHP 8.4+, SQLite starter). | composer + one install command gives a v5 site with layouts, REST, GraphQL and admin. | 16 / 248 |
| [exponential-platform-dxp](ecosystem/exponential-platform-dxp.md) | Metapackage se7enxweb/exponential-platform-dxp for Platform v5. | One package name for a full v5 install. | 20 / 112 |
| [exponential-platform-nexus](ecosystem/exponential-platform-nexus.md) | Exponential Platform Nexus: Netgen Layouts + Media Site + tags + platform and legacy. | A working site with demo content, layouts and an admin, installed in minutes. | 97 / 97 |
| [cjw-exponential-platform-nexus](ecosystem/cjw-exponential-platform-nexus.md) | CJW flavour of Nexus (se7enxweb/cjw-exponential-platform-nexus). | Nexus with the CJW starter content. | 89 / 89 |
| [exponential-platform-legacy](ecosystem/exponential-platform-legacy.md) | Exponential Platform Legacy 2.5 distribution (platform plus legacy). | Composer project for the 2.5 LTS line on PHP 8.1 and 8.2. | 46 / 46 |
| [exponential-platform-v4x-dxp-skeleton](ecosystem/exponential-platform-v4x-dxp-skeleton.md) | Project skeleton for the Platform 4.6.x DXP. | create-project for 4.6. | 19 / 25 |
| [exponential-platform-dxp-skeleton](ecosystem/exponential-platform-dxp-skeleton.md) | Project skeleton for Platform v5 DXP (create-project). | composer create-project and a documented install path. | 15 / 21 |
| [exponential-platform](ecosystem/exponential-platform.md) | Exponential Platform 3.x distribution (Symfony). | Composer project that boots an Exponential Platform 3.2.9 site. | 11 / 11 |
| [oss](ecosystem/oss.md) | Metapackage for the platform open source edition (3.3 / 4.6 line). | A single require without any ezsystems/* package. | 5 / 10 |
| [oss-skeleton](ecosystem/oss-skeleton.md) | Project package for installing the open source edition. | Reference skeleton. | 2 / 8 |
| [mirror.exponential.se7enx.com](ecosystem/mirror.exponential.se7enx.com.md) | README-only repository that describes the documentation and book mirror. | Where to download the books. | 5 / 5 |
| [exponential-legacy-installer](ecosystem/exponential-legacy-installer.md) | Composer plugin that installs the legacy kernel and legacy extensions. | composer install keeps working with current Composer. | 4 / 4 |

## Kernel

| Repository | What it is | What a user gets | Changes (own / total) |
|---|---|---|---|
| [core](ecosystem/core.md) | Fork of the Ibexa core 5.0 content repository (the Symfony-based new stack). | Install and run Exponential Platform v5 with no database server at all (SQLite), alongside MySQL, MariaDB and PostgreSQL. | 4 / 655 |
| [exponential](ecosystem/exponential.md) | The Exponential 6 legacy kernel (Composer package se7enxweb/exponential, PHP 8.1 and newer). | PHP 8.1 to 8.5 support, an SQLite database driver and installer path, the responsive admin3 design, new template operators, multi-site INI handling and the v6.0.x releases. | 420 / 423 |
| [ezplatform-kernel](ecosystem/ezplatform-kernel.md) | Fork of the eZ Platform 1.13 / 2.5 / 3.3 kernel (content repository, APIs, Symfony integration). | Exponential branding, PHP 8.5 constraints, exponential:* console commands and SQLite install support on the 3.x / 4.6 line. | 23 / 38 |
| [ezpublish-kernel](ecosystem/ezpublish-kernel.md) | Fork of the 2014.11 era kernel used by the Symfony stack that wraps the legacy kernel. | Composer installs on PHP 8.1 to 8.3 and later, PHP 8.5 template fixes, working cache configuration, SQLite installer. | 30 / 31 |
| [ezplatform-core](ecosystem/ezplatform-core.md) | Container package that pulls in the Exponential Platform core bundles. | One require line that brings the matching core bundles. | 2 / 8 |

## Admin user interface

| Repository | What it is | What a user gets | Changes (own / total) |
|---|---|---|---|
| [admin-ui-7x](ecosystem/admin-ui-7x.md) | Fork of the Ibexa admin UI (se7enxweb/admin-ui) for Platform v5. | Exponential logo, favicons, translated strings and asset paths that resolve inside se7enxweb packages, so the admin builds without the upstream vendor directories. | 11 / 1193 |
| [admin-ui-ibexa](ecosystem/admin-ui-ibexa.md) | Same repository as admin-ui-7x seen through the upstream remote naming. | See admin-ui-7x; this page keeps the releases of the 5.0.5.x line. | 10 / 1192 |
| [admin-ui-assets](ecosystem/admin-ui-assets.md) | External JavaScript and CSS dependencies of the Platform v5 admin UI. | Admin assets install from the se7enxweb vendor. | 3 / 44 |
| [ezplatform-admin-ui](ecosystem/ezplatform-admin-ui.md) | Fork of the eZ Platform 2.x / 3.x admin UI (Exponential Platform Admin v2). | Exponential branding, SCSS migration to @use/@forward, working asset paths on PHP 8.5. | 23 / 36 |
| [ezplatform-standard-design](ecosystem/ezplatform-standard-design.md) | Standard design bundle of eZ Platform (Exponential Platform Standard Design Bundle). | Front-end design bundle installs on current PHP. | 4 / 4 |
| [ezplatform-admin-ui-assets](ecosystem/ezplatform-admin-ui-assets.md) | External asset dependencies for the 2.x / 3.x admin UI. | Admin assets install from the se7enxweb vendor. | 2 / 3 |
| [ezplatform-design-engine](ecosystem/ezplatform-design-engine.md) | Design fallback mechanism (theme chain) for the platform. | Theme fallback keeps working in forked installs. | 3 / 3 |
| [ezplatform-alloyeditor-element-width](ecosystem/ezplatform-alloyeditor-element-width.md) | Bundle that adds element-width editing to the online editor. | Editors can set the width of elements in rich text. | 1 / 1 |

## Field types

| Repository | What it is | What a user gets | Changes (own / total) |
|---|---|---|---|
| [fieldtype-richtext-ibexa](ecosystem/fieldtype-richtext-ibexa.md) | Fork of the Ibexa RichText field type for Platform v5 (se7enxweb/fieldtype-richtext). | Rich text field renders and edits correctly when installed from se7enxweb packages. | 2 / 244 |
| [ezplatform-richtext](ecosystem/ezplatform-richtext.md) | Fork of the eZ Platform RichText field type (3.x line). | Rich text keeps working on PHP 8.5. | 7 / 7 |
| [ezplatform-xmltext-fieldtype](ecosystem/ezplatform-xmltext-fieldtype.md) | XmlText field type for the Symfony platform. | Existing XML text fields from legacy content display on the new stack. | 7 / 7 |
| [mediata-ezpage-fieldtype-bundle-main](ecosystem/mediata-ezpage-fieldtype-bundle-main.md) | The page (landing page) field type for Ibexa 4. | Page field type available on Platform 4.6 / 3.3. | 7 / 7 |
| [ezplatform-matrix-fieldtype](ecosystem/ezplatform-matrix-fieldtype.md) | Matrix (table) field type. | Matrix fields install on current PHP. | 4 / 4 |
| [metadata-bundle](ecosystem/metadata-bundle.md) | Metadata field type bundle, compatible with the xrowmetadata legacy extension. | The same SEO metadata field works on the legacy kernel and on the new stack. | 4 / 4 |
| [ezplatform-query-fieldtype](ecosystem/ezplatform-query-fieldtype.md) | Field type that stores a content query. | Query fields install on current PHP. | 3 / 3 |
| [ibexa-xmltext-fieldtype](ecosystem/ibexa-xmltext-fieldtype.md) | XmlText field type for Ibexa OSS. | XML text content readable on Platform v4/v5. | 1 / 1 |

## Search, cache and API

| Repository | What it is | What a user gets | Changes (own / total) |
|---|---|---|---|
| [ez-support-tools](ecosystem/ez-support-tools.md) | System information pages for administrators (system info). | Admin system info page no longer breaks on null package versions. | 5 / 7 |
| [ezplatform-http-cache](ecosystem/ezplatform-http-cache.md) | HTTP cache handling (Varnish / Symfony) for the platform. | Reverse-proxy caching works on PHP 8.5. | 6 / 7 |
| [ezplatform-solr-search-engine](ecosystem/ezplatform-solr-search-engine.md) | Solr search engine integration. | Full-text search through Solr. | 3 / 6 |
| [ezplatform-graphql](ecosystem/ezplatform-graphql.md) | GraphQL server for the content repository. | GraphQL API on forked installs. | 4 / 4 |
| [ezplatform-search](ecosystem/ezplatform-search.md) | Platform search bundle. | Search bundle installs from se7enxweb. | 4 / 4 |
| [ezplatform-user](ecosystem/ezplatform-user.md) | User bundle (login, registration, profile). | User features on PHP 8.5. | 4 / 4 |
| [ezplatform-cron](ecosystem/ezplatform-cron.md) | Simple cron bundle. | Scheduled jobs on the platform. | 1 / 1 |

## Legacy bridge and site bundles

| Repository | What it is | What a user gets | Changes (own / total) |
|---|---|---|---|
| [site-bundle](ecosystem/site-bundle.md) | Netgen Site Bundle: common site features (menus, layouts glue, site context) for Ibexa sites. | Site features Nexus builds on. | 2 / 105 |
| [legacyBridge](ecosystem/legacyBridge.md) | Bridge that runs the Exponential legacy kernel inside the Symfony platform (se7enxweb/legacy-bridge). | Run an existing Exponential 6 site beside Platform 3.x / 4.6 / 5.x, with PHP 8.x compatible code and exponential:legacy:* commands. | 49 / 49 |
| [ibexa-legacy-bridge---7x](ecosystem/ibexa-legacy-bridge---7x.md) | Bridge for Ibexa 4 to the Exponential legacy kernel (se7enxweb/ibexa-legacy-bridge). | Legacy bridge for Platform 4.6. | 7 / 16 |
| [site-legacy-bundle](ecosystem/site-legacy-bundle.md) | Netgen Site Legacy Bundle: glue between the new and the legacy kernel. | Legacy admin pages inside Nexus show correct branding and work on platform 3.3. | 10 / 10 |
| [ngsymfonytools](ecosystem/ngsymfonytools.md) | Legacy extension that includes Twig templates and Symfony sub-requests from legacy templates. | A legacy .tpl template can embed a Twig template or a Symfony route. | 7 / 7 |

## Layouts and recipes

| Repository | What it is | What a user gets | Changes (own / total) |
|---|---|---|---|
| [layouts-core](ecosystem/layouts-core.md) | Netgen Layouts core (PHP/Symfony page builder engine), fork se7enxweb/layouts-core. | Layout page builder on PHP 8.4: getter shims make Twig read parameters. | 1 / 450 |
| [ibexa-recipes](ecosystem/ibexa-recipes.md) | Symfony Flex recipes of the platform packages. | Flex configures bundles on composer require. | 1 / 142 |

## Framework forks

| Repository | What it is | What a user gets | Changes (own / total) |
|---|---|---|---|
| [symfony](ecosystem/symfony.md) | Fork of the Symfony 3.4 framework split into se7enxweb/symfony. | PHP 8 return types, nullable types, closure and reflection fixes. | 18 / 18 |
| [twig](ecosystem/twig.md) | Fork of Twig 2.x (replaces twig/twig). | Templates render without deprecations on PHP 8.4 and 8.5. | 9 / 15 |
| [DoctrineBundle](ecosystem/DoctrineBundle.md) | Fork of Symfony DoctrineBundle 1.5. | Old stack installs on current PHP. | 2 / 2 |
| [PHP-Parser](ecosystem/PHP-Parser.md) | Fork of the PHP-Parser 0.9 line (replaces nikic/php-parser 0.9.5). | Old tooling parses PHP 8 code. | 2 / 2 |
| [doctrine-dbal-schema](ecosystem/doctrine-dbal-schema.md) | Cross-DBMS schema import layer used by the installer. | Schema import on current PHP; the 3.x kernel installer depends on it. | 2 / 2 |

## Database administration

| Repository | What it is | What a user gets | Changes (own / total) |
|---|---|---|---|
| [adminneo](ecosystem/adminneo.md) | AdminNeo and EditorNeo, a single-file database management tool (fork of Adminer). | Browse tables, run SQL, export and import for MySQL, MariaDB, PostgreSQL, SQLite, MS SQL, Oracle and MongoDB. | 0 / 1121 |

## By month

- **2018:** [11](ecosystem/months/2018-11.md)
- **2021:** [03](ecosystem/months/2021-03.md), [04](ecosystem/months/2021-04.md), [05](ecosystem/months/2021-05.md), [06](ecosystem/months/2021-06.md), [08](ecosystem/months/2021-08.md), [09](ecosystem/months/2021-09.md), [10](ecosystem/months/2021-10.md), [11](ecosystem/months/2021-11.md)
- **2022:** [02](ecosystem/months/2022-02.md), [03](ecosystem/months/2022-03.md), [07](ecosystem/months/2022-07.md), [10](ecosystem/months/2022-10.md), [11](ecosystem/months/2022-11.md)
- **2023:** [05](ecosystem/months/2023-05.md), [06](ecosystem/months/2023-06.md), [07](ecosystem/months/2023-07.md), [08](ecosystem/months/2023-08.md), [09](ecosystem/months/2023-09.md), [10](ecosystem/months/2023-10.md), [11](ecosystem/months/2023-11.md), [12](ecosystem/months/2023-12.md)
- **2024:** [01](ecosystem/months/2024-01.md), [02](ecosystem/months/2024-02.md), [03](ecosystem/months/2024-03.md), [04](ecosystem/months/2024-04.md), [05](ecosystem/months/2024-05.md), [06](ecosystem/months/2024-06.md), [07](ecosystem/months/2024-07.md), [08](ecosystem/months/2024-08.md), [09](ecosystem/months/2024-09.md), [10](ecosystem/months/2024-10.md), [11](ecosystem/months/2024-11.md), [12](ecosystem/months/2024-12.md)
- **2025:** [01](ecosystem/months/2025-01.md), [02](ecosystem/months/2025-02.md), [03](ecosystem/months/2025-03.md), [04](ecosystem/months/2025-04.md), [05](ecosystem/months/2025-05.md), [06](ecosystem/months/2025-06.md), [07](ecosystem/months/2025-07.md), [08](ecosystem/months/2025-08.md), [09](ecosystem/months/2025-09.md), [10](ecosystem/months/2025-10.md), [11](ecosystem/months/2025-11.md), [12](ecosystem/months/2025-12.md)
- **2026:** [01](ecosystem/months/2026-01.md), [02](ecosystem/months/2026-02.md), [03](ecosystem/months/2026-03.md), [04](ecosystem/months/2026-04.md), [05](ecosystem/months/2026-05.md), [06](ecosystem/months/2026-06.md), [07](ecosystem/months/2026-07.md), [09](ecosystem/months/2026-09.md)

## Notes on the numbers

- `admin-ui-7x` and `admin-ui-ibexa` are the same repository under two remote names; their hashes are identical.
- `exponential` shares every hash with the main installation repository; the narrative for it is in the main chronicle.
- Merge commits, funding metadata and repository language settings are classified as having no user benefit; every such row names its reason in the coverage table.
- Product names of upstream projects (Ibexa, Netgen, eZ Platform) appear only where a package or repository really has that name.

<!-- rev2-see-also:start -->
## See also

- [Complete ledgers](ledger/README.md)
- [Platform package map](../specifications/6.0/platform-package-map.md) and [console commands](../specifications/6.0/platform-console-commands.md)
- [Package forks and command renames](../bc/6.0/platform-package-forks-and-command-renames.md)
- [Admin interface](../features/6.0/platform-admin-ui-fork.md), [Nexus starter](../features/6.0/platform-nexus-starter.md), [layouts core](../features/6.0/platform-layouts-core-fork.md), [framework forks](../features/6.0/platform-php85-framework-forks.md), [site bundles](../features/6.0/platform-site-bundles.md)
- [Release changelogs of the packages](../changelogs/extensions/)

<!-- rev2-see-also:end -->
