---
title: "Module 5: Caches"
audience: trainer, admin
category: training
order: 50
summary: The fifth module of Site administration, what the caches hold, clearing them in the administration and from the shell, preloading, and the caches in front of PHP. Five lessons, 2 hours 55 minutes.
keywords: site administration, module 5, caches, cache management, ezcache.php, exp:cache, preload, velocity response cache, http cache, static cache
type: help
---
# Module 5: Caches

Caches make Exponential fast, and stale caches cause most "my change does not show" calls. This module teaches
learners to read the caches before clearing them, to clear the smallest thing that fixes a problem in the browser and
in a terminal, to warm a site after a clear, and to deal with the caches that answer before PHP does.

## At a glance

| | |
|---|---|
| Course | [Site administration](../README.md) |
| Duration | 2 hours 55 minutes (five lessons) |
| Lessons | 5 |
| Level | Intermediate to advanced |
| Format | Hands-on; one training installation per learner served by Velocity, a terminal, a private browser window |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [5.1 What the caches hold](what-the-caches-hold.md) | 35 min | Read the Caches page, ids, tags and last clears |
| [5.2 Clearing caches in the administration](clearing-caches-in-the-administration.md) | 35 min | Clear selected caches, a tag, from the right column |
| [5.3 Clearing caches from the command line](clearing-caches-from-the-command-line.md) | 35 min | Use `ezcache.php` and `exp:cache` with `--dry-run` |
| [5.4 Preloading caches](preloading-caches.md) | 35 min | Dry run, preload, broken links, the shell command |
| [5.5 The caches in front of PHP](caches-in-front-of-php.md) | 35 min | Compare the response, HTTP and static caches, read headers |

## Preparing this module

- The training installations from Module 1, served by Velocity (`php bin/php/console exp:velocity start`), so the
  response cache is present.
- The lab's `SiteURL` must match the address it is served at, or the preload of lesson 5.4 requests the wrong
  address. Check with a dry run before class.
- `curl` on the command line for lesson 5.5, and the PHP curl extension for the preloader.
- On a shared training server, agree who may clear which cache: every clear slows the site for everyone.

## Timing plan

| Time | Item |
|---|---|
| 0:00 | Lesson 5.1 (35 min) |
| 0:35 | Lesson 5.2 (35 min) |
| 1:10 | Break (15 min) |
| 1:25 | Lesson 5.3 (35 min) |
| 2:00 | Lesson 5.4 (35 min) |
| 2:35 | Break (10 min) |
| 2:45 | Lesson 5.5 (35 min) |
| 3:20 | End (2 h 55 min of lessons plus 25 min of breaks) |

## Related pages

- [Site administration: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
