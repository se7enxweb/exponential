---
title: Deploy a PHP change
audience: government, admin, developer, hosting
category: public-sector
order: 260
summary: Step by step - ship a PHP class, operator or settings change with one command, exp:velocity deploy, after a dry run for the change record - autoloads, INI caches, the right PHP-FPM, Velocity, then the page caches in the right order.
keywords: deploy, exp:velocity deploy, dry run, PHP change, autoloads, PHP-FPM reload, Velocity restart, cache order, change record, kernel, packer
type: help
---
# Deploy a PHP change

This task is for administrators and developers putting an approved code or settings change live. One command runs
every step in the right order and stops at the first failure
([Deploying, section 7](../../../../guides/deploying.md#7-ship-a-php-change-with-one-command)).

## Contents
1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Options](#options)
4. [Check it worked](#check-it-worked)
5. [Related pages](#related-pages)

## Before you start

- The change is approved and tested on staging ([Staging and approval](../../operations/upgrades-and-change/staging-and-approval.md)).
- The new code is on the server (your deployment of the approved commit).
- Shell access in the installation directory.

## Steps

1. Dry run, for the change record (prints the fourteen steps, each marked `DRY`, changes nothing):

   ```bash
   ./console exp:velocity deploy --dry-run --allow-root-user
   ```

2. Run it:

   ```bash
   ./console exp:velocity deploy --allow-root-user
   ```

   Each step prints PASS, FAIL or SKIP with its time; the run stops at the first failure with exit status 1.

The steps include the extension autoloads, the INI caches, the template caches, the PHP-FPM step (it waits for OPcache
to revalidate instead of reloading, unless asked, because a reload stalls every pool of that PHP version), the
Velocity restart, the content cache, Velocity's response cache last, and warm-up requests for
`[DeploySettings] WarmUpURL[]`.

## Options

| Option | When |
|---|---|
| `--kernel` | a kernel class was added or renamed |
| `--fpm-reload` | `php.ini` or the pool changed, or files were renamed |
| `--no-fpm` | Velocity only |
| `--no-velocity` | Apache with PHP-FPM only |
| `--packer` | an ezjscore server function or packed scripts and styles changed |

Which PHP-FPM is reloaded comes from `[DeploySettings] PhpFpmService` (`auto` looks for the pool named after the
installation's host name): `./console exp:velocity config get DeploySettings PhpFpmService --allow-root-user`.

## Check it worked

- Every step PASS; keep the output with the change record.
- The changed behavior is visible; `./console exp:logs --tail=var/log/error.log --lines=20` shows nothing new.
- If something broke: [Broken deploy rollback](../../operations/incidents/broken-deploy-rollback.md).

## Related pages
- [Warm the caches after a deploy](warm-the-caches-after-a-deploy.md)
- [Clear caches after a settings change](clear-caches-after-a-settings-change.md)
- [Deploying changes (hosting)](../../../hosting/operations/deploying-changes.md)
- [Velocity engines: deploying a PHP change](../../../../bc/6.0/velocity-engines.md#deploying-a-php-change-expvelocity-deploy)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
