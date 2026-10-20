---
title: Reading release notes
audience: government, admin, project-manager
category: public-sector
order: 70
summary: Where Exponential's release notes, changelogs and behavior-change pages are, how to read them between your version and the target, and how to turn them into a risk assessment and test list for a change board.
keywords: release notes, changelog, behavior changes, upgrade notes, risk assessment, what changed, Added, Updated, Removed, Renamed, extensions changelog, change board
type: help
---
# Reading release notes

This page is for administrators and project managers who must assess an upgrade before a change board. It shows
where the notes are and how to read them efficiently.

## Contents
1. [Where the notes are](#where-the-notes-are)
2. [How to read them](#how-to-read-them)
3. [Turning notes into an assessment](#turning-notes-into-an-assessment)
4. [Related pages](#related-pages)

## Where the notes are

| Notes | Where | Covers |
|---|---|---|
| Changelog per version | `doc/changelogs/6.0/` ([6.0.15](../../../../../../changelogs/6.0/6.0.15.md), [6.0.14](../../../../../../changelogs/6.0/6.0.14.md) ...) | every change of the version; 6.0.15 is "Not released yet" while in development |
| Behavior changes by period | `doc/en/us/bc/6.0/`, for example [July and August 2026](../../../../bc/6.0/behaviour-changes-2026-07-08.md), [16 to 30 September 2026](../../../../bc/6.0/behaviour-changes-2026-09b.md), [1 to 2 October 2026](../../../../bc/6.0/behaviour-changes-2026-10.md) | what may break and what to do, with a checklist |
| The upgrading guide's table | [Upgrading, C3](../../../../guides/upgrading.md#c3-read-the-checklist-of-each-period-you-pass) | which notes to read for your starting version |
| Extensions | [Extension changelogs](../../../../../../changelogs/extensions/README.md) | each extension's releases |
| Security | [Security standard](../../../../standards/security.md), the changelog | security-relevant changes and known gaps |

Commit messages and changelog entries use the prefixes **Added**, **Updated**, **Removed** and **Renamed**
([Source control and releases](../../../../standards/source-control-and-releases.md)), which makes scanning easier.

## How to read them

1. Find your version and the target version.
2. Read the behavior-change page of **every period you pass**, starting with its quick checklist and "what to do"
   column; the upgrading guide's table lists them by starting version.
3. Scan the changelogs for **Removed** and **Renamed** first: they break things. Then **Updated** for defaults that
   changed (security headers, cookies, uploads, password checks).
4. For each extension you run, read its changelog between your version and the new one.
5. Note every setting the notes tell you to set or check.

## Turning notes into an assessment

| Column | Example |
|---|---|
| Change | "Accounts without an `ezuser_setting` row cannot sign in" |
| Affects us? | check with a query on staging; yes/no |
| Action | add the rows before the upgrade |
| Test | sign in with a migrated account on staging |
| Risk if missed | some staff cannot sign in |

The test column becomes your staging test list ([Staging and approval](staging-and-approval.md)); the risk column
feeds the change record ([Change management and advisory boards](change-management-and-advisory-boards.md)).

## Related pages
- [Upgrade overview for the public sector](upgrade-overview-for-public-sector.md)
- [Extension updates](extension-updates.md)
- [Immutable releases and versions](immutable-releases-and-versions.md)
- [Upgrading guide](../../../../guides/upgrading.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
