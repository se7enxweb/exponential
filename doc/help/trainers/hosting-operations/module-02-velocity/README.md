---
title: "Module 2: Velocity"
audience: trainer, hosting, admin
category: training
order: 20
summary: The second module of Hosting and operations, what Velocity is, keeping it running, workers and pools, the response cache and HTTPS certificates. Four lessons, 3 hours.
keywords: hosting and operations, module 2, Velocity, qbix, exp:velocity, workers, pool, response cache, HTTPS, certificates, watchdog, systemd
type: help
---
# Module 2: Velocity

Exponential Velocity is the recommended way to serve an Exponential site, from development to production. This module
teaches hosting staff to run it: the model of a parent process and persistent workers, the control verbs and a boot
unit, the worker pool and its memory, and the two things that replace other tools, the response cache and HTTPS with
Velocity's own certificates.

## At a glance

| | |
|---|---|
| Course | [Hosting and operations](../README.md) |
| Duration | 3 hours (four lessons) |
| Lessons | 4 |
| Level | Intermediate to advanced |
| Format | Hands-on; one machine with a terminal per learner |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [2.1 What Velocity is and a first start](what-velocity-is.md) | 45 min | Choose the engine, change a setting, start and check the server |
| [2.2 Keeping Velocity running](keeping-velocity-running.md) | 45 min | Use graceful and restart, write a boot unit, read logs and the tree |
| [2.3 Workers and pools](workers-and-pools.md) | 45 min | Set a dynamic pool and watch it grow and shrink |
| [2.4 The response cache and HTTPS certificates](response-cache-and-https.md) | 45 min | Prove a cache hit, switch on HTTPS with a certificate pair |

## Preparing this module

- One training installation per learner with PHP 8.1 or later and the `sockets`, `pcntl`, `posix` and `openssl`
  extensions; the engine package already required with Composer
  (`composer require se7enxweb/exponential-velocity:~0.0.4.44`).
- Root on the learners' virtual machines for the boot unit of lesson 2.2; without root, learners write the unit and
  read it with you.
- A browser on each learner's machine for the `/Q/dashboard` page (it opens from the machine itself).
- `openssl` for the certificate pair of lesson 2.4. No public DNS or ACME is needed in class.
- Check that no two learners share the same ports on a shared machine.

## Timing plan

| Time | Item |
|---|---|
| 0:00 | Lesson 2.1 (45 min) |
| 0:45 | Lesson 2.2 (45 min) |
| 1:30 | Break (15 min) |
| 1:45 | Lesson 2.3 (45 min) |
| 2:30 | Lesson 2.4 (45 min) |
| 3:15 | End (3 h of lessons plus one break) |

## Related pages

- [Hosting and operations: course overview](../README.md)
- [Velocity for hosts](../../../hosting/servers/velocity-for-hosts.md)
- [Velocity beside Apache or nginx](../../../hosting/servers/velocity-beside-apache-or-nginx.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
