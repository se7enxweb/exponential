---
title: "Agenda 2.2: The administrators day"
audience: trainer, admin
category: training
order: 22
summary: A one-day workshop for new site administrators, install a site, users and roles, sections, caches, cronjobs, logs, system information and the upgrade check, each practised once on the learner's own installation. 6 hours of sessions.
keywords: one day, administrators, workshop agenda, roles, policies, caches, cronjobs, logs, system information, upgrade check, install
type: help
---
# Agenda 2.2: The administrators day

New administrators need to know where everything is and how to do the routine tasks safely. This day gives each
learner their own installation and walks through the tasks they will do every week, with the guide for each.

## At a glance

| | |
|---|---|
| Course | [Workshops and bootcamps](../README.md) |
| Module | [Module 2: One-day agendas](README.md) |
| Duration | 6 hours of sessions (9:00 to 16:30 with breaks and lunch) |
| Level | Intermediate |
| Audience | New site administrators, 4 to 12 learners |
| Prerequisites | A laptop per learner meeting the [requirements](../../train-the-trainer/module-02-building-the-lab/requirements-for-a-training-machine.md); basic command-line use |

## Learning objectives

At the end of the day, learners can:

- install and serve a training site, and sign in safely;
- create a role with limited policies and assign it to a group;
- clear the right cache for a change and read the logs;
- run the cronjobs by hand and find the system information and the upgrade check.

## The agenda

| Time | Block | Uses | Checkpoint |
|---|---|---|---|
| 9:00 | Welcome; install and serve (60 min) | [One installation per learner](../../train-the-trainer/module-02-building-the-lab/one-install-per-learner.md), [Serving the lab sites](../../train-the-trainer/module-02-building-the-lab/serving-the-lab-sites.md) | Site served; password changed |
| 10:00 | The administration tour for administrators (30 min) | [A tour of the administration](../../../admin-tour.md) | Setup area opened |
| 10:30 | Break (15 min) | | |
| 10:45 | Users, groups, roles and policies (75 min) | [Roles, policies and unactivated users](../../../../guides/roles-and-policies.md) | A "News editors" role assigned to a group |
| 12:00 | Lunch (45 min) | | |
| 12:45 | Sections (30 min) | [Sections](../../../../guides/sections.md) | A section created and assigned |
| 13:15 | Caches and logs (60 min) | [Caches](../../../../guides/caches.md), [The log viewer](../../../../features/6.0/log-viewer.md) | A cache cleared from **Setup > Caches**; `error.log` opened |
| 14:15 | Break (15 min) | | |
| 14:30 | Cronjobs (40 min) | [Cronjobs](../../../../guides/cronjobs.md) | Cronjobs run once by hand |
| 15:10 | System information and upgrade check (35 min) | [System information](../../../../guides/system-information.md), [Upgrade check](../../../../guides/upgrade-check.md) | Version and PHP read; upgrade check opened |
| 15:45 | Wrap-up: what to do weekly; feedback (45 min) | [Operating a site](../../../../guides/operating-a-site.md) | Weekly checklist written |
| 16:30 | End | | |

Cut items, in order: the sections block (move to follow-up); the upgrade check.

## Trainer's notes

- **Rehearse the install on every machine type** the learners bring.
- **Roles are the heart of the day.** Read a role as sentences on its page before building one.
- **Caches**: teach "the smallest cache that covers the change" from
  [Troubleshooting, 12.12](../../../../install/12-troubleshooting.md#1212-caches-and-changes-that-do-not-show).
- **After the day**, point to the full Site administration course.

## Hands-on exercise: the capstone of the day

On the learner's own installation:

1. Create a user group "Newsroom" and a user in it with an address at `example.invalid`.
2. Create the role "News editors" with `content/read`, `content/create` and `content/edit` limited to one subtree, and
   assign it to "Newsroom".
3. Sign in as the new user and check what they can do; sign out.
4. Clear the template cache from **Setup > Caches** and find the entry for it in the logs or the audit trail if your
   installation shows it.
5. Write the weekly checklist: logs, cronjobs, caches, updates.

## Expected result

- The role page of "News editors" shows its policies as sentences and the "Newsroom" assignment.
- The new user can edit only in the chosen subtree.
- A weekly checklist of four or more items.

## Quiz (end of day)

1. What does a role do before it is assigned?
2. A template change does not show. Which cache do you clear?
3. Where do you read the error log in the browser?
4. Why assign roles to groups?

<details>
<summary>Answers</summary>

1. Nothing ([Roles and policies](../../../../guides/roles-and-policies.md)).
2. The template cache: `php bin/php/ezcache.php --clear-tag=template` or in **Setup > Caches**.
3. **Setup > Log files** ([The log viewer](../../../../features/6.0/log-viewer.md)).
4. Every member, now and later, gets the role.

</details>

## Further reading

- [Operating a site](../../../../guides/operating-a-site.md)
- [Security hardening](../../../security-hardening.md)
- [Two-factor login](../../../../guides/two-factor-login.md)

## Related pages

- [Module 2: One-day agendas](README.md)
- [Workshops and bootcamps: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
