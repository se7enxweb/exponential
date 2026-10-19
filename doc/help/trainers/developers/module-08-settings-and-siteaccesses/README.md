---
title: "Module 8: Settings and siteaccesses"
audience: trainer, developer
category: training
order: 80
summary: The eighth module of the Developers course. Learners follow how INI files are read and merged, place settings in the right scope for a siteaccess, change and move settings safely with exp:ini, and give their extension an INI file of its own. 3 hours 15 minutes.
keywords: developers course, module 8, settings, INI, eZINI, scopes, siteaccess, exp:ini, where, set, move, dry-run, own ini file, ezini
type: help
---
# Module 8: Settings and siteaccesses

Settings decide almost everything an installation does, and the same setting can come from many files. This module
teaches developers to read the load order, to choose the scope a setting belongs in, to change settings with
`exp:ini` instead of an editor, and to give their own code settings with documented defaults.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Duration | 3 hours 15 minutes (with one 10-minute break) |
| Lessons | 4 |
| Level | Intermediate |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [Lesson 8.1: How settings are read](how-settings-are-read.md) | 45 minutes | The file format, the scope order, arrays and resets, `exp:ini where` |
| [Lesson 8.2: Siteaccess settings](siteaccess-settings.md) | 45 minutes | Siteaccesses, matching, `-s`, choosing the scope of a setting |
| [Lesson 8.3: Changing settings with exp:ini](changing-settings-with-exp-ini.md) | 50 minutes | Actions, dry runs, backups, exit codes, moving into an extension |
| [Lesson 8.4: Settings of your own](settings-of-your-own.md) | 45 minutes | `mysite.ini` with defaults, read from PHP and templates, overridden per siteaccess |

## Preparing this module

- The development installations have at least the siteaccesses of an `exp:install` installation (a site, `admin`
  and `editor`); write their names on the board.
- Learners work as the site user, not root: `exp:ini` writes files, and root-owned settings files break a site.
- Lesson 8.3 step 7 moves settings; tell learners to stop at the dry run unless the block is their own.
- For the link to the Multisite and site packages course, check that its module 1 is available in your help center.

## Timing plan

| Part | Minutes |
|---|---|
| Lesson 8.1 | 45 |
| Lesson 8.2 | 45 |
| Break | 10 |
| Lesson 8.3 | 50 |
| Lesson 8.4 | 45 |
| **Total** | **195** |

## Related pages

- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
