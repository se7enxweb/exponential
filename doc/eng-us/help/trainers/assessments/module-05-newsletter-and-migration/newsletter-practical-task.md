---
title: "Lesson 5.2: Newsletters, practical task"
audience: trainer, editor, admin
category: training
order: 52
summary: A 60-minute practical task for the end of the newsletter course, set every newsletter transport to files on a training installation, subscribe two test subscribers at example.invalid with double opt-in, write and send an edition through the cron parts, read the resulting mail files, unsubscribe one subscriber and blacklist an address, scored with a six-criterion rubric. 85 minutes with scoring.
keywords: assessments and practical exercises, newsletters, cjw_newsletter, practical task, rubric, file transport, TransportMethodPreview, example.invalid, double opt-in, edition, mail queue, runcronjobs, eml files, unsubscribe, blacklist
type: help
---
# Lesson 5.2: Newsletters, practical task

The learner runs one complete newsletter cycle, from the first subscription to the unsubscribe, and proves each step
with the files the installation wrote. Nothing is sent: every transport writes to files, and every subscriber has an
address at `example.invalid`.

> **Note**
> This is the trainer's own assessment, not an official certification. **Never send mail in this task.** Use a
> training installation whose newsletter transports are all set to `file` (step 1 checks it), and only test
> subscribers at `example.invalid`. The test send transport, `TransportMethodPreview`, is `sendmail` as shipped: it must
> be changed before anyone presses a test send.

## At a glance

| | |
|---|---|
| Course | [Assessments and practical exercises](../README.md) |
| Module | [Module 5: Assessing the newsletter and migration courses](README.md) |
| Duration | 85 minutes (5 briefing, 60 task, 20 scoring) |
| Level | Intermediate |
| Audience | Learners at the end of the Newsletters with CJW Newsletter course |
| Prerequisites | [Lesson 5.1](newsletter-written-test.md); an administrator account and a shell on the learner's own training installation with `cjw_newsletter` active and its newsletter tree installed |

## Learning objectives

Learners who pass this task have shown that they can:

- check and set the newsletter transports so that no mail leaves the installation;
- subscribe test users and complete the double opt-in from the mail files;
- create an edition and send it through the two cron steps;
- read the resulting `.eml` files and the dashboard;
- unsubscribe a subscriber and blacklist an address.

## Trainer's notes

### How to run it

1. **Before**: one training installation per learner with the newsletter extension active and a newsletter system
   with one list, the site's mail transport `Transport=file`, no crontab. Set all three `TransportMethod*` to `file`
   yourself as well; step 1 is the learner's check, not the only safeguard.
2. **Briefing**: read the note above aloud. Hand out the steps and the rubric; 60 minutes.
3. **Scoring**: read the learner's notes, list `var/log/mail/` of the installation, open the newsletter dashboard
   (**Newsletter**, `/newsletter/index`) and the list's subscriptions.

### Common questions

**Where is the confirmation link?** In the `.eml` file of the confirmation mail in `FileTransportMailDir`. Learners
open the file in a text editor and copy the link into the browser.

**The cron run sent nothing.** The edition was not scheduled, or the queue was built but not processed. That is
exactly what the task assesses; let the learner find it.

### Pitfalls

- **A learner presses a test send before step 1.** If `TransportMethodPreview` was still `sendmail`, stop the task,
  check the installation's mail log, and treat it as an incident. This is why the trainer sets the values beforehand.
- **Real addresses.** Any address that is not at `example.invalid` scores 0 on **Safety**.

## Hands-on exercise

The task, as handed to learners. Use your own training installation. Every address you type ends in
`@example.invalid`.

1. Check the newsletter transports:

   ```bash
   ./console exp:ini get cjw_newsletter.ini/NewsletterMailSettings/TransportMethodCronjob --allow-root-user
   ./console exp:ini get cjw_newsletter.ini/NewsletterMailSettings/TransportMethodPreview --allow-root-user
   ./console exp:ini get cjw_newsletter.ini/NewsletterMailSettings/TransportMethodDirectly --allow-root-user
   ```

   If any is not `file`, set it in `settings/override/cjw_newsletter.ini.append.php` and clear the INI cache. Do not
   continue until all three print `file`. Note `FileTransportMailDir`.
2. On the public site, open the subscription form (`/newsletter/subscribe`) and subscribe `reader01@example.invalid`
   to the list.
