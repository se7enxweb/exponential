---
title: "Module 1: Siteaccesses for several sites"
audience: trainer, admin, developer
category: training
order: 10
summary: The first module of Multisite and site packages. Learners understand siteaccesses and the load order of settings, plan a multisite, add a second site to their training installation and give it a language siteaccess. 3 hours 35 minutes.
keywords: multisite and site packages, module 1, siteaccess, AvailableSiteAccessList, RelatedSiteAccessList, second site, RootNode, language siteaccess, SiteLanguageList, exp:ini
type: help
---
# Module 1: Siteaccesses for several sites

Every later module builds on this one. Learners first learn to read the siteaccesses of an installation and the order
in which settings files are loaded, then plan a multisite on paper, and finally turn their own training installation
into a two-site installation with a second language. The siteaccesses `site_b` and `site_b_ger` made here are used
again in modules 2 to 4.

## At a glance

| | |
|---|---|
| Course | [Multisite and site packages](../README.md) |
| Duration | 3 hours 35 minutes (with two 10-minute breaks) |
| Lessons | 4 |
| Level | Intermediate |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [Lesson 1.1: What a siteaccess is](what-a-siteaccess-is.md) | 45 minutes | Read the siteaccesses, the matching rules and the load order with `exp:ini` |
| [Lesson 1.2: Planning several sites on one installation](planning-several-sites.md) | 40 minutes | Plan a multisite for a brief and decide one installation or several |
| [Lesson 1.3: Adding a second site step by step](adding-a-second-site.md) | 60 minutes | Create a root folder and the siteaccess `site_b`, register it, reach `/site_b` |
| [Lesson 1.4: A siteaccess per language](a-siteaccess-per-language.md) | 50 minutes | Add `site_b_ger` with its own languages and translate one item |

## Preparing this module

- One training installation per learner, installed with `php bin/php/console exp:install --apply` (URL matching, the
  default), with shell access and an administrator account. How to build such a lab:
  [Lesson 2.3 of Train the trainer](../../train-the-trainer/module-02-building-the-lab/one-install-per-learner.md).
- Learners can run `php bin/php/console` in the installation root as the user that serves the site; if they must work
  as root, tell them to add `--allow-root-user`.
- Know how each lab site is served (the built-in server, Apache or Velocity) and its address, for example
  `http://127.0.0.1:8087`; with Velocity, show `php bin/php/console exp:velocity restart` once.
- Print or share the brief of lesson 1.2.
- Rehearse lessons 1.3 and 1.4 once on a fresh lab installation and note the node ID the folder got there.

## Timing plan

| Part | Minutes |
|---|---|
| Lesson 1.1 | 45 |
| Lesson 1.2 | 40 |
| Break | 10 |
| Lesson 1.3 | 60 |
| Break | 10 |
| Lesson 1.4 | 50 |
| **Total** | **215** |

## Related pages

- [Multisite and site packages: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
