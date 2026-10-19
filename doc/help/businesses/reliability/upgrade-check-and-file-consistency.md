---
title: Upgrade check and file consistency
audience: businesses, admin, project-manager
category: business-solutions
order: 180
summary: What the upgrade check in the Exponential administration proves to a business, how the file and database consistency checks work, how to read their findings, and when to run them, before and after an upgrade and after an incident.
keywords: upgrade check, file consistency, database consistency, filelist.md5, checkmanifest, modified files, missing files, Setup Upgrade check, systemupgrade, verification, evidence
type: help
---
# Upgrade check and file consistency

This page is for managers who want proof that a site's files and database match the release they think it runs, and
for the administrator who runs the checks. It explains what the upgrade check compares, what each finding means for a
business, and when to run it. The full guide is [The upgrade check](../../../guides/upgrade-check.md); the manifest
behind it is described in [File consistency check](../../../features/6.0/file-consistency-check.md).

## Contents

1. [What it proves](#what-it-proves)
2. [The two checks](#the-two-checks)
3. [Reading the file findings](#reading-the-file-findings)
4. [Reading the database findings](#reading-the-database-findings)
5. [When to run it](#when-to-run-it)
6. [From a shell](#from-a-shell)
7. [Safety](#safety)
8. [Checklist](#checklist)

## What it proves

Every release of Exponential ships a list of checksums of its files, `share/filelist.md5`. The upgrade check compares
the installation with that list and with the schema of the release. For a business the result answers three
questions ([File consistency check, why you want it](../../../features/6.0/file-consistency-check.md#why-you-want-it)):

- **Before an upgrade**: has anyone changed release files by hand, which an upgrade would overwrite?
- **After an upgrade**: was anything left behind or edited by hand?
- **After an incident**: was a file modified without anyone noticing?

## The two checks

Both are on **Setup > Upgrade check** (`/setup/systemupgrade`), which needs the `setup/setup` policy
([The upgrade check](../../../guides/upgrade-check.md#1-open-the-page)):

| Check | Compares | Typical time on the demonstration server |
|---|---|---|
| **Check file consistency** | Every file in `share/filelist.md5`, and in the manifest of each active extension that carries one, with its checksum | About 8,600 files in about one second with a warm file cache |
| **Check database consistency** | The tables, fields and indexes of the database with the schema files of Exponential and the active extensions | About 0.1 seconds on SQLite |

Times are from the guide ([sections 3 and 6](../../../guides/upgrade-check.md#3-run-the-file-consistency-check)); a
larger site or a busy server takes longer. Each check shows its own duration.

"**Both checks only read.** No file is written and no SQL is run. The SQL the database check shows is text for you to
read" ([The upgrade check, in short](../../../guides/upgrade-check.md#in-short)).

## Reading the file findings

From [The upgrade check, each finding and what to do](../../../guides/upgrade-check.md#4-each-finding-and-what-to-do),
in business terms:

| Finding | What it means for the business | What to do |
|---|---|---|
| **Modified** | A release file differs from what was shipped. An upgrade will replace it, and the change will be lost. | If it was changed on purpose, have your developer move the change into an override, a design or an extension of your own. If nobody meant to change it, restore it from the release. |
| **Missing** | A release file is not there; something may fail. | Copy it back from the release. |
| **Unreadable** | The web server cannot read a file. | Fix owner and mode. |
| **Not listed**, **Malformed lines**, **Out of order** | Notes about the list itself. | Nothing on an installed site; they do not stop an upgrade. |

The result line is green ("File consistency check OK.") or amber ("Warning: it is not safe to upgrade without checking
the modifications done to the following files").

> **Tip**
> A list of "modified" files is also a map of where your site depends on hand changes. Each one is a future upgrade
> cost. Ask your developer to move them into your own extension or design over time.

## Reading the database findings

From [The upgrade check, database consistency](../../../guides/upgrade-check.md#6-run-the-database-consistency-check):

| Finding | What it means | What to do |
|---|---|---|
| **Missing table** | A feature that uses it fails until it is there | Run its CREATE statement, after a backup |
| **Table not in the schema** | Usually a table of your own extension, or of a removed one | Leave it unless you know its data is no longer needed |
| **Table of an extension that is not active** | Belongs to an installed, inactive extension | Leave it, or activate the extension |
| **Changed table** | Fields or indexes differ from the shipped definition | Read each statement; statements marked **Removes something** can remove data |
| **Engine notes** | Differences the engine writes no SQL for | Nothing; they do not fail the check |

The page never runs any SQL. Every statement is reviewed by a person and run, if at all, after a backup.

## When to run it

| Moment | Why |
|---|---|
| Before planning an upgrade | Hand changes become tasks in the plan ([Upgrades without surprises](upgrades-without-surprises.md)) |
| On the rehearsal copy, after the upgrade | Proves the rehearsal left nothing behind |
| On the live site, after the upgrade | The guide's verification step ([Upgrading, verify the upgrade](../../../guides/upgrading.md#verify-the-upgrade)) |
| After a security incident | Shows whether release files were modified ([Incident response for small teams](incident-response-for-small-teams.md)) |
| Once a quarter | A quick health check of the installation |

Download the report each time (**Download CSV** or **Download text** for the files, **Download SQL** for the database)
and keep it with the upgrade record. It is the evidence that the check was done and what it found.

## From a shell

The same file findings, from the installation root
([The upgrade check, from a shell](../../../guides/upgrade-check.md#7-the-same-from-a-shell)):

```bash
php bin/php/checkmanifest.php --all                 # exit 0 when nothing is modified or missing
php bin/php/checkmanifest.php --all --extensions    # also every extension with its own manifest
php bin/php/checkmanifest.php --all --extensions --csv > report.csv
```

The command needs no kernel, settings or database, and uses the same class as the page, so the findings agree. Its
exit status makes it usable in an automated check after every deploy.

## Safety

From [The upgrade check, safety](../../../guides/upgrade-check.md#safety):

- Both checks and all downloads only read; the page has no button that writes a file or changes the database.
- Each run and each download is written to the audit trail as `system.upgrade.run`, with the outcome and the number
  of differences ([Audit trail for operations](audit-trail-for-operations.md)).

## Checklist

- [ ] The upgrade check is run and its report saved before every upgrade.
- [ ] Every **Modified** file has an owner and a plan: keep, move into your own code, or restore.
- [ ] The check is run again after the upgrade, on the copy and on the live site.
- [ ] Database statements are only run after a backup, and after a person has read them.
- [ ] After a security incident, the file check is part of the investigation.

## Related pages

- [Upgrades without surprises](upgrades-without-surprises.md)
- [The upgrade check](../../../guides/upgrade-check.md)
- [File consistency check](../../../features/6.0/file-consistency-check.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
