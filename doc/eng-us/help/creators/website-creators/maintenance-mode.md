---
title: Maintenance mode during changes
audience: website-creator, admin
category: website-creation
order: 720
summary: Take a site offline for upgrades, imports and restores - Setup > Maintenance or maintenance.php, the message and expected end, who still gets in, what visitors and search engines get, and the way back in.
keywords: maintenance mode, offline, 503, Setup > Maintenance, maintenance.php, exp:maintenance, maintenance.json, allow-admin, allow-ip, upgrade, website creator
type: help
---
# Maintenance mode during changes

This page is for website creators who must take a site offline for a while: an upgrade, an import, a restore. Maintenance
mode answers every public page with a friendly 503 page while you work, and keeps the administration reachable
([Maintenance mode](../../../guides/maintenance-mode.md)).

## Contents

1. [What visitors get](#what-visitors-get)
2. [From the administration](#from-the-administration)
3. [From a shell](#from-a-shell)
4. [The way back in](#the-way-back-in)
5. [Good practice](#good-practice)

## What visitors get

Every page request gets the maintenance page with status `503 Service Unavailable`, `Retry-After` and `no-store`, so
search engines come back later and nothing is cached. Images, styles and scripts are still served. Your message and the
expected end are shown.

## From the administration

**Setup > Maintenance** (`/setup/maintenance`): an optional message, an optional expected duration (15 minutes to 4
hours in one click), who still gets in (your own address with one tick, other addresses), a preview, and a confirmation
before **Take the site offline**. While offline, the page shows an amber banner and **Bring the site back online**.
Maintenance never ends by itself.

![Setup, Maintenance: the site is online, with the plan for a maintenance window and a preview of the page visitors will see](../../images/creators/maintenance-window.webp)

## From a shell

```bash
php bin/php/maintenance.php on --message="Back soon" --until=2h --allow-admin --allow-root-user
php bin/php/maintenance.php status --allow-root-user
php bin/php/maintenance.php off --allow-root-user
```

Without `--allow-admin` the administration is offline too. The same is `./console exp:maintenance`.

## The way back in

The whole state is one file, `var/maintenance.json`. Deleting it ends maintenance, whatever else is wrong. Every switch
is recorded in the audit trail.

## Good practice

- Announce planned maintenance in advance and keep the window short.
- Let your own address through to test before switching back.
- Under Velocity, clear the response cache after switching back.
- For a backup alone, maintenance mode is only needed to keep database and files consistent.

## Related pages

- [Backups and restores](backups-and-restores.md)
- [Updates and upgrades for site owners](updates-and-upgrades.md)
- [Maintenance mode feature](../../../features/6.0/maintenance-mode.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