3. Find the confirmation mail as a file in the mail directory. Open it in a text editor, copy the confirmation link
   and open it. Note the subscription's state before and after.
4. Subscribe `reader02@example.invalid` the same way and confirm it.
5. In the administration, open **Newsletter**, find the list and use **Create newsletter here** to write an edition:
   a subject with your name and two short paragraphs. Publish it.
6. Make a test send of the edition to `reader01@example.invalid`, and check that it appears as a file.
7. Schedule the edition for sending to the list.
8. Run the two cron steps by hand and note the output of each:

   ```bash
   php runcronjobs.php -s <siteaccess> cjw_newsletter_mailqueue_create
   php runcronjobs.php -s <siteaccess> cjw_newsletter_mailqueue_process
   ```

9. List the mail directory and open one edition file. Note the subject line and the recipient.
10. Unsubscribe `reader02@example.invalid` with the unsubscribe link in its edition file. Check its state.
11. Add `blocked01@example.invalid` to the blacklist (**Blacklists**), then try to subscribe it on the public form.
    Note what happens.
12. Run `./console ext:cjw_newsletter:status` and copy its summary into your notes.

## Expected result

- All three transports print `file`; the mail directory holds the confirmation mails, the test send and one edition
  file per confirmed subscriber.
- `reader01` is confirmed; `reader02` is removed by the user; `blocked01` is on the blacklist and receives nothing.
- The edition is listed under **Last sends** on the newsletter dashboard.
- The trainer checks the directory listing, the subscriptions of the list and the notes.

## Scoring rubric

| Criterion | 0: Not shown | 1: Partly | 2: Mostly | 3: Fully | Evidence |
|---|---|---|---|---|---|
| Safety (must) | A transport not `file`, or a real address used | Checked, one value left | All `file`, not proven | All three checked and `file`, only `example.invalid` addresses | Notes, `exp:ini get` output |
| Double opt-in | No subscription | Subscribed, not confirmed | One confirmed | Both confirmed from the mail files, states noted | Notes, the list's subscriptions |
| Edition | None | Created, not published | Published, test send missing | Published, test send as a file | The edition, the mail directory |
| Sending by cron | Not sent | One step run | Both run, result not checked | Both run, edition files read | Cron output, the mail directory |
| Unsubscribe and blacklist | Neither | One | Both, effect not checked | Both, states and refusal noted | The list's subscriptions, **Blacklists** |
| Reading the state | Nothing | Dashboard or status only, not explained | Both, partly explained | Status summary and dashboard explained | Notes |

**Pass**: 12 of 18 points or more and **Safety** at 3. In a newsletter course, one real mail sent by mistake is a
real incident; there is no partial credit for it.

## Quiz

Debrief questions to ask after scoring.

1. Which of the three transports was the dangerous one on a fresh installation, and why?
2. What did the first cron step do, and the second?
3. Why could you complete the double opt-in without any mail server?
4. What would you change to send this list for real, and what must be in place first?

<details>
<summary>Answers</summary>

1. `TransportMethodPreview`, `sendmail` as shipped: a test send would leave the machine
   ([10.5](../../../../install/10-after-installing.md#newsletters-cjw_newsletter)).
2. The first built the mail queue of the editions due; the second sent what waited in the queue
   ([10.5](../../../../install/10-after-installing.md#newsletters-cjw_newsletter)).
3. The file transport wrote the confirmation mail as a file with the link in it
   ([cjw_newsletter](../../../../features/6.0/extensions/cjw_newsletter.md#rehearse-the-whole-thing-without-sending-a-single-mail)).
4. The transports set to `smtp` or `sendmail`, with a sender of a domain you control and SPF records for every address
   family the server sends from, consent records, and bounce handling
   ([10.4](../../../../install/10-after-installing.md#104-mail-the-file-transport-and-real-sending),
   [Mail consent and bounces](../../../hosting/operations/mail-consent-and-bounces.md)).

</details>

## Further reading

- [cjw_newsletter: newsletters in the admin](../../../../features/6.0/extensions/cjw_newsletter.md): the rehearsal
  without sending a single mail.
- [Running an assessment session](../module-01-designing-assessments/running-an-assessment-session.md): preparing
  transports before the session.

## Related pages

- [Lesson 5.1: Newsletters, written test](newsletter-written-test.md)
- [Module 5: Assessing the newsletter and migration courses](README.md)
- [Assessments and practical exercises: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
