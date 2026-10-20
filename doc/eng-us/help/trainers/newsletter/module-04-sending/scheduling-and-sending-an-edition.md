---
title: "Lesson 4.2: Scheduling and sending an edition"
audience: trainer, editor, admin
category: training
order: 42
summary: Learners schedule an edition on the send page (date, skin, tracking), let the queue run, and find one mail file per subscriber in the outbox of the training installation. 60 minutes.
keywords: newsletters with cjw newsletter, send newsletter, send out newsletter, schedule, skin of this send, tracking of this send, mail queue, send now, outbox, file transport
type: help
---
# Lesson 4.2: Scheduling and sending an edition

This is the moment the course has prepared for. Learners send their edition to their test subscribers: they choose
when it goes out and in which skin, confirm, and then watch the queue turn one edition into one mail per subscriber.
On the training installation every one of those mails is a file.

> **Note**
> This lesson sends. It is safe only because every transport of the training installation writes files and every
> subscriber has an address at `example.invalid` (lesson 1.2, checked again in step 1). Never run this exercise on an
> installation where you have not checked both.

## At a glance

| | |
|---|---|
| Course | [Newsletters with CJW Newsletter](../README.md) |
| Module | [Module 4: Sending](README.md) |
| Duration | 60 minutes (20 presenting, 40 exercise) |
| Level | Intermediate |
| Audience | Editors who send, administrators who support them |
| Prerequisites | [Lesson 4.1](previews-and-test-sends.md): a tested edition; subscribers from module 3 |

## Learning objectives

After this lesson, learners can:

- open the send page of an edition and fill in the send time, the skin and the tracking of the send;
- confirm a send and explain what happens next (queue, process, archive);
- follow the progress in **Newsletter processing info** and on the dashboard's **Last sends**;
- find one mail file per confirmed or approved subscriber, and none for the others.

## Trainer's notes

### How to present it

