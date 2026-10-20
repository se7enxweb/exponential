---
title: Growing your site
audience: customer, businesses, project-manager
category: customer-success
order: 1
summary: The landing page for site owners who want more from an Exponential site they already run - newsletters, campaign landing pages, more languages and sites, apps and APIs, the writing assistant, feeds, forms and member areas - with every page of the section and a way to plan the next step.
keywords: growth, site owner, newsletter, landing pages, layouts, languages, multisite, mobile, REST API, remote services, Exponential Intelligence, feeds, forms, members, roadmap
type: help
---
# Growing your site

This section is for owners and managers whose site runs well and who want it to do more: reach readers by e-mail, run
campaigns, add a language or a brand, feed an app, let a writing assistant prepare drafts, publish feeds and podcasts,
collect information or open a members area. Much of this ships with Exponential and only needs switching on and a
routine. Each page says what the software offers, what to decide, who on the team does the work, what to ask your
partner, and links to the pages with the technical steps.

## Contents

1. [Before you grow](#before-you-grow)
2. [The pages of this section](#the-pages-of-this-section)
3. [Suggested reading order](#suggested-reading-order)
4. [What ships, what needs a partner](#what-ships-what-needs-a-partner)

## Before you grow

Every new channel is a new routine for your team. Before you add one, check that the basics of
[Running your site day to day](../day-to-day/README.md) are in place: roles, approvals where needed, and a weekly
look at the site. Then plan with the [Growth roadmap worksheet](growth-roadmap-worksheet.md).

## The pages of this section

| Area | Pages |
|---|---|
| E-mail | [Newsletters](newsletters.md), [Newsletter routine](newsletter-routine.md) |
| Campaigns | [Landing pages with layouts](landing-pages-with-layouts.md), [Campaign landing page checklist](campaign-landing-page-checklist.md) |
| More audiences | [Adding a language](adding-a-language.md), [Adding a site](adding-a-site.md) |
| Apps and integrations | [Mobile and apps](mobile-and-apps.md), [The REST API for owners](rest-api-for-owners.md), [Remote services (expservices)](remote-services-expservices.md), [Planning an integration](integrations-planning.md) |
| Writing assistant | [Exponential Intelligence](exponential-intelligence.md), [Using AI drafts responsibly](using-ai-drafts-responsibly.md) |
| Syndication | [RSS and podcast feeds](rss-and-podcast-feeds.md) |
| Visitors who take part | [Forms and collected information](forms-and-collected-information.md), [Member areas and registration](member-areas-and-registration.md) |
| Planning | [Growth roadmap worksheet](growth-roadmap-worksheet.md) |

## Suggested reading order

1. [Growth roadmap worksheet](growth-roadmap-worksheet.md): decide what you want, in what order.
2. The page of the area you chose.
3. Its checklist or routine page, before the first real use.
4. [Planning an integration](integrations-planning.md) whenever another system is involved.

## What ships, what needs a partner

| Capability | Ships with Exponential | Usually needs a partner for |
|---|---|---|
| Newsletters (`cjw_newsletter`) | yes, in `composer.json` ([Newsletters for businesses](../../businesses/newsletters/README.md)) | classes, cronjobs, mail transport, sender domain |
| Landing pages (Exponential Layouts) | yes ([The layouts editor](../../layouts-editor.md)) | new layout types or block types |
| More content languages | yes ([Content languages](../../../guides/content-languages.md)) | siteaccess settings, language switcher |
| More sites on one installation | yes ([One installation, many sites](../../businesses/multisite/one-installation-many-sites.md)) | siteaccess, DNS, certificates |
| REST API and API keys | yes ([REST API v1 and v2](../../../guides/rest-api-v1-and-v2.md)) | the app or front end itself |
| Remote services (`expservices`) | yes, in the default distribution ([Remote services and apps](../../../guides/remote-services-and-apps.md)) | scripts and portals |
| Writing assistant (`expai`) | yes, active on new installations; idle without a provider key (`README.md`) | provider choice, cron, data policy |
| RSS, Atom and podcast feeds | yes ([RSS feeds](../../../guides/rss-feeds.md)) | templates for feed output, if needed |
| Forms and member registration | yes ([Collected information](../../../guides/collected-information.md)) | form classes, CAPTCHA, templates |

## Related pages

- [Running your site day to day](../day-to-day/README.md)
- [Mobile and headless for businesses](../../businesses/mobile-and-headless/README.md)
- [What you can build](../../creators/website-creators/what-you-can-build.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
