---
title: Notifications and subscriptions
audience: government, admin, website-creator
category: public-sector
order: 50
summary: How people with an account follow news, notices or any folder on an Exponential site and get e-mail when something is published - Notify me, My notification settings, digests, the notification cronjob, publishing without notification, and limits for urgent use.
keywords: notifications, subscriptions, Notify me, My notification settings, digest, notification cronjob, notification/use, Publish without notification, e-mail, account holders
type: help
---
# Notifications and subscriptions

This page is for administrators and website creators who let people follow parts of a public-sector site by e-mail.
Exponential's notification system mails account holders when content is published below an item they follow.
Sources: [Notifications: the user's guide](../../../../features/6.0/notifications.md) and
[Notifications for administrators](../../../../guides/notifications-administrator.md).

## Contents

1. [How it works](#how-it-works)
2. [What people do](#what-people-do)
3. [What administrators set up](#what-administrators-set-up)
4. [Editors and corrections](#editors-and-corrections)
5. [Limits](#limits)

## How it works

| Step | Who |
|---|---|
| Publishing writes an event | the kernel, at once |
| The notification run gives events to handlers, which write a message per follower, sent at once or kept for a digest | the notification cronjob, **Run now** on the status page, or `exp:notification:run` |
| Digests that are due are sent | the same run |

Nothing is sent without a run. The notification part is in the cronjob group `frequent` and can run alone.

## What people do

| Action | Where |
|---|---|
| Follow an item (a folder such as News or Notices) | **Notify me** in the item's menu, or **Add items** in **My notification settings** (`/notification/settings`) |
| Choose at once or a digest (daily, weekly, monthly) | **My notification settings**, **Receive all messages combined in one digest** |
| Stop following | tick and **Remove selected**, or **Stop notifications for this item** on the item's **Notify me** page |

People need an account with the `notification/use` policy. Mails go to the account's address. The pages work in the
admin4 and admin4l designs; on a public design, offer the **Notify me** link in your templates.

## What administrators set up

1. Give the role of public account holders `notification/use`.
2. Make sure the frequent cronjob part (or the notification part) runs: **Setup > Cronjobs**.
3. Check **Setup > Notification** (`/notification/status`): events waiting, last run, and **Run now** to try.
4. Set the mail transport; test with the file transport first ([Mail to receivers](../forms/mail-to-receivers.md#testing-without-sending)).
5. Optionally limit which classes make notification events, so images and banners do not mail followers
   ([Publish without notification](../../../../features/6.0/publish-without-notification.md#notifications-for-some-classes-only)).

## Editors and corrections

Where `notification.ini [NotificationSettings] PublishWithoutNotification=enabled` and the role has
`content/publish_without_notification`, the edit form offers **Publish without notification**: the version is
published, but followers get no mail. Use it for typo fixes, not for changes people need to know about
([Publish without notification](../../../../features/6.0/publish-without-notification.md)).

## Limits

| Limit | Consequence |
|---|---|
| Only account holders | the public without accounts uses RSS or newsletters |
| Sent at the next run, or later as a digest | not for warnings that must arrive in minutes |
| Only content the follower may read | restricted content notifies only those allowed to read it |
| E-mail deliverability | depends on your mail setup; bounces handled by the mail side |

Notification mails are a service the person chose; they are listed under e-mail preferences categories where your
site uses them ([Mail preferences](../../../../features/6.0/mail-preferences.md)).

## Related pages

- [News and alerts](README.md)
- [RSS feeds](rss-feeds.md)
- [Newsletters](newsletters.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
