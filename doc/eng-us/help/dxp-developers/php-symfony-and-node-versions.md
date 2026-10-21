---
title: PHP, Symfony and Node.js versions
audience: dxp-developer
category: dxp-development
order: 7
summary: The versions a DXP project needs - PHP 8.3 or newer with its extensions and ini settings, Symfony 7.4, Composer 2, Node.js 20 and Yarn 1.22, the databases and the optional services - with the commands to check a machine before you install.
keywords: requirements, PHP 8.3, PHP 8.5, Symfony 7.4, Node.js 20, Yarn 1.22, Composer 2, extensions, memory_limit, MySQL, MariaDB, PostgreSQL, SQLite, Redis, Solr, Varnish
type: help
---
# PHP, Symfony and Node.js versions

This page is for developers and system administrators who prepare a machine for a DXP project. All numbers come from
the skeleton's installation guide (section 1, "Requirements") and the two `composer.json` files.

## Contents

1. [The table](#the-table)
2. [PHP extensions and settings](#php-extensions-and-settings)
3. [Node.js and Yarn](#nodejs-and-yarn)
4. [Check a machine in two minutes](#check-a-machine-in-two-minutes)
5. [Other lines of the family](#other-lines-of-the-family)

## The table

| Component | Minimum | Recommended |
|---|---|---|
| PHP | 8.3 | 8.3 or 8.5 |
| Symfony | 7.4 (LTS) | 7.4 |
| Composer | 2.x | latest 2.x |
| Node.js | 20 LTS | 20 LTS through nvm |
| Yarn | 1.x | 1.22 through corepack |
| MySQL | 8.0 (utf8mb4) | 8.0 or newer |
| MariaDB | 10.3 | 10.6 or newer |
| PostgreSQL | 14 | 16 or newer |
| SQLite | 3.35 | 3.39 or newer, development and testing |
| Redis (optional) | 6.0 | 7.x |
| Solr (optional) | 8.x | 8.11.x |
| Varnish (optional) | 6.0 | 7.1 or newer |
| Apache | 2.4 | 2.4, event MPM with PHP-FPM |
| Nginx | 1.18 | 1.24 or newer |

## PHP extensions and settings

The skeleton's `composer.json` requires `ctype`, `iconv`, `gd`, `curl`, `json`, `xsl`, `xml`, `intl` and `mbstring`.
The guide adds `pdo_mysql`, `pdo_pgsql` or `pdo_sqlite` for your database, `imagick` as an alternative to `gd`, and
`opcache`.

| Setting | Value from the guide |
|---|---|
| `memory_limit` | 256M or more, 512M recommended |
| `date.timezone` | must be set |
| `max_execution_time` | 120 or more, 300 recommended for command line work |

## Node.js and Yarn

Node.js and Yarn build the site assets and the administration interface. The guide is strict: use **Node.js 20 LTS**
only ("Do not use Node.js 18 or 22 — only 20 LTS is tested and supported for this release") and Yarn 1.22, activated
with `corepack enable`. The Nexus v5 starter uses Node.js 22; follow the guide of the project you install
([DXP skeleton, Limits](../../features/6.0/platform-dxp-skeleton.md#limits)).

```bash
source ~/.nvm/nvm.sh
nvm install 20
nvm use 20
corepack enable
```

Production servers do not need Node.js if you build the assets elsewhere and deploy the result
([Building assets](building-assets-with-yarn-and-encore.md)).

## Check a machine in two minutes

```bash
php -v                                   # 8.3 or newer
php -m | grep -iE "ctype|iconv|gd|imagick|curl|json|xsl|xml|intl|mbstring|pdo|opcache|sqlite"
php -i | grep -E "memory_limit|date.timezone|max_execution_time"
composer --version                       # Composer version 2.x
node -v                                  # v20.x
yarn -v                                  # 1.22.x
```

For an existing project, Composer can tell you what blocks a version:

```bash
composer check-platform-reqs
composer why-not php 8.2
```

## Other lines of the family

| Line | PHP | Symfony | Node.js |
|---|---|---|---|
| DXP v5 (this section) | 8.3 or newer | 7.4 | 20 |
| [4.6.x skeleton](the-v4x-skeleton.md) | 7.4 to 8.5 | 5.4 | 20 |
| Nexus 1.3 and the v5 starter | 8.4 or newer | 7.4 | 22 |

Source: [Platform package forks, Step 3](../../bc/6.0/platform-package-forks-and-command-renames.md#step-3-check-php-and-node-versions).

## Related pages

- [Installing with the skeleton](installing-with-the-skeleton.md)
- [Databases](databases-mysql-postgresql-sqlite.md)
- [Web server setup](web-server-setup.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
