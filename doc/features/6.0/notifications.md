# Notifications: the user's guide

Exponential tells you by e-mail when content you follow changes, and about the collaboration items you take part in. This
page is for editors and visitors who have an account: what you can choose, how to follow and stop following an item, how the
digest works and what the mails look like. Administrators find the status page, the cronjob and troubleshooting in
[the administrator's guide](../../guides/notifications-administrator.md). The page works in the admin4 and admin4l designs;
other designs keep their own settings pages.

You need an account with the policy `notification/use` (a role that gives it; ask your administrator if the menu entry
is missing). The mails go to the e-mail address of your account.

## My notification settings

Open **My notification settings** in the left menu of the dashboard, or go to `/notification/settings`. The page opens
with three boxes: how many items you follow, how the messages come (**At once**, **Daily**, **Weekly** or **Monthly**) and the
address they go to. Below are the item list, the digest settings and the collaboration settings. Every change you make
shows a notice at the top ("Your notification settings were saved.", "Removed 2 notification(s).").

### Items I follow

The list shows every item you follow with its name (a link), its class, the place it is in ("In Websites > Newsletter") and
the date of the last change below it. You get an e-mail when something is published below an item you follow, if you may read it.

- **Follow an item.** Press **Add items**, pick content in the browser and confirm. Or open an item in the content
  structure and choose **Notify me** in its menu: a page asks "Notify me about updates" and only the button **Notify me**
  subscribes. Opening that address by itself changes nothing.
- **Stop following.** Tick the items and press **Remove selected**. A box lists what will be removed; **Remove** confirms,
  **Cancel** changes nothing. You can also open the item's **Notify me** page again: it says "You already follow this item"
  and offers **Stop notifications for this item**.
- **Items whose content was deleted** are marked "Content no longer exists". Nothing can be sent for them; remove them.
- **Find an item.** With many items, type part of a name in **Name contains** and press **Filter**; the filter is in the
  address (`/notification/settings/(q)/news`). **Show all** clears it. When you follow items of more than one class a
  type list narrows it further.
- **Paging.** The list shows 10, 25 or 50 items per page, the setting you use in the other administration lists.
  **Select all on this page** ticks the page.

### E-mail digest

Normally every change is mailed at once, one mail per change. A digest holds the messages back and sends one mail with all
of them:

| Choice | You get one mail |
|---|---|
| **Daily, at** an hour | every day at that hour |
| **Once per week, on** a weekday | every week on that day, at the hour chosen above |
| **Once per month, on day number** | every month on that day (day 29 to 31 means the last day of a short month), at the hour chosen above |

Tick **Receive all messages combined in one digest**, choose, press **Save digest settings**. Untick it to get mail at
once again. The hour is the time of the server. A digest is sent by the next run of the notification cronjob after
its time; it comes only if something happened.

### Collaboration notification

The box lists the kinds of collaboration items (for example **Approval**) that mail you. Tick the ones you want and press
**Save collaboration settings**. You are notified about the items you take part in, as author or as approver, for example
when something waits for your decision.

## What the mails look like

A mail about published content (the subject and text are the standard template; your site may have overridden them):

```text
To: undisclosed-recipients:;
Subject: Article "NOTTEST first article" was published [alpha.se7enx.com - NOTTEST UI folder]
From: Graham Brookins <info@se7enx.com>
Bcc: nottest-ui@nottest.invalid
Content-Type: text/plain; charset=utf-8
Message-ID: <node.21950.eznotification@alpha.se7enx.com>
In-Reply-To: <node.21931.eznotification@alpha.se7enx.com>

This email is to inform you that a new item has been published at alpha.se7enx.com.
The item can be viewed by using the URL below.

NOTTEST first article - Graham Brookins
http://alpha.se7enx.com/websites/nottest-ui-folder3/nottest-first-article


If you do not want to continue receiving these notifications,
change your settings at:
http://alpha.se7enx.com/notification/settings

--
alpha.se7enx.com notification system
```

The subject names the class, the title and the place; the sender is the author. The recipients are in `Bcc`, so no
one sees the others. The link at the end leads back to **My notification settings**. A mail about an updated item says
"was updated" and "an updated item has been published". The headers `Message-ID`, `In-Reply-To` and `References` let
mail programs group the mails of one place into a thread.

A digest (the items of the day, each in the form the class template gives them):

```text
Subject: [alpha.se7enx.com] Digest for Sunday October 04 2026 10:40:33 pm
Bcc: nottest-ui@nottest.invalid

This digest email is to inform you on new items at alpha.se7enx.com.

NOTTEST digest example

Current version-1
Title:  NOTTEST digest example
...
```

A collaboration mail (an approval that waits for you):

```text
Subject: [alpha.se7enx.com] Approval of "Sample: Editorial guidelines" awaits your  attention
Bcc: anna.sample@example.invalid

This email is to inform you that "Sample: Editorial guidelines" awaits your attention at alpha.se7enx.com.
The publishing process has been halted and it is up to you to decide if it should continue or stop.
The approval can be viewed by using the URL below.
http://alpha.se7enx.com/collaboration/item/full/63

If you do not want to continue receiving these notifications,
change your settings at:
http://alpha.se7enx.com/notification/settings
```

These mails were made on a test installation and written to files; none was sent.

## Why did I not get a mail?

| Check | Because |
|---|---|
| You do not follow the item or one above it | A subscription counts for the item and everything below it. Look at the list |
| You may not read the new content | Notifications go only to those who may read it |
| The content is hidden or invisible | Hidden content is not announced |
| You chose a digest | Messages wait for the hour of the digest |
| It is an update of an old version | Only the current version is announced |
| The editor published it without notification | Corrections can be published without telling the subscribers, where the site allows it ([Publish without notification](publish-without-notification.md)) |
| Nothing ran yet | Mail is sent by the notification cronjob; ask your administrator |
| Your account is disabled or has no address | |

## Related pages

- [The administrator's guide](../../guides/notifications-administrator.md), [the developer's guide](../../guides/notifications-developer.md)
- [The specification](../../specifications/6.0/notifications.md), [the commands](../../specifications/6.0/notifications-cli.md)
- [Upgrade notes](../../bc/6.0/notification-ui-and-commands.md)
