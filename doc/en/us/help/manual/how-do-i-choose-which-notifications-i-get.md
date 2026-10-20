---
title: How do I choose which notifications I get?
audience: editor, admin, user
category: manual
order: 260
summary: My notification settings lists the items you follow, sets how often the mail comes (at once, daily, weekly or monthly) and which collaboration messages mail you; My e-mail preferences decides which optional e-mail you get at all.
keywords: notifications, notification settings, digest, daily, weekly, follow, collaboration notification, notification/settings, e-mail preferences
context: notification/settings
type: help
---
# How do I choose which notifications I get?

Notifications mail you when content you follow changes. You choose what you follow and how often the mail comes.

## Who can do this

Users whose role has `notification/use`.

## Steps

1. Open **Dashboard** in the top menu and choose **My notification settings**, or click **Notification settings** on
   **My account**.
   The page **My notification settings** opens.
2. Under **Items I follow**, see what you follow. Tick items and click **Remove selected** to stop following them.
3. Under **E-mail digest**, tick **Receive all messages combined in one digest** and choose **Daily**, **Once per
   week** or **Once per month**, with the hour. Click **Save digest settings**. Without the tick, each change mails you
   at once.
4. Under **Collaboration notification**, tick the kinds of items that should mail you (for example **Approval**) and
   click **Save collaboration settings**.
5. To stop optional e-mail altogether, open **My e-mail preferences** and click **Turn off all optional e-mail**, or
   untick categories and click **Save my choices**.

![My notification settings with the boxes Items I follow, E-mail digest and Collaboration notification](images/notification-settings.webp)

## What can go wrong

- **No mail arrives.** The notification cronjob has not run yet, your e-mail preferences turn the category off, or the
  mail went to spam. See "Why did I not get a mail?" in the notifications guide.
- **Too much mail.** Choose a digest instead of at once, or follow a smaller part of the tree.
- **An item says "Content no longer exists".** It was deleted. Remove it from the list.

## Related pages

- [How do I follow a part of the site?](how-do-i-follow-a-part-of-the-site.md)
- [Notifications: the user's guide](../../features/6.0/notifications.md)
- [E-mail preferences: the user's guide](../../features/6.0/mail-preferences.md)
- [User manual](README.md)
