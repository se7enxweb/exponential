---
title: "Lesson 3.1: The subscribe form and double opt-in"
audience: trainer, editor, admin
category: training
order: 31
summary: Learners subscribe a test address at example.invalid on the public form, find the confirmation mail as a file, confirm through its link, and watch the subscription change from pending to confirmed. 60 minutes.
keywords: newsletters with cjw newsletter, subscribe, double opt-in, newsletter/subscribe, newsletter/configure, confirmation mail, pending, confirmed, file transport, example.invalid
type: help
---
# Lesson 3.1: The subscribe form and double opt-in

Visitors subscribe on a public form. A double opt-in means the subscription only counts after the person clicks a
link in a confirmation mail, which proves the address belongs to them. On the training installation that mail is
written to a file, so learners play both roles: the visitor who subscribes and the mailbox that receives.

> **Note**
> This exercise sends a confirmation mail. On the training installation it is written to a file, because
> `TransportMethodDirectly=file` (lesson 1.2). Subscribe only addresses at `example.invalid`. Check the transports
> before you start.

## At a glance

| | |
|---|---|
| Course | [Newsletters with CJW Newsletter](../README.md) |
| Module | [Module 3: Subscribers](README.md) |
| Duration | 60 minutes (15 presenting, 45 exercise) |
| Level | Beginner |
| Audience | Editors and administrators |
| Prerequisites | [Lesson 2.2](../module-02-systems-lists-and-editions/creating-and-configuring-a-list.md): a list; read access to `var/log/mail` (shell or a file viewer the trainer provides) |

## Learning objectives

After this lesson, learners can:

- open the public subscribe form and subscribe a test address to a list;
- explain why a new subscription is *pending* and what makes it *confirmed*;
- find the confirmation mail in the file outbox and read its configure link;
- confirm the subscription and check its new status in the administration.

## Trainer's notes

### How to present it

Sources: [Subscribe forms on the site](../../../creators/website-creators/newsletters-with-cjw-newsletter.md#subscribe-forms-on-the-site),
[Statuses](../../../creators/website-creators/newsletter-lists-and-subscriptions.md#statuses) and the
[feature page](../../../../features/6.0/extensions/cjw_newsletter.md#rehearse-the-whole-thing-without-sending-a-single-mail).

1. **The form** (3 minutes). `newsletter/subscribe` on the public siteaccess. It shows the lists that can be
   subscribed, the fields **E-mail**, **First name**, **Last name** and **Salutation**, and the button
   **Subscribe**. In the administration, **Subscription form** in the newsletter's left menu shows the same view.
   Anonymous visitors need the `newsletter/subscribe` policy.
2. **Pending** (3 minutes). After **Subscribe** the page says a mail was sent and that the subscription is only
   active after clicking the confirmation link. The subscription is now *pending*.
3. **The mail as a file** (5 minutes). The confirmation goes through `TransportMethodDirectly`. With `file`, it is a
   `.eml` file in `var/log/mail`. Open one on the projector: the headers, the text, the configure link
   `.../newsletter/configure/<hash>`.
4. **Confirming** (4 minutes). Opening the link shows the page to configure the newsletter settings, with the lists
   and the fields. **Confirm** confirms the subscription. Back in the administration, the list's **Confirmed**
   count goes up.

### Common questions

**Why not subscribe people directly?** Without a double opt-in anyone could subscribe anyone else. Confirmation is
also your record that the person agreed ([Consent](../../../creators/website-creators/newsletter-lists-and-subscriptions.md#consent)).

**The link in the file starts with `http://` but the site is `https://`.** The confirmation template builds the link
from the host name; most servers redirect. If yours does not, tell the trainer; it is a setup point for the live site.

**What if a subscriber never confirms?** The subscription stays pending and receives nothing. Pending subscriptions
show under **Pending** on the list's page.

**Can someone subscribe twice with the same address?** No; the configure page asks to register another address to
subscribe to the same list twice.

### Pitfalls

- **No list on the form.** The list must allow subscribing and be shown in the siteaccess of the form. Check the
  list's configuration from lesson 2.2.
- **No file appears.** Check the transport in the settings viewer and the permissions of `var/log/mail`.
- **Opening the link as the admin user.** It still works, but learners confuse the two roles. Open the link in a
  private browser window.
- **A real address.** If a learner typed one, stop and check the outbox together: with the file transport nothing
  left the machine.

## Hands-on exercise

Use the training installation (file transport, lesson 1.2). Replace `<name>` with your first name.

1. In a private browser window, open the public site's subscribe form: `/newsletter/subscribe` on the public
   siteaccess (the trainer gives the full address).
2. Tick your list `Weekly tips <name>`.
3. **E-mail**: `anna-<name>@example.invalid`. **First name**: `Anna`. **Last name**: `Test`.
4. Press **Subscribe** and read the message.
5. In the administration, open your list and note the counts of **Pending** and **Confirmed**.
6. List the newest files of the outbox:

   ```bash
   ls -lt var/log/mail | head -5
   ```

7. Open the newest `.eml` file whose name contains `anna-<name>` and find the line with `/newsletter/configure/`.
8. Copy that address into the private browser window and open it.
9. On the page, check that your list is ticked and press **Confirm**.
10. In the administration, reload your list's page and note **Pending** and **Confirmed** again.
11. Repeat steps 1 to 4 with `ben-<name>@example.invalid`, but do not confirm. Keep Ben pending for lesson 3.2.

## Expected result

- The outbox holds one confirmation file for Anna and one for Ben.
- Anna's subscription is confirmed; Ben's is pending.
- On the list page, **Confirmed** shows 1 and **Pending** shows 1.
- The trainer checks by opening the outbox listing and one learner's list page.

## Quiz

1. What status does a subscription have right after **Subscribe**?
2. Which transport method carries the confirmation mail?
   A. `TransportMethodCronjob` B. `TransportMethodPreview` C. `TransportMethodDirectly` D. the kernel's `Transport`
3. Where do you find the confirmation mail on the training installation?
4. Which subscribers receive editions: pending, confirmed, approved?
5. Why is a double opt-in important beyond technical reasons?

<details>
<summary>Answers</summary>

1. Pending ([Statuses](../../../creators/website-creators/newsletter-lists-and-subscriptions.md#statuses)).
2. **C.** Subscription confirmations and info mails ([10.5](../../../../install/10-after-installing.md#105-notifications-and-newsletters)).
3. As an `.eml` file in `var/log/mail` (`FileTransportMailDir`).
4. Confirmed and approved only.
5. It proves the person owns the address and agreed, which is your record of consent
   ([Consent](../../../creators/website-creators/newsletter-lists-and-subscriptions.md#consent)).

</details>

## Further reading

- [Newsletters with cjw_newsletter](../../../creators/website-creators/newsletters-with-cjw-newsletter.md#subscribe-forms-on-the-site):
  subscribe forms on the site.
- [Newsletter lists and subscriptions](../../../creators/website-creators/newsletter-lists-and-subscriptions.md):
  every status.
- [Compliance and GDPR](../../../hosting/security/compliance-and-gdpr.md): consent and records.

## Related pages

- [Module 3: Subscribers](README.md)
- [Newsletters with CJW Newsletter: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
