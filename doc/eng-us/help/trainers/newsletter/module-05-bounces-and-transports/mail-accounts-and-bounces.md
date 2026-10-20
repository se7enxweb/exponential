---
title: "Lesson 5.1: Mail accounts and bounces"
audience: trainer, admin
category: training
order: 51
summary: Administrators explain how returned mail reaches the newsletter, read the mail account form without connecting a real mailbox, follow the bounce pages and the mailbox command, and explain hard and soft bounces and the bounce threshold. 45 minutes.
keywords: newsletters with cjw newsletter, bounce, mail account, mailbox, return-path, collect all mails, parse mails, BounceThresholdValue, hard bounce, soft bounce, cjw_newsletter_mailbox
type: help
---
# Lesson 5.1: Mail accounts and bounces

Some mails come back: the address does not exist, the mailbox is full, the server refuses. A newsletter that ignores
those returns keeps mailing dead addresses, and mail providers take that as a sign of a careless sender. This lesson
shows how returned mail reaches CJW Newsletter and what it does with it.

> **Note**
> In class, nobody connects a real mailbox: the mail account form is read and discarded. A training installation that
> writes mail to files produces no real bounces; the trainer shows the pages and explains the flow.

## At a glance

| | |
|---|---|
| Course | [Newsletters with CJW Newsletter](../README.md) |
| Module | [Module 5: Bounces and transports](README.md) |
| Duration | 45 minutes (25 presenting, 20 exercise) |
| Level | Intermediate |
| Audience | Administrators |
| Prerequisites | [Module 4](../module-04-sending/README.md); shell access for step 7 |

## Learning objectives

After this lesson, learners can:

- explain the way a bounce takes: return-path, mailbox, the mailbox run, the user's bounce count;
- name the fields of a newsletter mail account;
- tell a hard bounce from a soft bounce and say what happens to each;
- explain `BounceThresholdValue` and find bounced users;
- run the mailbox command as a dry run.

## Trainer's notes

### How to present it

