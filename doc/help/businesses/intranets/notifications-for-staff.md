---
title: Notifications for staff
audience: businesses, admin, editor
category: business-solutions
order: 90
summary: How staff are told about new and changed content on an Exponential intranet - following items, digests, collaboration mail for approvers, the e-mail preferences gate, the notification cronjob and status page, and how to avoid flooding people.
keywords: notifications, notify me, subscriptions, digest, daily digest, collaboration notification, approvers, e-mail preferences, mail categories, DefaultOn, notification cronjob, frequent, notification status, publish without notification, staff communication
type: help
---
# Notifications for staff

This page is for intranet owners and internal communications teams. It explains how Exponential tells staff about
new content, what each person can choose, what the organization must keep running, and how to stay on the right
side of "useful" rather than "noise". The user's view is in
[Notifications: the user's guide](../../../features/6.0/notifications.md); the operator's view in
[the administrator's guide](../../../guides/notifications-administrator.md).

## Contents

1. [What staff can be told](#what-staff-can-be-told)
2. [Following items](#following-items)
3. [At once or as a digest](#at-once-or-as-a-digest)
4. [Mail for approvers](#mail-for-approvers)
5. [The e-mail preferences gate](#the-e-mail-preferences-gate)
6. [What must be running](#what-must-be-running)
7. [Avoiding noise](#avoiding-noise)
8. [Checklist](#checklist)
9. [Related pages](#related-pages)

## What staff can be told

| Mail | When | Who chooses it |
|---|---|---|
| Content notification | something is published below an item the person follows, and the person may read it | the person (follow / stop following) |
| Digest | the same messages collected into one mail per day, week or month | the person |
| Collaboration notification | an approval item they take part in, as author or approver | the person, per kind of item |
| Account mail (activation, password changed) | always | nobody; essential mail is always sent |

All of these need an account with the policy `notification/use`
([Notifications: the user's guide](../../../features/6.0/notifications.md)). Give it to the Staff role.

## Following items

A person follows an item with **Notify me** in its menu, or with **Add items** on **My notification settings**
(`/notification/settings`). Opening the Notify me address alone changes nothing; only the button subscribes. The
settings page lists everything followed, with filters and paging, and **Remove selected** stops following
([Items I follow](../../../features/6.0/notifications.md#items-i-follow)).

Mail is sent only for content the person may read. Permissions stay in force even in their inbox; a member of Sales
following the whole intranet does not learn about new HR pages
([Content policy limitations](../../../features/6.0/content-limitation-handlers.md#what-you-can-do) lists subtree
notifications among the places permissions are checked).

Good items to recommend to staff: the company news folder, their department's folder, the policies folder.

## At once or as a digest

Normally every change is mailed at once. A digest holds messages back and sends one mail daily at a chosen hour,
weekly on a chosen day, or monthly on a chosen day number; it is sent only if something happened
([E-mail digest](../../../features/6.0/notifications.md#e-mail-digest)). For most staff a daily digest is the
friendly default to recommend.

## Mail for approvers

When approval before publishing is used ([Approval workflows](approval-workflows.md)), approvers learn that something
waits through collaboration notifications: they tick the kind **Approval** under **Collaboration notification** on
their settings page ([Collaboration notification](../../../features/6.0/notifications.md#collaboration-notification)).
Make this part of the approvers' onboarding; an approver who never gets the mail delays every article.

## The e-mail preferences gate

Since 6.0.15 every mail passes a gate that checks the recipient's preferences
([E-mail preferences: setting them up](../../../guides/mail-preferences-administrator.md)). In
`settings/mailpreferences.ini`, content notifications (`[Category_content]`) and collaboration mail
(`[Category_collaboration]`) are optional categories with `DefaultOn=false`: they are opt-in. Essential mail such as
account security always goes.

What that means for an intranet:

- The state of these categories is read from the person's existing subscriptions
  (`HandlerClass=expNotificationMailCategoryHandler`), so someone who followed items before sees Content
  notifications as on. A person can switch the category off on
  **My e-mail preferences**, and every optional mail carries a one-click unsubscribe link
  ([E-mail preferences: the user's guide](../../../features/6.0/mail-preferences.md)).
- `AllowDefaultOn=enabled` makes an optional category on for everybody who never chose. The comment in
  `mailpreferences.ini` restricts it to sites whose recipients are under a law that allows it for that mail. Whether
  that applies to your staff is a question for your legal advisor; this page is not legal advice.

## What must be running

Nothing is sent until a notification **run** happens. The run is started by the cronjob group `frequent`, by the
**Run now** button on the status page, or by `./console exp:notification:run`
([How it works in one minute](../../../guides/notifications-administrator.md#1-how-it-works-in-one-minute)).

**Setup > Notification** (`/notification/status`, policy `notification/administrate`) shows events waiting, messages
kept for digests, subscriptions, the last run and recent runs. If the last run is old, the cronjob is not running and
nobody gets mail ([The status page](../../../guides/notifications-administrator.md#3-the-status-page)).

Mail delivery itself (sender address, SPF and DKIM at your mail provider) is the host's job
([Mail delivery](../../hosting/operations/mail-delivery.md)).

## Avoiding noise

- Recommend digests rather than mail at once.
- Let editors **Publish without notification** for corrections
  ([Publishing without notification](../../../guides/notifications-administrator.md#8-publishing-without-notification)).
- Keep company-wide announcements in one news folder, so following it is a clear choice.
- Use a newsletter for a curated weekly summary instead of many single mails
  ([Newsletters](../newsletters/README.md)).
- Check the status page once a month for subscriptions to content that no longer exists.

## Checklist

- [ ] Staff role has `notification/use`.
- [ ] `frequent` cronjob group runs; the status page shows a recent run.
- [ ] Mail sender and delivery tested with a real staff address.
- [ ] Approvers have ticked the Approval collaboration notification.
- [ ] Editors may publish without notification.
- [ ] A short note to staff on how to follow folders and choose a digest.

## Related pages

- [Notifications: the user's guide](../../../features/6.0/notifications.md)
- [Notifications: the administrator's guide](../../../guides/notifications-administrator.md)
- [News and announcements](news-and-announcements.md)
- Member communications
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
