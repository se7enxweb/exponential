---
title: "An upgrade test environment"
audience: designer, developer
category: theme-lifecycle
order: 90
summary: Set up a place to test a theme against new Exponential and PHP versions - a separate installation installed with Composer, demo content, the theme as a Git checkout, and a way to reset it - never a live site.
keywords: test environment, staging, upgrade test, test installation, demo content, reset, composer, git checkout
type: help
---
# An upgrade test environment

This page is for designers who maintain themes. You need an installation where breaking things is fine.

## What it consists of

| Part | How |
|---|---|
| An Exponential installation | Installed with Composer, on a server or your computer; SQLite is the default database and needs no server |
| Your theme | As a Git checkout, so you can switch branches ([Develop with Git](../../develop-with-git.md)) |
| Demo content | The theme's demo package or seed data ([Demo content](../distribution/demo-content.md)) |
| A second PHP version | To test the oldest and newest PHP you claim |
| A reset | A copy of the database and `var/` you can restore, or a scripted reinstall |

## Step by step

1. Install Exponential into a new directory following [Getting started](../../../guides/getting-started.md).
2. Let Composer clone your theme as a source checkout:

   ```bash
   composer config 'preferred-install.yourvendor/*' source
   composer require yourvendor/mytheme:dev-main
   ```

3. Activate the theme and choose it for a siteaccess ([Activating a theme](../extensions/activating-a-theme-extension.md)).
4. Import the demo content.
5. Save a reset point: copy the database file (SQLite) or dump the database, and `var/storage`.
6. Write the steps into your theme's `doc/testing.md`, so the next test starts in minutes.

## Rules

- Never test on a live site or with a copy of real customer data.
- Send mail of the test installation to files, never to real addresses.

## Related pages

- [Upgrade testing, step by step](upgrade-test-step-by-step.md)
- [Upgrading](../../../guides/upgrading.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
