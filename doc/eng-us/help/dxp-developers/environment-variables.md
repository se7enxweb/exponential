---
title: Environment variables
audience: dxp-developer
category: dxp-development
order: 9
summary: Every environment variable the DXP skeleton's guide documents - application, database (MySQL, PostgreSQL, SQLite), JWT, search engine, HTTP cache, application cache, mail, sessions and CORS - and the rules for keeping secrets out of Git.
keywords: .env, .env.local, environment variables, APP_ENV, APP_SECRET, DATABASE_URL, JWT_PASSPHRASE, SEARCH_ENGINE, HTTPCACHE_PURGE_TYPE, CACHE_POOL, MAILER_DSN, MESSENGER_TRANSPORT_DSN, secrets
type: help
---
# Environment variables

This page is for developers who configure a DXP project. A Symfony project reads its settings from environment
variables; the skeleton keeps defaults in `.env` and your host's values in `.env.local`. All variables below are the
ones the skeleton's guide (section 4) documents.

## Contents

1. [The files and the rule](#the-files-and-the-rule)
2. [Application](#application)
3. [Database](#database)
4. [JWT](#jwt)
5. [Search, cache, mail and the rest](#search-cache-mail-and-the-rest)
6. [Keeping a reference without secrets](#keeping-a-reference-without-secrets)
7. [Checking what Symfony sees](#checking-what-symfony-sees)

## The files and the rule

| File | Committed | Holds |
|---|---|---|
| `.env` | yes | Defaults without secrets |
| `.env.local` | **never** | This host's values and secrets |
| `.env.local.dist` (optional) | yes | A copy of `.env.local` with every secret redacted, as a reference |

Real environment variables set by the web server or the process manager win over the files.

## Application

```bash
APP_ENV=prod                  # or dev
APP_SECRET=<32 random hex characters>
```

`APP_DEBUG` and `APP_HTTP_CACHE` are set in the web server configuration in the guide's examples
([Web server setup](web-server-setup.md)).

## Database

MySQL or MariaDB from parts:

```bash
DATABASE_DRIVER=pdo_mysql
DATABASE_HOST=127.0.0.1
DATABASE_PORT=3306
DATABASE_NAME=your_db_name
DATABASE_USER=your_db_user
DATABASE_PASSWORD=your_db_password
DATABASE_CHARSET=utf8mb4
DATABASE_COLLATION=utf8mb4_unicode_520_ci
DATABASE_VERSION=mariadb-10.6.0    # or 8.0 for MySQL
```

PostgreSQL uses `DATABASE_DRIVER=pdo_pgsql`, port `5432`, `DATABASE_CHARSET=utf8` and `DATABASE_VERSION=16`. A full
DSN in `DATABASE_URL` replaces the parts. SQLite uses only the DSN and a synchronous messenger transport:

```bash
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data_%kernel.environment%.db"
MESSENGER_TRANSPORT_DSN=sync://
```

Details: [Databases](databases-mysql-postgresql-sqlite.md), [SQLite for development](sqlite-for-development.md).

## JWT

```bash
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=<64 random hex characters>
```

The key files are generated per installation and never committed ([JWT authentication](jwt-authentication.md)).

## Search, cache, mail and the rest

| Variable | Default in the guide | Other values | Page |
|---|---|---|---|
| `SEARCH_ENGINE` | `legacy` (the database search engine) | `solr`, with `SOLR_DSN` and `SOLR_CORE` | [Search](search-legacy-and-solr.md) |
| `HTTPCACHE_PURGE_TYPE` | `local` | `varnish` | [HTTP cache](http-cache-and-varnish.md) |
| `HTTPCACHE_DEFAULT_TTL` | `86400` | seconds | |
| `HTTPCACHE_PURGE_SERVER` | `http://localhost:80` | the Varnish address | |
| `HTTPCACHE_VARNISH_INVALIDATE_TOKEN`, `TRUSTED_PROXIES` | unset | with Varnish | |
| `CACHE_POOL` | `cache.tagaware.filesystem` | `cache.redis` with `CACHE_DSN` | [Application cache](application-cache-and-redis.md) |
| `MAILER_DSN` | `null://null` (no delivery) | `smtp://localhost:25` | |
| `IMAGEMAGICK_PATH` | `/usr/bin` | | [Image variations](image-variations.md) |
| `CORS_ALLOW_ORIGIN` | localhost pattern | your client origins | [Headless with the DXP](headless-with-the-dxp.md) |
| `SESSION_HANDLER_ID`, `SESSION_SAVE_PATH` | native file sessions under `var/sessions/` | | |

For test mail in development keep `MAILER_DSN=null://null`, and use `example.invalid` addresses for test users, so no
mail reaches a real person.

## Keeping a reference without secrets

```bash
cp .env.local .env.local.dist
$EDITOR .env.local.dist          # replace every password, secret and passphrase with a placeholder
git add .env.local.dist
```

Read the file once more before you commit it.

## Checking what Symfony sees

```bash
php bin/console debug:dotenv               # which files were loaded and the values they set
php bin/console debug:container --env-vars # environment variables used by the container
```

## Related pages

- [Installing with the skeleton](installing-with-the-skeleton.md)
- [Deploying](deploying-dxp.md)
- [DXP skeleton](../../features/6.0/platform-dxp-skeleton.md#create-a-project)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
