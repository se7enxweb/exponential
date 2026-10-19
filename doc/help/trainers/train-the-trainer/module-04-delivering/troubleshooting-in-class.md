---
title: "Lesson 4.4: Troubleshooting in class"
audience: trainer, admin
category: training
order: 44
summary: Fix the problems that stop learners most often, permissions, a maintenance page, stale caches, a lost password, with the troubleshooting chapter, the logs and the log viewer, without stopping the room. 50 minutes.
keywords: troubleshooting, class problems, permissions, maintenance mode, caches, error log, log viewer, error reference, resetuserpassword, debug output
type: help
---
# Lesson 4.4: Troubleshooting in class

Something always breaks in a hands-on course. A trainer who knows the five most common causes and where the logs are
can fix most problems in minutes. This lesson prepares trainers with the troubleshooting chapter of the installation
guide and the log viewer.

## At a glance

| | |
|---|---|
| Course | [Train the trainer](../README.md) |
| Module | [Module 4: Delivering a course](README.md) |
| Duration | 50 minutes (15 presenting, 35 exercise) |
| Level | Intermediate |
| Audience | Trainers and their helpers |
| Prerequisites | [Module 2](../module-02-building-the-lab/README.md) |

## Learning objectives

After this lesson, learners can:

- follow the five steps of "Start here" in the troubleshooting chapter;
- find an error in `var/log/error.log` by its reference, on the command line and in **Setup > Log files**;
- fix the common lab problems: file ownership, maintenance mode, stale caches, a lost password;
- decide when to give a learner a fresh installation instead.

## Trainer's notes

### How to present it

1. The five steps (5 minutes, from [Troubleshooting, 12.1](../../../../install/12-troubleshooting.md#121-start-here)):
   read the message exactly; read `var/log/error.log` (an error page shows a reference such as `ERR-1A2B3C4D5E`, and
   the same reference is in the log); turn on debug output for yourself only; change one thing and clear the smallest
   cache that covers it; on the command line, run as the installation's owner.
2. The common lab problems (10 minutes):

   | Symptom | Fix |
   |---|---|
   | Errors after a command was run as root | `chown -R <web user>:<web group> var`; run scripts as the owner |
   | A maintenance page after a failed install | `php bin/php/maintenance.php status`, then `off` |
   | A change does not show | clear the smallest cache: `php bin/php/ezcache.php --clear-tag=template`, `--clear-tag=ini`, or `--clear-all` |
   | Lost administrator password | `php bin/php/resetuserpassword.php -u admin -g --allow-root-user` (as root) |
   | Generated password unknown | `var/log/initial-admin-password` (until it was deleted) |

### Common questions

**When do I give up and reinstall?** When the fix takes longer than a reinstall (a few minutes with SQLite). Keep
the learner's notes; reinstall; let them catch up with a neighbour.

**Can learners read the logs in the browser?** Administrators can: **Setup > Log files** shows the logs with
masking of private values; editors cannot.

### Pitfalls

- **Five changes at once.** Change one thing, then test.
- **Sharing logs with personal data.** Mask or remove e-mail addresses and IP addresses before posting logs anywhere.

## Hands-on exercise

On your training installation:

1. Open **Setup > Log files** (`/setup/logs` in the admin siteaccess, for example `/admin/setup/logs`) and open
   `error.log`. Find the headings **Controls**, **Lines of error.log** and **Actions**.
2. On the command line, run `tail -n 20 var/log/error.log`.
3. Simulate a maintenance window (only on your training installation): `php bin/php/maintenance.php on --until=5m`,
   open the site and see the maintenance page, then `php bin/php/maintenance.php off` and
   `php bin/php/maintenance.php status`.
4. Clear one cache tag with `php bin/php/ezcache.php --clear-tag=template` and read the output.
5. List the cache tags and ids: `php bin/php/ezcache.php --list-tags` and `--list-ids`.
6. Write a one-page "lab fixes" card with the table from the trainer's notes for your helpers.

## Expected result

- The learner found `error.log` in the browser and on the command line.
- The maintenance page appeared and disappeared again.
- A "lab fixes" card exists for the helpers.

## Quiz

1. An error page shows `ERR-1A2B3C4D5E`. Where do you look?
2. Pages broke after a learner ran a script as root. What is the likely cause and fix?
3. Which command clears every cache?
4. Which command resets a lost administrator password?

<details>
<summary>Answers</summary>

1. In `var/log/error.log` (and its rotations `error.log.1` ...), or in **Setup > Log files** by searching the
   reference ([Finding an error by its reference](../../../../guides/finding-an-error-by-its-reference.md)).
2. Root-owned cache or storage files; `chown -R <web user>:<web group> var`.
3. `php bin/php/ezcache.php --clear-all`.
4. `php bin/php/resetuserpassword.php -u admin -g --allow-root-user` (as root), see
   [Reset a user password](../../../../features/6.0/reset-user-password.md).

</details>

## Further reading

- [Troubleshooting](../../../../install/12-troubleshooting.md)
- [The log viewer](../../../../features/6.0/log-viewer.md)
- [Finding an error by its reference](../../../../guides/finding-an-error-by-its-reference.md)
- [When something goes wrong](../../../when-something-goes-wrong.md)

## Related pages

- [Module 4: Delivering a course](README.md)
- [Train the trainer: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
