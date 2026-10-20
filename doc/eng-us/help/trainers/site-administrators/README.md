---
title: "Site administration: course overview"
audience: trainer, admin
category: training
order: 1
summary: The course for people who run an Exponential site, the Setup area and settings, users, roles and policies, sections, siteaccesses and states, caches, cronjobs and workflows, logs and the audit trail, security, backups and upgrades. Ten modules, 48 lessons, about 30 hours of lessons.
keywords: site administration, course overview, reading path, administrator, users, roles, policies, sections, siteaccesses, caches, cronjobs, logs, audit trail, security, backups, upgrades, packages
type: help
---
# Site administration: course overview

This course is for the people who run an Exponential site day to day: they create accounts and decide who may do
what, keep the settings, caches and cronjobs in order, read the logs and the audit trail when something goes wrong,
keep the site secure, back it up and upgrade it. It is taught in the administration (design admin4) and on the
command line of each learner's own training installation.

## Who it is for

- Site and intranet administrators.
- Webmasters and team leads who take over a site from an agency.
- Hosting support staff, as the first half of their path before Hosting and operations.

**Outcome:** after the course, a learner can run an Exponential site on their own: manage users, roles and
sections, find and change a setting, clear the right cache, keep cronjobs and workflows running, investigate a
problem with the logs and the audit trail, apply the security settings, back up and restore the site, and plan and
check an upgrade.

## Format and duration

| | |
|---|---|
| Duration | 48 lessons, about 30 hours of lessons (five days with breaks) |
| Format | Classroom or online; hands-on in the browser and on a terminal |
| Level | Intermediate |
| Prerequisites | Editors 101 (or the same experience as an editor); basic command line |
| Lab | One training installation per learner, installed with `php bin/php/console exp:install` (see [Installing from the command line](../../../install/07-console-install.md)) and served with `exp:velocity start`; the learner needs the administrator account of their own installation and a terminal on its machine |

## Modules

| Module | Lessons | Duration | Outcome |
|---|---|---|---|
| [1. Getting oriented as a site administrator](module-01-orientation/README.md) | 5 | 2 h 55 min | Knows the job, the Setup area, System information, the settings page and exp:ini |
| [2. Users and groups](module-02-users-and-groups/README.md) | 5 | 3 h 10 min | Creates and manages accounts, resets passwords, checks accounts, changes owners |
| [3. Roles and policies](module-03-roles-and-policies/README.md) | 5 | 3 h 15 min | Builds, assigns and tests roles; controls anonymous access and registration |
| [4. Sections, siteaccesses and object states](module-04-sections-siteaccesses-and-states/README.md) | 4 | 2 h 50 min | Uses sections and object states, reads siteaccesses, manages content languages |
| [5. Caches](module-05-caches/README.md) | 5 | 2 h 55 min | Names and clears the right cache in the administration and on the command line, preloads caches |
| [6. Cronjobs, workflows and notifications](module-06-cronjobs-workflows-and-notifications/README.md) | 5 | 3 h 30 min | Runs and schedules cronjobs, builds an approval workflow, runs notifications safely |
| [7. Logs and the audit trail](module-07-logs-and-the-audit-trail/README.md) | 5 | 3 h 15 min | Reads the logs and the log viewer, follows an error reference, investigates with the audit trail |
| [8. Security](module-08-security/README.md) | 5 | 3 h 20 min | Hardens a site, sets sign-in policy and two-step sign-in, sessions and a Content Security Policy |
| [9. Backups and maintenance](module-09-backups-and-maintenance/README.md) | 4 | 2 h 55 min | Backs up and restores a site, uses maintenance mode, checks files and storage |
| [10. Upgrades, extensions and packages](module-10-upgrades-extensions-and-packages/README.md) | 5 | 3 h 20 min | Plans and checks an upgrade, uses ezupdate and git_manager, the Extensions page and packages |

## Reading path

For a trainer preparing to give this course, in this order:

1. [Trainers: start here](../trainers-start-here.md) and the [Course catalogue](../course-catalogue.md).
2. [The administration tour](../../admin-tour.md) and [Operating a site](../../../guides/operating-a-site.md).
3. [Installing from the command line](../../../install/07-console-install.md) and
   [After installing](../../../install/10-after-installing.md): the lab.
4. [The settings page](../../../guides/settings-page.md), [System information](../../../guides/system-information.md)
   and [The exp:ini command](../../../features/6.0/exp-ini-command.md).
5. [Roles, policies and unactivated users](../../../guides/roles-and-policies.md) and
   [Sections](../../../guides/sections.md).
6. [Caches](../../../guides/caches.md) and [Cronjobs](../../../guides/cronjobs.md).
7. [The log viewer](../../../features/6.0/log-viewer.md) and [The audit trail](../../../features/6.0/audit-trail.md).
8. [Security hardening](../../security-hardening.md), [Two-factor login](../../../guides/two-factor-login.md) and
   [Content Security Policy](../../../features/6.0/content-security-policy.md).
9. [Maintenance mode](../../../guides/maintenance-mode.md) and [Upgrading](../../../guides/upgrading.md).
10. Modules 1 to 10 of this course, in order. Rehearse every exercise on an installation made exactly like the
    learners'; several exercises change settings or restore the site, and each lesson says how to set it back.

## Suggested schedules

| Schedule | Plan |
|---|---|
| Five days | Day 1: Modules 1 and 2. Day 2: Modules 3 and 4. Day 3: Modules 5 and 6. Day 4: Modules 7 and 8. Day 5: Modules 9 and 10 |
| Four days, intensive | Day 1: Modules 1, 2 and lessons 3.1 to 3.2. Day 2: lessons 3.3 to 3.5, Modules 4 and 5. Day 3: Modules 6, 7 and lessons 8.1 to 8.2. Day 4: lessons 8.3 to 8.5, Modules 9 and 10 (shorten the exercises of 9.4 and 10.4 to demonstrations) |
| Hosting support staff | Modules 1, 5, 6, 7, 9 and 10 (about 19 hours, three days), then Hosting and operations |
| Ten half days | One module per half day, in order |
| Self-study | One module per week, with the exercises done on your own installation |

## Assessing learners

Every lesson has a quiz and an expected result the trainer checks. As a final practical task, each learner gets a
short brief on their own installation: create a group and a user at example.invalid, give them a role limited to
one section, prove what the user may and may not do, find a prepared error in the logs, and take and restore a
backup. This is the trainer's own assessment, not an official certification; the partner program's certifications
are separate ([Certifications](../../partners/certifications.md)). The Assessments and practical exercises course
in the catalogue has more material.

## Related pages

- [Trainers: start here](../trainers-start-here.md)
- [Course catalogue](../course-catalogue.md)
- [Hosting and operations: course overview](../hosting-operations/README.md)
- [Search: course overview](../search/README.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
