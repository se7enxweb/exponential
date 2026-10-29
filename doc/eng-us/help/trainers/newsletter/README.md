---
title: "Newsletters with CJW Newsletter: course overview"
audience: trainer, editor, admin
category: training
order: 1
summary: The course for people who write, send and run newsletters with the cjw_newsletter extension - 6 modules and 20 lessons, 16 hours of lessons, on a training installation that writes every mail to a file and uses only test addresses at example.invalid.
keywords: newsletters with cjw newsletter, newsletter course, cjw_newsletter, newsletter training, subscribers, double opt-in, sending, cronjobs, bounces, transports, skins, course overview
type: help
---
# Newsletters with CJW Newsletter: course overview

This course teaches the newsletter system of Exponential, the extension `cjw_newsletter`, from the first look at the
dashboard to a skin of your own. Learners build a list, gather made-up subscribers through the real double opt-in,
write and test editions, send them, run the queue by hand, and plan the switch to real mail for their own site.

One rule holds from the first lesson to the last: **nothing is ever sent to a real person**. The training
installation writes every mail to a file, and every subscriber, sender and test address ends in `@example.invalid`.

## Who it is for

- Editors who will write and send newsletters (modules 1 to 4 and lesson 6.1).
- Administrators who set up and run the newsletter: cron, bounces, transports, deliverability (all modules; module 5
  is theirs).
- Designers and developers who give the newsletter its look (module 6, after a look at modules 2 and 4).

## Outcome

After the course, learners can create newsletter systems and lists with a sound configuration, write editions with
articles, run the subscribe form and double opt-in, manage, import and export subscribers with recorded consent,
unsubscribe and blacklist addresses, preview and test editions, send them and follow the queue, abort a send, read
the archive and statistics, handle bounces, plan the switch from files to real sending, use rate limits and test
groups, and write a skin.

## Format and length

| | |
|---|---|
| Lessons | 20 lessons in 6 modules |
| Lesson time | 16 hours (presenting and exercises) |
| With breaks | about 17 hours, as in the modules' timing plans |
| Format | Classroom with a projector, or online with screen sharing; every learner at their own computer |
| Group size | Up to about 10 learners per trainer; the sending exercises need the trainer to watch the outbox |
| Level | Beginner to advanced (the later modules for administrators and designers) |

## Prerequisites

- Learners: basic editing of content as taught in [Editors 101](../editors-101/README.md) (creating, editing and
  publishing an item). Administrators: a shell on a training installation. Designers: the template language.
- Trainer: has run the whole course on their own training installation once, including every sending exercise, and
  knows the train-the-trainer material ([Train the trainer: course overview](../train-the-trainer/README.md)).

## Lab setup

- A training installation, for example installed with `php bin/php/console exp:install --apply`
  ([7. The console install](../../../install/07-console-install.md)), with `cjw_newsletter` active, its tables
  created, the newsletter tree with its section, and the subscribe form reachable on the public siteaccess. How to
  build and reset a lab: [Module 2: Building the lab](../train-the-trainer/module-02-building-the-lab/README.md) of
  Train the trainer.
- **Every mail written to files**: `site.ini [MailSettings] Transport=file`, and in
  `cjw_newsletter.ini [NewsletterMailSettings]` the methods `TransportMethodCronjob`, `TransportMethodPreview` and
  `TransportMethodDirectly` all `file`, checked in the settings viewer. The shipped `TransportMethodPreview` is
  `sendmail`; [lesson 1.2](module-01-getting-started/preparing-a-safe-training-installation.md) changes it.
- **Only made-up people**: subscribers, senders, test receivers and test groups at `example.invalid`. Never a copy of
  a real subscriber list.
- One admin training account per learner; shell access as the web server user for administrators and designers.
- No cron for the newsletter parts on a shared installation during the course: the trainer starts the queue.

## Modules

