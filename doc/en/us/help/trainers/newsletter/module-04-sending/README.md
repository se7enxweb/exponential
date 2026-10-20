---
title: "Module 4: Sending"
audience: trainer, editor, admin
category: training
order: 40
summary: Previews and test sends, scheduling and sending an edition, the cronjobs and console commands, aborting a send, the archive and statistics - all on a training installation that writes every mail to a file. 3 hours 15 minutes.
keywords: newsletters with cjw newsletter, sending, test send, send newsletter, cronjob, console, queue, abort, archive, statistics
type: help
---
# Module 4: Sending

This module sends. Learners test an edition, send it to their made-up subscribers, run the queue by hand as
administrators, abort a send, and read what was sent. Every mail is a file in `var/log/mail` and every address is at
`example.invalid`; the trainer checks both before each lesson.

## At a glance

| | |
|---|---|
| Course | [Newsletters with CJW Newsletter](../README.md) |
| Duration | 3 hours 15 minutes |
| Lessons | 4 |
| Level | Beginner to intermediate |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [4.1 Previews and test sends](previews-and-test-sends.md) | 45 minutes | Preview an edition, send a test, read the test file |
| [4.2 Scheduling and sending an edition](scheduling-and-sending-an-edition.md) | 60 minutes | Schedule a send, watch the queue, count the mail files |
| [4.3 Cronjobs and console commands](cronjobs-and-console-commands.md) | 45 minutes | Run the queue with a dry run first, read `status`, write crontab lines |
| [4.4 Aborting a send, the archive and statistics](aborting-the-archive-and-statistics.md) | 45 minutes | Abort a send, read the archive and a report |

## Preparing this module

- [ ] On the day, in the settings viewer: `TransportMethodCronjob`, `TransportMethodPreview` and
      `TransportMethodDirectly` are `file`; `site.ini [MailSettings] Transport` is `file`.
- [ ] Every subscriber, test receiver and test group address in the database is at `example.invalid`. Search the
      **Users** page for anything else and remove or blacklist it before class.
- [ ] No cron runs the newsletter parts on the shared training installation during the module (the trainer starts the
      queue), so the exercises happen when the trainer says.
- [ ] Shell access for administrators (lesson 4.3), as the web server user.
- [ ] Optional for lesson 4.4: a send on a prepared installation with tracking on and some recorded opens and clicks,
      to show a report with numbers.

## Timing plan

| Time | Activity |
|---|---|
| 0:00 to 0:45 | Lesson 4.1 |
| 0:45 to 1:45 | Lesson 4.2 |
| 1:45 to 2:30 | Lesson 4.3 (editors who do not use the shell follow on the projector) |
| 2:30 to 3:15 | Lesson 4.4 |

Plan a break of 15 minutes after lesson 4.2 on top of this.

## Related pages

- [Newsletters with CJW Newsletter: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
