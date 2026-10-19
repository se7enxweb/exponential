---
title: Mobile checklist
audience: businesses, project-manager, app-creator
category: business-solutions
order: 110
summary: A checklist for launching a mobile site, an app on the REST API, a partner integration or a feed with Exponential - strategy, responsive site, API and credentials, content and preview, feeds and partners, reliability, and the routine after launch.
keywords: checklist, mobile, app launch, responsive, REST API, API keys, OAuth, feeds, partners, reliability, navigation cache, business
type: help
---
# Mobile checklist

This page is for project leads, product owners and administrators who launch something for phones or for other
programs: a responsive site, an app, a partner integration, a feed. Copy the parts that apply into your project
tracker. Each item links to the page that explains it.

## Contents

1. [Strategy](#strategy)
2. [Responsive site](#responsive-site)
3. [API and credentials](#api-and-credentials)
4. [Content, preview and languages](#content-preview-and-languages)
5. [Feeds and partners](#feeds-and-partners)
6. [Reliability](#reliability)
7. [Launch](#launch)
8. [After launch](#after-launch)

## Strategy

- [ ] The channels are chosen from what customers do, measured on your own data
      ([Mobile strategy](mobile-strategy.md#start-from-what-customers-do)).
- [ ] Website first: the site works on phones before an app is started.
- [ ] For an app: hybrid (site in templates, app on the API) unless there is a reason not to
      ([The three options](mobile-strategy.md#the-three-options)).
- [ ] Every row of "what an app adds to your workload" has an owner and a budget
      ([Mobile strategy](mobile-strategy.md#what-an-app-adds-to-your-workload)).

## Responsive site

- [ ] The design works from 320 px wide, without horizontal scrolling, and at 200% zoom
      ([Responsive sites](responsive-sites.md#the-design-decides)).
- [ ] Images use aliases sized for small screens ([Images that fit small screens](responsive-sites.md#images-that-fit-small-screens)).
- [ ] Forms and checkout pass the phone test ([A test plan you can run yourself](responsive-sites.md#a-test-plan-you-can-run-yourself)).
- [ ] Editors know they can work from a phone in the administration ([Editors on phones](mobile-strategy.md#editors-on-phones)).

## API and credentials

- [ ] `ezprestapi` 1.2.6 or later is active and `ezprestapiprovider` removed
      ([Questions for your administrator](rest-api-v1-and-v2-for-businesses.md#questions-for-your-administrator)).
- [ ] The app reads at v1 or v2; writes, if any, at v2; no update through REST is planned (not implemented yet).
- [ ] The right credential per program: OAuth2 for apps whose users sign in, a personal key for one user's script,
      an expservices token for remote services ([Apps and API keys](apps-and-api-keys.md)).
- [ ] A dedicated integration user per app and partner, with the smallest role and scope.
- [ ] Secrets kept on a server or in a secret store, never inside the app or a repository.
- [ ] HTTPS required for every API request.
- [ ] The `Authorization` header reaches PHP on your web server.

## Content, preview and languages

- [ ] The content the app reads is named: classes, folders, languages.
- [ ] Editors can preview content as it will appear in the app before publishing
      ([Things to plan early](headless-with-the-rest-api.md#things-to-plan-early)).
- [ ] The app learns about changes by a purge hook or by limited polling.
- [ ] Image aliases for the app's sizes exist.
- [ ] Each language of the app has its content translated ([Multilingual checklist](../multilingual/multilingual-checklist.md)).

## Feeds and partners

- [ ] Feeds have sources, addresses, formats and autodiscovery links
      ([Feed checklist](feeds-and-syndication.md#feed-checklist)).
- [ ] Podcast feeds have owner, artwork and category, and playable episodes.
- [ ] Syndication endpoints are protected; credentials are out of version control.
- [ ] Partners have written agreements on what they may show, refresh intervals and an exit plan
      ([Agreements and controls](selling-through-networks-and-partners.md#agreements-and-controls)).

## Reliability

- [ ] Monitoring checks the site, the administration login and one authenticated API read
      ([Watching it: health checks](app-backend-reliability.md#watching-it-health-checks)).
- [ ] The app handles 503 with `Retry-After`, 401, 403 and rate limits
      ([What the app must do](app-backend-reliability.md#what-the-app-must-do)).
- [ ] Rate limits configured and enforceable (APCu available).
- [ ] Key and token expiry dates in the operations calendar.
- [ ] If the browser navigation cache is used: support knows about the "second view", and someone can switch it off
      ([Offline and repeat visits](offline-and-repeat-visits.md)).

## Launch

- [ ] A test user installs the app, signs in, reads content and, if offered, orders or submits a form.
- [ ] A content change published in the administration appears in the app within the agreed time.
- [ ] Revoking the test credential stops access at the next request.
- [ ] The website looks and behaves exactly as before.
- [ ] Partners confirm their feeds or imports work.

## After launch

- [ ] Monthly: the API keys overview for keys never used, expiring soon, or refused
      ([Control, limits and the audit trail](apps-and-api-keys.md#control-limits-and-the-audit-trail)).
- [ ] Monthly: feed exports and imports ran; syndication status has no failed items.
- [ ] After each upgrade: the API check, a feed check and the phone test are repeated.
- [ ] When a partner or an app is retired: its credentials are revoked the same day.

## Related pages

- [Mobile apps, headless use and networks](README.md)
- [Multi-brand checklist](../multisite/multi-brand-checklist.md)
- Go-live checklist template
- [The launch checklist](../../creators/website-creators/launch-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
