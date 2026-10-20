---
title: "Module 10: Upgrades, extensions and packages"
audience: trainer, admin
category: training
order: 100
summary: The tenth module of Site administration, planning and rehearsing an upgrade, the upgrade check, updates from the administration with ezupdate and git_manager, the Extensions page and packages. Five lessons, 3 hours 20 minutes.
keywords: site administration, module 10, upgrade, upgrade check, ezupdate, git_manager, extensions, ActiveExtensions, packages, ezpkg
type: help
---
# Module 10: Upgrades, extensions and packages

The last module of the course keeps a site current. Learners plan and rehearse an upgrade within the 6.0 line, check
files and database for drift before it, use the administration's update tools with their dry runs, manage the list of
active extensions, and handle packages without surprises.

## At a glance

| | |
|---|---|
| Course | [Site administration](../README.md) |
| Duration | 3 hours 20 minutes (five lessons) |
| Lessons | 5 |
| Level | Intermediate to advanced |
| Format | Hands-on; a training installation with administration and shell access per learner |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [10.1 Planning and running an upgrade](planning-and-running-an-upgrade.md) | 50 min | Find the version, choose the guide's part, rehearse the steps and verify |
| [10.2 The upgrade check](the-upgrade-check.md) | 35 min | Run both checks, see a modified file reported, use checkmanifest.php |
| [10.3 Updates with ezupdate and git_manager](updates-with-ezupdate-and-git-manager.md) | 45 min | Check for updates and dry run; read the upstream card |
| [10.4 The Extensions page](the-extensions-page.md) | 35 min | Read cards, plan and review a change, discard it |
| [10.5 Packages](packages.md) | 35 min | Find, read, validate and export a package |

## Preparing this module

- One training installation per learner with administration and shell access; a backup from Module 9 to fall back on.
- The `sqlite3` tool for lesson 10.1 on SQLite installations (or the client of your engine, read only).
- For lesson 10.3: a projector installation with `ezupdate` active (and `AllowUpdate` left disabled), and, if possible,
  one that is a git checkout with `git_manager`. Check before class which tools the learners' installations have.
- Read the current changelog and the behavior change documents the upgrading guide lists, so you can answer "what
  changed" questions.

## Timing plan

| Time | Item |
|---|---|
| 0:00 | Lessons 10.1 and 10.2 (85 min) |
| 1:25 | Break (15 min) |
| 1:40 | Lesson 10.3 (45 min) |
| 2:25 | Break (10 min) |
| 2:35 | Lessons 10.4 and 10.5 (70 min) |
| 3:45 | End (3 h 20 min of lessons plus breaks) |

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
