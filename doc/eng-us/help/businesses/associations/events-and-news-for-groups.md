---
title: Events and news for groups
audience: businesses, editor, website-creator
category: business-solutions
order: 30
summary: How a club, society or small group publishes news and events on Exponential - the shipped article, event and event calendar classes, keeping the calendar current, feeds of news and events, notifications for members who follow a page, a newsletter, and the home page in Layouts.
keywords: events, event calendar, news, club news, meetings, ezwebin, article, event_calendar, multicalendar, RSS feed, notifications, newsletter, small group
type: help
---
# Events and news for groups

This page is for the volunteers who keep a group's news and calendar up to date, and for the web coordinator who sets
it up. It shows which shipped classes to use, how members learn about new items without checking the site every day,
and how to keep the calendar from going stale when volunteers change.

## Contents

1. [The classes for news and events](#the-classes-for-news-and-events)
2. [Setting up news](#setting-up-news)
3. [Setting up the calendar](#setting-up-the-calendar)
4. [Keeping the calendar current](#keeping-the-calendar-current)
5. [Letting members know](#letting-members-know)
6. [The home page](#the-home-page)
7. [Members-only news and events](#members-only-news-and-events)
8. [Events and news checklist](#events-and-news-checklist)

## The classes for news and events

The `ezwebin` site interface, required by `composer.json`, ships these class packages in
`extension/ezwebin/packages/`:

| Package | Use |
|---|---|
| `article`, `article_main_page`, `article_sub_page` | News items and longer articles |
| `folder` | The news archive, grouped by year if you like |
| `event` | One meeting or event |
| `event_calendar` | A calendar that holds events |
| `multicalendar` | A page that combines several calendars |
| `blog`, `blog_post` | An informal group blog |

In the multisite site package's roles, `event` objects are created below an `event_calendar`
([Site packages: roles and policies](../../site-packages/sevenx-multisite/roles-and-policies.md)). The ezwebin event
calendar caches its page for a short time ("The ezwebin event calendar keeps its page for 400 seconds",
[Caching for designers](../../designers/theme-design/caching-for-designers.md)), so a new event can take a few minutes
to appear in the calendar view.

## Setting up news

1. Create a News folder in the public section.
2. Editors create articles in it; each article has a title, an intro, a body and an image.
3. Give the folder a full view that lists the newest articles; the website creator sets this up once
   ([Menus and navigation](../../creators/website-creators/menus-and-navigation.md)).
4. Decide how long news stays on the front page and when old news moves to an archive folder.

## Setting up the calendar

1. Create an event calendar where members expect it ("Events").
2. Editors add events below it, with date and time, place and a short description.
3. For several activity groups, create one calendar each and a multicalendar page that combines them.
4. Link the calendar from the main menu and the home page.

Templates and views are the website creator's work; see
[Content classes for websites](../../creators/website-creators/content-classes-for-websites.md) for changing a class
if your group needs an extra field (for example "registration required").

## Keeping the calendar current

A calendar with last year's events is worse than none. Make it someone's job:

- [ ] One named role ("events editor") owns the calendar.
- [ ] Events are entered when they are agreed, not the week before.
- [ ] Cancelled events are marked as cancelled in their text, not silently removed, so members who planned to come see it.
- [ ] Past events are left in place or moved to an archive; links to them keep working.
- [ ] At each handover the calendar is reviewed ([Handover when volunteers change](handover-when-volunteers-change.md)).

## Letting members know

| Channel | How | Page |
|---|---|---|
| Notifications | Members with an account follow the News folder or the calendar and get an e-mail "when something is published below an item you follow", at once or as a daily, weekly or monthly digest | [Notifications: the user's guide](../../../features/6.0/notifications.md) |
| Feeds | An RSS or Atom export of the News folder and of the calendar at `/rss/feed/<address>` | [RSS feeds for your site](../../creators/website-creators/rss-feeds-for-your-site.md) |
| Newsletter | A regular newsletter with `cjw_newsletter`, optionally filled automatically from new articles | [Newsletters](../newsletters/README.md) |

Notifications need an account with the policy `notification/use` (same notifications page). Every optional mail
passes the site's e-mail preferences, so members choose what they get and can stop it with one click
([E-mail preferences](../../../features/6.0/mail-preferences.md)).

> **Tip**
> For a small group, notifications are often enough: no newsletter to write, members who want news follow the News
> folder. A newsletter is worth it when you want to choose and explain what matters.

## The home page

The home page usually shows the next events and the latest news. With Exponential Layouts, a `list` block with a
dynamic collection keeps itself current ("the latest five articles in this section", in the
[Layouts reference](../../../bc/6.0/LAYOUTS.md)), and an editor can rearrange the page without a developer
([Landing pages with layouts](../../creators/website-creators/landing-pages-with-layouts.md)). Agree that one person
edits the home page layout at a time, because layout drafts are shared.

## Members-only news and events

Some news (minutes, internal decisions) is for members only. Put it in a section only members may read; visitors then
cannot open it, and search shows visitors only what they may read
([Site search](../../creators/website-creators/site-search.md#the-built-in-engine)). Do not use members-only folders
as sources of public feeds or of a public newsletter's article pool
([Sections and object states](../../creators/website-creators/sections-and-object-states.md),
[Members-only content](../members/members-only-content.md)).

## Events and news checklist

- [ ] News folder and event calendar created from the shipped classes.
- [ ] An events editor named; the calendar reviewed at each handover.
- [ ] Feeds for news and events active and linked.
- [ ] Members told how to follow news (notifications) or subscribe (newsletter).
- [ ] Home page shows the next events and latest news automatically.
- [ ] Members-only items in a members-only section; checked while logged out.

## Related pages

- [Associations and small groups](README.md)
- [Events and calendars](../members/events-and-calendars.md)
- [Member communications](../members/member-communications.md)
- [Feeds for new acquisitions](../libraries/feeds-for-new-acquisitions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
