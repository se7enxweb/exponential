---
title: What a municipality publishes
audience: government, project-manager, website-creator
category: public-sector
order: 20
summary: The content cities, towns and councils typically publish - service pages, council business, notices, news, local information, events - and what each needs from Exponential.
keywords: municipal website, council website, services, waste collection, permits, council meetings, public notices, local news, events, scope
type: help
---
# What a municipality publishes

This page is for project managers and website creators at a city, town or council who define the scope of a site. It
lists what local government typically publishes (general public-sector practice) and what each kind of content needs.

## Contents

1. [Services](#services)
2. [Council business](#council-business)
3. [Notices, news and alerts](#notices-news-and-alerts)
4. [Local information and events](#local-information-and-events)
5. [What the CMS must do](#what-the-cms-must-do)
6. [Questions to settle](#questions-to-settle)

## Services

Most visits to a municipal site are about a service. A typical list of service areas: waste and recycling, roads and
parking, housing, planning and building permits, local taxes and payments, civil registration (births, marriages,
deaths), social care, schools and childcare, libraries, sports and leisure, cemeteries, licenses for businesses and
events.

Each service page answers the same questions: what it is, who can use it, what it costs, how to apply, how long it
takes, what happens next, who to contact. A Service class with those fields keeps the pages consistent (see
[Content model](content-model.md)). The transaction itself (applying, paying, booking) usually happens in a separate
system that the page links to.

## Council business

| Content | Rhythm |
|---|---|
| Council and committee members (their public role) | after each election |
| Meeting calendar | per year |
| Agendas and meeting papers | days before each meeting, deadlines often set by law |
| Minutes | after approval at the next meeting |
| Decisions | after each meeting |
| Petitions, questions from the public | per meeting |

The details are in [Council meetings and local services](council-meetings-and-local-services.md).

## Notices, news and alerts

| Content | Life |
|---|---|
| Public notices (planning applications, road closures, statutory notices) | a fixed period, then hidden or archived |
| News | weeks |
| Urgent alerts (severe weather, water supply, service disruption) | hours to days |
| Changes to collection days, opening hours | until the next change |

Exponential has no dedicated alert banner. An alert is usually an object in a folder that the front page template
lists when it is not empty, or a block in the layouts editor that editors fill and empty
([The layouts editor](../../../layouts-editor.md)). The `hide.php` cronjob can hide notices whose end date has passed
([Cronjobs](../../../../guides/cronjobs.md)).

## Local information and events

Opening hours of offices, parks, facilities, local history, maps, election information, events calendar. Events are
covered by the shipped Event and Event calendar classes, whose calendar view comes with the ezwebin design
(`event_view_calendar.tpl`, `event_view_program.tpl`); a site on another design needs its own templates.

## What the CMS must do

| Need | Exponential | Read |
|---|---|---|
| Consistent service pages | a Service class with required fields | [Content classes for websites](../../../creators/website-creators/content-classes-for-websites.md) |
| Departments edit only their pages | sections and roles | [Sections](../../../../guides/sections.md) |
| Forms for reports and requests | the information collector, with CSRF form tokens and optional CAPTCHA | [Forms with the information collector](../../../creators/website-creators/forms-with-the-information-collector.md) |
| Notices that expire | `hide.php` / `unpublish.php` cronjobs | [Cronjobs](../../../../guides/cronjobs.md) |
| Residents follow news | RSS exports, notifications, newsletters | [RSS feeds](../../../../guides/rss-feeds.md), [Newsletters with CJW Newsletter](../../../creators/website-creators/newsletters-with-cjw-newsletter.md) |
| Finding a service fast | site search and good titles | [Site search](../../../creators/website-creators/site-search.md) |

## Questions to settle

- [ ] Which ten services bring most visits? Build those pages first, and best.
- [ ] Where do council documents come from (a committee management system?), and who uploads them?
- [ ] Who may publish an urgent alert at night or on a weekend?
- [ ] Which languages do residents need beyond the official one?

## Related pages

- [Municipal and local government](README.md)
- [Content model for municipal and local government](content-model.md)
- [Planning your website](../../../creators/website-creators/planning-your-website.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
