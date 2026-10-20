# Exponential Platform Nexus: a finished site in minutes

This page is for developers and agencies who want a complete demo website on Exponential Platform, the Symfony based
sibling of Exponential 6, and then shape it into their own site. It is not part of the legacy kernel.

**Repositories:** `se7enxweb/exponential-platform-nexus` (one branch per platform generation, `1.0.0.x` to `1.3.0.x`,
plus the `v2.5.0.x` tags), `se7enxweb/exponential-platform-nexus-starter` (Platform v5), `se7enxweb/cjw-exponential-platform-nexus`
(CJW flavour).

## What it is

Nexus is a ready-made website project. You run Composer once, run one install command, and you have a site with demo content, the Netgen Layouts page builder, a Twig front end, an administration interface, a REST API and a GraphQL API. You change the content model and the design afterwards instead of assembling bundles first.

There is one line per platform generation, and you pick by the platform you want to run. Read from the
`composer.json` and `composer.lock` of each branch and tag:

| Line | Repository, branch and tags | Runs on | Legacy kernel | Use it when |
|---|---|---|---|---|
| Nexus 1.0.0.x | `exponential-platform-nexus` branch `1.0.0.x` (also `master`), tags `v1.0.0.0.1` to `1.0.0.10` | eZ Platform 2.5 (`se7enxweb/ezpublish-kernel ~7.5.40`), Symfony 3.4 (`se7enxweb/symfony`), PHP 7.1.3 to 8.5 | yes: `se7enxweb/exponential ^6.0.12`, `se7enxweb/legacy-bridge ^2.1` | You keep a legacy site and its legacy admin on the 2.5 stack. |
| Nexus 1.1.0.x | branch `1.1.0.x`, tags `v1.1.0.0` to `v1.1.0.7` | eZ Platform 3.3 (`se7enxweb/oss ~3.3.0`), Symfony 5.4, PHP `^8.0` | yes: `se7enxweb/legacy-bridge ^3.0`, `se7enxweb/site-legacy-bundle ^2.0` | 3.3 generation. |
| Nexus 1.2.0.x | branch `1.2.0.x`, tag `v1.2.0.0` | Ibexa OSS 4.6 (`se7enxweb/oss ~4.6.0`), Symfony 5.4, PHP 8.2 or newer | yes: `se7enxweb/site-legacy-bundle v2.0.0` pulls in `se7enxweb/ibexa-legacy-bridge` 4.x and `se7enxweb/exponential` (dev-main in the lock) | 4.6 generation. |
| Nexus 1.3.0.x | branch `1.3.0.x`, tags `1.3.0.0.0`, `1.3.0.1` to `1.3.0.5` | Platform v5 (`se7enxweb/exponential-platform-dxp-core v5.0.7` in the lock), Symfony 7.4, PHP 8.4 or newer | no | You start a new project on v5. |
| Nexus v5 starter | `exponential-platform-nexus-starter` | as 1.3.0.x: Symfony 7.4 LTS, PHP 8.4+ (8.5 recommended), Node.js 22; SQLite (development default), MySQL 8.0+, MariaDB 10.3+, PostgreSQL 14+ | no | The same v5 site as a separate repository. |
| Nexus 2.5 | tags `v2.5.0.0` to `v2.5.0.6` | the Exponential Platform Legacy 2.5 distribution packaged as Nexus | yes | You follow the 2.5 long-term line. |
| CJW Nexus | `cjw-exponential-platform-nexus` tag `1.0.0.6` | as Nexus 1.0.0.x | yes | You want the CJW starter content. |

Two things about versions to know before you install:

- **Five-part tags are not on Packagist.** `v1.0.0.0.1` to `v1.0.0.0.3` and `1.3.0.0.0` exist only as git tags; Composer
  finds `1.0.0.4` to `1.0.0.10`, `v1.1.0.0` to `v1.1.0.7`, `v1.2.0.0`, `1.3.0.1` to `1.3.0.5` and `v2.5.0.0` to
  `v2.5.0.6`.
- **Always name a version.** The repository is a fork of the Netgen media site and carries its tags too; Packagist
  lists them under the Nexus name. `composer create-project se7enxweb/exponential-platform-nexus` without a version
  picks the highest of them, `3.1.6`, which is the upstream `netgen/media-site`, not a Nexus release. The default
  branch, `master`, is the 2.5 line.

## What you get in the v5 starter

From the repository README:

