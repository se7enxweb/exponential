---
title: Restore test
audience: customer, businesses, hosting
category: customer-success
order: 7
summary: Why an owner should ask for a regular restore test of the site's backups, what a good test checks, how to keep the restored copy from mailing real people, and the record to keep.
keywords: restore test, restore drill, backup, recovery time, recovery point, evidence, test copy, mail transport
type: help
---
# Restore test

"A backup you have never restored is a hope" ([Operating a site](../../../guides/operating-a-site.md)). This page
is for the owner who wants proof instead of hope: what to ask for, what a good restore test checks, and what to
keep as a record. The step-by-step drill is in [Restore drills](../../businesses/reliability/restore-drills.md) and,
for hosts, in [Restore and restore tests](../../hosting/operations/restore-and-restore-tests.md).

## How often

The sources set a minimum: restore "on another machine at least once and after every major upgrade", and the
weekly checklist of the operating guide asks that a recent backup "has been restored at least once on another
machine" ([Restore drills](../../businesses/reliability/restore-drills.md)). The backup checklist for businesses asks
for a restore on another machine "in the last quarter"
([Backups that restore](../../businesses/reliability/backups-that-restore.md)). Choose a rhythm, put it in the calendar and name the person who reports the result to you.

## What to ask for

Ask the host or operator to restore a recent backup on a machine that is not public, and to send you:

| Item | Why |
|---|---|
| The date and the backup used | Shows which copy was tested |
| How long the restore took | Your real recovery time |
| The newest content found in the copy | Your real recovery point |
| The results of the checks below | Proof the copy works |
| Anything that went wrong | What to fix before a real emergency |

## Checks that prove a restore

From [Restore drills](../../businesses/reliability/restore-drills.md):

| Check | Good result |
|---|---|
| The front page, a deep page, an image, a download | They load, images included |
| Sign in to the administration | The dashboard opens |
| **Setup > System information** | The expected release; no failed health check |
| The cache status command | Ends in `PASS` |
| The audit trail verification | Exit status 0 |
| Publish a test item | It appears on the test copy |
| A form | It submits; the mail is written to a file, not sent |
| The newest content you expect | It is there |

For a shop, add: the newest order you expect is in the order list.

## Keep the copy quiet and private

A restored copy is a full site with real accounts and real e-mail addresses. The drill page names four rules
([Restore drills](../../businesses/reliability/restore-drills.md)):

1. Mail is written to files, not sent (`[MailSettings] Transport=file` on the test machine).
2. The crontab is not installed on the test machine, so no notifications or newsletters run.
3. The copy is kept off the public internet.
4. The copy is removed when the test is done, database and files included.

> **Warning**
> A forgotten test copy that sends a newsletter to all members, or stays online with real customer data, turns a
> test into an incident. Ask your host to confirm all four rules in the report.

## A record to keep

```text
Restore test record
Date:                     ____
Done by:                  ____
Backup used (date/time):  ____
Restore time:             ____ (start) to ____ (end)
Newest content found:     ____
Checks passed:            front page / deep page / image / sign-in / system information /
                          cache status / audit verify / publish / form / shop
Problems found:           ____
Actions agreed:           ____ (who, by when)
Test copy removed on:     ____
```

Keep the records together. They are evidence for an audit, an insurer or a customer who asks how you protect their
data ([Compliance evidence from the audit trail](../../businesses/security/compliance-evidence-from-the-audit-trail.md)).

## When a test fails

A failed test is a success of the process: you learned it on a quiet day. Agree the fix, a date and a new test.
Common findings, from the drill page: images missing because the files half was not backed up, errors about missing
tables because the backup is from another release, old content because caches were not cleared, and a restore that
takes far longer than expected because the site has grown
([Restore drills](../../businesses/reliability/restore-drills.md)). Fix the cause, update the written procedure, and
test again.

## Related pages

- [Backups](backups.md)
- [When the site is down](when-the-site-is-down.md)
- [Restore from backup (runbook)](../../hosting/runbooks/restore-from-backup.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
