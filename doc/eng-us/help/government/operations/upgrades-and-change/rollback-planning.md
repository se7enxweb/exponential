---
title: Rollback planning
audience: government, admin, project-manager, hosting
category: public-sector
order: 100
summary: How to plan the way back before every change to a public website - which changes can be reversed by redeploying, which need a backup restore because the database changed, the decision point inside a maintenance window, and what Exponential does not undo for you.
keywords: rollback, roll back, back out, restore, backup, redeploy, previous version, database update, decision point, maintenance window, change plan, fallback
type: help
---
# Rollback planning

This page is for administrators and change managers who must state, before a change, how they would undo it. Change
boards usually ask for this. Exponential has no "undo upgrade" button; the way back depends on what the change
touched.

## Contents
1. [What kind of change is it?](#what-kind-of-change-is-it)
2. [The backup is the rollback](#the-backup-is-the-rollback)
3. [The decision point](#the-decision-point)
4. [Rolling back step by step](#rolling-back-step-by-step)
5. [What is not undone](#what-is-not-undone)
6. [Related pages](#related-pages)

## What kind of change is it?

| Change | Way back |
|---|---|
| Template, stylesheet | redeploy the previous commit; clear template caches and the Velocity response cache |
| Settings (INI) | redeploy the previous settings; clear the INI cache (`--clear-tag=ini`); reload PHP |
| PHP code, extension without database changes | redeploy the previous commit and `composer.lock`; regenerate autoloads; `exp:velocity deploy` |
| Extension with new tables | as above; the new tables stay unless dropped |
| Exponential upgrade with a database update | **restore the backup** of database and files taken just before |
| Roles, workflows, sections | change them back by hand; the audit trail shows the old values for many of them |
| Content | versions page: **Make a new draft** from the old version; restore from the trash |

## The backup is the rollback

The upgrading guide is explicit: if a SQL file failed half way, "restore the backup of step 1 and apply the file
again from the start; never repair by hand and re-run" ([Upgrading, "If something goes wrong"](../../../../guides/upgrading.md#if-something-goes-wrong)).
So, for every change that touches the database:

1. Take the backup inside the window, after maintenance mode is on, so database and files match
   ([What to back up](../backups-and-recovery/what-to-back-up.md)).
2. Know how long a restore takes: you measured it in your last restore test
   ([Restore testing](../backups-and-recovery/restore-testing.md)).
3. Keep the previous release's code (the previous commit or release directory) at hand.

## The decision point

Write into the plan: "If the checks at step N fail and cannot be fixed by time T, we roll back." T leaves enough time
for the restore before the window ends. The person who decides is named in the change record.

## Rolling back step by step

1. The site is still in maintenance mode (`php bin/php/maintenance.php status --allow-root-user`).
2. Put the previous code back (deployment of the previous commit, or the previous release directory).
3. Restore the database with the engine's tool, and the files (`var/storage`, settings) if they changed
   ([Backups per database engine](../backups-and-recovery/backups-per-database-engine.md)).
4. Regenerate autoloads and clear caches:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all --allow-root-user
   ./console exp:velocity deploy --allow-root-user
   ```

5. Check from an allowed address; **Setup > System information** shows the old version.
6. Switch maintenance off; record the rollback and the reason.

## What is not undone

- Content published, mail sent and cronjob work done between the change and the rollback. Restoring a backup loses
  content edited after it; that is why the window keeps editors out.
- The audit trail files of the window: they live in `var/<site>/log/audit/`; keep them, do not overwrite them with
  the restored copy ([Audit trail archives and backups](../backups-and-recovery/audit-trail-archives-and-backups.md)).
- A published release cannot be "unpublished"; the fix is the next version.

## Related pages
- [Broken deploy rollback](../incidents/broken-deploy-rollback.md)
- [Maintenance windows](maintenance-windows.md)
- [Restore from backup (hosting runbook)](../../../hosting/runbooks/restore-from-backup.md)
- [Disaster recovery plan](../backups-and-recovery/disaster-recovery-plan.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
