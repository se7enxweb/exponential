---
title: Error digest and log health
audience: government, admin, developer
category: public-sector
order: 40
summary: The two summary pages of the log viewer - the error digest that groups and counts the last day's distinct errors per log, and log health with disk use, growth, write permissions and rotation settings - and how to use them in a daily check.
keywords: error digest, log health, Setup Log files, distinct errors, Mark as known, disk budget, log growth, writable logs, rotation settings, daily check, logsdigest, logshealth
type: help
---
# Error digest and log health

This page is for administrators who check a public website's logs every day without reading them line by line. The
log viewer has two summary pages for that: **Error digest** (`/setup/logsdigest`) and **Log health**
(`/setup/logshealth`), both under **Setup > Log files** and both needing `setup/logs`
([Log viewer](../../../../features/6.0/log-viewer.md#error-digest)).

## Contents
1. [The error digest](#the-error-digest)
2. [Working with the digest](#working-with-the-digest)
3. [Log health](#log-health)
4. [A daily five minutes](#a-daily-five-minutes)
5. [Related pages](#related-pages)

## The error digest

The digest lists the distinct errors and warnings of the last 24 hours (`[DigestSettings] Hours`) of each kernel log:
each message masked, then normalized (numbers, ids, quoted values, paths and `ERR-` references set aside), grouped and
counted, most frequent first, at most 50 per log, with the most severe level and when it was last seen. Each group
links to the log filtered to it.

- `/setup/logsdigest/<key>` shows one log; `/(hours)/N` another window (1 to 168 hours).
- The request logs are grouped their own way: `404.log` by reason, `security.log` by category and rule, `csp.log` by
  directive and blocked source. The digest on the demonstration server lists, among others, "Not found requests
  (404)", "Security (request classifier)" and "Content Security Policy reports".

## Working with the digest

- **Mark as known** hides a group you have looked at and accepted (a preference of your own, at most 200 groups);
  **Show known messages** lists them again. Use it so new problems stand out.
- A group that is new today is the first thing to look at; a group whose count jumps is the second.
- Open the group's link, then a line, to see the whole request and the audit records of the same minute.
- Put recurring errors into your ticket system with the permalink; developers fix causes, operators should not
  "mark as known" a real fault forever.

## Log health

**Log health** shows at a glance:

- disk used by all logs and archives against `[HealthSettings] DiskBudget` (256 MB by default; a warning from 80 %,
  an alert above it);
- the fastest growing logs and the logs over their size limit;
- logs the web server's user cannot write, and a warning when logs have mixed owners (for example root from the
  command line and the web server's user), which stops rotation and new lines;
- the newest error of each log, linked;
- the rotation settings in effect per log, with the INI file, section and setting each comes from.

## A daily five minutes

1. **Error digest**, default window: anything new or growing?
2. **Log health**: any red? any unwritable log?
3. **Audit** tab, **Alerts**: anything fired since yesterday?
4. Note findings in your operations log or ticket system.

Procedures: [Read the error digest](../../tasks/operations/read-the-error-digest.md) and
[Check log health](../../tasks/operations/check-log-health.md).

## Related pages
- [The log viewer](the-log-viewer.md)
- [Log retention and rotation](log-retention-and-rotation.md)
- [Request logs: 404 and security](request-logs-404-and-security.md)
- [Disk full](../incidents/disk-full.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
