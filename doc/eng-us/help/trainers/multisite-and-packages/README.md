---
title: "Multisite and site packages: course overview"
audience: trainer, admin, developer
category: training
order: 1
summary: The course for administrators and developers who run several sites on one installation and build sites from packages, 6 modules and 26 lessons, about 22 hours of lessons, from siteaccesses and request matching to per-site settings, logs and caches, cross-site links, packages, exp:install, the kickstarter and a capstone.
keywords: multisite and site packages, multisite, siteaccess, host matching, MatchOrder, language redirect, site extension, exp:ini, logs per site, caches per site, ezurlmultisite, site package, ezpm, package wizard, exp:install, kickstarter, course overview
type: help
---
# Multisite and site packages: course overview

This course teaches how one Exponential installation serves several sites, and how sites are built from packages and
installed from the command line. Learners start with a single-site training installation, turn it step by step into a
multisite with a second brand and a second language, keep each site's settings, logs and caches apart, then learn the
package tools and the installers, and finish by building a multisite from an empty directory on their own.

## Who it is for

- Administrators who run, or will run, several sites, brands or languages on one installation.
- Developers who build multisites, site extensions or packages, and who automate installations.
- Hosts and agencies who provision sites from site packages with `exp:install` or the kickstarter.

## Outcome

After the course, learners can plan a multisite; add sites and language siteaccesses; match requests by path, host,
host and path, and port; send visitors to their language; keep a site's settings in a site extension, with its own
logs and INI cache; make links between sites work; limit teams to their site; inspect, create, export, compare,
import and install packages in the administration and with `ezpm`; and install, reinstall and regenerate sites with
`exp:install` and the kickstarter.

## Format and length

| | |
|---|---|
| Lessons | 26 lessons in 6 modules |
| Lesson time | 21 hours 55 minutes (presenting and exercises) |
| With breaks | about 23 hours 45 minutes, as in the modules' timing plans |
| Format | Classroom with a projector, or online with screen sharing; every learner at their own computer with a shell |
| Group size | Up to about 10 learners per trainer: the exercises change settings, and the trainer must be able to look at every terminal |
| Level | Intermediate to advanced |

## Prerequisites

- Learners: comfortable in a terminal (directories, a text editor, `curl`); have used the administration of
  Exponential as an editor or administrator. The Site administrators course, or equivalent experience, is a good
  basis.
- Trainer: has rehearsed every exercise on the version they teach, and has taken the train-the-trainer course or has
  equivalent experience ([Train the trainer: course overview](../train-the-trainer/README.md)).

## Lab setup

- **One installation per learner**, never a shared one: modules 2 to 4 change the global matching settings. Install
  each with `php bin/php/console exp:install` (default site package, SQLite, URL matching), as in
  [Lesson 2.3 of Train the trainer](../train-the-trainer/module-02-building-the-lab/one-install-per-learner.md), and
  serve it as in [Lesson 2.4](../train-the-trainer/module-02-building-the-lab/serving-the-lab-sites.md).
- Shell access as the user that serves the site, `curl`, and `sqlite3` (lesson 6.4).
- Three further fresh code directories per learner for module 6
  ([Lesson 2.2 of Train the trainer](../train-the-trainer/module-02-building-the-lab/getting-the-code-for-a-class.md)).
- Network access to the package index, or the site packages already imported for an offline install.
- An administrator account per learner on their own installation; test users with addresses at `example.invalid`;
  no real mail.

## Modules

| Module | Lessons | Duration | Topics |
|---|---|---|---|
| [Module 1: Siteaccesses for several sites](module-01-siteaccesses-for-several-sites/README.md) | 4 | 3 h 35 min | Siteaccesses, load order, planning, a second site, a language siteaccess |
| [Module 2: Matching requests to siteaccesses](module-02-matching-requests/README.md) | 4 | 3 h 45 min | Path, host, host and path, port, the redirect to the language |
| [Module 3: Settings, logs and caches per site](module-03-settings-logs-and-caches-per-site/README.md) | 4 | 3 h 35 min | Site extensions, per-site overrides, logs and INI cache per site |
| [Module 4: Links between sites](module-04-links-between-sites/README.md) | 3 | 2 h 25 min | Cross-site links with `ezurlmultisite`, one administration for several sites |
| [Module 5: Site packages](module-05-site-packages/README.md) | 6 | 5 h 20 min | Site packages, the packages pages, `ezpm`, the wizard, compare and import, upload and install |
| [Module 6: Installing sites with the kickstarter and exp:install](module-06-installing-sites/README.md) | 5 | 5 h 5 min | Install methods, `exp:install`, the kickstarter, reinstalling, the capstone |

