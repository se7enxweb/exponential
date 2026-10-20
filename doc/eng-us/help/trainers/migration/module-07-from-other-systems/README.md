---
title: "Module 7: From other systems"
audience: trainer, admin, developer
category: training
order: 70
summary: The module on sites from other systems - WordPress, Drupal and Joomla, and static HTML - applying the planning, mapping and tool methods of the course to real sources. Three lessons, 2 hours 45 minutes.
keywords: migrating to exponential, WordPress, Drupal, Joomla, static HTML, WXR, JSON:API, Web Services API, crawl, redirects, other systems, module
type: help
---
# Module 7: From other systems

Most migrations trainers see come from outside the Exponential family. This module applies the frame of modules 1 to
3 to the most common sources: WordPress, Drupal, Joomla and static HTML. The documentation has further guides for
Magento, Shopify, site builders, Contentful, Adobe Experience Manager and custom applications; trainers can build
extra lessons from them on the same pattern.

## At a glance

| | |
|---|---|
| Course | [Migrating to Exponential](../README.md) |
| Duration | 2 hours 45 minutes |
| Lessons | 3 |
| Level | Intermediate |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [7.1 From WordPress](from-wordpress.md) | 60 minutes | Plan a WordPress migration: export, mapping, import order, redirects, delta |
| [7.2 From Drupal and Joomla](from-drupal-and-joomla.md) | 45 minutes | Compare WordPress, Drupal and Joomla in one table; solve two address traps |
| [7.3 From a static HTML site](from-static-html.md) | 60 minutes | Take three pages through extraction, tree, import and a redirecting alias |

## Preparing this module

- [ ] Modules 2 and 3 taught first.
- [ ] For lesson 7.3: the three static pages (`about/index.html`, `about/team.html`, `products/widgets.html`) written
      and handed out, with an image each; nothing copied from a real site without permission.
- [ ] Each learner has a course folder and an editor or administrator account on the training installation; the
      training site answers on a known address for `curl`.
- [ ] If you show a real WXR file, use one from a test WordPress installation with test users at `example.invalid`.
- [ ] At the end of lesson 7.3, check that learners removed their test alias.

## Timing plan

| Block | Minutes |
|---|---|
| Lesson 7.1 | 60 |
| Lesson 7.2 | 45 |
| Break | 15 |
| Lesson 7.3 | 60 |
| Total | 180 (2 hours 45 minutes of lessons, plus a 15-minute break) |

## Related pages

- [Migrating to Exponential: course overview](../README.md)
- [Migrating to and from Exponential, section 4](../../../../install/migration/README.md#4-migrations-from-and-to-other-systems):
  every guide for other systems.
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
