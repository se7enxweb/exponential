---
title: Broken deploy rollback
audience: government, admin, developer, hosting
category: public-sector
order: 90
summary: Runbook for a site that broke right after a deployment - confirm the change is the cause, roll back code and settings, decide whether the database must be restored, redeploy in the right order, and record it for the change board.
keywords: broken deploy, rollback, failed deployment, regression, redeploy, previous version, autoloads, cache clear, exp:velocity deploy, PHP-FPM reload, change board, incident
type: help
---
# Broken deploy rollback

This runbook is for administrators and developers when errors start right after a deployment or an update. The cause
is almost always in that change. Plan background: [Rollback planning](../upgrades-and-change/rollback-planning.md).

## Contents
1. [Confirm](#confirm)
2. [Quick checks before rolling back](#quick-checks-before-rolling-back)
3. [Roll back code and settings](#roll-back-code-and-settings)
4. [When the database changed](#when-the-database-changed)
5. [After the rollback](#after-the-rollback)
6. [Related pages](#related-pages)

## Confirm

```bash
./console exp:logs --tail=var/log/error.log --lines=50
./console exp:audit search --channel=system --from='<time of the deploy>' --allow-root-user
```

Errors that began at the deploy time point to it. The audit `system` channel lists the deploy, settings writes and
cache clears of that window.

## Quick checks before rolling back

Many "broken deploys" are an incomplete deploy, fixed in a minute:

| Symptom | Fix |
|---|---|
| `Class ... not found` | `php bin/php/ezpgenerateautoloads.php -e`, then clear caches |
| Old behavior still visible | PHP-FPM or Velocity still run the old code: `./console exp:velocity deploy --allow-root-user` (reloads the right PHP-FPM and restarts Velocity in order) |
| Pages look old after a template change | `./console exp:cache clear --id=template --allow-root-user` and `./console exp:velocity cache clear --allow-root-user` |
| A new setting has no effect | `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`, then reload |

## Roll back code and settings

1. Maintenance mode if visitors are affected:
   `php bin/php/maintenance.php on --message="This service is temporarily unavailable." --allow-admin --allow-root-user`.
2. Deploy the previous commit (or previous release directory) with its `composer.lock` and settings.
3. Regenerate and clear in the right order, the caches holding finished pages last:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   ./console exp:velocity deploy --dry-run --allow-root-user
   ./console exp:velocity deploy --allow-root-user
   ```

   Without Velocity: clear caches, reload the PHP-FPM that serves the site, then clear the content cache.
4. Check from an allowed address, then switch maintenance off.

## When the database changed

If the deploy ran a database update or a migration script, the previous code may not work with the new schema.
Restore the backup taken just before the change (database and files), as the upgrading guide says for a failed SQL
file; never repair by hand and re-run ([Upgrading](../../../../guides/upgrading.md#if-something-goes-wrong)).
Content edited since the backup is lost: tell editors what to redo.

## After the rollback

- Clear the content and response caches after PHP and Velocity run the restored code.
- Record: what broke, when, rollback done, user impact. The change board needs it before the change is retried.
- Reproduce the failure on staging before the next attempt ([Staging and approval](../upgrades-and-change/staging-and-approval.md)).

## Related pages
- [Deploy a PHP change](../../tasks/operations/deploy-a-php-change.md)
- [Site down](site-down.md)
- [Deploying changes (hosting)](../../../hosting/operations/deploying-changes.md)
- [Deploying guide, section 7](../../../../guides/deploying.md#7-ship-a-php-change-with-one-command)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
