---
title: "Module 3: Stylesheets and scripts through the packer"
audience: trainer, designer, developer
category: training
order: 30
summary: The third module of Theme design. Learners list their stylesheets and scripts in design.ini, see what the ezjscore packer does with them, learn the routine after a change, and make their theme ready for a Content Security Policy. 2 hours 55 minutes.
keywords: theme design, module 3, stylesheets, scripts, design.ini, FrontendCSSFileList, ezcss_load, ezscript_load, ezjscore packer, cache clear, hard reload, Content Security Policy
type: help
---
# Module 3: Stylesheets and scripts through the packer

A theme's look lives in its stylesheets and its behavior in its scripts. In Exponential both are named in
`design.ini` and delivered by the ezjscore packer, never linked by hand. In this module learners add files to
`training_theme` the Exponential way, compare packed and unpacked pages, learn the three caches that remember an old
stylesheet, and remove every reason for inline code so their theme can run under the shipped Content Security Policy.

## At a glance

| | |
|---|---|
| Course | [Theme design](../README.md) |
| Duration | 2 hours 55 minutes (with one 10-minute break) |
| Lessons | 4 |
| Level | Beginner to intermediate |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [Lesson 3.1: Listing stylesheets and scripts in design.ini](listing-files-in-design-ini.md) | 45 minutes | Add `training.css` to the frontend list, require `article.css` from one template |
| [Lesson 3.2: The ezjscore packer](the-ezjscore-packer.md) | 45 minutes | Switch the packer off and on for one siteaccess, find a packed file and read its name |
| [Lesson 3.3: After a stylesheet change](after-a-stylesheet-change.md) | 30 minutes | Run the clear routine, hard reload, compare the packed names |
| [Lesson 3.4: A theme ready for a Content Security Policy](ready-for-a-content-security-policy.md) | 45 minutes | Read a report-only violation, replace inline code with classes, data attributes and a script file |

## Preparing this module

- Learners' `training_theme` from module 2 working on their public siteaccess, with the skip link and breadcrumb of
  lesson 2.2.
- A browser with developer tools (console and network panel) on every learner computer; show where they are before
  lesson 3.4.
- Check on a copy of the lab that `extension/ezjscore/settings/ezjscore.ini` has `Packer` commented out and that
  `DevelopmentMode` is `disabled`, so the packer is on at the start of lesson 3.2.
- If the lab runs on Velocity, have `php bin/php/console exp:velocity cache clear` on the board for lesson 3.3.

## Timing plan

| Part | Minutes |
|---|---|
| Lesson 3.1 | 45 |
| Lesson 3.2 | 45 |
| Break | 10 |
| Lesson 3.3 | 30 |
| Lesson 3.4 | 45 |
| **Total** | **175** |

## Related pages

- [Module 2: Templates, overrides and the pagelayout](../module-02-templates-overrides-and-the-pagelayout/README.md)
- [Theme design: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
