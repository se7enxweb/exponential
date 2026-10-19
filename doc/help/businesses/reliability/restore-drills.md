---
title: Restore drills
audience: businesses, admin, hosting, project-manager
category: business-solutions
order: 80
summary: How to prove that your backups work by restoring one on another machine on a schedule, what to check, how to keep the copy from mailing real customers, and how to record the result as evidence.
keywords: restore drill, restore test, recovery time, backup test, disaster recovery, rehearsal, evidence, test machine, mail transport file
type: help
---
# Restore drills

This page is for managers who need evidence that the business can recover from a lost server, and for the operator
who runs the drill. A restore drill is a planned restore of a real backup onto another machine, timed and checked.
It turns "we have backups" into "we restored one last month in this many minutes". The technical procedure is
[Restore and restore tests](../../hosting/operations/restore-and-restore-tests.md).

## Contents

1. [Why drill](#why-drill)
2. [How often](#how-often)
3. [Before the drill](#before-the-drill)
4. [The drill, step by step](#the-drill-step-by-step)
5. [Checks that prove the restore](#checks-that-prove-the-restore)
6. [Keep the copy quiet and private](#keep-the-copy-quiet-and-private)
7. [Record the result](#record-the-result)
8. [What a failed drill teaches](#what-a-failed-drill-teaches)

## Why drill

The guides are blunt: "a backup you have never restored is a hope"
([Operating a site](../../../guides/operating-a-site.md#6-back-up-and-restore)). A drill proves three things at once:

- **The backup is complete**: the database and the files are both there, from the same moment.
- **The procedure works**: the people on duty can follow it, and the instructions are current.
- **The recovery time is known**: "Write down how long it took; that is your recovery time"
  ([Backups and restores](../../creators/website-creators/backups-and-restores.md#the-restore-test)).

Without the last point, any promise of how fast you recover is a guess.

## How often

The sources set a minimum: restore "on another machine at least once and after every major upgrade"
([Restore and restore tests](../../hosting/operations/restore-and-restore-tests.md#a-restore-test-on-another-machine)),
and the weekly checklist asks that a recent backup "has been restored at least once on another machine"
([Operating a site, a weekly checklist](../../../guides/operating-a-site.md#10-a-weekly-checklist)). The hosting
section's monthly checklist restores one site's backup per month, in rotation
([Monthly operations](../../hosting/checklists/monthly-operations.md#backups)).

Choose your own rhythm above that minimum, and put it in the calendar with a named owner.

## Before the drill

- [ ] A test machine (or a temporary server) is available that is **not** reachable from the public internet.
- [ ] It runs the same Exponential release and the same `composer.lock` as the backup
      ([Restore and restore tests](../../hosting/operations/restore-and-restore-tests.md#before-you-restore)).
- [ ] The person running the drill is not always the same one: rotate it, so the knowledge is shared.
- [ ] A stopwatch, or a note of start and end times.
- [ ] The restore procedure is printed or open, and you follow it exactly as written.

## The drill, step by step

The steps match the restore procedure of the installation book
([Restore and restore tests](../../hosting/operations/restore-and-restore-tests.md#the-restore-step-by-step)):

1. Note the start time.
2. Fetch the backup from where it is stored, as you would after losing the server.
3. Switch maintenance on for the test copy (`php bin/php/maintenance.php on --message="Restoring" --allow-root-user`);
   under Velocity also stop the server.
4. Restore the database with its engine's tool and unpack the files.
5. Fix the owner and mode of the unpacked files.
6. Clear every cache and regenerate the autoloads:
   `php bin/php/ezcache.php --clear-all --allow-root-user` and `php bin/php/ezpgenerateautoloads.php -e`.
7. Under Velocity start the server and clear its response cache; switch maintenance off.
8. Run the checks below. Note the end time.

## Checks that prove the restore

From [Restore and restore tests, checks after a restore](../../hosting/operations/restore-and-restore-tests.md#checks-after-a-restore)
and [Backups and restores](../../creators/website-creators/backups-and-restores.md#the-restore-test):

| Check | Good result |
|---|---|
| The front page, a deep page, an image, a download | They load, images included |
| Sign in to the administration | The dashboard opens |
| **Setup > System information** | The expected release; no failed health check |
| `php bin/php/cache.php status --allow-root-user` | Ends in `PASS` |
| `php bin/php/audit.php verify --allow-root-user` | Exit status 0 |
| Publish a test item | It appears on the test site |
| A form (contact, sign-up) | It submits; the mail goes to the file transport, not a real address |
| The newest content you expect | It is there; tells you the real recovery point |
| `tail -n 50 var/log/error.log` | Nothing you cannot explain |

For a shop, add: the newest order you expect is in the order list
([Stores](../stores/README.md)).

## Keep the copy quiet and private

A restored copy is a full site with real accounts and real e-mail addresses. Before you start it
([Restore and restore tests](../../hosting/operations/restore-and-restore-tests.md#a-restore-test-on-another-machine)):

1. **Stop mail**: set `[MailSettings] Transport=file` in the test machine's
   `settings/override/site.ini.append.php`, so every mail is written to a file instead of being sent.
2. **Do not install the crontab** on the test machine, or notifications, newsletters and content jobs run against
   real addresses.
3. **Restrict access**: the copy holds personal data; keep it off the public internet.
4. **Remove the copy** when the drill is done, including its database and files.

> **Warning**
> A forgotten test copy that sends a newsletter to all members, or that stays online with real customer data, turns
> a drill into an incident. Steps 1 to 4 are not optional.

## Record the result

"Record each test in one line: date, site, backup used, time to restore, result, who ran it"
([Restore and restore tests](../../hosting/operations/restore-and-restore-tests.md#a-restore-test-on-another-machine)).
A table like this is enough:

| Date | Backup used | Who | Minutes to restore | Newest content found | Result | Follow-up |
|---|---|---|---|---|---|---|
| (fill in) | | | | | | |

This line is the evidence a manager, an auditor or a customer asks for. Keep the history; a rising restore time is
an early warning that the site has grown and the plan needs to change.

## What a failed drill teaches

A drill that fails is a success: you found the problem on a quiet day. Common findings
([Restore and restore tests, what goes wrong](../../hosting/operations/restore-and-restore-tests.md#what-goes-wrong)):

| Finding | Usual cause |
|---|---|
| Images missing, pages fine | The files half was not backed up, or is from another moment |
| Errors about missing tables or columns | The backup is from another release; apply the upgrade steps |
| The site shows old content | Caches were not cleared after the restore |
| `Class ... not found` | Autoloads or `vendor/` do not match |
| The web server cannot write `var/` | Owner or mode wrong after unpacking |
| The restore takes far longer than expected | The site has grown; review your recovery time promise |

Fix the cause, update the written procedure, and run the drill again.

## Related pages

- [Backups that restore](backups-that-restore.md)
- [Incident response for small teams](incident-response-for-small-teams.md)
- [Runbook: restore from backup](../../hosting/runbooks/restore-from-backup.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
