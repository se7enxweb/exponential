---
title: "Hosting and operations: course overview"
audience: trainer, hosting, admin
category: training
order: 1
summary: The course for system administrators and hosting staff who run Exponential sites, serving the site, Velocity, databases, deploying and cron, monitoring and logs, backups and recovery, performance and scaling. Seven modules, 28 lessons, about 20 hours of lessons.
keywords: hosting and operations, course overview, reading path, hosting, php-fpm, velocity, databases, deploying, cron, monitoring, logs, backups, benchmarking, valkey, clustering
type: help
---
# Hosting and operations: course overview

This course is for the people who keep Exponential sites running on servers: system administrators, hosting company
staff and the operations side of an agency. It follows a site through its life on a server: how requests are
served, the Velocity web server, the database, deploying changes and running cron, watching the site and its logs,
backing it up and restoring it, and making it fast and able to grow.

## Who it is for

- System administrators who host one or more Exponential sites.
- Support and operations staff of hosting companies.
- Developers who are responsible for their own production servers.

**Outcome:** after the course, a learner can serve a site with PHP-FPM or Velocity, choose and back up its database,
deploy a change, run cron, monitor the site, restore it from a backup and measure and improve its performance.

## Format and duration

| | |
|---|---|
| Duration | 28 lessons, about 20 hours of lessons (three to four days with breaks) |
| Format | Classroom or online; hands-on on a terminal |
| Level | Intermediate to advanced |
| Prerequisites | [Site administration](../site-administrators/README.md) (at least the modules on caches, cronjobs, logs and upgrades); Linux command line, a web server and PHP basics |
| Lab | One Linux machine or virtual machine per learner with root access, PHP 8.0 or later, Composer 2 and Git; a training installation made with `php bin/php/console exp:install --apply` and served with `exp:velocity start`; for lessons 1.2, 1.3 and 3.3 also Apache or nginx with PHP-FPM and a MariaDB or PostgreSQL server |

## Modules

| Module | Lessons | Duration | Outcome |
|---|---|---|---|
| [1. Serving the site](module-01-serving-the-site/README.md) | 4 | 2 h 45 min | Serves a site with Apache or nginx and PHP-FPM, tunes pools, checks OPcache and APCu |
| [2. Velocity](module-02-velocity/README.md) | 4 | 3 h | Starts and keeps Velocity running, sizes its pools, uses the response cache and HTTPS |
| [3. Databases](module-03-databases/README.md) | 4 | 2 h 55 min | Chooses a database, runs SQLite well, sets up a database server, backs up and moves a database |
| [4. Deploying and cron](module-04-deploying-and-cron/README.md) | 4 | 2 h 55 min | Deploys a change, installs cron, uses the cronjob console, plans an upgrade |
| [5. Monitoring and logs](module-05-monitoring-and-logs/README.md) | 4 | 2 h 55 min | Monitors a site, reads and rotates logs, works through the site-down and slow-site runbooks |
| [6. Backups and recovery](module-06-backups-and-recovery/README.md) | 4 | 2 h 45 min | Backs up the right things, writes a retention policy, restores and tests a restore |
| [7. Performance and scaling](module-07-performance-and-scaling/README.md) | 4 | 2 h 55 min | Benchmarks a site, uses caching layers and a Valkey cache, designs a site on several web nodes |

## Reading path

For a trainer preparing to give this course, in this order:

1. [Trainers: start here](../trainers-start-here.md) and the [Course catalogue](../course-catalogue.md).
2. [Hosting companies: start here](../../hosting/hosting-companies-start-here.md) and
   [What hosting Exponential involves](../../hosting/basics/what-hosting-exponential-involves.md).
3. [Requirements at a glance](../../hosting/basics/requirements-at-a-glance.md) and
   [PHP settings for Exponential](../../hosting/basics/php-settings-for-exponential.md).
4. The installation book chapters [7](../../../install/07-console-install.md),
   [8](../../../install/08-serving-the-site.md), [9](../../../install/09-databases.md) and
   [11](../../../install/11-upgrading.md).
5. [Velocity for hosts](../../hosting/servers/velocity-for-hosts.md) and
   [Velocity workers and pools](../../hosting/servers/velocity-workers-and-pools.md).
6. [Database support](../../database-support.md).
7. [Deploying](../../../guides/deploying.md) and [Cronjobs](../../../guides/cronjobs.md).
8. [Monitoring overview](../../hosting/operations/monitoring-overview.md) and the
   [runbooks](../../hosting/runbooks/README.md).
9. [Backups: what to back up](../../hosting/operations/backups-what-to-back-up.md) and
   [Restore and restore tests](../../hosting/operations/restore-and-restore-tests.md).
10. [Caching layers](../../hosting/performance/caching-layers.md) and
    [Benchmarking](../../../guides/benchmarking.md).
11. Modules 1 to 7 of this course, in order. Rehearse every exercise on a lab machine built the way the learners'
    machines will be; prepare the faults for lesson 5.4 in advance.

## Suggested schedules

| Schedule | Plan |
|---|---|
| Four days | Day 1: Modules 1 and 2. Day 2: Modules 3 and 4. Day 3: Modules 5 and 6. Day 4 morning: Module 7 |
| Three days, intensive | Day 1: Modules 1, 2 and lessons 3.1 to 3.2. Day 2: lessons 3.3 to 3.4, Modules 4 and 5. Day 3: Modules 6 and 7 |
| Two and a half days, Velocity sites only | Skip lessons 1.2 and 1.3. Day 1: lessons 1.1 and 1.4, Modules 2 and 3. Day 2: Modules 4, 5 and 6. Day 3 morning: Module 7 |
| Seven half days | One module per half day, in order |
| Self-study | One module per week, with the exercises done on your own virtual machine |

## Assessing learners

Every lesson has a quiz and an expected result the trainer checks. As a final practical task, the trainer breaks a
learner's lab site in a prepared way (a stopped server, a full disk, a missing cron entry or a lost database file)
and the learner restores service with the runbooks of Modules 5 and 6 and writes a short incident note. This is the
trainer's own assessment, not an official certification; the partner program's certifications are separate
([Certifications](../../partners/certifications.md)). The Assessments and practical exercises course in the
catalogue has more material.

## Related pages

- [Trainers: start here](../trainers-start-here.md)
- [Course catalogue](../course-catalogue.md)
- [Hosting companies: start here](../../hosting/hosting-companies-start-here.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
