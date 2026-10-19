---
title: "Module 2: Templates, overrides and the pagelayout"
audience: trainer, designer, developer
category: training
order: 20
summary: The second module of Theme design. Learners create their own theme extension with the design extension wizard, extend its pagelayout, write an override and put the expensive parts of the frame in cache blocks. 4 hours 5 minutes.
keywords: theme design, module 2, design extension wizard, theme extension, pagelayout, override, override.ini, Template Editor, cache-block, eztemplatecheck
type: help
---
# Module 2: Templates, overrides and the pagelayout

From this module on, learners build. Each learner creates the theme extension `training_theme` with the design
extension wizard and switches it on for their public siteaccess. They extend its pagelayout with a breadcrumb, a skip
link and a greeting, add an override for the line view of articles, and learn where cache blocks help and where they
leak one visitor's data to another. Every later module works in this extension.

## At a glance

| | |
|---|---|
| Course | [Theme design](../README.md) |
| Duration | 4 hours 5 minutes (with two 10-minute breaks) |
| Lessons | 4 |
| Level | Beginner to intermediate |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [Lesson 2.1: Your own design extension](your-own-design-extension.md) | 60 minutes | Create `training_theme` with the wizard, activate it, set `SiteDesign` for one siteaccess |
| [Lesson 2.2: The pagelayout, your site's frame](the-pagelayout.md) | 60 minutes | Add a skip link, a breadcrumb and a greeting; check templates with `eztemplatecheck.php` |
| [Lesson 2.3: Overrides with override.ini](overrides-with-override-ini.md) | 60 minutes | Read the wizard's override, write a line override for articles, find it in the Template Editor |
| [Lesson 2.4: Cache blocks in a design](cache-blocks-in-a-design.md) | 45 minutes | Cache the menu, provoke and fix a block whose keys forget the user |

## Preparing this module

- Each learner's own training installation, with an administrator account that has `setup/setup` (for the wizard in
  **Setup > RAD**) and shell access in the installation root.
- Published content on the training site: at least one folder below the top that lists two or more articles with an
  **Intro** (the `sevenx_multisite` package's demo content has them; see
  [Choosing the site package for a class](../../train-the-trainer/module-02-building-the-lab/choosing-the-site-package-for-class.md)).
- Run the wizard once on a copy of the lab and check its `page_head.tpl`: if the title uses `implode_key`, prepare the
  three edits from [The design wizard's parts](../../../designers/extensions/design-wizard-parts-reference.md#three-edits-the-generated-head-and-layout-need).
- If the lab is served by Velocity, have the restart command on the board: `php bin/php/console exp:velocity restart`.
- A private browser window available on every learner computer for lesson 2.4.

## Timing plan

| Part | Minutes |
|---|---|
| Lesson 2.1 | 60 |
| Break | 10 |
| Lesson 2.2 | 60 |
| Lesson 2.3 | 60 |
| Break | 10 |
| Lesson 2.4 | 45 |
| **Total** | **245** |

The lesson time is 3 hours 45 minutes. On a one-day course,
put the lunch break after lesson 2.2.

## Related pages

- [Module 1: Designs and the fallback chain](../module-01-designs-and-the-fallback-chain/README.md)
- [Theme design: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