- the Ibexa DXP v5 open source content repository (content classes, versions, translations, locations);
- Netgen Layouts 2.0.x as the page builder, with Netgen Content Browser and the Ibexa Site API;
- Netgen Information Collection for contact forms, Netgen Tags for taxonomy;
- the Platform v5 admin interface at `/adminui/`, the Netgen Layouts admin at `/nglayouts/admin`;
- REST API v2 with JWT login, a GraphQL endpoint at `/graphql`;
- a Webpack Encore 5 build for the site assets, multi-siteaccess support, optional Solr, Varnish and Redis;
- SQLite with no configuration, so a laptop or a demo server needs no database server.

The three se7enxweb forks the starter depends on exist because upstream packages broke on PHP 8.4 and Twig 3.24. They are explained in [PHP 8.4 / 8.5 forks of the framework layer](platform-php85-framework-forks.md), [the layouts-core fork](platform-layouts-core-fork.md) and [the admin UI fork](platform-admin-ui-fork.md).

## Install the v5 starter

Requirements: PHP 8.4 or newer with `gd`, `curl`, `json`, `xsl`, `xml`, `intl`, `mbstring`, `ctype`, `iconv` and one of `pdo_sqlite`, `pdo_mysql`, `pdo_pgsql`; Composer 2; Node.js 22 with Yarn 1.22 (the project's `package.json` does not build on Node 20 or earlier); a web server (Apache 2.4, Nginx 1.18 or the Symfony CLI for development).

```bash
git clone --branch 1.3.0.x git@github.com:se7enxweb/exponential-platform-nexus.git   # without --branch: master, the 2.5 line
cd exponential-platform-nexus
COMPOSER_ALLOW_SUPERUSER=1 composer install
cp .env .env.local                      # set APP_SECRET; keep the SQLite DATABASE_URL or change it
php bin/console exponential:install exponential-media --no-interaction
chmod 660 var/data_dev.db
chown $USER:www-data var/data_dev.db    # use your PHP-FPM user instead of www-data
source ~/.nvm/nvm.sh && nvm use 22
corepack enable
yarn install
yarn build:prod
php bin/console assets:install --symlink --relative public
yarn ibexa:build
php bin/console lexik:jwt:generate-keypair
php bin/console ibexa:graphql:generate-schema
php bin/console cache:clear
symfony server:start
```

Notes about the commands:

- Two install types exist. `exponential-media` installs the Nexus demo (layouts, content, tags and Netgen Layouts configuration); it is provided by `ExponentialMediaInstaller` in the `se7enxweb/exponential-platform-dxp-core` package and is a cross-database alternative to the MySQL-only `netgen-media` type: it loads `media_schema.sql` and `media_data.sql` for MySQL / MariaDB, PostgreSQL or SQLite, whichever your `DATABASE_URL` uses. `exponential-oss` installs the clean platform content only (the starter registers its own `App\Installer\ExponentialOssInstaller` for it). `php bin/console help exponential:install` shows the types of your checkout.
- The README of the starter repository clones `exponential-platform-nexus` too; whichever you clone, check that
  `composer.json` requires `se7enxweb/exponential-platform-dxp` and PHP `>=8.4` before you run `composer install`.
- The 1.1.0.x and 1.2.0.x branches install the same way (`exponential:install exponential-media`) on their own
  generation. Their projects also bring an `exponential:reindex` command of their own (`src/.../ExponentialReindexCommand.php`),
  which delegates to `ibexa:reindex`.
- `exponential:install` is the Exponential name of the upstream `ibexa:install` command. On the 3.x and 4.6 forks the
  old name still works as a deprecated alias; the v5 kernel the starter installs (`se7enxweb/exponential-platform-dxp-core`
  v5.0.7, `#[AsCommand(name: 'exponential:install')]`) registers only the new name. See [Platform console command names](../../specifications/6.0/platform-console-commands.md).
- The administrator account the installer creates is documented in the repository README. Change its password at once after the first login; never leave the default on a reachable server.

After the install these addresses answer:

| URL | What |
|---|---|
| `https://127.0.0.1:8000/` | the public site (`site` siteaccess) |
| `https://127.0.0.1:8000/adminui/` | Platform v5 admin interface |
| `https://127.0.0.1:8000/api/ibexa/v2/` | REST API v2, JWT authenticated (`ibexa.rest.path_prefix`; `/api/ezp/v2` was the prefix before 4.0) |
| `https://127.0.0.1:8000/graphql` | GraphQL endpoint |
| `https://127.0.0.1:8000/nglayouts/admin` | Netgen Layouts admin |

The complete step by step guide (web server configuration, permissions, production checklist, cron, Solr, Varnish, troubleshooting, database conversion) is `doc/sevenx/INSTALL.md` inside the starter repository.

## Install Nexus 1.0.0.x (the 2.5 line)

The 1.0.0.x line installs with Composer and the SQL dumps that ship in the project:

```bash
cd /var/www
composer create-project se7enxweb/exponential-platform-nexus:1.0.0.10 --ignore-platform-reqs
```

`--ignore-platform-reqs` is needed because the package definitions of the old stack still declare older PHP limits; the 1.0.0.x README says this requirement will go away as the packages are updated.

Then:

1. Create a database: `CREATE DATABASE <db_name> CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_general_ci;`
2. Import `src/AppBundle/Resources/database/sql/starter_project_database_sql_dump.sql` (everything), or the schema `.../sql/schema/schema.sql` plus the content `.../sql/data/content.sql`.
3. Put the database settings in `app/config/parameters.yml`.
4. Create the symlinks the install guide lists, so the legacy storage lives outside the `ezpublish_legacy` directory that Composer replaces on update:

```bash
cd ezpublish_legacy/extension/
ln -s ../../../src/AppBundle/ezpublish_legacy/extension/app .
ln -s ../../../vendor/se7enxweb/admin-ui-bundle/bundle/ezpublish_legacy/ngadminui .
cd ../var/site/ && mv storage storage-empty && ln -s ../../../src/AppBundle/ezpublish_legacy/var/site/storage .
cd ../../../web/bundles/ && ln -s ../../src/AppBundle/Resources/public app
cd ../../ && php bin/console cache:clear --env=dev
```

5. Review the host to siteaccess mapping in `app/config/ezplatform_siteaccess.yml`. Use at least two hostnames: one for visitors, one for editors.

APCu is strongly recommended and enabled by default in the 1.0.0.x configuration (it greatly speeds up uncached page rendering); it can be replaced through the service configuration.

### The `exponential-cjw` installer type (1.0.0.4 to 1.0.0.6)

Release `1.0.0.6` of Nexus (2026-04-21) adds an installer type `exponential-cjw` (class `ExponentialCjwInstaller`) that installs the whole CJW starter content into SQLite: a schema file with 164 tables that keeps the composite primary keys, and a data file with 41,568 insert statements. The same release adds the missing `ngsite.default.locations.tree_root.id` parameter and wires the SQLite `database_path` parameter into the Doctrine connection.

```bash
php bin/console ezplatform:install exponential-cjw
```

## Examples that work

Check the install is healthy:

```bash
php bin/console list exponential          # the exponential:* commands are registered
php bin/console debug:router | head       # routes load
curl -sI https://127.0.0.1:8000/ | head -1
```

Switch from SQLite to MySQL later: set `DATABASE_URL` in `.env.local` to a MySQL DSN, create the database, run the install again on the empty database, or use the database conversion recipes in the starter guide (any to SQLite, SQLite to MySQL / PostgreSQL, MySQL to PostgreSQL, PostgreSQL to MySQL).

## Limits

- The v5 starter needs PHP 8.4. For PHP 8.1 to 8.3 use the Exponential Platform 3.x / 4.6 lines or the legacy kernel.
- The Nexus 1.0.0.x line is built on the Symfony 3.4 stack; it needs the framework forks listed in [PHP 8.4 / 8.5 forks](platform-php85-framework-forks.md) to run on PHP 8.2 to 8.5.
- The public installation guides in the repositories contain a default administrator account for the demo data. Treat it as public knowledge and change it.

## Related pages

- [DXP project skeleton](platform-dxp-skeleton.md) (the plain skeleton without the Nexus demo design), [legacy bridge](legacy-bridge.md) (the legacy admin and the Symfony stack in one installation)
- Platform features: [platform administration interface](platform-admin-ui-fork.md), [Layouts on the platform](platform-layouts-core-fork.md), [PHP 8.5 framework forks](platform-php85-framework-forks.md), [site bundles](platform-site-bundles.md), [SQLite for Exponential Platform](platform-sqlite-install.md), [AdminNeo database manager](adminneo-database-manager.md)
- Specifications: [platform package map](../../specifications/6.0/platform-package-map.md), [platform console command names](../../specifications/6.0/platform-console-commands.md), [platform SQLite installer](../../specifications/6.0/platform-sqlite-installer.md), [legacy bridge bundle](../../specifications/6.0/legacy-bridge-bundle.md)
- Upgrade notes: [package forks and command renames](../../bc/6.0/platform-package-forks-and-command-renames.md)
- Changelog: [platform changelog](../../changelogs/extensions/exponential-platform.md)
- History: [exponential-platform-nexus-starter](../../history/ecosystem/exponential-platform-nexus-starter.md), [exponential-platform-nexus](../../history/ecosystem/exponential-platform-nexus.md), [cjw-exponential-platform-nexus](../../history/ecosystem/cjw-exponential-platform-nexus.md), [ecosystem overview](../../history/ecosystem.md), [ecosystem months](../../history/ecosystem/months/2026-04.md), [change ledger](../../history/ledger/README.md)
