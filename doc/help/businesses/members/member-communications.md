---
title: Member communications
audience: businesses, editor, sales-marketing
category: business-solutions
order: 40
summary: How a member organization keeps members informed with Exponential - newsletters with cjw_newsletter, notifications about new content, the e-mail preferences page with one-click unsubscribe and consent log, mail delivery, and a rehearsal without sending a single mail.
keywords: member communications, newsletter, cjw_newsletter, double opt-in, lists, editions, bounces, blacklist, notifications, digest, e-mail preferences, unsubscribe, consent log, mail delivery, rehearsal
type: help
---
# Member communications

This page is for communications leads of member organizations. Members expect to hear from you regularly, and to
stop hearing from you with one click. Exponential gives you three channels and one page where each member decides
what they get. The newsletter system is covered in depth in the [Newsletters](../newsletters/README.md) section; this
page shows how the pieces fit a member site.

## Contents

1. [The channels](#the-channels)
2. [The newsletter](#the-newsletter)
3. [Notifications about new content](#notifications-about-new-content)
4. [One page for every choice](#one-page-for-every-choice)
5. [What every optional mail carries](#what-every-optional-mail-carries)
6. [Rehearse before sending](#rehearse-before-sending)
7. [Delivery](#delivery)
8. [A communications calendar](#a-communications-calendar)
9. [Checklist](#checklist)
10. [Related pages](#related-pages)

## The channels

| Channel | Best for | Feature |
|---|---|---|
| Newsletter | a curated edition to everyone on a list, monthly or weekly | `cjw_newsletter` |
| Notifications | "tell me when something new appears in this area" | the kernel's notification system |
| The site | news, the newsletter archive, events | content and the front page |
| Essential mail | account and security messages | always sent, cannot be switched off |

## The newsletter

`cjw_newsletter` is a complete newsletter system in the administration (**Newsletter**, `/newsletter/index`):
editions are written as content, visitors subscribe to lists with double opt-in, editions are sent by cron in the
background, bounces and a blacklist keep lists clean, and every send is archived
([cjw_newsletter](../../../features/6.0/extensions/cjw_newsletter.md)).

| Part | What the organization does there |
|---|---|
| Systems, lists, editions | write editions as content; one list per audience (all members, board, events) |
| Subscriptions | see who is pending, confirmed, approved, unsubscribed, bounced or blacklisted |
| Public forms | subscribe, confirm, change and leave (`newsletter/subscribe`, `configure`, `unsubscribe`) |
| Sending | schedule an edition; cron queues and sends the mails |
| Bounces | after `BounceThresholdValue` bounces (default 3) a subscriber stops receiving |
| CSV | import and export subscribers |

The statuses are explained in [Newsletter lists and subscriptions](../../creators/website-creators/newsletter-lists-and-subscriptions.md#statuses),
writing and sending in [Sending newsletters](../../creators/website-creators/sending-newsletters.md).

> **Tip**
> A list per audience lets members choose what they get, and keeps the edition relevant. A members-only edition can
> link to members-only pages; non-members who receive a forwarded mail are asked to sign in.

## Notifications about new content

Members with the policy `notification/use` can follow an area with **Notify me** and get a mail when something is
published below it, at once or as a daily, weekly or monthly digest
([Notifications: the user's guide](../../../features/6.0/notifications.md)). Mail is sent only about content the member
may read. Add `notification/use` to the Member role if you offer it, and run the notification cronjob
([Notifications for staff](../intranets/notifications-for-staff.md#what-must-be-running)).

## One page for every choice

Since 6.0.15 every person has one e-mail preferences page (`/mailpreferences/settings`, or the **Manage** link in any
optional mail, no login needed) ([E-mail preferences: the user's guide](../../../features/6.0/mail-preferences.md)):

- Optional mail (notifications, newsletters, offers, notices) is off until the person turns it on.
- Newsletters and offers need a confirmation by mail before they start; a request lapses after seven days.
- Essential mail about the account (password reset, activation, orders, legal notices) is always sent and never
  carries advertising.
- A person can download their choices and every change to them as JSON or CSV.

Every change of a preference is written to a **consent log** with the exact text the person saw
([E-mail preferences: setting them up](../../../guides/mail-preferences-administrator.md)). That record is useful when
someone asks why they received a mail. The legal side is covered in
[Opt-in and consent](../newsletters/opt-in-and-consent.md) and GDPR basics; these pages are
not legal advice.

## What every optional mail carries

The mail gate adds a footer to every optional mail: why the person got it, an **Unsubscribe** link that works with one
click, a link to the preferences page, and your organization's name and postal address; it also sends the one-click
headers of RFC 8058. The organization name and postal address ship empty and must be filled in
(`mailpreferences.ini [FooterSettings]`, in a settings override)
([E-mail preferences: setting them up, 2.2](../../../guides/mail-preferences-administrator.md#22-the-footer-who-sends-and-from-where)).

## Rehearse before sending

`cjw_newsletter.ini` can write every mail to files instead of sending it, so a complete rehearsal (subscribe, double
opt-in, send, cron, unsubscribe) needs no mail server and reaches no real recipient
([cjw_newsletter, Rehearse](../../../features/6.0/extensions/cjw_newsletter.md#rehearse-the-whole-thing-without-sending-a-single-mail)).
Do this before the first real edition, and after any change of templates.

## Delivery

Whether mail arrives depends on the sender domain's SPF, DKIM and DMARC records and on the mail server; that is work
for the host ([Mail delivery](../../hosting/operations/mail-delivery.md),
[Mail consent and bounces for hosts](../../hosting/operations/mail-consent-and-bounces.md)).

## A communications calendar

| When | What | Channel |
|---|---|---|
| Monthly | member newsletter | newsletter, all members list |
| Before each event | invitation and reminder | newsletter, events list |
| On publication | new member documents | notifications for those who follow |
| Yearly | membership renewal reminder | newsletter or a mail from the office |

Adapt the rhythm to your organization; fewer, better mails keep unsubscribes low.

## Checklist

- [ ] Newsletter lists per audience.
- [ ] Subscribe and unsubscribe forms on the site.
- [ ] Footer organization name and postal address filled in.
- [ ] Rehearsal with file transport done.
- [ ] Newsletter cron parts scheduled.
- [ ] `notification/use` in the Member role, if notifications are offered.
- [ ] Sender domain records checked by the host.

## Related pages

- [Newsletters](../newsletters/README.md)
- [Newsletters with cjw_newsletter](../../creators/website-creators/newsletters-with-cjw-newsletter.md)
- [Events and calendars](events-and-calendars.md)
- [Notifications for staff](../intranets/notifications-for-staff.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
