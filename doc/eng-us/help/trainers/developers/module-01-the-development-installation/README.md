---
title: "Module 1: The development installation"
audience: trainer, developer
category: training
order: 10
summary: The first module of the Developers course. Learners find their way around an installation, build a development installation from Git clones with exp:install, switch on debug output and run their first script with the kernel loaded. 3 hours 25 minutes.
keywords: developers course, module 1, development installation, git clone, exp:install, debug output, logs, ezexec, folder map
type: help
---
# Module 1: The development installation

Every later module needs an installation where learners can break things and see what went wrong. This module
builds it: the folder map and the two rules of the course, an installation from Git clones, visible errors, and a
first script that talks to the kernel.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Duration | 3 hours 25 minutes (with one 10-minute break) |
| Lessons | 4 |
| Level | Beginner |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [Lesson 1.1: The developer's map of an installation](the-developers-map.md) | 45 minutes | Walk the folders, follow an address and a class name to their files |
| [Lesson 1.2: A development installation with Git](a-development-installation-with-git.md) | 60 minutes | Clone, `composer install`, `exp:install`, `exp:velocity start`, sign in |
| [Lesson 1.3: Debug output and logs](debug-output-and-logs.md) | 45 minutes | Switch on debug output with `exp:ini`, read the report, follow the logs |
| [Lesson 1.4: A script with the kernel loaded](a-script-with-the-kernel-loaded.md) | 45 minutes | Create `extension/mysite_hello`, run a script with `ezexec.php` |

## Preparing this module

- Each learner needs a machine (or a folder of their own on a shared server) with PHP, Composer 2 and Git 2.28 or
  later. Check the versions against [Requirements](../../../../install/02-requirements.md).
- On a slow or metered connection, prepare a finished clone with `composer install` done on each machine; learners
  then start lesson 1.2 at the install step.
- Decide the port per learner if several share one server (`[PHPServerSettings] Port` in `settings/velocity.ini`,
  8087 by default), and write the ports on the board.
- Rehearse the whole module once on a clean machine; note the time the clone and `composer install` take.
- No real mail: development installations must not send e-mail to real addresses.

## Timing plan

| Part | Minutes |
|---|---|
| Lesson 1.1 | 45 |
| Lesson 1.2 | 60 |
| Break | 10 |
| Lesson 1.3 | 45 |
| Lesson 1.4 | 45 |
| **Total** | **205** |

## Related pages

- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