Sources: [How a send runs](../../../creators/website-creators/sending-newsletters.md#how-a-send-runs),
[Before every send](../../../creators/website-creators/sending-newsletters.md#before-every-send) and the
[example in the specification](../../../../specifications/6.0/cjw_newsletter.md#example-send-a-newsletter-from-the-shell-and-look-at-the-result).

1. **The checklist** (4 minutes). Read [Before every send](../../../creators/website-creators/sending-newsletters.md#before-every-send)
   aloud: test checked, subject and sender right, links work, images have alternative texts, the right list, a good
   time. A send cannot be called back.
2. **The send page** (6 minutes). **Send Newsletter** on the edition opens the page `Send out newsletter - <title>`.
   **Year**, **Month**, **Day**, **Hour**, **Minute** set when the send may start. **Skin of this send** offers the
   skins the list allows. **Tracking of this send** (*As the list says*, *Off*, *Anonymous totals*, *Per person with
   consent*) stays *Off* in this course. **A/B subject test** is shown in lesson 4.4. The page also links
   **Preview the skins** and **Preview as a subscriber**.
3. **What happens next** (5 minutes). The send is created and the edition is rendered once in the skin of the send.
   When its time has come, `cjw_newsletter_mailqueue_create` makes one queue item per receiving subscriber, and
   `cjw_newsletter_mailqueue_process` sends them through `TransportMethodCronjob`. On the dashboard, **Send now** does
   the same work in the background. The edition is then archived.
4. **Who receives** (5 minutes). Confirmed and approved subscribers only. Pending, removed, bounced and blacklisted
   ones get nothing. Ask the room to predict the number of files before the run.

### Common questions

**Can I change the edition after pressing Send Newsletter?** The send keeps the version rendered at that moment.
Abort the send (lesson 4.4) and send again if the content was wrong.

**Why does nothing happen after I confirm?** Cron has not run yet. In class, the trainer presses **Send now** or runs
the queue (lesson 4.3).

**What if the list requires approval?** The send form refuses an edition version that is not approved (lesson 2.4).

**How long does a big list take?** It depends on the transport and the rate limits (lesson 5.3). A training list of
six people takes seconds.

### Pitfalls

- **Learners press Send now.** Agree that only the trainer starts the queue, once everybody has confirmed their send.
- **Time in the past or the future.** A send time in the future waits; learners think it failed. Keep the default
  time for the exercise.
- **Counting files wrongly.** Confirmation and test files from earlier lessons are in the same folder. Use the time
  stamps.

## Hands-on exercise

Use the training installation. Replace `<name>` with your first name.

1. Open **INI Settings** and check that `TransportMethodCronjob` reads `file`. Check that every subscriber of your
   list is at `example.invalid`.
2. Write down who should receive: from module 3, your list has Anna (removed), Ben (approved), Carla, Dora, Emil
   (blacklisted), Fay. Predict the number of mails.
3. Open your edition `Tips for week 2` and press **Send Newsletter**.
4. On *Send out newsletter*, leave **Year**, **Month**, **Day**, **Hour** and **Minute** as offered.
5. **Skin of this send**: keep the list's skin. **Tracking of this send**: choose **Off**. Leave **A/B subject test**
   unticked.
6. Press **Send Newsletter** and confirm the question whether you really want to send out this newsletter.
7. On the edition's page, read **Newsletter processing info** and **Cronjob status**.
8. Wait for the trainer: the trainer presses **Send now** on the dashboard once for the whole class.
9. Reload the edition's page and read **Statistics**: **Emails count**, **Emails sent**, **Emails not sent**.
10. List the outbox and count the files for your send:

    ```bash
    ls -lt var/log/mail | head -15
    ```

11. Compare with your prediction from step 2.

## Expected result

- The send of `Tips for week 2` is finished; **Emails sent** equals the number of confirmed and approved subscribers.
- The outbox holds one `.eml` file per receiving subscriber, all at `example.invalid`; none for Anna or Emil.
- The dashboard's **Last sends** shows the send with its **Status**, **Mails** and **Failed**.
- The trainer checks by comparing the dashboard numbers with two learners' predictions.

![Scheduled sends of the newsletters](../../../images/trainers/nl-schedule.webp)

## Quiz

1. Which subscribers receive a send?
2. Which transport method carries the editions of a send?
   A. `TransportMethodDirectly` B. `TransportMethodPreview` C. `TransportMethodCronjob` D. the kernel's `Transport`
3. You confirmed the send but nothing was sent. What is the most likely reason?
4. What does **Skin of this send** let you choose?
5. Name three items of the checklist before every send.

<details>
<summary>Answers</summary>

1. Confirmed and approved subscribers ([Statuses](../../../creators/website-creators/newsletter-lists-and-subscriptions.md#statuses)).
2. **C.** ([Sending newsletters safely](../../../creators/website-creators/sending-newsletters.md#the-transports)).
3. The queue has not run yet: cron, or **Send now** on the dashboard ([How a send runs](../../../creators/website-creators/sending-newsletters.md#how-a-send-runs)).
4. One of the skins the list allows; a send in another skin is rendered again in it (rendering guide, linked from the
   [feature page](../../../../features/6.0/extensions/cjw_newsletter.md#the-features-of-420)).
5. Any three of [Before every send](../../../creators/website-creators/sending-newsletters.md#before-every-send).

</details>

## Further reading

- [Sending newsletters safely](../../../creators/website-creators/sending-newsletters.md): the transports, how a
  send runs, the checklist.
- [Specification: the cjw_newsletter extension](../../../../specifications/6.0/cjw_newsletter.md#example-send-a-newsletter-from-the-shell-and-look-at-the-result):
  a send followed in the shell.
- [cjw_newsletter: newsletters in the admin](../../../../features/6.0/extensions/cjw_newsletter.md): the parts of
  sending.

## Related pages

- [Module 4: Sending](README.md)
- [Newsletters with CJW Newsletter: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
