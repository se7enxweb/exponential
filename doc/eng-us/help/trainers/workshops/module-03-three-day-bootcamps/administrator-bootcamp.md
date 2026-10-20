---
title: "Bootcamp 3.2: The three-day administrator bootcamp"
audience: trainer, admin
category: training
order: 32
summary: Three days for people who take over running an Exponential site, day 1 installation, users and roles, day 2 sections, settings, caches, cronjobs and logs, day 3 security, two-factor sign-in, backups, upgrades and an incident drill.
keywords: three days, bootcamp, administrators, roles, sections, settings, caches, cronjobs, logs, security, two-factor, backups, upgrades, incident drill
type: help
---
# Bootcamp 3.2: The three-day administrator bootcamp

An administrator must be able to keep a site safe, fast and up to date, and to recover when something breaks. Three
days give room for every routine task and an incident drill at the end.

## At a glance

| | |
|---|---|
| Course | [Workshops and bootcamps](../README.md) |
| Module | [Module 3: Three-day bootcamps](README.md) |
| Duration | 3 days, 6 hours of sessions each |
| Level | Intermediate |
| Audience | Site administrators, 4 to 12 learners |
| Prerequisites | A laptop per learner meeting the [requirements](../../train-the-trainer/module-02-building-the-lab/requirements-for-a-training-machine.md); command-line basics |

## Learning objectives

At the end of the bootcamp, learners can:

- install, serve and reset an installation;
- manage users, groups, roles, policies and sections;
- read and change settings, clear the right caches, run cronjobs and read logs;
- harden a site, switch on two-factor sign-in, back up, restore and upgrade;
- handle an incident from the error reference to the fix.

## The agenda

| Day | Morning (3 h) | Afternoon (3 h) |
|---|---|---|
| 1 | Install and serve ([Lesson 2.3](../../train-the-trainer/module-02-building-the-lab/one-install-per-learner.md), [2.4](../../train-the-trainer/module-02-building-the-lab/serving-the-lab-sites.md)); [A tour of the administration](../../../admin-tour.md) | Users, groups, roles and policies ([Roles and policies](../../../../guides/roles-and-policies.md)) |
| 2 | Sections ([Sections](../../../../guides/sections.md)); settings and INI ([Settings page](../../../../guides/settings-page.md), [exp:ini](../../../../features/6.0/exp-ini-command.md)) | Caches ([Caches](../../../../guides/caches.md)), cronjobs ([Cronjobs](../../../../guides/cronjobs.md)), logs ([The log viewer](../../../../features/6.0/log-viewer.md)) |
| 3 | Security ([Security hardening](../../../security-hardening.md)), two-factor sign-in ([Two-factor login](../../../../guides/two-factor-login.md)), backups | Upgrades ([Upgrading](../../../../guides/upgrading.md), [Upgrade check](../../../../guides/upgrade-check.md)); incident drill |

Breaks: 15 minutes mid-morning and mid-afternoon, 45 minutes lunch, each day.

## Trainer's notes

- **Each learner owns an installation** for three days; keep a golden copy for quick resets
  ([Lesson 2.6](../../train-the-trainer/module-02-building-the-lab/resetting-the-lab.md)).
- **The incident drill** (day 3, last 90 minutes): the trainer prepares a fault on each installation in advance (for
  example a root-owned cache directory, or maintenance mode left on); learners find and fix it with
  [Troubleshooting](../../../../install/12-troubleshooting.md).
- **Upgrades** on a training installation: update to a newer release of the same line following
  [Update with Git](../../../update-with-git.md), when the class uses Git clones.

## Hands-on exercise: the bootcamp capstone

1. Set up a "Newsroom" group with a limited role and a section of its own.
2. Switch on two-factor sign-in for one test user, following the guide.
3. Back up the installation (database file and `var/storage`), change something, restore, and clear the caches.
4. In the incident drill, find and fix the prepared fault, and write a three-line incident note: symptom, cause, fix.

## Expected result

- The role and section work for the test user.
- The restore brought back the earlier state.
- The incident note names the symptom, the cause and the fix with the command used.

## Quiz (end of bootcamp)

1. What does `--clear-tag=ini` clear that `--clear-id=ini` leaves?
2. Where is an error reference `ERR-...` found?
3. A page shows maintenance after a failed install. What do you run?
4. Why keep the 2FA policies in the roles that sign in?

<details>
<summary>Answers</summary>

1. The global INI cache ([Troubleshooting, 12.12](../../../../install/12-troubleshooting.md#1212-caches-and-changes-that-do-not-show)).
2. In `var/log/error.log` and its rotations, or **Setup > Log files**.
3. `php bin/php/maintenance.php status`, then `off` after fixing the cause.
4. So users can set up and verify their own second step ([Two-factor login](../../../../guides/two-factor-login.md)).

</details>

## Further reading

- [Operating a site](../../../../guides/operating-a-site.md)
- [Security and audit](../../../../guides/security-and-audit.md)
- [Upgrading](../../../../install/11-upgrading.md)

## Related pages

- [Module 3: Three-day bootcamps](README.md)
- [Workshops and bootcamps: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
