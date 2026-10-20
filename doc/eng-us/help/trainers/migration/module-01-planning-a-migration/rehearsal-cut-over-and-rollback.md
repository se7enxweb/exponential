---
title: "Lesson 1.4: Rehearsal, cut-over and rollback"
audience: trainer, admin, developer, project-manager
category: training
order: 14
summary: Learners plan rehearsals that measure the cut-over window, write a cut-over runbook with a freeze and a maintenance window, and define a rollback that means switching back rather than undoing. 45 minutes.
keywords: migrating to exponential, rehearsal, dress rehearsal, cut-over, freeze, maintenance mode, rollback, DNS, decision window, runbook
type: help
---
# Lesson 1.4: Rehearsal, cut-over and rollback

A migration succeeds or fails in its last hours. This lesson teaches the three habits the documentation repeats in
every chapter: rehearse until the run is boring, cut over from a runbook, and roll back by switching back, never by
undoing statements.

## At a glance

| | |
|---|---|
| Course | [Migrating to Exponential](../README.md) |
| Module | [Module 1: Planning a migration](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Administrators, developers and project managers |
| Prerequisites | [Lesson 1.3](phases-and-the-checklist.md); a training installation with shell access |

## Learning objectives

After this lesson, learners can:

- explain why a migration is rehearsed at least twice and what each rehearsal measures;
- write a cut-over runbook with freeze, final import, checks and the switch;
- put an Exponential installation into maintenance mode and take it out again;
- define rollback triggers, the person who decides, and the decision window.

## Trainer's notes

### How to present it

Sources: [checklist, sections 6, 8 and 9](../../../../install/migration/checklist.md),
[chapter 14.2](../../../../install/14-migrating-from-4x.md#142-plan-the-migration-on-a-staging-copy) and
[chapter 14.14](../../../../install/14-migrating-from-4x.md#1414-rollback-plan).

1. **Rehearse** (6 minutes). Build the new site next to the old one; restore a production copy; run the whole
   procedure; write down every command and how long it took; fix and repeat until it runs top to bottom without
   manual repair. The checklist asks for at least two rehearsals.
2. **Cut-over** (6 minutes). Section 8 of the checklist: announce the freeze, final export and import, counts and
   redirect tests, lower the DNS time to live the day before, switch, clear caches and warm pages, watch the logs and
   the 404 list, submit the sitemap.
3. **Maintenance mode** (3 minutes). Exponential takes a site offline with `php bin/php/maintenance.php`, which
   answers HTTP 503 (chapter 14.16). Show the guide [Maintenance mode](../../../../guides/maintenance-mode.md).
4. **Rollback** (5 minutes). Chapter 14.14: "A migration is rolled back by **not** switching, never by undoing the
   migrated database." Keep the old site and its database untouched; switching back is the same DNS or proxy
   operation reversed. Changes made on the new site after the switch are lost on a rollback, so keep the window
   short and editing frozen or logged.

### Common questions

**Can we re-run an update file to repair a half-done run?** No. Chapter 14.14: restore the dump taken before the run
and start the runbook again.

**Who decides on a rollback?** Decide in advance, with the triggers (checklist, section 9), for example a set share of
key pages broken or sign-in not working.

**How long do we keep the old site?** Until sign-off; chapter 16.9 says at least one full editorial cycle on the new
system.

### Pitfalls

- **Rehearsing on a different database engine.** The rehearsal measures the real run only if it uses the production
  engine and a realistic copy.
- **Clearing caches before the PHP reload.** A page rendered by old code between the clear and the reload is cached
  again; chapter 14.13 lists "stale pages after the switch".
- **No dumps kept.** Chapter 14.14 names three to keep: the old production dump, the migrated dump after the checks,
  and the `var/` archive.

## Hands-on exercise

Use your own training installation (installed with `php bin/php/console exp:install`) as the "new site". You will put
it into maintenance mode and back, as in a cut-over.

1. In the installation root, show the options of the maintenance script:

   ```bash
   php bin/php/maintenance.php --help
   ```

2. Open the front page of your training site in a browser and note what you see.
3. Switch maintenance mode on with a message for visitors, then check the state:

   ```bash
   php bin/php/maintenance.php on --message="Training: migration cut-over rehearsal" --until=30m
   php bin/php/console exp:maintenance status
   ```
4. Reload the front page. Note the page and, in the browser's developer tools, the HTTP status.
5. Switch maintenance mode off with `php bin/php/maintenance.php off` and reload the front page.
6. In your notes, write a cut-over runbook of ten numbered lines for the WordPress scenario of lesson 1.3, using
   section 8 of [the checklist](../../../../install/migration/checklist.md#8-cut-over). Put the maintenance step at
   the right place, on the **old** site, and name who does each line.
7. Add a rollback section: two triggers, who decides, and the decision window in days.
8. Add the three dumps of chapter 14.14 that you will keep, with where each is stored.

## Expected result

- Step 4: the site answered with the maintenance page and HTTP status 503.
- Step 5: the front page is back.
- The runbook has the freeze, the final export and import, counts and redirect tests, the DNS or proxy switch, cache
  clearing and warming, and watching logs and 404s.
- The rollback section names concrete triggers, one decider and a window.
- The trainer checks the maintenance step on one learner's screen and reads one runbook aloud.

## Quiz

1. How many rehearsals does the checklist ask for at least, and what do you measure in them?
2. A final run fails halfway through the database steps. What do you do?
   A. Run the failed update file again B. Restore the dump taken before the run and start again C. Edit the tables
   by hand D. Switch DNS anyway
3. Why lower the DNS time to live a day before the switch?
4. True or false: rollback means undoing the migrated database.
5. Which HTTP status does Exponential's maintenance mode answer with?

<details>
<summary>Answers</summary>

1. Two; the time of each run, from which the cut-over window follows, and the counts against the inventory
   ([checklist, section 6](../../../../install/migration/checklist.md#6-build-the-import-and-rehearse-it)).
2. **B** ([chapter 14.14](../../../../install/14-migrating-from-4x.md#1414-rollback-plan)).
3. So that the switch, and a switch back, reach visitors quickly
   ([checklist, section 8](../../../../install/migration/checklist.md#8-cut-over)).
4. **False.** Rollback is switching back to the old site, which was kept untouched (chapter 14.14).
5. 503 ([chapter 14.16](../../../../install/14-migrating-from-4x.md#1416-the-vendors-upgrade-pages-point-by-point)).

</details>

## Further reading

- [The common migration checklist](../../../../install/migration/checklist.md): rehearsal, cut-over and rollback.
- [Maintenance mode](../../../../guides/maintenance-mode.md): taking a site offline and back.
- [Chapter 16.9: Rollback](../../../../install/16-migrating-from-ez-platform-and-ibexa.md#169-rollback): the rollback
  for the platform paths.

## Related pages

- [Module 1: Planning a migration](README.md)
- [Migrating to Exponential: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
