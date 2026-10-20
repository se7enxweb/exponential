---
title: News and announcements
audience: businesses, editor, website-creator
category: business-solutions
order: 50
summary: How an intranet publishes company news and announcements with Exponential - article classes, a news folder per audience, a front page built with the layouts editor or ezflow, timed publishing, notifications, feeds, and publishing a correction without a new mail.
keywords: intranet news, announcements, articles, front page, landing page, layouts editor, ezflow, Wait until date, notifications, notify me, RSS, publish without notification, newsletter, staff communication
type: help
---
# News and announcements

This page is for internal communications teams and intranet owners. It explains how company news gets onto the
intranet, how a front page shows the latest items, how staff are told about them, and which department may publish
what. Every feature named here is part of Exponential or of an extension listed in `composer.json`.

## Contents

1. [What staff expect](#what-staff-expect)
2. [News as content](#news-as-content)
3. [Who may publish news](#who-may-publish-news)
4. [The front page](#the-front-page)
5. [Publishing at a set time](#publishing-at-a-set-time)
6. [Telling staff](#telling-staff)
7. [Corrections without a second mail](#corrections-without-a-second-mail)
8. [Feeds](#feeds)
9. [Checklist](#checklist)
10. [Related pages](#related-pages)

## What staff expect

- The newest company news on the start page.
- Department news where the department lives.
- A way to be told, without being flooded.
- An archive they can search.

## News as content

News items are content objects of an article class, kept in a folder. The ready-made website design `ezwebin`
ships classes and templates for articles, blogs, events, forums and more
([ezwebin](../../../features/6.0/extensions/ezwebin.md)), and the
[content classes for websites](../../creators/website-creators/content-classes-for-websites.md) page explains how to
choose or define your own. Each article has its own versions, an owner and a publication date.

A simple structure that works for many organizations:

| Folder | Section | Readers | Editors |
|---|---|---|---|
| Company news | Staff (or Standard on a closed intranet) | all staff | internal communications |
| Sales news | Sales | all staff, or sales only | sales editors |
| Management announcements | Management | all staff | management office |

The section decides who reads and who edits ([Departments, roles and sections](departments-roles-and-sections.md)).

## Who may publish news

A role can be limited by class, so an editor of the news area may create articles and nothing else, and by section,
so department editors publish only in their own area
([Roles and policies, 3](../../../guides/roles-and-policies.md#3-create-a-role-with-limited-policies)). When texts
must be checked before they go live, add an approval workflow ([Approval workflows](approval-workflows.md)).

## The front page

Two tools build start pages with blocks such as "latest news", "featured item" and "upcoming events":

| Tool | What it is | Source |
|---|---|---|
| The layouts editor | landing pages from layout types, zones and blocks, edited in a browser editor | [Landing pages with layouts](../../creators/website-creators/landing-pages-with-layouts.md) |
| `ezflow` | the classic landing pages with blocks and timelines | [Classic landing pages with ezflow](../../creators/website-creators/classic-landing-pages-with-ezflow.md) |

The comparison of the two is in
[ezflow or the layouts editor](../../creators/website-creators/classic-landing-pages-with-ezflow.md#ezflow-or-the-layouts-editor).
A shared header and footer for every page is described in
[Shared header and footer](../../creators/website-creators/shared-header-and-footer.md).

## Publishing at a set time

Workflows have an event type **Wait until date** next to Approve
([Workflows, 1](../../../guides/workflows.md#1-workflows-events-triggers-and-processes)). It holds back
publishing until the date in a date field of the class, the field being chosen in the event
(`kernel/classes/workflowtypes/event/ezwaituntildate/`), which suits an announcement prepared in advance. Like every waiting
workflow it needs the workflow cronjob (`workflow.php`) to run; without it nothing waiting is ever published
([Workflows, 6](../../../guides/workflows.md#6-the-workflow-cronjob-how-processes-resume)). Ask your partner to set it
up and test it with one article before relying on it.

## Telling staff

| Way | How it works | Source |
|---|---|---|
| Notifications | a staff member follows the news folder (**Notify me**) and gets a mail when something is published below it, at once or as a daily, weekly or monthly digest | [Notifications: the user's guide](../../../features/6.0/notifications.md) |
| A newsletter | `cjw_newsletter` sends editions to lists with double opt-in | [Newsletters section](../newsletters/README.md) |
| The front page | the latest items in a block | above |

Notifications are sent by the notification run, which the `frequent` cronjob group starts
([Notifications for staff](notifications-for-staff.md)).

## Corrections without a second mail

A typo fixed after publishing should not send everyone another mail. With
`notification.ini [NotificationSettings] PublishWithoutNotification=enabled` and the policy
`content/publish_without_notification`, editors see **Publish without notification** next to the publish button. A
version published that way makes no notification event; approval mails are still sent
([Notifications administrator guide, 8](../../../guides/notifications-administrator.md#8-publishing-without-notification)).

## Feeds

Setup > RSS (`/rss/list`) publishes feeds of the newest objects below chosen locations, as RSS 1.0, RSS 2.0, Atom,
OPML or an Apple Podcasts feed ([RSS feeds](../../../guides/rss-feeds.md)).

> **Note**
> On an intranet whose siteaccess has `RequireUserLogin=true`, every request that is not in `AnonymousAccessList[]`
> goes to the login page (`settings/site.ini`). The shipped list does not contain `rss/feed`, so a feed reader that
> cannot sign in gets no feed. Decide deliberately whether news may leave the intranet this way.

## Checklist

- [ ] News folders created, each in the right section.
- [ ] A role for news editors limited by class and section.
- [ ] Approval decided for management announcements.
- [ ] Front page block for the latest news.
- [ ] Notification cronjob running; staff told how to follow news.
- [ ] Publish without notification enabled for editors who fix typos.
- [ ] Feed decision made for the intranet.

## Related pages

- [Notifications for staff](notifications-for-staff.md)
- [Approval workflows](approval-workflows.md)
- [Editing content](../../editing-content.md)
- [Publishing and versions](../../publishing-and-versions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