## Reading path

For a trainer preparing the course, in this order:

1. [10.2 Siteaccesses and site addresses](../../../install/10-after-installing.md#102-siteaccesses-and-site-addresses),
   [Multisite on one installation](../../hosting/provisioning/multisite-on-one-installation.md) and
   [Setting up several sites in one installation](../../creators/website-creators/multisite-setup.md).
2. [Siteaccess by host prefix, and the redirect to the language](../../../features/6.0/siteaccess-host-and-language-matching.md)
   and the comments of `settings/site.ini [SiteAccessSettings]`.
3. [Changing settings from the command line: exp:ini](../../../bc/6.0/console-exp-ini.md),
   [Per-site settings inside extensions](../../../features/6.0/multi-site-ini-overrides.md) and
   [Logs, INI cache and expiry timestamps per site](../../../features/6.0/multi-site-log-and-cache-paths.md).
4. [Links to content of another site](../../../features/6.0/multisite-cross-site-links.md) and
   [Roles and policies](../../../guides/roles-and-policies.md).
5. [4.3 Site packages](../../../install/04-choosing-an-install-method.md#43-site-packages-what-is-installed),
   [Packages](../../../guides/packages.md), [ezpm](../../../features/6.0/ezpm-package-manager-cli.md),
   [The package wizard](../../../features/6.0/package-wizard.md),
   [Package licenses and Semantic Versioning](../../../features/6.0/package-licenses-and-versions.md),
   [Compare and import](../../../features/6.0/package-compare-and-import.md) and
   [Installer batching](../../../features/6.0/package-installer-batching.md).
6. [4. Choosing an install method](../../../install/04-choosing-an-install-method.md),
   [6. The kickstarter](../../../install/06-kickstarter.md) and [7. The console install](../../../install/07-console-install.md).
7. For context: [Site packages for hosts](../../hosting/provisioning/site-packages.md) and
   [Site packages and the themes that ship](../../creators/website-creators/site-packages-and-shipped-themes.md).
8. Then the modules in order: each module's overview first (its preparation list and timing plan), then its lessons.
   Rehearse every exercise on a fresh lab installation; the modules build on each other's results.

## Suggested schedules

Lesson times add up to 21 hours 55 minutes; the schedules add breaks and a short daily review.

### Four days

| Day | Morning | Afternoon |
|---|---|---|
| 1 | Module 1 | Module 2 |
| 2 | Module 3 | Module 4 |
| 3 | Module 5, lessons 5.1 to 5.3 | Module 5, lessons 5.4 to 5.6 |
| 4 | Module 6, lessons 6.1 to 6.4 | Lesson 6.5 (capstone), open questions |

### Two separate two-day courses

- **Multisite** (modules 1 to 4, about 13 hours 20 minutes with breaks): for teams who already have their installation.
- **Packages and installers** (modules 5 and 6, about 10 hours 25 minutes with breaks): for hosts and developers. Before
  lesson 5.3, each learner creates a folder *Site B* with a sub folder *News* on their installation, since lessons
  5.3 and 5.5 package and compare that content; the capstone (6.5) then needs modules 1 to 3 as reading, or replace it
  with lessons 6.2 to 6.4 repeated on a database server.

## Assessing learners

- Each lesson's quiz and expected result check understanding during the course.
- [Lesson 6.5](module-06-installing-sites/capstone-a-multisite-from-scratch.md) has an eight-point checklist for a
  final practical check.
- The [assessments course](../assessments/README.md) has further material for technical courses.
- All of this is the trainer's own assessment, not an official certification; the partner program's certifications
  are separate ([Certifications](../../partners/certifications.md)).

## Related pages

- [Trainers: start here](../trainers-start-here.md)
- [Course catalogue](../course-catalogue.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
