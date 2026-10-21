---
title: Installing with the skeleton
audience: dxp-developer
category: dxp-development
order: 8
summary: Step by step from an empty folder to a running DXP project - create-project or git clone, environment, database, install command, permissions, assets, JWT keys, GraphQL schema, reindex and the first start - with a save point after each stage.
keywords: install, create-project, skeleton, git clone, exponential:install, exponential-oss, JWT, GraphQL schema, reindex, symfony server, first start, admin password
type: help
---
# Installing with the skeleton

This page is for developers who install a DXP project for the first time. It follows the skeleton's installation
guide (sections 3 to 11) in a shorter form and links to the pages that explain each step. Allow about 30 minutes on
a prepared machine ([Versions you need](php-symfony-and-node-versions.md)).

## Contents

1. [Two ways to start](#two-ways-to-start)
2. [The steps](#the-steps)
3. [After the install](#after-the-install)
4. [Save points](#save-points)
5. [When a step fails](#when-a-step-fails)

## Two ways to start

| Way | Command | Use it when |
|---|---|---|
| Create a project (recommended) | `composer create-project se7enxweb/exponential-platform-dxp-skeleton my-project` | You start your own project |
| Clone the skeleton | `git clone git@github.com:se7enxweb/exponential-platform-dxp-skeleton.git` then `composer install --keep-vcs` | You work on the skeleton itself |

`create-project` downloads the packages, runs the Flex recipes and the post-install scripts (`assets:install`,
`cache:clear`).

## The steps

1. **Create the project.**

   ```bash
   composer create-project se7enxweb/exponential-platform-dxp-skeleton my-project
   cd my-project
   ```

2. **Configure the environment** in `.env.local`, never in `.env`, and never commit it
   ([Environment variables](environment-variables.md)).

   ```bash
   cp .env .env.local
   $EDITOR .env.local
   ```

3. **Create the database** (MySQL, MariaDB or PostgreSQL) or set the SQLite DSN
   ([Databases](databases-mysql-postgresql-sqlite.md), [SQLite for development](sqlite-for-development.md)).

4. **Install the schema and seed data.**

   ```bash
   php bin/console exponential:install exponential-oss
   ```

   The argument is the install type; `exponential-oss` is the default. `--skip-indexing` skips the automatic reindex
   ([Console commands](console-commands-and-renames.md)).

5. **Set the permissions** for the web server user on `var/` and `public/var/` ([File permissions](file-permissions.md)).

6. **Build the site assets.**

   ```bash
   source ~/.nvm/nvm.sh && nvm use 20
   yarn install
   yarn dev
   ```

7. **Build the administration assets.**

   ```bash
   php bin/console assets:install --symlink --relative public
   yarn ibexa:build
   ```

8. **Generate the JWT keypair** for the REST API ([JWT authentication](jwt-authentication.md)).

   ```bash
   php bin/console lexik:jwt:generate-keypair
   ```

9. **Generate the GraphQL schema** ([GraphQL](graphql-and-graphiql.md)).

   ```bash
   php bin/console ibexa:graphql:generate-schema
   ```

10. **Clear the cache and build the search index.**

    ```bash
    php bin/console cache:clear
    php bin/console exponential:reindex
    ```

11. **Start a development server.**

    ```bash
    symfony server:start
    ```

## After the install

| URL | What it is |
|---|---|
| `https://127.0.0.1:8000/` | The public site (Twig) |
| `https://127.0.0.1:8000/adminui/` | The administration interface |
| `https://127.0.0.1:8000/api/ibexa/v2/` | REST API v2 (the guide shows `/api/ezp/v2/`; check the prefix as on [The REST API](the-rest-api.md#where-it-lives)) |
| `https://127.0.0.1:8000/graphql` | GraphQL |

The seed data creates an administrator account whose sign-in is printed in the public installation guide. **Change
its password in the administration interface right away**, before the site is reachable from anywhere else.

## Save points

The guide suggests a Git commit after each stage, so you can return to a working state:

```bash
git init && git add -A && git commit -m "Added: project created from the DXP skeleton"
git add composer.lock && git commit -m "Updated: locked vendor dependencies"
```

Before `git add -A`, check that `.gitignore` excludes `.env.local`, `config/jwt/*.pem`, `var/` and `vendor/`
([Develop with Git](../develop-with-git.md)).

## When a step fails

| Symptom | Page |
|---|---|
| Composer refuses a PHP version | [Versions](php-symfony-and-node-versions.md#check-a-machine-in-two-minutes) |
| `yarn ibexa:build` reports "Module not found" | Run `assets:install` first ([Building assets](building-assets-with-yarn-and-encore.md)) |
| `attempt to write a readonly database` | [SQLite for development](sqlite-for-development.md) |
| Anything else | [Troubleshooting](troubleshooting-dxp.md) |

## Related pages

- [DXP skeleton: create-project and a documented install](../../features/6.0/platform-dxp-skeleton.md)
- [Environment variables](environment-variables.md)
- [Day-to-day commands](day-to-day-commands.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
