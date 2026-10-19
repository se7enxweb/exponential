---
title: Change management and advisory boards
audience: government, admin, project-manager, hosting
category: public-sector
order: 40
summary: How to run Exponential under a public body's change management - which changes go to a change advisory board, which are standard or routine, what evidence the platform gives for each (dry runs, upgrade check, audit trail), and a change record template.
keywords: change management, change advisory board, CAB, ITIL, change request, standard change, normal change, emergency change, change record, approval, evidence, dry run, audit trail, deploy
type: help
---
# Change management and advisory boards

This page is for change managers, service owners and administrators in public bodies that run a change advisory
board (CAB) or a similar approval step. It sorts the changes an Exponential site sees into kinds, says which usually
need the board, and shows the evidence the platform produces for each. Change processes differ between
organizations; adapt the categories to yours.

## Contents
1. [Three kinds of change](#three-kinds-of-change)
2. [Which change is which](#which-change-is-which)
3. [Evidence the platform gives](#evidence-the-platform-gives)
4. [A change record template](#a-change-record-template)
5. [Emergency changes](#emergency-changes)
6. [Related pages](#related-pages)

## Three kinds of change

| Kind | Meaning in many change processes | Approval |
|---|---|---|
| Standard | pre-approved, low risk, repeated by a written procedure | none per change; the procedure was approved once |
| Normal | assessed and approved by the board | the board |
| Emergency | needed at once to restore service or close a hole | an emergency approver, reviewed afterwards |

## Which change is which

| Change on an Exponential site | Usual kind | Why |
|---|---|---|
| Publishing content | not a change; governed by the editorial workflow | [Approval workflows overview](../editorial-workflows/approval-workflows-overview.md) |
| Clearing caches, running a cronjob part, rotating logs | standard | reversible, documented ([Operations tasks](../../tasks/operations/README.md)) |
| Adding a user, assigning an existing role to a group | standard (by your joiners procedure) | recorded in the audit trail |
| A new role, a new section, a changed workflow | normal | changes who may do what |
| A settings change (INI) | normal | changes behavior; with [ManagedByDeployment](managed-by-deployment.md) it comes through version control |
| A template or design change | normal | changes every page |
| A new or updated extension | normal | new code ([Extension updates](extension-updates.md)) |
| An upgrade of Exponential | normal, often major | database update, new code ([Upgrade overview](upgrade-overview-for-public-sector.md)) |
| PHP or database server upgrade | normal | [PHP and database upgrades](php-and-database-upgrades.md) |
| A security fix release | normal, fast track or emergency | [Security updates and disclosure](security-updates-and-disclosure.md) |
| Maintenance mode for a planned window | part of the approved change | [Maintenance windows](maintenance-windows.md) |

## Evidence the platform gives

| Evidence | Command or page | Use in the change |
|---|---|---|
| What a deploy will do | `./console exp:velocity deploy --dry-run --allow-root-user` (fourteen steps, each marked DRY) | attach to the request |
| What a cache clear will do | `php bin/php/cache.php clear --all --dry-run --allow-root-user` | standard procedure |
| What an update will install | **Setup > Updates and packages**, **Preview (dry run)** (ezupdate) | attach to the request |
| Code differs from the release | **Setup > Upgrade check**, **Check file consistency**; `php bin/php/checkmanifest.php --all` | before and after |
| Database schema differs | **Check database consistency** on the same page | before and after |
| Who did it and when | the audit trail's `system` channel (settings writes, cache clears, deploys, maintenance) | closing the change |
| The state of the server | **Setup > System information**, **Download report (.txt)** | before and after |

The upgrade check pages are described in [The upgrade check](../../../../guides/upgrade-check.md); runs are recorded
as `system.upgrade.run` in the audit trail.

## A change record template

| Field | Example (placeholders) |
|---|---|
| Summary | "Upgrade Exponential from 6.0.14 to the next 6.0.x release on www.example.gov" |
| Reason | security fixes listed in the changelog |
| Kind and risk | normal; medium (database update) |
| Environments | tested on test.example.gov on <date>, result attached |
| Plan | maintenance window, backup, steps, checks ([Upgrade overview](upgrade-overview-for-public-sector.md)) |
| Rollback | restore the backup taken in step 1 ([Rollback planning](rollback-planning.md)) |
| Evidence before | upgrade check, system information report, dry run output |
| Evidence after | the same, plus the audit trail entries of the window |
| Communication | notice to the public and to editors ([Maintenance windows](maintenance-windows.md)) |

## Emergency changes

An emergency change still leaves a record: the audit trail captures settings writes, cache clears, maintenance and
deploys with their time and user, so the after-the-fact review can rely on it. List what happened in the window:

```bash
./console exp:audit search --channel=system --from='<start of the window>' --to='<end>' --limit=100 --allow-root-user
```

The filters of `search` are listed in [the audit guide, section 3.3](../../../../bc/6.0/audit.md#33-the-command-expaudit).

## Related pages
- [Staging and approval](staging-and-approval.md)
- [Configuration managed by deployment](managed-by-deployment.md)
- [Separation of duties](../people-and-permissions/separation-of-duties.md)
- [Deploying changes (hosting)](../../../hosting/operations/deploying-changes.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
