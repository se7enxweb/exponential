---
title: Publish without notifying subscribers
audience: government, editor, admin
category: public-sector
order: 480
summary: For editors - publish a minor correction without sending notification mails to people who follow the page's location, with Publish without notification, where the site has switched it on and your role has the policy; and when not to use it.
keywords: Publish without notification, notifications, subscribers, minor correction, typo, PublishWithoutNotification, content/publish_without_notification, notification.ini
type: help
---
# Publish without notifying subscribers

This page is for editors who correct a typo or metadata in a page that people follow by e-mail. Where the site offers
it, **Publish without notification** publishes the version without making a notification event, so followers get no
mail for it ([Publish without notification](../../../../features/6.0/publish-without-notification.md)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check the result](#check-the-result)
4. [When not to use it](#when-not-to-use-it)
5. [Setup for administrators](#setup-for-administrators)

## Before you start

- The button appears only when `notification.ini [NotificationSettings] PublishWithoutNotification=enabled` and your
  role has `content/publish_without_notification`. If you do not see it, the site does not offer it to you.

## Steps

1. **Edit** the page, make the small change.
2. Press **Publish without notification** (next to **Send for publishing**; also in the version preview).
3. The version is published as usual; approval rules still apply.

## Check the result

- [ ] The page shows the change.
- [ ] **Setup > Notification** shows no new event for it (ask the web team if you need to confirm).

## When not to use it

| Change | Use |
|---|---|
| A typo, formatting, a metadata fix | **Publish without notification** |
| A changed date, amount, place or rule | **Send for publishing**: people need to know |
| A correction of something people acted on | **Send for publishing**, with a correction note ([Correct a published notice and keep the history](correct-a-published-notice-and-keep-the-history.md)) |

## Setup for administrators

1. `notification.ini [NotificationSettings] PublishWithoutNotification=enabled` in `settings/override/` (all
   siteaccesses) or in the administration siteaccess only; clear the INI cache.
2. Add the policy function **content / publish_without_notification** to the roles of editors who may use it (a
   `content/*` policy already includes it).

Without the policy, a posted button publishes with notification and a warning is logged.

## Related pages

- [Publishing tasks](README.md)
- [Follow changes with notifications](follow-changes-with-notifications.md)
- [Notifications and subscriptions](../../building/news-and-alerts/notifications-and-subscriptions.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
