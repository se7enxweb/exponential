---
title: Notifications instead of a newsletter
audience: government, admin, editor
category: public-sector
order: 80
summary: When the kernel's notifications serve better than a newsletter: signed-in users follow a page or section and get an e-mail, at once or as a digest, when content changes; how it works, the cronjob, and its limits for public audiences.
keywords: notifications, follow, subscribe to changes, digest, notification/use, My notification settings, notification cronjob, alerts, change notification
type: help
---
# Notifications instead of a newsletter

This page is for administrators and editors who want people to hear about changes without writing an edition each
time. Exponential's notifications e-mail users with an account when content they follow changes
([Notifications: the user's guide](../../../../features/6.0/notifications.md),
[Notifications for administrators](../../../../guides/notifications-administrator.md)).

## Contents

1. [How it works](#how-it-works)
2. [Setting it up](#setting-it-up)
3. [Compared with a newsletter](#compared-with-a-newsletter)
4. [Limits](#limits)

## How it works

- A user with the policy `notification/use` follows an item or a part of the site.
- They choose how messages come: **At once**, **Daily**, **Weekly** or **Monthly** (digest), in **My notification
  settings** (`/notification/settings`).
- Publishing writes an event; the notification run, started by the cronjob, writes and sends the messages.
- Editors can choose **Publish without notification** for a minor change, where the site offers it.

## Setting it up

1. Give the role of the people who should use it the policy `notification/use`.
2. Make sure the `frequent` cronjob group runs (it contains the notification part); without a run nobody gets mail.
3. Check **Setup > Notification** (`/notification/status`) for the state, and try a run without sending as the
   administrator's guide shows.

## Compared with a newsletter

| | Notifications | Newsletter (`cjw_newsletter`) |
|---|---|---|
| Who | users with an account | anyone who subscribes |
| What is sent | an automatic message per change or a digest | an edition written for the purpose |
| Effort per message | none | writing and approving an edition |
| Suits | staff, members, partners following a section | the public |

## Limits

People need an account, which the public usually does not want for this; for public alerts use a newsletter list or a
feed (see "Alert lists for public notices"). Notification mails go through the mail gate like all mail.

## Related pages

- [Notifications: the user's guide](../../../../features/6.0/notifications.md)
- [Alert lists for public notices](alert-lists-for-public-notices.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
