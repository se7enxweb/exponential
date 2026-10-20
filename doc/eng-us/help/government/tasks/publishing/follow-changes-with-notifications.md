---
title: Follow changes with notifications
audience: government, editor, user
category: public-sector
order: 490
summary: For editors and account holders - follow a folder or page with Notify me so you get an e-mail when something is published below it, choose at once or a digest in My notification settings, and stop following.
keywords: notifications, Notify me, follow, My notification settings, digest, daily, weekly, e-mail, subscriptions, Add items, Remove selected, notification/use
type: help
---
# Follow changes with notifications

This page is for editors who want to know when colleagues publish in an area (approvers, team leads), and for account
holders of the public site who follow news or notices. Exponential mails you when something is published below an
item you follow ([Notifications: the user's guide](../../../../features/6.0/notifications.md)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check the result](#check-the-result)
4. [Pitfalls](#pitfalls)

## Before you start

- Your account has the policy `notification/use` (if the menu entry is missing, ask your administrator).
- Mails go to your account's e-mail address.

## Steps

**Follow an item:**

1. Open the folder or page in the content structure; in its menu choose **Notify me**.
2. On the page "Notify me about updates", press **Notify me**. (Opening the address alone changes nothing.)

Or: **My notification settings** (`/notification/settings`), **Add items**, pick content in the browser, confirm.

**Choose how mails come:**

1. **My notification settings**.
2. Tick **Receive all messages combined in one digest**, choose **Daily, at** an hour, **Once per week, on** a day, or
   **Once per month, on day number**; press **Save digest settings**. Untick it to get mail at once.

**Stop following:** in **My notification settings**, tick the items and press **Remove selected**, then **Remove**; or
open the item's **Notify me** page again and choose **Stop notifications for this item**.

## Check the result

- [ ] **My notification settings** lists the item ("Items I follow").
- [ ] After someone publishes below it and the notification cronjob runs, you get a mail (or the digest at its time).

## Pitfalls

| Pitfall | Avoid it |
|---|---|
| Expecting mail within seconds | mails go at the next notification run, or with the digest |
| Following the whole site | too many mails; follow the folders that matter |
| Items whose content was deleted | marked "Content no longer exists"; remove them |
| No mail at all | the notification cronjob may not run; tell the web team |

## Related pages

- [Publishing tasks](README.md)
- [Publish without notifying subscribers](publish-without-notifying-subscribers.md)
- [Your account and signing in](../../../accounts-and-sign-in.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
