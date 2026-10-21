---
title: Communications for non-profits
audience: non-profit, businesses, editor, sales-marketing
category: non-profits
order: 10
summary: How a non-profit keeps in touch with supporters, volunteers and the people it serves through its Exponential site - the cjw_newsletter supporter newsletter, events with the ezwebin event classes, several languages, and campaign and appeal pages with Exponential Layouts - and what is not built in.
keywords: communications, supporters, newsletter, cjw_newsletter, events, event calendar, multilingual, languages, campaigns, appeals, Exponential Layouts, non-profit
type: help
---
# Communications for non-profits

This folder is for communications staff, fundraisers and editors. A non-profit's site is not only a brochure: it is
where supporters subscribe, where events are announced, where appeals run, and often where people find help in their
own language.

## Contents

1. [What exists](#what-exists)
2. [The pages of this folder](#the-pages-of-this-folder)
3. [How the channels work together](#how-the-channels-work-together)
4. [What is not built in](#what-is-not-built-in)

## What exists

| Channel | What Exponential ships | Source |
|---|---|---|
| Supporter newsletter | `cjw_newsletter`: lists, double opt-in, editions as content, sending by cron, bounces, blacklist, archive | [cjw_newsletter](../../../features/6.0/extensions/cjw_newsletter.md) |
| Events | `ezwebin` classes `event`, `event_calendar` and `multicalendar`, with calendar and program views | [Events and calendars](../../businesses/members/events-and-calendars.md) |
| Languages | Up to 62 content languages per installation, translation per object, language switcher | [The 62-language limit](../../government/languages/the-62-language-limit.md) |
| Campaign pages | Exponential Layouts, with layout rules that can switch on and off by date | [Landing pages with Layouts](../../creators/website-creators/landing-pages-with-layouts.md) |
| Feeds | RSS 2.0, Atom, podcast feeds | [RSS feeds for your site](../../creators/website-creators/rss-feeds-for-your-site.md) |
| Social previews | Title, description and image for sharing | [Metadata and social sharing](../../creators/website-creators/metadata-and-social-sharing.md) |

All are installed through `composer.json`; none has a license fee.

## The pages of this folder

| Page | What it answers |
|---|---|
| [Supporter newsletter with cjw_newsletter](supporter-newsletter-with-cjw-newsletter.md) | Lists, consent, writing and sending, delivery, list hygiene |
| [Events and calendars for non-profits](events-and-calendars-for-non-profits.md) | Event pages, calendars, sign-up, what is missing |
| [Multilingual non-profit sites](multilingual-non-profit-sites.md) | Serving people in several languages with a small team |
| [Campaigns and appeals](campaigns-and-appeals.md) | Appeal pages that switch on and off by date, and connecting them to giving |

## How the channels work together

| Moment | Site | Newsletter | Event | Campaign |
|---|---|---|---|---|
| An appeal starts | Campaign layout switches on | Edition announces it | | Donate button |
| A fundraising event | Event page with sign-up form | Edition invites | Event in the calendar | Campaign page links to it |
| The year ends | Annual publication | Edition thanks supporters | | Thank-you message |
| A new service in a new language | Translated pages | Edition in that language | | |

A simple [content calendar](../../customers/day-to-day/content-calendar.md) keeps them in step.

## What is not built in

| Not built in | Plan instead |
|---|---|
| Social media posting from the site | Post by hand or with an outside tool; share links carry your metadata |
| Ticketing, seat limits, iCal export, recurring events | See [Events and calendars for non-profits](events-and-calendars-for-non-profits.md#what-is-not-built-in) |
| SMS campaigns | Not part of `cjw_newsletter`'s documented features; use an outside service with consent |
| Donor relationship management (CRM) | An outside CRM; the site is not one |

## Related pages

- [Non-profits: start here](../non-profits-start-here.md)
- [Newsletters (Businesses)](../../businesses/newsletters/README.md)
- [Campaigns and landing pages (Businesses)](../../businesses/nonprofits/campaigns-and-landing-pages.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
