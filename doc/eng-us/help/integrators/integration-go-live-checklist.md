---
title: Integration go-live checklist
audience: integrator, admin, project-manager
category: integration
order: 510
summary: The checklist for taking an Exponential integration live - readiness, the initial load, switching schedules and hooks on, monitoring, the first runs, rollback, and the first days of hypercare - with the commands for each step.
keywords: go-live, launch, checklist, cut-over, initial load, schedule, cronjob, monitoring, rollback, hypercare, readiness, production
type: help
---
# Integration go-live checklist

This page is for the team that takes an integration into production. Work through it in order; each section ends
with a decision point. The steps for the site as a whole are in
[New customer site go-live](../hosting/checklists/new-customer-site-go-live.md).

## Contents

1. [One week before](#one-week-before)
2. [The day before](#the-day-before)
3. [Go-live](#go-live)
4. [First runs](#first-runs)
5. [Rollback](#rollback)
6. [Hypercare](#hypercare)

## One week before

- [ ] Acceptance cases passed in staging; sign-off recorded.
- [ ] Security review passed ([checklist](integration-security-checklist.md)).
- [ ] Production credentials created and stored; service accounts and roles in place.
- [ ] The integration extension released; the version to install is a tag.
- [ ] Monitoring checks configured against production, alerts going to the right people
      ([Monitoring](monitoring-integrations.md)).
- [ ] Runbook reviewed by an operator ([Handover](handover-of-an-integration.md)).
- [ ] The owners of the other systems know the date and are reachable on the day.
- [ ] Rollback plan written (below) and a backup procedure tested
      ([Backups: what to back up](../hosting/operations/backups-what-to-back-up.md)).

**Decision**: go / postpone.

## The day before

- [ ] Backup of database and `var/storage` taken and verified.
- [ ] Deployment rehearsed on staging with the same commands.
- [ ] A dry run of each import on a fresh copy of production: numbers plausible.
- [ ] Cronjob lines prepared but not yet active.

## Go-live

1. Deploy the extension and its settings:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/console exp:velocity deploy --allow-root-user
   ```

2. Create the extension's tables (its install command) and check them.
3. Check the surfaces from outside (one REST call with the production key, one `expservices` call, the feed address).
4. Run the **initial load** by hand, as the site user, with output kept:

   ```bash
   nice -n 15 php bin/php/console ext:myintegration:importproducts --file=var/import/articles.csv -s <siteaccess> \
       2>&1 | tee var/log/myintegration-initial-load.log
   ```

5. Clear what the load does not clear on its own ([Caching after imports](caching-after-imports.md#recipes)); rebuild
   the search index if indexing was delayed.
6. Connect workflows to triggers, switch listeners and sinks on.
7. Activate the cronjob lines; check them at **Setup > Cronjobs**.

**Decision**: continue / roll back.

## First runs

- [ ] The first scheduled run of each flow happened and its summary line is plausible.
- [ ] The second run of each import reports (almost) only `unchanged`.
- [ ] The other systems' teams confirm receipt of the first outbound changes.
- [ ] No new errors naming the integration in `error.log`; no refusals in the audit.
- [ ] Monitoring shows green; one alert was triggered on purpose and arrived.

## Rollback

Write down before go-live how to stop and reverse each flow:

| Step | How |
|---|---|
| Stop the flow | Pause its cronjob parts; disconnect its workflow at Setup > Triggers; remove the sink from the channel |
| Revoke access | Revoke the integration's API keys; disable the service account |
| Undo imported content | Remove or hide objects by their remote id prefix with a prepared, tested script; or restore the backup when nothing else changed since |
| Tell the others | The owners of the other systems and the editors |

## Hypercare

- [ ] Daily check of every flow's summary lines for the agreed period (two to four weeks).
- [ ] Thresholds tuned to real volumes.
- [ ] Defects fixed and released as new versions, not patched on the server.
- [ ] Open items handed over with owners at the end of hypercare.

## Related pages

- [Integration project playbook](integration-project-playbook.md)
- [Testing integrations](testing-integrations.md)
- [Quality gates and acceptance](../leadership/quality-gates-and-acceptance.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
