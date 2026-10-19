---
title: Updates and upgrades for site owners
audience: website-creator, admin
category: website-creation
order: 710
summary: Keep Exponential, its extensions and PHP up to date - how versions work, reading the changelog and BC notes, updating with Composer and Git, the database update, testing on a copy, and the upgrade check afterwards.
keywords: update, upgrade, version, Composer, changelog, behavior changes, database update, upgrade check, extensions, PHP version, staging, website creator
type: help
---
# Updates and upgrades for site owners

This page is for website creators who keep a site current. Updates bring security fixes and features; doing them
regularly keeps each one small. The full procedure is in [Upgrading](../../../install/11-upgrading.md) and
[Upgrading (guide)](../../../guides/upgrading.md).

## Contents

1. [What gets updated](#what-gets-updated)
2. [Before you update](#before-you-update)
3. [The update, step by step](#the-update-step-by-step)
4. [After the update](#after-the-update)
5. [PHP versions](#php-versions)

## What gets updated

| Part | How |
|---|---|
| The kernel (Exponential 6.0.x) | Git or Composer, then the database update files |
| Extensions | Composer packages, each with its own version ([Extensions](../../../guides/extensions.md)) |
| Velocity | the engine package, then `exp:velocity restart` |
| PHP | the operating system |

Published versions are permanent: a fix comes as the next version, never as a changed old one.

## Before you update

- [ ] Read the changelog of every version you pass (`doc/changelogs/6.0/`) and the behavior change notes (`doc/bc/6.0/`).
- [ ] Note your own extensions and template overrides that touch changed areas.
- [ ] Take a backup ([Backups and restores](backups-and-restores.md)).
- [ ] Try the update on a copy (staging) first.

## The update, step by step

From [11.6 From any 6.0.x to today](../../../install/11-upgrading.md#116-from-any-60x-to-today):

1. Put the new code in place (Git pull or Composer update of the root package, in a staging copy first).
2. Apply the database update for your engine.
3. Read the checklist of each period you pass.
4. Check your own extensions.
5. Regenerate autoloads, clear caches, reload PHP and restart Velocity (`exp:velocity deploy --kernel` does it in order).
6. Verify ([11.7](../../../install/11-upgrading.md#117-verify-the-upgrade)).

Use maintenance mode for the database step ([Maintenance mode during changes](maintenance-mode.md)).

## After the update

- **Setup > Upgrade check**: file consistency and database checks ([Upgrade check](../../../guides/upgrade-check.md)).
- Click through every page type and form; run your accessibility and benchmark checks.
- Watch the error log for a day.

## PHP versions

Exponential 6.0.15 runs on PHP 8.0 to 8.5; Velocity needs 8.1 or later. Use the newest PHP your system offers; older
versions lose security support first ([2.2 PHP](../../../install/02-requirements.md#22-php)). Test a PHP upgrade on
staging like any other update.

## Related pages

- [Release and upgrade planning](../../leadership/release-and-upgrade-planning.md)
- [Maintaining your website](maintaining-your-website.md)
- [Versions and releases of your extension](../app-creators/versions-and-releases.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
