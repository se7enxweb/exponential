---
title: "Trainer toolkit"
audience: trainer
category: training
order: 7
summary: The checklists and templates a trainer reuses for every course, before, on the day and after, a lab sheet, a learner pre-work message, a handout skeleton, a question board, a feedback slip and a follow-up message.
keywords: toolkit, checklists, templates, pre-course, day of the course, after the course, lab sheet, handout, feedback slip, follow-up, trainer resources
type: help
---
# Trainer toolkit

Every course needs the same small set of documents. This page collects them as checklists and templates you can copy.
Each one comes from a lesson of [Train the trainer](train-the-trainer/README.md), which explains the reasons.

## Checklist: two weeks before

- [ ] Course and modules chosen for the group ([Lesson 1.3](train-the-trainer/module-01-preparing-to-teach/knowing-your-learners.md))
- [ ] Pre-course survey sent, including the question about arrangements ([Lesson 5.4](train-the-trainer/module-05-accessibility-and-inclusion/accommodations-and-inclusive-practice.md))
- [ ] Release chosen; lab design chosen: one installation per learner or a shared site ([Lesson 2.7](train-the-trainer/module-02-building-the-lab/shared-server-or-one-per-learner.md))
- [ ] Every exercise rehearsed on that release ([Lesson 3.4](train-the-trainer/module-03-designing-a-course/writing-exercises-that-work.md))
- [ ] Machine requirements sent to learners ([Lesson 2.1](train-the-trainer/module-02-building-the-lab/requirements-for-a-training-machine.md))
- [ ] Handouts accessible and correctly licensed ([Lesson 5.1](train-the-trainer/module-05-accessibility-and-inclusion/accessible-course-materials.md), [Lesson 1.4](train-the-trainer/module-01-preparing-to-teach/your-material-and-the-gfdl.md))

## Checklist: the day before

- [ ] Labs installed or the install tested on each machine type
- [ ] Learner accounts created with addresses at `example.invalid` ([Lesson 2.5](train-the-trainer/module-02-building-the-lab/learner-accounts-and-roles.md))
- [ ] Demo browser profile without saved logins; notifications off ([Lesson 4.2](train-the-trainer/module-04-delivering/demonstrating-safely.md))
- [ ] Lab quick card printed for helpers ([Lab quick card](lab-quick-card.md))
- [ ] Question board ready

## Checklist: on the day

- [ ] First hour: everyone signed in, own password, Help center found ([Lesson 4.1](train-the-trainer/module-04-delivering/running-the-first-hour.md))
- [ ] A break at least every hour; buffers after installs ([Lesson 3.3](train-the-trainer/module-03-designing-a-course/timing-and-pacing.md))
- [ ] Expected results checked on every screen ([Lesson 6.1](train-the-trainer/module-06-assessment-and-follow-up/checking-understanding-during-class.md))
- [ ] Feedback slips collected at the end ([Lesson 6.3](train-the-trainer/module-06-assessment-and-follow-up/collecting-feedback.md))

## Checklist: after the course

- [ ] Follow-up message sent with sourced answers ([Lesson 6.5](train-the-trainer/module-06-assessment-and-follow-up/after-the-course.md))
- [ ] Servers stopped, learner accounts removed, lab directories deleted
- [ ] Survey answers about arrangements deleted
- [ ] One course change and one help fix or report from the feedback

## Template: lab sheet

```text
COURSE: <name>                          DATES: <dates>               TRAINER: <name>
Release:            v6.0.<n>            (git describe --tags)
Site package:       sevenx_multisite | sevenx_multisite_clean
Lab design:         one installation per learner | shared site <address>
Install command:    php bin/php/console exp:install --apply --url=http://127.0.0.1:8087 ...
Serve command:      php bin/php/console exp:velocity start
Learner accounts:   learner01 .. learnerNN @example.invalid, group <group>, role <role>
Reset routine:      <golden copy | reinstall>, takes <minutes>
Helpers:            <names>, one per 8 to 10 learners
```

## Template: pre-work message to learners

```text
Subject: Preparing for "<course>" on <date>

Hello,

To start on time, please check before the course:
  1. PHP 8.1 or later:   php -v
  2. These PHP extensions are listed by php -m: intl, xsl, mbstring, iconv, dom, zlib, sqlite3, gd
  3. Composer and Git:   composer --version ; git --version
If anything is missing, reply to this message and we will help, or use a prepared machine.

Do you need any arrangements to take part (materials in another format, breaks, captions)?
You do not need to say why.

The course follows the Exponential help. You can look at it in advance in any installation:
the "?" in the header, then "Open the Help center".
```

## Template: handout skeleton

```text
<COURSE NAME>: <MODULE NAME>                                          <date>, <place>

Today you will be able to:      <the module's objectives>
Exercises:                      <lesson> -> <help page title>
Commands:                       <only those this module uses>
Help:                           the "?" in the header; Help center: /exphelp/dashboard
Ask:                            <your contact>; share.exponential.earth/forums
Report a bug:                   Help > Report an error (never passwords or personal data)

Based on the Exponential documentation, free documentation under the GNU FDL 1.3 or later.
<your changes and your name, if you adapted pages>
```

## Template: question board

| # | Question (first name only) | Parked at | Answer with source | Sent |
|---|---|---|---|---|
| 1 | | | | |

## Template: feedback slip

```text
1. What worked well today?
2. What was hard?
3. What would you like to learn next?
(No name needed.)
```

## Related pages

- [Trainers: start here](trainers-start-here.md)
- [Lab quick card](lab-quick-card.md)
- [Train the trainer: course overview](train-the-trainer/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
