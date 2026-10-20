---
title: "Module 3: Settings, logs and caches per site"
audience: trainer, admin, developer
category: training
order: 30
summary: The third module of Multisite and site packages. Learners move a site's settings into a site extension, write settings for all of a site's siteaccesses, and give a site its own logs and INI cache. 3 hours 35 minutes.
keywords: multisite and site packages, module 3, site extension, exp:ini move-all, extension settings/override, override__group, UseGlobalLogDir, LogDir, LogVarDir, CacheVarDir, ExpiryDir, INICacheDir
type: help
---
# Module 3: Settings, logs and caches per site

With several sites running, the next questions are operational: where does each site's configuration live, how do
settings apply to one site and not the others, and how are logs and caches kept apart. This module moves the second
site of the lab into its own extension and then separates its settings, logs and INI cache from the main site.

## At a glance

| | |
|---|---|
| Course | [Multisite and site packages](../README.md) |
| Duration | 3 hours 35 minutes (with two 10-minute breaks) |
| Lessons | 4 |
| Level | Advanced |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [Lesson 3.1: A site in its own extension](a-site-in-its-own-extension.md) | 55 minutes | Move the settings of `site_b` and `site_b_ger` into the extension `site_clubb` with `exp:ini move-all` |
| [Lesson 3.2: Settings for every siteaccess of a site](settings-for-every-siteaccess-of-a-site.md) | 45 minutes | Write the extension's `settings/override/` and prove with `exp:ini where` that only its sites read it |
| [Lesson 3.3: Logs per site](logs-per-site.md) | 45 minutes | Give `site_b` its own log directory and find its `404.log` line |
| [Lesson 3.4: Caches and the INI cache per site](caches-and-the-ini-cache-per-site.md) | 50 minutes | Give `site_b` its own INI cache, clear it on its own, plan caches in memory |

## Preparing this module

- Every learner has finished modules 1 and 2 on their own training installation.
- Learners can create files below `extension/` and `var/` as the user that serves the site (check ownership once:
  files created by root cannot be changed by the web server later).
- Rehearse lesson 3.1 on a fresh lab: note the exact refusal messages your version prints, so you can show them.
- If labs run on Exponential Velocity, plan a restart after each lesson's settings change
  (`php bin/php/console exp:velocity restart`).
- Lesson 3.4's memory file system is planned on paper only; do not mount anything on a shared training server.

## Timing plan

| Part | Minutes |
|---|---|
| Lesson 3.1 | 55 |
| Lesson 3.2 | 45 |
| Break | 10 |
| Lesson 3.3 | 45 |
| Break | 10 |
| Lesson 3.4 | 50 |
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
