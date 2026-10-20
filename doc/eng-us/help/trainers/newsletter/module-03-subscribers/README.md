---
title: "Module 3: Subscribers"
audience: trainer, editor, admin
category: training
order: 30
summary: The people side of the newsletter - the subscribe form and double opt-in, users and subscriptions, CSV import and export, consent, unsubscribing and the blacklist. 3 hours 15 minutes.
keywords: newsletters with cjw newsletter, subscribers, subscribe, double opt-in, users, csv import, export, unsubscribe, blacklist, consent
type: help
---
# Module 3: Subscribers

This module is about people: how they subscribe, how administrators find and manage them, how lists are imported
and exported, and how people leave. Every subscriber in the module is made up, with an address at `example.invalid`,
and every confirmation mail is a file.

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
| [3.1 The subscribe form and double opt-in](the-subscribe-form-and-double-opt-in.md) | 60 minutes | Subscribe on the public form, confirm through the link in the mail file |
| [3.2 Managing users and subscriptions](managing-users-and-subscriptions.md) | 45 minutes | Filter users, create one by hand, approve a pending subscription |
| [3.3 CSV import and export](csv-import-and-export.md) | 45 minutes | Import a made-up file with mapping, preview and dry run; export the list |
| [3.4 Consent, unsubscribing and the blacklist](consent-unsubscribing-and-the-blacklist.md) | 45 minutes | Unsubscribe on the public page, add a blacklist entry |

## Preparing this module

- [ ] All transports write files (lesson 1.2), checked again on the day.
- [ ] Learners can read `var/log/mail`: shell access, or a way for the trainer to show the files on the projector.
- [ ] The public subscribe form shows the learners' lists (the lists allow subscribing in the public siteaccess).
- [ ] Anonymous users have the `newsletter/subscribe`, `configure` and `unsubscribe` policies on the public siteaccess.
- [ ] A text editor on each learner's machine for the CSV file of lesson 3.3.
- [ ] The rule announced: only addresses at `example.invalid`, no real subscriber files.

## Timing plan

| Time | Activity |
|---|---|
| 0:00 to 1:00 | Lesson 3.1 |
| 1:00 to 1:45 | Lesson 3.2 |
| 1:45 to 2:30 | Lesson 3.3 |
| 2:30 to 3:15 | Lesson 3.4 |

Plan a break of 15 minutes after lesson 3.2 on top of this.

## Related pages

- [Newsletters with CJW Newsletter: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