| Module | Lessons | Duration | Topics |
|---|---|---|---|
| [Module 1: Getting started](module-01-getting-started/README.md) | 3 | 2 h | The parts, a training installation that cannot send, the dashboard |
| [Module 2: Systems, lists and editions](module-02-systems-lists-and-editions/README.md) | 4 | 3 h 30 min | Newsletter root and systems, list configuration, editions and articles, recurring sends, pools, approval |
| [Module 3: Subscribers](module-03-subscribers/README.md) | 4 | 3 h 15 min | Subscribe form and double opt-in, users and subscriptions, CSV import and export, consent, unsubscribing, blacklist |
| [Module 4: Sending](module-04-sending/README.md) | 4 | 3 h 15 min | Previews and test sends, sending, cronjobs and console, aborting, archive, statistics |
| [Module 5: Bounces and transports](module-05-bounces-and-transports/README.md) | 3 | 2 h 15 min | Mail accounts and bounces, going live, rate limits, test groups, suppression |
| [Module 6: Templates and skins](module-06-templates-and-skins/README.md) | 2 | 1 h 45 min | Skins, HTML and text parts, placeholders, writing a skin |

## Reading path

For a trainer preparing the course, in this order:

1. [cjw_newsletter: newsletters in the admin](../../../features/6.0/extensions/cjw_newsletter.md): the parts, the
   rehearsal without mail, the settings and what changed in the Exponential 6 releases.
2. [Newsletters with cjw_newsletter](../../creators/website-creators/newsletters-with-cjw-newsletter.md),
   [Newsletter lists and subscriptions](../../creators/website-creators/newsletter-lists-and-subscriptions.md) and
   [Sending newsletters safely](../../creators/website-creators/sending-newsletters.md): the three short pages the
   course follows.
3. [10.4 Mail](../../../install/10-after-installing.md#104-mail-the-file-transport-and-real-sending) and
   [10.5 Notifications and newsletters](../../../install/10-after-installing.md#105-notifications-and-newsletters):
   the transports and the cron parts.
4. [Specification: the cjw_newsletter extension](../../../specifications/6.0/cjw_newsletter.md): every view, command,
   cronjob part and dashboard problem.
5. [E-mail preferences: setting them up and running them](../../../guides/mail-preferences-administrator.md) and
   [Cronjobs](../../../guides/cronjobs.md).
6. The extension's own guides for 4.2 (deliverability, editorial, rendering, statistics, import and export), linked
   from the feature page.
7. Then the modules in order: each module's overview first (its preparation list and timing plan), then its lessons.
   Rehearse every exercise on your training installation, and look at the outbox after each one.

## Suggested schedules

Lesson times add up to 16 hours; the schedules add breaks.

### Two and a half days, the whole course

| Day | Morning | Afternoon |
|---|---|---|
| 1 | Module 1, lessons 2.1 and 2.2 | Lessons 2.3 and 2.4, lessons 3.1 and 3.2 |
| 2 | Lessons 3.3 and 3.4, lessons 4.1 and 4.2 | Lessons 4.3 and 4.4, lesson 5.1 |
| 3 | Lessons 5.2 and 5.3, Module 6 | |

### One and a half days for editors

Module 1 (lesson 1.2 as a demonstration by the trainer), Module 2, Module 3 without lesson 3.3, lessons 4.1, 4.2 and
4.4, and lesson 6.1: about 11 hours 15 minutes of lessons. Administrators join for lessons 3.3 and 4.3 and Module 5 on a later
half day.

### Half day for administrators who already know the editing side

Lessons 1.2, 4.3, 5.1, 5.2 and 5.3: 4 hours of lessons.

## Assessing learners

- Each lesson's quiz and expected result check understanding during the course.
- A good final practical check: each learner runs the whole cycle alone on a fresh training list (list, two
  subscribers through the double opt-in, an edition, a test, a send, an abort), and shows the outbox.
- The assessments course (Assessments and practical exercises, in this section of the help) has a written test and a
  practical task for this course. All assessments are the trainer's own material, not an official certification
  ([Certifications](../../partners/certifications.md) says what the partner program publishes).

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
