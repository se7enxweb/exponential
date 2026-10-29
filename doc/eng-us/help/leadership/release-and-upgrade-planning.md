---
title: Release and upgrade planning
audience: project-manager, admin
category: leadership
order: 90
summary: How Exponential versions are numbered and published, what the project commits to, and how to plan, schedule and verify an upgrade of a site from any 6.0.x (or older) to the current line.
keywords: release, upgrade, version, versioning, tag, immutable, changelog, behavior changes, bc, upgrade check, maintenance mode, backup, PHP version, Packagist, planning, schedule, rollback
type: help
---
# Release and upgrade planning

This page is for project managers and administrators who must decide **when** a site moves to a new version of
Exponential, **what** that move involves, and **how** to know it worked. It explains how the project numbers and
publishes its releases, what it commits to, and turns the upgrade chapter of the installation book into a plan you
can put in a schedule: a decision table, a timeline, a checklist and a worked example.

The step-by-step commands are not repeated here in full. They live in
[11. Upgrading](../../install/11-upgrading.md) and in the [Upgrading guide](../../guides/upgrading.md); this page
tells you which parts to read and who does what.

## Contents

1. [How Exponential releases work](#how-exponential-releases-work)
2. [What the project commits to](#what-the-project-commits-to)
3. [Where to read what changed](#where-to-read-what-changed)
4. [Deciding when to upgrade](#deciding-when-to-upgrade)
5. [The upgrade plan, phase by phase](#the-upgrade-plan-phase-by-phase)
6. [Roles and handoffs during an upgrade](#roles-and-handoffs-during-an-upgrade)
7. [Upgrade checklist](#upgrade-checklist)
8. [Worked example: from 6.0.12 to the current line](#worked-example-from-6012-to-the-current-line)
9. [When something goes wrong](#when-something-goes-wrong)
10. [Releasing your own extensions](#releasing-your-own-extensions)

## How Exponential releases work

### The versions so far

Exponential 6.0.0 was released on 1 January 2024, and 6.0.14 on 7 June 2026. The 6.0.15 line is in development: the
[6.0.15 changelog](../../../changelogs/6.0/6.0.15.md) says "Not released yet" and lists what the line contains. The full
list of dates is on [Who is 7x?](../who-is-7x.md#the-60x-releases), taken from the release overview and the Git
tags.

Facts a planner needs from that table:

| Release | Why it matters for planning | Source |
|---|---|---|
| 6.0.7 | The last release for PHP 7.4 until 6.0.15 | [11. Upgrading](../../install/11-upgrading.md#step-3-read-the-checklist-of-each-period-you-pass) |
| 6.0.8 | Needs PHP 8.1 | same |
| 6.0.10 | The first release named Exponential: the product name in templates, translations and overrides | same, [The product is called Exponential](../../features/6.0/rebranding-to-exponential.md) |
| 6.0.13 | Security fixes and sort column checks | same |
| 6.0.14 | MongoDB as a database, and the upgrade file check (`share/filelist.md5`) | [6.0.14 changelog](../../../changelogs/6.0/6.0.14.md) |
| 6.0.15 line | Velocity, the admin4 design, the audit trail, Exponential Layouts, Exponential Intelligence, the help system and much more | [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md) |

> **Note**
> The sources do not publish a fixed release calendar. The 6.0.x tags came at intervals of about one to five months.
> The se7enxweb profile README, quoted on [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#the-foundation-and-7x),
> names "monthly releases" among the work that sponsoring 7x funds. Plan on the tags that exist, not on a date.

### The rules every release follows

From [Releases and version numbers](../who-is-the-exponential-foundation.md#releases-and-version-numbers) and
[SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md):

- **A published version never changes.** "A published tag is never changed: a fix for a problem in `v6.0.14` or an
  earlier tag reaches you as the next release, not as a corrected copy of the old one" (SECURITY.md).
- **The last position counts on.** After `0.1.9` comes `0.1.10`. Versions are sorted with
  `git tag -l 'v*' --sort=version:refname`, never by name, because by name `0.1.10` sorts before `0.1.8`
  ([Extensions, "Release it"](../../guides/extensions.md#7-release-it)).
- **Every package is on GitHub and Packagist.** The kernel is the Composer package `se7enxweb/exponential`, and
  every extension has its own repository and package ([Getting the code](../../install/03-getting-the-code.md)).
- **Extensions release on their own.** Each extension carries its version in `ezinfo.php` and `extension.xml`, and
  **Setup > Extensions** shows it ([Extensions](../../guides/extensions.md#7-release-it)).

What this means for your plan:

- A version you tested is exactly the version you deploy. Nobody can change it under you.
- A problem in a release is never "patched in place". Wait for, or ask for, the next version.
- Your project's own record of "which version runs where" can use version numbers with confidence.

## What the project commits to

The project does not publish one "support policy" document. The commitments are spread over several files and are
collected on [The support commitment](../who-is-the-exponential-foundation.md#the-support-commitment). In short:

| Topic | Commitment | Source |
|---|---|---|
| Maintained line | "Exponential 6.0 is the maintained line" | SECURITY.md |
| Security fixes | Made on `main` and published with the next 6.0.x release | SECURITY.md |
| Older lines | 5.x, 4.x, 3.x and older are not supported | SECURITY.md |
| Released 6.0.x tags | Fixed by upgrading to the newest 6.0.x | SECURITY.md |
| Security reports | Private; "You can expect a first answer within one business day" | SECURITY.md |
| PHP | 7.4 is the oldest supported version (from 6.0.15); 8.0 to 8.5 supported; CI tests 8.1 to 8.5 and checks that 8.0 boots | [PHP versions](../who-is-the-exponential-foundation.md#php-versions) |

Two planning consequences:

1. **Budget for staying current.** A site that stays on an old 6.0.x tag gets its security fixes only by moving to
   a newer 6.0.x. Treat "upgrade to the newest 6.0.x" as a recurring task, not a one-off project.
2. **Plan PHP with the code.** The PHP version of the server and the Exponential version move together. Check the
   PHP table before you promise a date, and ask your hosting provider early.

## Where to read what changed

| Document | Use it for | Where |
|---|---|---|
| Changelog per release | What a version holds, with an "Upgrading" section at the top | `doc/changelogs/6.0/` ([6.0.14](../../../changelogs/6.0/6.0.14.md), [6.0.15](../../../changelogs/6.0/6.0.15.md)) |
| Behavior change notes | What behaves differently and what to check, per period | `doc/eng-us/bc/6.0/`, for example [July and August 2026](../../bc/6.0/behaviour-changes-2026-07-08.md), [16 to 30 September 2026](../../bc/6.0/behaviour-changes-2026-09b.md), [1 and 2 October 2026](../../bc/6.0/behaviour-changes-2026-10.md) |
| Extensions' release notes | What an extension release changed | [Extensions' release notes](../../../changelogs/extensions/README.md) |
| The history | The story of every change since December 2023, by month | [The history](../../history/README.md) |
| The upgrade chapter | The table "From ... Read ..." that lists the notes of each period you pass | [11.6 step 3](../../install/11-upgrading.md#step-3-read-the-checklist-of-each-period-you-pass) |

> **Tip**
> Give the person who reads the release notes a fixed slot in the plan, before any estimate is made. The notes say
> which accounts may stop signing in, which settings changed their default and which extensions need attention. Those
> findings change the estimate.

## Deciding when to upgrade

Use this table to choose between "now", "next window" and "plan a project".

| Situation | Recommendation | Why |
|---|---|---|
| A security release exists for your line | Upgrade in the next possible window | Fixes reach you only through the next release (SECURITY.md) |
| You run 6.0.x and are one or two releases behind | Plan a routine upgrade | Part C of the [Upgrading guide](../../guides/upgrading.md) covers it |
| You run 6.0.7 or older on PHP 7.4 | Move to 6.0.15 on PHP 7.4, then plan the PHP move | 6.0.8 to 6.0.14 need PHP 8.1 |
| You run 5.4 or 5.90 | Plan a project | Part B, then Part C; database update files for each step |
| You run 3.10 up to 5.3 | Plan a migration project | Part A, then B, then C; read [14. Migrating from the 4.x line](../../install/14-migrating-from-4x.md) |
| You run your own patches in kernel files | Plan time for merging | Keep them on your own Git branch ([Develop with Git](../develop-with-git.md#keep-up-to-date)) |
| A feature you need is only in the development line | Decide whether to run `main` or wait for a tag | `main` moves every day; a tag never changes |

Questions to answer before you commit to a date:

- Which version runs today, in the code and in the database? ([11.2](../../install/11-upgrading.md#112-which-version-do-you-run))
- Which PHP version runs, and which does the target need?
- Which extensions are installed, and which of them are your own?
- Who can take the site offline, and when may it be offline?
- Where will the rehearsal run: a copy of the site on another machine?

## The upgrade plan, phase by phase

| Phase | What happens | Who | Done when | Read |
|---|---|---|---|---|
| 1. Inventory | Record the code version, database version, PHP, database engine, web server, extensions | Administrator | The inventory is written down | **Setup > System information**; [11.2](../../install/11-upgrading.md#112-which-version-do-you-run) |
| 2. Read | Read every changelog and behavior change note between your version and the target | Developer, administrator | A list of "things to check" exists | [11.6 step 3](../../install/11-upgrading.md#step-3-read-the-checklist-of-each-period-you-pass) |
| 3. Estimate | Turn the list into tasks, with owners | Project manager | Tasks have owners and an order | [Estimating and scoping with extensions](estimating-and-scoping-with-extensions.md) |
| 4. Before-check | Run the upgrade check on the live site and keep the report | Administrator | The report is saved | [The upgrade check](../../guides/upgrade-check.md) |
| 5. Rehearse | Upgrade a copy of the site, run every verification | Developer, administrator | The copy passes the checks | [11.6](../../install/11-upgrading.md#116-from-any-60x-to-today), [11.7](../../install/11-upgrading.md#117-verify-the-upgrade) |
| 6. Accept | Editors test the copy with their real tasks | Editors, project manager | Acceptance is signed off | [Quality gates and acceptance](quality-gates-and-acceptance.md) |
| 7. Announce | Tell editors and visitors about the window | Project manager | The window is announced | [Maintenance mode](../../features/6.0/maintenance-mode.md) |
| 8. Back up | Database and files, at the same time | Administrator | A backup exists **and** was restored once elsewhere | [11.1](../../install/11-upgrading.md#111-before-you-start) |
| 9. Upgrade | Maintenance mode on, code, database update, caches, reload, maintenance mode off | Administrator | Every step printed its expected result | [11.6](../../install/11-upgrading.md#116-from-any-60x-to-today) |
| 10. Verify | The checks of 11.7, the upgrade check again, editors' smoke test | Administrator, editors | The checks pass | [11.7](../../install/11-upgrading.md#117-verify-the-upgrade) |
| 11. Close | Record the new versions, file the lessons learned | Project manager | The handover document is updated | [Project templates](project-templates.md) |

### The one rule

**Never run an installer over an existing site.** The setup wizard, the kickstarter and `exp:install` create a new
database. An upgrade replaces the code and updates the existing database in place
([11.1](../../install/11-upgrading.md#111-before-you-start)). Put this rule into the briefing of everyone who
touches the server during the window.

### The window

What the window holds, in the order of [11.6](../../install/11-upgrading.md#116-from-any-60x-to-today):

1. Maintenance mode on: `php bin/php/maintenance.php on --message="Upgrade in progress" --until=30m`. Every page
   request is answered with the maintenance page (HTTP 503, never cached).
2. The new code: a checkout of the tag, then `composer install`.
3. The class maps and the caches: `php bin/php/ezpgenerateautoloads.php -e`, then `php bin/php/ezcache.php --clear-all`.
4. The PHP reload, and under Velocity `exp:velocity deploy`, which runs every step in the right order and stops at
   the first failure.
5. The database update file for the line, applied **once, whole, in order**, and the audit tables script.
6. The verification commands of 11.7.
7. Maintenance mode off.

The [Upgrading guide](../../guides/upgrading.md) says to "plan for twenty minutes for a small site", and that "the
risky step is the database". Your own window depends on your site; the rehearsal in phase 5 tells you how long it
really takes. Write that measured time into the plan, not a guess.

## Roles and handoffs during an upgrade

| Task | Project manager | Administrator | Developer | Editors |
|---|---|---|---|---|
| Inventory | Asks for it | Produces it | Adds own extensions | |
| Reading release notes | Schedules it | Reads server and settings notes | Reads code and template notes | |
| Rehearsal | Books the copy and the time | Runs it | Fixes own code | |
| Acceptance | Runs it, signs off | | Fixes what fails | Test their daily tasks |
| Announcement | Writes it | Sets maintenance mode | | Receive it |
| Window | Keeps the time and the go/no-go | Runs the steps | On call | Stop editing |
| Verification | Records the result | Runs the checks | Checks own features | Smoke test |

The person who signs in to the administration during the window needs the policies the pages ask for: the upgrade
check needs `setup/setup`, System information needs `setup/system_info`
([The upgrade check](../../guides/upgrade-check.md), [System information](../../guides/system-information.md)).

## Upgrade checklist

Copy this into your ticket or plan.

```text
Upgrade of <site name> from <current version> to <target version>
Window: <date, start, end>     Owner: <name of role>     Go/no-go by: <role>

Before
[ ] Code version and database version recorded (Setup > System information; 11.2)
[ ] PHP version of the server checked against the target
[ ] Extension list recorded (Setup > Extensions), own extensions marked
[ ] Every changelog and behavior change note between the versions read; list of checks written
[ ] Upgrade check run on the live site, report downloaded
[ ] Rehearsal on a copy done; time measured: <minutes>
[ ] Editors accepted the copy
[ ] Window announced to editors and, if needed, visitors
[ ] Backup of database and files taken, and restored once on another machine

During
[ ] Maintenance mode on, status checked
[ ] New code in place (tag <tag>), composer install finished
[ ] Autoloads regenerated, caches cleared
[ ] PHP reloaded / exp:velocity deploy passed
[ ] Database update applied once, whole, in order; audit tables script run
[ ] Version row in the database shows the target

After
[ ] ezcache --clear-all ends without an error
[ ] exp:checkdbfiles prints nothing
[ ] exp:checkclasses reports no class PHP refuses to load
[ ] exp:checkmanifest --all exit status 0 (or only files changed on purpose)
[ ] Front page loads, admin sign-in works, a test item can be published
[ ] Content cache and, under Velocity, the response cache cleared after the reload
[ ] Maintenance mode off
[ ] New versions recorded in the handover document
```

## Worked example: from 6.0.12 to the current line

A fictional site runs 6.0.12 on PHP 8.3 with MySQL and Apache with PHP-FPM. The project manager wants it on the
current line.

1. **Inventory.** System information shows 6.0.12. PHP 8.3 is inside the supported range, so no PHP move is needed.
2. **Read.** The table of [11.6 step 3](../../install/11-upgrading.md#step-3-read-the-checklist-of-each-period-you-pass)
   lists for this path: the 6.0.13 changelog (security fixes, sort column checks, with the hardening notes), the
   6.0.14 changelog (MongoDB, the upgrade file check), and for the 6.0.15 line its changelog and the behavior change
   notes of July and August, 16 to 30 September (with the security defaults) and 1 and 2 October 2026, and the notes
   on e-mail preferences.
3. **Findings that become tasks.** The upgrade chapter names the changes people meet most often: accounts without an
   `ezuser_setting` row cannot sign in; override blocks follow `Priority`; security headers, `HttpOnly`/`SameSite`
   session cookies and a stricter upload check are on by default; legacy extensions were brought to one standard;
   static cache defaults changed. Each becomes a check with an owner: "find accounts without a settings row"
   (administrator), "review override.ini priorities" (developer), "check that nothing frames the admin" (developer).
4. **The audit trail arrives.** From 6.0.15 it is on by default; the window includes
   `php update/common/scripts/6.0/createaudittables.php`, and the plan adds a task to schedule the `frequent`
   cronjob group so the audit is indexed ([The audit trail](../../features/6.0/audit-trail.md)).
5. **Rehearsal** on a copy, then acceptance by two editors, then the window.

## When something goes wrong

The upgrade chapter has a table of symptoms and fixes ([11.8](../../install/11-upgrading.md#118-if-something-goes-wrong)).
For the plan, decide in advance:

- **Who decides to roll back**, and by when. A rollback is "restore the backup" for the database and "check out the
  previous tag" for the code; it is only possible if the backup was tested.
- **An SQL file that failed half way**: restore the backup and apply the file again from the start (11.8).
- **Editors cannot sign in**: the `ezuser_setting` change of August 2026 (11.6, step 3).
- **A bug in Exponential itself**: report it as described in
  [When something goes wrong](../when-something-goes-wrong.md#filing-a-bug-in-exponential), and remember that the fix
  will come as the next version.

## Releasing your own extensions

If your project builds its own extensions, release them by the project's own rules
([Extensions, "Release it"](../../guides/extensions.md#7-release-it)):

1. Choose the number from what is already published (`git fetch --tags`, `git tag -l 'v*' --sort=version:refname`).
2. Write it into `ezinfo.php` and `extension.xml` (and `composer.json` if it has a version).
3. Refresh the extension's `share/filelist.md5` if it ships one.
4. Commit, then tag that commit. Never tag first.
5. Check that Packagist shows the new version.
6. If a published version is wrong, do not move its tag: publish the next one and say what was wrong.

More on teamwork around tags is in [Running a team on Git](running-a-team-on-git.md).

## Related pages

- [11. Upgrading](../../install/11-upgrading.md) and the [Upgrading guide](../../guides/upgrading.md)
- [The upgrade check](../../guides/upgrade-check.md) and [File consistency check](../../features/6.0/file-consistency-check.md)
- [Maintenance mode](../../features/6.0/maintenance-mode.md)
- [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#the-support-commitment): the support commitment
- [Quality gates and acceptance](quality-gates-and-acceptance.md), [Risk management for Exponential projects](risk-management.md)
- [Project templates](project-templates.md): handover document and launch checklist
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
