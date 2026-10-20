---
title: Pause cronjobs during maintenance
audience: government, admin, hosting
category: public-sector
order: 120
summary: Step by step - pause one or all cronjob parts during a release or migration from Setup > Cronjobs, with a note saying why, so no runner starts them, and resume them afterwards without forgetting.
keywords: pause cronjobs, Pause all parts, resume, maintenance, release, migration, cronpart_deactivated, PauseSettings, Setup Cronjobs, several servers
type: help
---
# Pause cronjobs during maintenance

This task is for administrators opening a maintenance window, a data migration or an index rebuild. A paused part is
left out by `runcronjobs.php`, whoever starts it (the crontab, the console, Velocity), without editing the crontab
([Pausing cronjob parts](../../../../features/6.0/cronjob-pause.md)).

## Contents
1. [Before you start](#before-you-start)
2. [Pause](#pause)
3. [Resume](#resume)
4. [Check it worked](#check-it-worked)
5. [Pitfalls](#pitfalls)
6. [Related pages](#related-pages)

## Before you start

- `setup/managecronjobs`.
- Know which parts matter: pausing `frequent` stops approvals, notifications and the audit's alerts and indexing for
  the duration.

## Pause

1. **Setup > Cronjobs**.
2. On a part's card press **Pause** (or **Pause all parts**), and give a note such as "Upgrade window, change
   <reference>".
3. A part paused while it runs finishes the script that runs and leaves out the scripts after it.

The card then shows **Paused**, since when, by whom and the note; the overview counts paused parts.

## Resume

Right after the window, on the same page, resume the parts; then run the frequent part once
([Run a cronjob part by hand](run-a-cronjob-part-by-hand.md)) so approvals and alerts catch up.

## Check it worked

- While paused: `php runcronjobs.php --siteaccess=site frequent --allow-root-user` prints "Notice: The cronjob part
  'frequent' is paused (Setup > Cronjobs). Paused since ... by ...: <note>" and exits 0.
- After resuming: the cards show no **Paused** badge; the Setup overview tile no longer counts paused parts.

## Pitfalls

- Several web servers: pause files are local to each server unless `cronjob.ini [PauseSettings] Directory` points at
  a shared directory.
- A forgotten pause silently stops approvals and alerts; put "resume cronjobs" on the window's closing checklist.

## Related pages
- [Take the site into maintenance for an upgrade](take-the-site-into-maintenance-for-an-upgrade.md)
- [Bring the site back online](bring-the-site-back-online.md)
- [Cronjob monitoring](../../operations/monitoring/cronjob-monitoring.md)
- [Cronjobs guide](../../../../guides/cronjobs.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
