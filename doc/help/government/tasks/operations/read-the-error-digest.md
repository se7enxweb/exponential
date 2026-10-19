---
title: Read the error digest
audience: government, admin, developer
category: public-sector
order: 130
summary: Step by step - the daily five-minute look at Setup > Log files > Error digest - what is new, what grows, how to open a group, when to mark it as known and how to hand a real fault to developers.
keywords: error digest, daily check, logs, Setup Log files, distinct errors, Mark as known, permalink, developers, ticket, logsdigest
type: help
---
# Read the error digest

This task is for administrators doing the daily log check. At the end you know whether anything new is wrong. The
digest groups and counts the distinct errors and warnings of the last 24 hours of each kernel log
([Error digest and log health](../../operations/monitoring/error-digest-and-log-health.md)).

## Contents
1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Deciding what a group means](#deciding-what-a-group-means)
4. [Related pages](#related-pages)

## Before you start

- `setup/logs` (see [Give log access without admin rights](../people-and-permissions/give-log-access-without-admin-rights.md)).

## Steps

1. **Setup > Log files**, then **Error digest** (`/setup/logsdigest`).
2. Read each log's table, most frequent first: the normalized message, count, most severe level, last seen.
3. Groups you accepted earlier are hidden; **Show known messages** lists them.
4. For a group you do not recognize, open its link: the log filtered to it. Open a line to see the whole request and
   the audit records of the same minute.
5. For another window: `/setup/logsdigest/(hours)/72` (1 to 168 hours); for one log: `/setup/logsdigest/error`.

## Deciding what a group means

| Group | Action |
|---|---|
| New since yesterday, from a deploy | check with the developer of the change; [Broken deploy rollback](../../operations/incidents/broken-deploy-rollback.md) if it hurts visitors |
| A known harmless message (an old extension's notice) | **Mark as known** and record why |
| Growing quickly | find the cause before the log fills the disk |
| "Not found requests (404)" grouped by reason | broken links: fix links or add URL aliases |
| "Security (request classifier)" | [Review the security log weekly](review-the-security-log-weekly.md); now, if injections got a 200 |

For a developer ticket, use **Copy permalink** on the filtered view: the link reproduces the filter.

## Related pages
- [Check log health](check-log-health.md)
- [Find an error by its reference](find-an-error-by-its-reference.md)
- [The log viewer](../../operations/monitoring/the-log-viewer.md)
- [Log viewer feature](../../../../features/6.0/log-viewer.md#error-digest)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
