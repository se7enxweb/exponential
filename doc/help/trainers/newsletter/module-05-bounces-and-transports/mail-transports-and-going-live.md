---
title: "Lesson 5.2: Mail transports and going live"
audience: trainer, admin
category: training
order: 52
summary: Administrators plan the switch of a real site from the file transport to SMTP or sendmail, write the override as a paper plan, and list the sender domain records and checks needed before the first real send. 45 minutes.
keywords: newsletters with cjw newsletter, smtp, sendmail, file transport, going live, SmtpTransportServer, SPF, DKIM, DMARC, IPv6, sender domain, settings override
type: help
---
# Lesson 5.2: Mail transports and going live

On the training installation every mail is a file, and it stays that way. On a real site, the day comes when the
newsletter must reach real inboxes. This lesson teaches how that switch is planned and made, step by step, as a
desk exercise: learners write the plan, but nothing on the training installation is switched.

> **Note**
> Do not change any transport of the training installation in this lesson. The override written here goes into a
> plan document, not into `settings/override/`. The course never sends real mail.

## At a glance

| | |
|---|---|
| Course | [Newsletters with CJW Newsletter](../README.md) |
| Module | [Module 5: Bounces and transports](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Advanced |
| Audience | Administrators, hosting staff |
| Prerequisites | [Lesson 1.2](../module-01-getting-started/preparing-a-safe-training-installation.md); [Lesson 5.1](mail-accounts-and-bounces.md) |

## Learning objectives

After this lesson, learners can:

- name the transports `file`, `smtp` and `sendmail` and the settings of the SMTP connection;
- order the switch: preview first, own address only, then confirmations and editions;
- list the DNS records a sender domain needs (SPF, DKIM, DMARC) and the IPv6 trap;
- explain where secrets belong and where they must never be;
- write a go-live checklist for their own organization.

## Trainer's notes

### How to present it

Sources: [Sending newsletters safely](../../../creators/website-creators/sending-newsletters.md),
[10.4 Mail](../../../../install/10-after-installing.md#104-mail-the-file-transport-and-real-sending) and the
[feature page](../../../../features/6.0/extensions/cjw_newsletter.md#rehearse-the-whole-thing-without-sending-a-single-mail).

1. **The transports** (4 minutes). Each of the three methods is `file`, `smtp` or `sendmail`. With `smtp`, the
   connection is `SmtpTransportServer`, `SmtpTransportPort`, `SmtpTransportUser`, `SmtpTransportPassword` and
   `SmtpTransportConnectionType` (empty, `ssl`, `sslv2`, `sslv3` or `tls`). The newsletter's transports are separate
   from the kernel's `[MailSettings]`.
2. **The order of the switch** (5 minutes), from [From rehearsal to real mail](../../../creators/website-creators/sending-newsletters.md#from-rehearsal-to-real-mail):
   rehearse with `file`; switch `TransportMethodPreview` and test to your own address only; check several mail
   programs and the unsubscribe link; set up the sender domain; then switch `TransportMethodDirectly` and
   `TransportMethodCronjob`.
3. **The sender domain** (5 minutes). A sender address of a domain you control, with SPF (and preferably DKIM and
   DMARC) allowing the sending server, for every address family it sends from. If the server sends over IPv6 and SPF
   lists only IPv4, large providers reject the mail.
4. **Secrets** (3 minutes). The SMTP password goes into `settings/override/`, never into version control or a
   ticket. The settings viewer redacts it.
5. **The first real send** (3 minutes). A small internal list first; watch the bounce mailbox and the dashboard.

### Common questions

**SMTP or sendmail?** Whatever your hosting provides reliably. SMTP to an organization's mail server or a relay
service is the usual choice for production ([10.4](../../../../install/10-after-installing.md#104-mail-the-file-transport-and-real-sending)).

**Which port?** Secure SMTP is usually 465 (`ssl`) or 587 (`tls`); your provider decides.

**Do we need to change the kernel's transport too?** Yes, if the site sends other mail (passwords, forms); it is set
in `site.ini [MailSettings]`.

**Can we keep the training installation on files forever?** Yes, and you should.

### Pitfalls

- **Switching all three methods at once.** A mistake then reaches every subscriber. Switch one at a time.
- **A shared password in the plan.** Write "from the mail provider" instead of a real value.
- **Forgetting the dashboard hint.** On the live site, the hint "mails are written to files" after go-live means the
  switch did not take effect (INI cache).

## Hands-on exercise

A desk exercise. Write in a document on your own machine, not in the training installation's settings.

1. Write the heading `Newsletter go-live plan for <organization>`.
2. Copy this skeleton into the plan and fill in what you can; use placeholders for secrets:

   ```ini
   # settings/override/cjw_newsletter.ini.append.php (live site only)
   [NewsletterMailSettings]
   TransportMethodPreview=smtp
   TransportMethodDirectly=file
   TransportMethodCronjob=file
   SmtpTransportServer=<from the mail provider>
   SmtpTransportPort=<465 or 587>
   SmtpTransportConnectionType=<ssl or tls>
   SmtpTransportUser=<from the mail provider>
   SmtpTransportPassword=<kept in settings/override only>
   ```

3. Under it, write the second and third stage of the switch (which keys change, after which check).
4. List the sender address and domain, and the DNS records to ask for: SPF, DKIM, DMARC.
5. Ask your hosting staff (or note as an open question) whether the server sends over IPv6, and add the SPF note.
6. Add the kernel's `[MailSettings]` change for the live site's other mail.
7. Write three checks for the first real send (for example: own address only, unsubscribe link works, bounce mailbox
   read).
8. Swap plans with a neighbor and review each other's plan against
   [Sending newsletters safely](../../../creators/website-creators/sending-newsletters.md).

## Expected result

- Each learner has a plan with the three stages, the DNS records, the IPv6 question and three first-send checks.
- No password or real server name of a learner's organization is in a shared document.
- The training installation's transports are unchanged (the trainer checks the settings viewer at the end).

## Quiz

1. Which method do you switch first, and to whom do you send with it?
2. Which records does a sender domain need?
3. A server sends over IPv6 but SPF lists only IPv4 addresses. What happens?
   A. nothing B. large providers reject the mail C. the mail is sent twice D. DKIM fixes it
4. Where does the SMTP password belong?
5. Why switch the three methods one at a time?

<details>
<summary>Answers</summary>

1. `TransportMethodPreview`, sending tests to your own address only ([From rehearsal to real mail](../../../creators/website-creators/sending-newsletters.md#from-rehearsal-to-real-mail)).
2. SPF, and preferably DKIM and DMARC ([10.4](../../../../install/10-after-installing.md#104-mail-the-file-transport-and-real-sending)).
3. **B.** ([10.4](../../../../install/10-after-installing.md#104-mail-the-file-transport-and-real-sending)).
4. In `settings/override/`, never in version control ([Sending newsletters safely](../../../creators/website-creators/sending-newsletters.md#from-rehearsal-to-real-mail)).
5. So a mistake reaches only test addresses before it can reach subscribers.

</details>

## Further reading

- [Sending newsletters safely](../../../creators/website-creators/sending-newsletters.md): the transports and the
  switch to real mail.
- [10.4 Mail: the file transport and real sending](../../../../install/10-after-installing.md#104-mail-the-file-transport-and-real-sending):
  the kernel's mail and the sender domain.
- [cjw_newsletter: newsletters in the admin](../../../../features/6.0/extensions/cjw_newsletter.md#rehearse-the-whole-thing-without-sending-a-single-mail):
  the rehearsal and the SMTP keys.

## Related pages

- [Module 5: Bounces and transports](README.md)
- [Newsletters with CJW Newsletter: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