Sources: [Bounces and the blacklist](../../../creators/website-creators/newsletter-lists-and-subscriptions.md#bounces-and-the-blacklist),
[Cronjob parts](../../../../specifications/6.0/cjw_newsletter.md#cronjob-parts) and the deliverability guide of the
extension (linked from the [feature page](../../../../features/6.0/extensions/cjw_newsletter.md#the-features-of-420)).

1. **The way back** (5 minutes). A returned mail goes to the list's **Email return-path** (lesson 2.2). That mailbox
   is read either by a newsletter **mail account** (the part `cjw_newsletter_mailbox`, the command
   `ext:cjw_newsletter:mailbox`, the dashboard's **Process the mail accounts**) or by the bounce reader of the kernel's
   e-mail preferences, which hands every message to the newsletter too. The newsletter's own headers
   (`X-Cjwnl-Senditem`, `X-Cjwnl-User`) say which mail and which person bounced.
2. **Mail accounts** (5 minutes). **Mail accounts** in the left menu, then **Add mail account**: **Email**,
   **Server**, **Port**, **User**, **Password**, **Type**, **SSL**, **Delete mails from server**, **Active**, and
   **Store Changes** or **Discard**. The form checks its fields and never shows the stored password.
3. **The bounce pages** (5 minutes). **Bounces** in the left menu lists the collected mails, with **Collect all
   mails** and **Parse mails** (background runs). Each mail can be opened.
4. **Hard and soft** (6 minutes). Hard (the address or domain does not exist) and complaints put the address on the
   suppression list and the newsletter blacklist (4.2 default `SuppressHardBounces=enabled`). Soft (full mailbox,
   server down) raises the user's bounce count and the mail is tried again later. After
   `[BounceSettings] BounceThresholdValue` bounces (3 by default) a user stops receiving.
5. **Cron** (2 minutes). The part `cjw_newsletter_mailbox` is not in `cjw_newsletter`; it needs its own crontab line.
6. **Dashboard** (2 minutes). "No active mail account", "bounced users" and "unparsed mails" are reported as problems.

### Common questions

**Do I need a separate mailbox?** A dedicated address for returns is usual, so bounces are not mixed with replies.
Your mail provider sets it up.

**Why did a soft-bounced user still get the next edition?** A soft bounce only counts; the user stops receiving after
the threshold. Retries of the same mail follow `[DeliverabilitySettings]`.

**What if a person's address bounced by mistake?** Lift the suppression in Setup, which also takes the address off the
newsletter blacklist (deliverability guide).

### Pitfalls

- **Testing with a real mailbox in class.** It risks reading real mail. Use a dedicated test mailbox on a test
  installation, outside the course.
- **Delete mails from server on the first try.** On a real setup, leave it off until parsing works, so nothing is lost.
- **Forgetting the cron line.** Without `cjw_newsletter_mailbox` (or the kernel's bounce reader) nothing is ever read.

## Hands-on exercise

Use the training installation. Do not store a mail account.

1. Open **Newsletter**, press **Mail accounts** in the left menu, and note the number in the heading.
2. Press **Add mail account**. Read each field: **Email**, **Server**, **Port**, **User**, **Password**, **Type**,
   **SSL**, **Delete mails from server**, **Active**.
3. Write on paper which value each field would get for your organization's return mailbox (no passwords on paper:
   write "from the mail provider").
4. Press **Discard**.
5. Press **Bounces** in the left menu. Read the heading and the buttons **Collect all mails** and **Parse mails**. Do
   not press them.
6. Press **Users**, set **Status** to *Bounced* and search. Note the **Bounce** column.
7. On your own training installation, run the mailbox command as a dry run and read what it reports:

   ```bash
   php bin/php/console ext:cjw_newsletter:mailbox --dry-run
   ```

8. In **INI Settings**, find `BounceThresholdValue` under `[BounceSettings]` and `SuppressHardBounces` under
   `[DeliverabilitySettings]`.
9. Draw the way of a hard bounce from the subscriber's mail server to the blacklist, and compare with your neighbor.

## Expected result

- No mail account was stored; the heading of **Mail accounts** shows the same number as in step 1.
- Each learner has a filled-in paper plan of the return mailbox and a drawing of a hard bounce's way.
- The dry run reported that there was nothing (or which accounts there were) to read, and changed nothing.
- The trainer checks the drawings against the deliverability guide.

![The mail accounts the bounce handling reads](../../../images/trainers/nl-mail-accounts.webp)

## Quiz

1. Which list address do mail servers send bounces to?
2. Which cronjob part reads the bounce mail accounts?
   A. `cjw_newsletter` B. `cjw_newsletter_mailqueue_process` C. `cjw_newsletter_mailbox` D. `notification`
3. What happens to an address after a hard bounce with the shipped 4.2 settings?
4. What is the default of `BounceThresholdValue`, and what happens when a user reaches it?
5. Why should **Delete mails from server** stay off on a first setup?

<details>
<summary>Answers</summary>

1. The return-path (lesson 2.2; [feature page](../../../../features/6.0/extensions/cjw_newsletter.md#the-parts)).
2. **C.** ([Cronjob parts](../../../../specifications/6.0/cjw_newsletter.md#cronjob-parts)).
3. It goes on the suppression list and the newsletter blacklist (deliverability guide, linked from the
   [feature page](../../../../features/6.0/extensions/cjw_newsletter.md#the-features-of-420)).
4. 3; the user stops receiving ([Bounces and the blacklist](../../../creators/website-creators/newsletter-lists-and-subscriptions.md#bounces-and-the-blacklist)).
5. So that no returned mail is lost while parsing is not yet proven to work.

</details>

## Further reading

- [Newsletter lists and subscriptions](../../../creators/website-creators/newsletter-lists-and-subscriptions.md#bounces-and-the-blacklist):
  bounces and the blacklist.
- [E-mail preferences: setting them up and running them](../../../../guides/mail-preferences-administrator.md): the
  kernel's bounce reader and suppression list.
- [Specification: the cjw_newsletter extension](../../../../specifications/6.0/cjw_newsletter.md#console-commands):
  the `mailbox` command.

## Related pages

- [Module 5: Bounces and transports](README.md)
- [Newsletters with CJW Newsletter: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
