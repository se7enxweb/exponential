# Exponential Platform DXP skeleton: create-project and a documented install

This page is for developers who start a new project on Exponential Platform (the Symfony based platform) from a clean
skeleton, without a demo design.

**Repositories:** `se7enxweb/exponential-platform-dxp-skeleton` (Platform v5), `se7enxweb/exponential-platform-v4x-dxp-skeleton`
(Platform 4.6.x), `se7enxweb/exponential-platform-dxp` (the metapackage both require), `se7enxweb/oss`, `se7enxweb/oss-skeleton`.

## What it is

A clean Symfony project for Exponential Platform with no demo design. `composer create-project` downloads the packages, a Symfony Flex recipe configures them, and the skeleton's own guide (`INSTALL.md`, 22 sections) takes you from an empty folder to a running administration interface. Use the [Nexus starter](platform-nexus-starter.md) when you want a finished demo site instead.

| Skeleton | Platform | Symfony | PHP | Node.js |
|---|---|---|---|---|
| `exponential-platform-dxp-skeleton` | v5 | 7.4 LTS | 8.3+ (8.3 or 8.5 recommended) | 20 LTS (guide states only 20 is tested) |
| `exponential-platform-v4x-dxp-skeleton` | 4.6.x | 5.4 (`extra.symfony.require` is pinned to `5.4.*` so Flex cannot pick a mismatching version) | 7.4 to 8.5 | 20 LTS |

The metapackage `se7enxweb/exponential-platform-dxp` was created on 2026-03-12 and, on 2026-03-21, its core and admin packages were switched to the se7enxweb forks (core tagged `v5.0.1.0`). In April 2026 it also replaced `ibexa/system-info`, `ibexa/admin-ui-assets` and `ibexa/fieldtype-richtext` with the se7enxweb packages and required the layouts-core fork. See the [package map](../../specifications/6.0/platform-package-map.md).

## Why it helps

- One documented path. Every command in the guide carries the Exponential name (`exponential:install`, `exponential:reindex`); the old `ibexa:*` names are kept as aliases so old scripts keep working.
- The Flex recipe endpoint of the se7enxweb recipes is placed before the upstream recipes in the skeleton's `composer.json` (commit "Add sevenx-recipes Flex endpoint before ibexa/recipes", 2026-03-12), so bundles are configured with the fork-aware recipes.
- Three databases are documented: MySQL / MariaDB, PostgreSQL, and SQLite without any server ([SQLite for the platform](platform-sqlite-install.md)).

## Create a project

```bash
composer create-project se7enxweb/exponential-platform-dxp-skeleton my-project
cd my-project
```

Composer downloads the packages, runs the recipes and the post-install scripts (`assets:install`, `cache:clear`).

Configure the environment (never commit `.env.local`):

```bash
cp .env .env.local
$EDITOR .env.local
```

Minimum variables for MySQL / MariaDB:

| Variable | Example | Meaning |
|---|---|---|
| `APP_ENV` | `prod` or `dev` | Symfony environment |
| `APP_SECRET` | 32 random hex characters | Symfony secret |
| `DATABASE_DRIVER` | `pdo_mysql` (`pdo_pgsql` for PostgreSQL) | PDO driver |
| `DATABASE_HOST`, `DATABASE_PORT` | `127.0.0.1`, `3306` | server |
| `DATABASE_NAME`, `DATABASE_USER`, `DATABASE_PASSWORD` | your values | credentials |
| `DATABASE_CHARSET`, `DATABASE_COLLATION` | `utf8mb4`, `utf8mb4_unicode_520_ci` | character set |
| `DATABASE_VERSION` | `mariadb-10.6.0` or `8.0` | server version Doctrine assumes |
| `JWT_SECRET_KEY`, `JWT_PUBLIC_KEY`, `JWT_PASSPHRASE` | key paths under `config/jwt/`, a random passphrase | REST API login |

`DATABASE_URL` is derived from the parts, or you can set a full DSN directly.

Create the database and install:

```bash
mysql -e "CREATE DATABASE exponential CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;"
php bin/console exponential:install exponential-oss
```

Then build the assets and the keys:

