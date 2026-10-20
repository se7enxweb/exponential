---
title: "Module 3: Designs, overrides and debugging"
audience: trainer, developer
category: training
order: 30
summary: The third module of the Developers course. Learners follow the design chain, write override rules by hand and in the Template Editor, trace any part of a page to its template, and clear exactly the cache a change needs. 4 hours 15 minutes.
keywords: developers course, module 3, design chain, override.ini, Template Editor, template path comments, ShowUsedTemplates, eztemplatecheck, cache-block, view cache, ezcache
type: help
---
# Module 3: Designs, overrides and debugging

Module 2 taught the language; this module puts templates where a site uses them. Learners give their extension a
design folder, replace a template for one class and for one object, learn to find the file behind any piece of a
page, and learn the caches that decide whether a change is visible.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Duration | 4 hours 15 minutes (with two 10-minute breaks) |
| Lessons | 5 |
| Level | Intermediate |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [Lesson 3.1: The design chain](the-design-chain.md) | 45 minutes | Read the chain with `exp:ini where`, add the extension's design folder |
| [Lesson 3.2: Override rules in override.ini](override-rules.md) | 60 minutes | Write an override for a class in the extension, clear the caches |
| [Lesson 3.3: Overrides in the Template Editor](overrides-in-the-template-editor.md) | 40 minutes | Create, reorder and remove an override for one object in **Design** > **Template Editor** |
| [Lesson 3.4: Finding the template behind a page](finding-the-template-behind-a-page.md) | 45 minutes | Path comments, used templates, `exp:ini where`, `eztemplatecheck.php` |
| [Lesson 3.5: Caches for template developers](caches-for-template-developers.md) | 45 minutes | Compiled templates, view cache, `{cache-block}`, clearing by tag and id |

## Preparing this module

- Each learner's installation has `extension/mysite_hello` from module 1 and at least two published articles and a
  folder on the public siteaccess.
- Write the public siteaccess name and its site design on the board (`exp:ini where
  site.ini/DesignSettings/SiteDesign <sa>`).
- Lesson 3.3 needs an administrator account on each learner's installation and the **admin4** design for the
  administration; on a shared installation, give each learner a different article to match.
- Rehearse lesson 3.5 once: whether a template change shows before the content clear depends on the view cache
  state of the page.

## Timing plan

| Part | Minutes |
|---|---|
| Lesson 3.1 | 45 |
| Lesson 3.2 | 60 |
| Break | 10 |
| Lesson 3.3 | 40 |
| Lesson 3.4 | 45 |
| Break | 10 |
| Lesson 3.5 | 45 |
| **Total** | **255** |

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
