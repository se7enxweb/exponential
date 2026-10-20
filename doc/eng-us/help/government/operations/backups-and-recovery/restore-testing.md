---
title: Restore testing
audience: government, hosting, admin, project-manager
category: public-sector
order: 80
summary: For public bodies that must prove their backups work. Why a backup that was never restored is a hope, how often to test, what a restore test checks, how to record the result as evidence for auditors and continuity plans, and the measured restore time as the basis of your recovery time.
keywords: restore test, backup test, recovery test, evidence, audit, continuity, recovery time, measured restore, test report, verification, integrity check
type: help
---
# Restore testing

This page is for operations teams, continuity planners and auditors in public bodies who must show that backups can
be restored. It answers how often to test, what a restore test checks, and how to record the result so it serves as
evidence. The procedure itself is on [Restoring to a test server](restoring-to-a-test-server.md); the hosting page is
[Restore and restore tests](../../../hosting/operations/restore-and-restore-tests.md).

## Contents

1. [Why test](#why-test)
2. [How often](#how-often)
3. [What a test checks](#what-a-test-checks)
4. [Recording the result](#recording-the-result)
5. [What usually goes wrong](#what-usually-goes-wrong)
6. [Checklist](#checklist)

## Why test

"A backup that was never restored is a hope, not a backup"
([10.9 Backups and restore](../../../../install/10-after-installing.md#109-backups-and-restore)). A job log that says
"success" proves only that something was written. A restore test proves the backup, the procedure and the time it
takes, and the time is what your recovery objective must be based on ([RPO and RTO as decisions](rpo-and-rto-as-decisions.md)).

## How often

The sources ask for a restore "on another machine at least once and after every major upgrade", and the weekly
checklist asks that a recent backup "has been restored at least once on another machine"
([Operating a site](../../../../guides/operating-a-site.md#10-a-weekly-checklist)). Beyond that, choose a schedule that
fits your continuity duties, for example:

| Occasion | Test |
|---|---|
| After setting up or changing the backup | full restore |
| After every major upgrade | full restore |
| On a fixed schedule you decide | full restore, rotating through engines and sites |
| After a change of database engine or storage | full restore |

## What a test checks

| Check | How | Good result |
|---|---|---|
| The site answers | `curl -s -o /dev/null -w '%{http_code}\n' https://<test host>/` | `200` |
| The administration | open `/user/login` of the administration, sign in | the dashboard |
| Version | **Setup > System information** or `./console exp:system:info` | the expected release, no failed health check |
| Caches | `php bin/php/cache.php status --allow-root-user` | ends in `PASS` |
| Audit trail | `./console exp:audit verify --allow-root-user` | exit status 0 |
| Content | open recent pages and images; publish a test item | images load, publishing works |
| SQLite | `sqlite3 <db file> "PRAGMA integrity_check"` | `ok` |
| Errors | `tail -n 50 var/log/error.log` | nothing unexplained |

From [Restore and restore tests](../../../hosting/operations/restore-and-restore-tests.md).

## Recording the result

Keep a short report per test; it is the evidence an auditor or continuity reviewer will ask for:

| Field | Example content |
|---|---|
| Date and tester | date; team name |
| Backup used | date and time of the backup, location |
| Target | test machine name |
| Steps | the procedure followed, with deviations |
| Duration | from start to "site answers and checks pass" |
| Result | each check above, pass or fail |
| Follow-up | fixes to the backup or procedure, with owners |

Store reports with the continuity plan. A growing duration over time is a signal to act before an incident.

## What usually goes wrong

- Files restored as root that the web server cannot write.
- A missing key file, so the audit trail cannot be verified.
- A SQLite file copied while in use instead of taken with `.backup`.
- A test copy that sends real mail or runs real cronjobs: see [Restoring to a test server](restoring-to-a-test-server.md).
- A backup taken from another release than the code on the test machine; restore onto the same release and
  `composer.lock`.

## Checklist

- [ ] A restore test is scheduled and owned.
- [ ] Each test produces a report with its duration and results.
- [ ] Failures lead to fixes with owners and a retest.
- [ ] The recovery time in your plans is the measured one.

## Related pages

- [Backups and recovery](README.md) index of this folder
- [Restore from backup](../../../hosting/runbooks/restore-from-backup.md) runbook
- [Disaster recovery plan](disaster-recovery-plan.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