```bash
source ~/.nvm/nvm.sh && nvm use 20
yarn install
yarn dev                                              # site assets (yarn build for production)
php bin/console assets:install --symlink --relative public
yarn ibexa:build                                      # admin interface assets
php bin/console lexik:jwt:generate-keypair            # REST API keys
php bin/console ibexa:graphql:generate-schema         # GraphQL schema
php bin/console exponential:reindex                   # search index
php bin/console cache:clear
```

File permissions for the web server user (replace `www-data`):

```bash
setfacl -R  -m u:www-data:rwX -m g:www-data:rwX var/ public/var/
setfacl -dR -m u:www-data:rwX -m g:www-data:rwX var/ public/var/
```

Siteaccesses: `site` serves the Twig front end at `/`, `admin` serves the administration interface at `/adminui/`. REST is at `/api/ezp/v2/`, GraphQL at `/graphql`.

The demo data creates an administrator account that the guide documents publicly. Change its password in the admin interface immediately.

## Day to day

```bash
php bin/console cache:clear --env=prod                # after configuration or code changes
php bin/console cache:warmup --env=prod
php bin/console cache:pool:clear cache.tagaware.filesystem
php bin/console fos:httpcache:invalidate:path / --all # when Varnish or the HTTP cache is in use
php bin/console exponential:reindex --content-type=article
php bin/console doctrine:migration:migrate --dry-run  # preview pending migrations
php bin/console doctrine:migration:migrate --allow-no-migration
```

Cron (every five minutes):

```bash
*/5 * * * * /usr/bin/php /var/www/exponential/bin/console ibexa:cron:run --env=prod >> /var/log/exponential-cron.log 2>&1
```

Note that `ibexa:cron:run` and `ibexa:graphql:generate-schema` still carry their upstream names in this release; only commands that were migrated have an `exponential:` name ([list](../../specifications/6.0/platform-console-commands.md)).

## Database conversion

The guide (section 21) describes moving between engines: any database to SQLite (`mysql2sqlite` for MySQL, `pgloader` for PostgreSQL), SQLite to MySQL or PostgreSQL, MySQL to PostgreSQL and the reverse, and an export-only path to Oracle. It ends with a post-conversion checklist. After converting to SQLite set `DATABASE_URL="sqlite:///%kernel.project_dir%/var/data_dev.db"` and `MESSENGER_TRANSPORT_DSN=sync://`.

## Limits

- The v5 skeleton refuses PHP older than 8.3. The 4.6.x skeleton keeps PHP 7.4 to 8.5 and Symfony 5.4.
- Node.js: the v5 skeleton guide says use Node 20 LTS only; the Nexus v5 starter requires Node 22. Follow the guide of the project you install.
- A clone of the skeleton with `git clone` instead of `create-project` needs `composer install --keep-vcs` and the same steps in order.

## Related pages

- [Installing Exponential 6 itself](../../INSTALL.md)
- Platform features: [Nexus starter](platform-nexus-starter.md), [legacy bridge](legacy-bridge.md), [platform administration interface](platform-admin-ui-fork.md), [Layouts on the platform](platform-layouts-core-fork.md), [PHP 8.5 framework forks](platform-php85-framework-forks.md), [site bundles](platform-site-bundles.md), [SQLite for Exponential Platform](platform-sqlite-install.md), [AdminNeo database manager](adminneo-database-manager.md)
- Specifications: [platform console command names](../../specifications/6.0/platform-console-commands.md), [platform package map](../../specifications/6.0/platform-package-map.md), [platform SQLite installer](../../specifications/6.0/platform-sqlite-installer.md), [legacy bridge bundle](../../specifications/6.0/legacy-bridge-bundle.md)
- Upgrade notes: [package forks and command renames](../../bc/6.0/platform-package-forks-and-command-renames.md)
- Changelog: [platform changelog](../../changelogs/extensions/exponential-platform.md)
- History: [exponential-platform-dxp-skeleton](../../history/ecosystem/exponential-platform-dxp-skeleton.md), [exponential-platform-v4x-dxp-skeleton](../../history/ecosystem/exponential-platform-v4x-dxp-skeleton.md), [exponential-platform-dxp](../../history/ecosystem/exponential-platform-dxp.md), [oss](../../history/ecosystem/oss.md), [ecosystem overview](../../history/ecosystem.md), [ecosystem months](../../history/ecosystem/months/2026-04.md), [change ledger](../../history/ledger/README.md)
