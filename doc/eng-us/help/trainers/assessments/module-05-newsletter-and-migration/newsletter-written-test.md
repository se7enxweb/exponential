---
title: "Lesson 5.1: Newsletters, written test"
audience: trainer, editor, admin
category: training
order: 51
summary: A thirteen-question written test for the end of the Newsletters with CJW Newsletter course, on systems, lists and editions, double opt-in, subscription states, sending by cron, the newsletter's own mail transports, bounces and the blacklist, with an answer key that cites the documentation. 25 minutes for learners.
keywords: assessments and practical exercises, newsletters, cjw_newsletter, written test, newsletter system, list, edition, double opt-in, subscription status, mail queue, cronjob, TransportMethodCronjob, TransportMethodPreview, file transport, bounces, blacklist
type: help
---
# Lesson 5.1: Newsletters, written test

This test closes the newsletter course. Its most important questions are about mail that leaves the building: which
of the extension's three transports sends what, why a training installation writes every mail to files, and what cron
does between "send" and the inbox.

> **Note**
> This is the trainer's own assessment, not an official certification. No question needs a mail to be sent.

## At a glance

| | |
|---|---|
| Course | [Assessments and practical exercises](../README.md) |
| Module | [Module 5: Assessing the newsletter and migration courses](README.md) |
| Duration | 40 minutes (5 briefing, 25 test, 10 going through the answers) |
| Level | Intermediate |
| Audience | Learners at the end of the Newsletters with CJW Newsletter course |
| Prerequisites | The newsletter course; [Lesson 1.2](../module-01-designing-assessments/writing-written-tests.md) for the trainer |

## Learning objectives

Learners who pass this test have shown that they can:

- explain systems, lists and editions and where they live;
- describe double opt-in and the subscription states;
- explain the two cron steps of sending;
- name the three newsletter transports and their shipped values;
- explain how bounces and the blacklist keep lists clean.

## Trainer's notes

### How to run it

1. Hand out the questions without the key; 25 minutes.
2. Go through the answers with [cjw_newsletter](../../../../features/6.0/extensions/cjw_newsletter.md) and
   [10.5](../../../../install/10-after-installing.md#newsletters-cjw_newsletter) on the projector.

### Common questions

**Our release is older than 4.2.0.** The dashboard and some features differ; questions 1 to 11 hold for the releases
documented in [cjw_newsletter](../../../../features/6.0/extensions/cjw_newsletter.md).

### Pitfalls

- **Question 9.** Many learners assume every transport is `file` on a fresh installation. The test send transport is
  `sendmail` as shipped; that is the reason the question exists.

## The test

1. Newsletter systems, lists and editions: are they settings, database tables of their own, or content?
2. Which setting names the node whose children are the newsletter systems?
3. What is double opt-in?
4. Name four states a subscription can have.
5. An editor schedules an edition. Which two cron steps follow, and what does each do?
6. Which cron part collects bounce mails, and is it part of `cjw_newsletter`?
7. After how many bounces, by default, does a user stop receiving newsletters?
8. What is the blacklist for?
9. Name the three newsletter transports and their shipped values.
10. Where does the file transport write, and what must be true of that directory?
11. You want to rehearse subscribe, confirm, send and unsubscribe without any real mail. Which setting values do you
    use?
12. Which command shows the extension's state from a shell?
13. Why do training installations use test subscribers at `example.invalid`?

<details>
<summary>Answers</summary>

1. Content, of the classes `cjw_newsletter_system`, `_list` and `_edition`, edited like any other content
   ([cjw_newsletter](../../../../features/6.0/extensions/cjw_newsletter.md#the-parts)).
2. `[NewsletterSettings] RootFolderNodeId` in `cjw_newsletter.ini`
   ([cjw_newsletter](../../../../features/6.0/extensions/cjw_newsletter.md#settings-you-will-use)).
3. A subscriber confirms the subscription from a mail before receiving newsletters
   ([cjw_newsletter](../../../../features/6.0/extensions/cjw_newsletter.md),
   [10.5](../../../../install/10-after-installing.md#newsletters-cjw_newsletter)).
4. Four of: pending, confirmed, approved, removed by the user, removed by an administrator, soft bounced, hard
   bounced, blacklisted ([cjw_newsletter](../../../../features/6.0/extensions/cjw_newsletter.md#the-parts)).
5. `cjw_newsletter_mailqueue_create` builds the mail queue of the editions due; `cjw_newsletter_mailqueue_process`
   sends what waits in the queue; the part `cjw_newsletter` runs both
   ([10.5](../../../../install/10-after-installing.md#newsletters-cjw_newsletter)).
6. `cjw_newsletter_mailbox`; it is not part of `cjw_newsletter`
   ([10.5](../../../../install/10-after-installing.md#newsletters-cjw_newsletter)).
7. 3, `[BounceSettings] BounceThresholdValue`
   ([cjw_newsletter](../../../../features/6.0/extensions/cjw_newsletter.md#settings-you-will-use)).
8. Addresses that never get mail
   ([cjw_newsletter](../../../../features/6.0/extensions/cjw_newsletter.md#the-parts)).
9. `TransportMethodCronjob` `file` (editions), `TransportMethodPreview` `sendmail` (test sends),
   `TransportMethodDirectly` `file` (subscription confirmations and info mails)
   ([10.5](../../../../install/10-after-installing.md#newsletters-cjw_newsletter)).
10. `FileTransportMailDir`, `var/log/mail` by default, one `.eml` file per mail; it must be writable by the web server
    user and by the cron user
    ([cjw_newsletter](../../../../features/6.0/extensions/cjw_newsletter.md#rehearse-the-whole-thing-without-sending-a-single-mail)).
11. All three `TransportMethod*` set to `file`, in `settings/override/cjw_newsletter.ini.append.php`
    ([cjw_newsletter](../../../../features/6.0/extensions/cjw_newsletter.md#rehearse-the-whole-thing-without-sending-a-single-mail)).
12. `./console ext:cjw_newsletter:status`
    ([cjw_newsletter](../../../../features/6.0/extensions/cjw_newsletter.md#dashboard-commands-and-background-runs-4117)).
13. The `.invalid` top-level domain never delivers, so even a misconfigured transport cannot reach a real person.

</details>

## Scoring

- One point per question; question 5 needs both steps, question 9 all three transports with their values.
- Pass: 9 of 13 points or more, with questions 9 and 11 correct: both decide whether real mail leaves a training or
  test installation.

## Further reading

- [cjw_newsletter: newsletters in the admin](../../../../features/6.0/extensions/cjw_newsletter.md)
- [10.4 Mail](../../../../install/10-after-installing.md#104-mail-the-file-transport-and-real-sending) and
  [10.5 Newsletters](../../../../install/10-after-installing.md#newsletters-cjw_newsletter)
- [Mail consent and bounces](../../../hosting/operations/mail-consent-and-bounces.md)

## Related pages

- [Lesson 5.2: Newsletters, practical task](newsletter-practical-task.md)
- [Module 5: Assessing the newsletter and migration courses](README.md)
- [Assessments and practical exercises: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
