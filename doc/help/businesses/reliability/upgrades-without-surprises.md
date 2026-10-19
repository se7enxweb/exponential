---
title: Upgrades without surprises
audience: businesses, project-manager, admin, developer
category: business-solutions
order: 170
summary: How a business plans, rehearses and runs an Exponential upgrade so that customers notice nothing but the improvements, with the phases, the roles, the tools that ship with the product and the decisions to make before the window opens.
keywords: upgrade, upgrades, update, release, rehearsal, maintenance window, rollback, ezupdate, update with Git, changelog, behavior changes, upgrade check
type: help
---
# Upgrades without surprises

This page is for owners and managers who approve upgrades, and for the project lead who plans them. It explains why
regular upgrades are a reliability measure, how an upgrade is planned and rehearsed, which tools ship with
Exponential to make it predictable, and what to decide before the window opens. The detailed plan, with a copyable
checklist, is [Release and upgrade planning](../../leadership/release-and-upgrade-planning.md); the technical steps
are in [Upgrading](../../../guides/upgrading.md).

## Contents

1. [Why upgrading is a reliability measure](#why-upgrading-is-a-reliability-measure)
2. [How often](#how-often)
3. [The phases of an upgrade](#the-phases-of-an-upgrade)
4. [The tools that make it predictable](#the-tools-that-make-it-predictable)
5. [Two ways to update: command line or administration](#two-ways-to-update-command-line-or-administration)
6. [Decide before the window](#decide-before-the-window)
7. [The one rule](#the-one-rule)
8. [Checklist for the business](#checklist-for-the-business)

## Why upgrading is a reliability measure

Security fixes reach you only through the next 6.0.x release: "A published tag is never changed: a fix for a problem
in `v6.0.14` or an earlier tag reaches you as the next release, not as a corrected copy of the old one"
(SECURITY.md). A site that does not upgrade slowly collects known problems, and its next
upgrade grows larger and riskier.

Small, regular upgrades are the opposite: each one is short, well understood and easy to rehearse. The planning page
says it as a rule: "Treat 'upgrade to the newest 6.0.x' as a recurring task, not a one-off project"
([Release and upgrade planning](../../leadership/release-and-upgrade-planning.md#what-the-project-commits-to)).

## How often

From [Release and upgrade planning, deciding when to upgrade](../../leadership/release-and-upgrade-planning.md#deciding-when-to-upgrade):

| Situation | Recommendation |
|---|---|
| A security release exists for your line | Upgrade in the next possible window |
| You run 6.0.x and are one or two releases behind | Plan a routine upgrade |
| You run 6.0.7 or older on PHP 7.4 | Plan the PHP move first; 6.0.8 needs PHP 8.1 |
| You run 5.x or older | Plan a project ([Upgrading](../../../guides/upgrading.md)) |
| You have your own changes in kernel files | Plan time for merging them |

Live sites should follow release tags; the development line `main` "moves every day; a tag never changes" (same
page). See [Release line and support policy](release-line-and-support-policy.md).

## The phases of an upgrade

The planning page lists eleven phases; in business terms they group into four
([Release and upgrade planning, phase by phase](../../leadership/release-and-upgrade-planning.md#the-upgrade-plan-phase-by-phase)):

| Group | Phases | What the business sees |
|---|---|---|
| Prepare | Inventory, read the release notes, estimate, run the upgrade check | A list of things to check, with owners and an estimate |
| Prove | Rehearse on a copy, editors accept the copy | A signed-off rehearsal and a **measured** duration |
| Do | Announce, back up, upgrade inside a maintenance window | A short window at a quiet time |
| Confirm | Verify, run the upgrade check again, record the new versions | A short report: what changed, what was checked |

The rehearsal is what removes surprises. "Your own window depends on your site; the rehearsal in phase 5 tells you how
long it really takes. Write that measured time into the plan, not a guess" (same page).

## The tools that make it predictable

| Tool | What it does for the business | Read |
|---|---|---|
| Changelogs and behavior change notes | Say what changed and what to check, per release and period | [Release line and support policy](release-line-and-support-policy.md#where-to-read-what-changed) |
| **Setup > System information** | Records the versions before and after; warns about PHP and settings | [System information](../../../guides/system-information.md) |
| **Setup > Upgrade check** | Compares files and database with the release, only reading | [Upgrade check and file consistency](upgrade-check-and-file-consistency.md) |
| Maintenance mode | A clean 503 page while the work happens | [Maintenance mode and planned work](maintenance-mode-and-planned-work.md) |
| `exp:velocity deploy` | Runs the code reload and cache steps in the right order, stops at the first failure | [Graceful deploys](graceful-deploys.md) |
| The audit trail | Records the maintenance window, deploys and settings changes | [Audit trail for operations](audit-trail-for-operations.md) |
| `ezupdate` | Checks for updates and runs Composer from the administration, with a dry run | [Automated updates with ezupdate](../../hosting/operations/automated-updates-with-ezupdate.md) |

## Two ways to update: command line or administration

A site made of Git clones can be updated on the command line or from the administration
([Update with Git](../../update-with-git.md#command-line-or-administration)):

| | Command line | Administration |
|---|---|---|
| For | Administrators with a shell on the server | Administrators without a shell |
| Kernel | `git merge` of the newest release tag, or another branch | **Git Manager** pulls a branch; it cannot merge a release tag |
| Extensions | `composer update` | **Updates and packages** runs Composer in the background, with **Preview** first; off until `[UpdateSettings] AllowUpdate` is switched on |
| Code reload | `exp:velocity deploy` on a Velocity site | Cache management; it cannot restart Velocity |

The page's summary: the command line "can do everything", while the administration "covers a routine update of a site
that follows a branch ... without opening a shell, but a release tag, a merge conflict and the Velocity restart still
need the command line".

About automation: `ezupdate` "has no schedule of its own that updates a site unattended, and the shipped switches keep
updating and installing off until an administrator turns them on"
([Automated updates with ezupdate](../../hosting/operations/automated-updates-with-ezupdate.md#what-it-does)). The
update-with-Git page describes a self-updating script for sites that choose it
([Let it update itself](../../update-with-git.md#let-it-update-itself)); for a live business site, a rehearsed
upgrade in a window is the safer default.

## Decide before the window

- **Go or no-go**: who decides, and by when, that the window goes ahead.
- **Rollback**: who decides to roll back, and by when. "A rollback is 'restore the backup' for the database and 'check
  out the previous tag' for the code; it is only possible if the backup was tested"
  ([Release and upgrade planning, when something goes wrong](../../leadership/release-and-upgrade-planning.md#when-something-goes-wrong)).
- **Communication**: who tells customers the site is back, or that the window is extended.
- **Editing freeze**: editors stop publishing during the window and know when they may start again.
- **On call**: who is reachable for the first day after the upgrade.

## The one rule

"**Never run an installer over an existing site.** The setup wizard, the kickstarter and `exp:install` create a new
database. An upgrade replaces the code and updates the existing database in place"
([Release and upgrade planning, the one rule](../../leadership/release-and-upgrade-planning.md#the-one-rule)). Put
this rule into the briefing of everyone who touches the server.

## Checklist for the business

- [ ] Upgrading to the newest 6.0.x is a recurring item in the calendar.
- [ ] Each upgrade has an owner, a rehearsal on a copy and a measured duration.
- [ ] Editors accepted the rehearsed copy.
- [ ] The window is at a quiet time, announced, and covered by a fresh, tested backup.
- [ ] Go/no-go and rollback decisions have named owners.
- [ ] The upgrade check was run before and after, and the reports are kept.
- [ ] The new versions are recorded in the handover document.

The full technical checklist to copy into a ticket is on
[Release and upgrade planning, upgrade checklist](../../leadership/release-and-upgrade-planning.md#upgrade-checklist).

## Related pages

- [Upgrade check and file consistency](upgrade-check-and-file-consistency.md)
- [Upgrading](../../../guides/upgrading.md)
- Budgeting for upgrades and maintenance
- [Upgrading customer sites (hosting)](../../hosting/operations/upgrading-customer-sites.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
