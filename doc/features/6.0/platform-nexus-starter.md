# Exponential Platform Nexus: a finished site in minutes

**Repositories:** `se7enxweb/exponential-platform-nexus-starter` (Platform v5), `se7enxweb/exponential-platform-nexus` (Platform 1.x and 2.5 lines), `se7enxweb/cjw-exponential-platform-nexus` (CJW flavour).
**Applies to:** Exponential Platform, the Symfony based sibling of Exponential 6. It is not part of the legacy kernel you may be reading this page in.
**History:** [exponential-platform-nexus-starter](../../history/ecosystem/exponential-platform-nexus-starter.md), [exponential-platform-nexus](../../history/ecosystem/exponential-platform-nexus.md), [cjw-exponential-platform-nexus](../../history/ecosystem/cjw-exponential-platform-nexus.md).

## What it is

Nexus is a ready-made website project. You run Composer once, run one install command, and you have a site with demo content, the Netgen Layouts page builder, a Twig front end, an administration interface, a REST API and a GraphQL API. You change the content model and the design afterwards instead of assembling bundles first.

There are three generations, and you pick by the platform you want to run.

| Line | Repository | Runs on | Database | Use it when |
|---|---|---|---|---|
| Nexus v5 starter | `exponential-platform-nexus-starter` | Symfony 7.4 LTS, PHP 8.4+ (8.5 recommended), Node.js 22 | SQLite (development default), MySQL 8.0+, MariaDB 10.3+, PostgreSQL 14+ | You start a new project. |
| Nexus 1.x | `exponential-platform-nexus` branches `1.0.0.x` to `1.3.0.x` | eZ Platform 2.5 with the Exponential legacy kernel beside it, Symfony 3.4 stack with PHP 8.x fixes | MySQL / MariaDB; SQLite for the `exponential-cjw` type | You keep a legacy site and its legacy admin. |
| Nexus 2.5 | tags `v2.5.0.0`, `v2.5.0.1` | Exponential Platform Legacy 2.5 distribution | MySQL / MariaDB | You follow the 2.5 long-term line. |
| CJW Nexus | `cjw-exponential-platform-nexus` tag `1.0.0.6` | as Nexus 1.x | MySQL, SQLite | You want the CJW starter content. |

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
git clone git@github.com:se7enxweb/exponential-platform-nexus.git
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
- `exponential:install` is the Exponential name of the upstream `ibexa:install` command. The old name still works as a deprecated alias. See [Platform console command names](../../specifications/6.0/platform-console-commands.md).
- The administrator account the installer creates is documented in the repository README. Change its password at once after the first login; never leave the default on a reachable server.

After the install these addresses answer:

| URL | What |
|---|---|
| `https://127.0.0.1:8000/` | the public site (`site` siteaccess) |
| `https://127.0.0.1:8000/adminui/` | Platform v5 admin interface |
| `https://127.0.0.1:8000/api/ezp/v2/` | REST API v2, JWT authenticated |
| `https://127.0.0.1:8000/graphql` | GraphQL endpoint |
| `https://127.0.0.1:8000/nglayouts/admin` | Netgen Layouts admin |

The complete step by step guide (web server configuration, permissions, production checklist, cron, Solr, Varnish, troubleshooting, database conversion) is `doc/sevenx/INSTALL.md` inside the starter repository.

## Install Nexus 1.x

The 1.x line installs with Composer and the SQL dumps that ship in the project:

```bash
cd /var/www
composer create-project se7enxweb/exponential-platform-nexus:v1.0.0.0.3 --ignore-platform-reqs
```

`--ignore-platform-reqs` is needed because the package definitions of the old stack still declare older PHP limits; the 1.x README says this requirement will go away as the packages are updated.

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

APCu is strongly recommended and enabled by default in the 1.x configuration (it greatly speeds up uncached page rendering); it can be replaced through the service configuration.

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
- The Nexus 1.x line is built on the Symfony 3.4 stack; it needs the framework forks listed in [PHP 8.4 / 8.5 forks](platform-php85-framework-forks.md) to run on PHP 8.2 to 8.5.
- The public installation guides in the repositories contain a default administrator account for the demo data. Treat it as public knowledge and change it.

## Related

- [DXP project skeleton](platform-dxp-skeleton.md): the plain skeleton without the Nexus demo design.
- [Legacy bridge](legacy-bridge.md): how the legacy admin and the Symfony stack run in one installation.
- [Package map](../../specifications/6.0/platform-package-map.md) and [upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md).
- [The ecosystem overview](../../history/ecosystem.md).
