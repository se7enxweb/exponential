---
title: "Module 6: Installing sites with the kickstarter and exp:install"
audience: trainer, admin, developer
category: training
order: 60
summary: The last module of Multisite and site packages. Learners compare the install methods, install a multisite with exp:install and with the kickstarter, reinstall and regenerate settings safely, and build a multisite from scratch in a capstone. 5 hours 5 minutes.
keywords: multisite and site packages, module 6, exp:install, kickstarter, kickstart.ini, install methods, --print, --dry-run, --force, DatabaseAction, reinstall, regenerate settings, capstone
type: help
---
# Module 6: Installing sites with the kickstarter and exp:install

Every multisite starts with an installation, and many are installed more than once: test systems, staging copies,
training labs. This module teaches the two command-line installers, `exp:install` and the kickstarter, the safe way to
reinstall and to regenerate settings, and ends with a capstone in which each learner builds a multisite from an empty
directory on their own.

## At a glance

| | |
|---|---|
| Course | [Multisite and site packages](../README.md) |
| Duration | 5 hours 5 minutes (with two 10-minute breaks) |
| Lessons | 5 |
| Level | Intermediate to advanced |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [Lesson 6.1: One installer engine, three front ends](one-engine-three-front-ends.md) | 40 minutes | Compare the methods and choose one per situation |
| [Lesson 6.2: Installing a multisite with exp:install](installing-with-exp-install.md) | 55 minutes | Install with host matching after `--print` and `--dry-run`, test three hosts |
| [Lesson 6.3: The kickstarter and kickstart.ini](the-kickstarter-and-kickstart-ini.md) | 55 minutes | Write and review `kickstart.ini`, install with port matching |
| [Lesson 6.4: Reinstalling, resetting and regenerating settings](reinstalling-and-regenerating-settings.md) | 45 minutes | Back up, reset with `remove`, regenerate with `skip` |
| [Lesson 6.5: Capstone, a multisite from scratch](capstone-a-multisite-from-scratch.md) | 90 minutes | Build a two-brand multisite alone and check it against the checklist |

## Preparing this module

- Each learner needs three fresh code directories (lessons 6.2, 6.3 and 6.5), prepared as in
  [Train the trainer, Lesson 2.2](../../train-the-trainer/module-02-building-the-lab/getting-the-code-for-a-class.md),
  and a way to serve them on free ports ([Lesson 2.4](../../train-the-trainer/module-02-building-the-lab/serving-the-lab-sites.md)).
- The machine needs network access to the package index for the default site packages, or the packages already
  imported (an offline install); test one `exp:install --dry-run` before the class.
- Installs take a few minutes each; on a shared server, stagger them, and run them with a low priority.
- `sqlite3` must be installed for the backup in lesson 6.4.
- Print the capstone brief and checklist of lesson 6.5.
- Remind learners that `kickstart.ini`, the summary and `var/log/initial-admin-password` contain passwords: nothing of
  it goes into chat, screenshots or shared folders.

## Timing plan

| Part | Minutes |
|---|---|
| Lesson 6.1 | 40 |
| Lesson 6.2 | 55 |
| Break | 10 |
| Lesson 6.3 | 55 |
| Lesson 6.4 | 45 |
| Break | 10 |
| Lesson 6.5 | 90 |
| **Total** | **305** |

## Related pages

- [Multisite and site packages: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
