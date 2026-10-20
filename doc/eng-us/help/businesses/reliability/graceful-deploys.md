---
title: Graceful deploys
audience: businesses, admin, developer, project-manager
category: business-solutions
order: 100
summary: How to ship changes to a live Exponential site without dropping visitors' requests or serving stale pages, what the one-command deploy does, and the rules a business should set for when and how changes go live.
keywords: deploy, graceful, graceful restart, zero downtime, exp:velocity deploy, PHP-FPM reload, cache order, change window, rollback, release
type: help
---
# Graceful deploys

This page is for managers who approve changes to the live site and for the developer or operator who ships them. It
explains what can go wrong during a deploy, how Exponential's deploy tools avoid dropped requests and stale pages,
and the rules a business should agree on. The technical guide is
[Deploying, ship a PHP change with one command](../../../guides/deploying.md#7-ship-a-php-change-with-one-command).

## Contents

1. [What goes wrong in a careless deploy](#what-goes-wrong-in-a-careless-deploy)
2. [Graceful reload: no dropped requests](#graceful-reload-no-dropped-requests)
3. [The one-command deploy](#the-one-command-deploy)
4. [Why the order of the steps matters](#why-the-order-of-the-steps-matters)
5. [Small changes need less](#small-changes-need-less)
6. [When a deploy needs a maintenance window](#when-a-deploy-needs-a-maintenance-window)
7. [Rules for the business](#rules-for-the-business)
8. [Checklist](#checklist)

## What goes wrong in a careless deploy

- **Dropped requests**: a server restarted the hard way refuses connections for a moment, and a customer in the
  middle of checkout gets an error.
- **Old code still running**: PHP-FPM and Velocity keep classes in memory. "Never run only `exp:cache clear --all`
  and then forget the web server: FPM and Velocity keep the old classes in memory until they are reloaded"
  ([Deploying](../../../guides/deploying.md#7-ship-a-php-change-with-one-command)).
- **Stale pages cached again**: if the page caches are cleared before the server runs the new code, a page rendered
  by the old code in between is stored again and served as if the change had not worked
  ([Operating a site](../../../guides/operating-a-site.md#the-caches-in-front-of-php)).
- **A slow first visitor**: after a cache clear, the first visitor pays for the render.

## Graceful reload: no dropped requests

On Exponential Velocity, `graceful` "re-executes the server without releasing the listening socket, so a deploy does
not drop requests" ([README, "Using it"](https://github.com/se7enxweb/exponential/blob/main/README.md#using-it)):

```bash
php bin/php/console exp:velocity graceful --allow-root-user
```

What each engine does on `graceful`, from [Velocity engines](../../../bc/6.0/velocity-engines.md#in-short):

| Engine | `graceful` |
|---|---|
| Qbix | Re-exec, socket kept |
| FrankenPHP | Configuration reload, no dropped connection |
| PHP's built-in server | Restart (development only) |

When several Qbix instances share one port, `graceful` "reloads them one at a time, so the port keeps answering"
([Velocity engines](../../../bc/6.0/velocity-engines.md#several-qbix-servers-on-one-port-instances)). From engine
0.0.4.50 a stop also drains HTTP/2 connections gracefully (same page).

On Apache or Nginx with PHP-FPM, a graceful reload of the PHP-FPM service plays the same role; see
[Deploying, after every PHP change](../../../guides/deploying.md#34-after-every-php-change-reload-the-right-php-fpm).

## The one-command deploy

For a change to PHP code, an operator or a settings file, one command runs every step in the right order
([Deploying](../../../guides/deploying.md#7-ship-a-php-change-with-one-command)):

```bash
php bin/php/console exp:velocity deploy --dry-run --allow-root-user   # what it would do, nothing else
php bin/php/console exp:velocity deploy --allow-root-user             # the usual case
php bin/php/console exp:velocity deploy --kernel --allow-root-user    # a kernel class was added or renamed
```

What a business should know about it:

- The **dry run** prints every step without changing anything. Ask for it to be part of every change request.
- A real run prints PASS, FAIL or SKIP and the time of each step, and **stops at the first failure** with exit
  status 1. A failed deploy does not carry on half done.
- The last step requests warm-up addresses (`[DeploySettings] WarmUpURL[]`) in the background, "so the first visitor
  finds the caches built".
- Options cover sites without Velocity (`--no-velocity`, Apache only) and sites without PHP-FPM (`--no-fpm`).
- A deploy whose caller is killed between stop and start finishes on its own, so the server is not left down
  ([Velocity engines](../../../bc/6.0/velocity-engines.md#a-restart-or-deploy-that-is-cut-off)).

## Why the order of the steps matters

"The order matters: the caches that hold finished pages are cleared last, after PHP-FPM and Velocity run the new
code, so a page rendered by old code is never cached again"
([Deploying](../../../guides/deploying.md#7-ship-a-php-change-with-one-command)). The deploy also avoids a
needless PHP-FPM reload when OPcache picks up the new files by itself, because "a reload stalls every pool of that
PHP version until its longest request ends" (same page).

## Small changes need less

Not every change needs a full deploy (same page):

| Change | What it needs |
|---|---|
| A template or a stylesheet | Clear the template cache and Velocity's response cache; no restart |
| A settings file, a PHP class or an operator | `exp:velocity deploy` |
| A new or renamed kernel class | `exp:velocity deploy --kernel` |
| Content | No deploy: publishing clears the cached views of the item, its parents and related objects ([View cache and cache blocks](../../creators/website-creators/view-cache-and-cache-blocks.md#what-a-publish-clears)) |

## When a deploy needs a maintenance window

`exp:velocity deploy` "does not switch maintenance on or off". For a deploy that changes the database or many files at
once, open a maintenance window first and close it after the smoke test
([Maintenance mode guide](../../../guides/maintenance-mode.md#10-installations-cronjobs-and-deploys)). See
[Maintenance mode and planned work](maintenance-mode-and-planned-work.md).

## Rules for the business

Agree these with your team or partner, and write them down:

- **Changes are tested first** on a copy of the site, never first on the live one.
- **Every change has a dry run** and a named person who approves it.
- **No deploys right before a peak** (a sale, a newsletter send, a campaign launch) or at the end of the week.
- **Every change can be undone**: the previous version of the code is in Git, and a backup exists when the database
  changes ([Backups that restore](backups-that-restore.md)).
- **Every deploy is checked**: front page, a deep page, the administration, `var/log/error.log`.
- **Every deploy is recorded**: the audit trail records deploys in its `system` channel
  ([Audit trail for operations](audit-trail-for-operations.md)); add a line to your own change log too.

## Checklist

- [ ] The change was tested on a copy of the site.
- [ ] `exp:velocity deploy --dry-run` was read and approved.
- [ ] A maintenance window is open if the database or many files change.
- [ ] The deploy ended with every step PASS or SKIP.
- [ ] Front page, deep page, administration and error log were checked afterwards.
- [ ] The way back is known and a backup exists.

## Related pages

- [Velocity for businesses](velocity-for-businesses.md)
- [Upgrades without surprises](upgrades-without-surprises.md)
- [Deploying changes (hosting)](../../hosting/operations/deploying-changes.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
