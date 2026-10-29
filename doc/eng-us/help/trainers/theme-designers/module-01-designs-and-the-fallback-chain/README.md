---
title: "Module 1: Designs and the fallback chain"
audience: trainer, designer, developer
category: training
order: 10
summary: The first module of Theme design. Learners find the designs of an installation, read the design chain of a siteaccess, find the template behind any markup, and tell site designs from administration designs. 3 hours 10 minutes.
keywords: theme design, module 1, design, fallback chain, SiteDesign, AdditionalSiteDesignList, DesignExtensions, template path comments, admin4, administration design
type: help
---
# Module 1: Designs and the fallback chain

Every later module builds on one idea: a siteaccess has a chain of designs, and the first file found along it wins.
This module makes that idea concrete. Learners look at the designs on disk, read the chain from the settings, prove
their reading with template path comments, and learn where the administration designs differ from the site designs.
Nothing in this module changes the look of a page; it is about reading before writing.

## At a glance

| | |
|---|---|
| Course | [Theme design](../README.md) |
| Duration | 3 hours 10 minutes (with one 10-minute break) |
| Lessons | 4 |
| Level | Beginner |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [Lesson 1.1: What a design is and where it lives](what-a-design-is.md) | 45 minutes | List the designs on disk, read their folders and the `DesignExtensions[]` lines |
| [Lesson 1.2: Reading the design chain of a siteaccess](reading-the-chain-of-a-siteaccess.md) | 45 minutes | Read the chain with `exp:ini where` and `get`, predict which `pagelayout.tpl` is used |
| [Lesson 1.3: Which template wrote this markup?](which-template-wrote-this-markup.md) | 45 minutes | Switch template path comments on, find files in the page source, switch them off |
| [Lesson 1.4: Site designs and administration designs](site-designs-and-admin-designs.md) | 45 minutes | Read the administration chain, switch the administration design for their own session |

## Preparing this module

- One training installation per learner with shell access in the installation root, installed for example with
  `php bin/php/console exp:install --apply` ([7. The console install](../../../../install/07-console-install.md)); see
  [Module 2: Building the training lab](../../train-the-trainer/module-02-building-the-lab/README.md) of Train the
  trainer.
- Each installation served in a browser (for example with `php bin/php/console exp:velocity start`).
- An administrator account per learner for lesson 1.4; the role must have the policy `uiswitch/design` if learners are
  to try the administration designs.
- Run every command of the module once on a copy of the lab installation and note the name of a public siteaccess and
  of the administration siteaccess; write them on the board.
- A printed sheet per learner with two empty columns, "public chain" and "administration chain".

## Timing plan

| Part | Minutes |
|---|---|
| Lesson 1.1 | 45 |
| Lesson 1.2 | 45 |
| Break | 10 |
| Lesson 1.3 | 45 |
| Lesson 1.4 | 45 |
| **Total** | **190** |

## Related pages

- [Theme design: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
