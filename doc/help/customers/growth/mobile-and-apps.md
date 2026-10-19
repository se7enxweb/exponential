---
title: Mobile and apps
audience: customer, businesses, partner
category: customer-success
order: 8
summary: The owner's view of reaching people on phones and through apps with an Exponential site - a responsive site first, then an app or front end on the REST API or remote services, feeds and repeat visits - with the questions to answer before commissioning an app.
keywords: mobile, apps, responsive, headless, REST API, remote services, API keys, OAuth, feeds, app backend, site owner
type: help
---
# Mobile and apps

This page is for owners who are asked "should we have an app?" or who want the site to work better on phones.
Exponential offers responsive designs, a REST content API, personal API keys and OAuth for apps, remote services,
feeds and an optional browser navigation cache for repeat visits
([Mobile and headless for businesses](../../businesses/mobile-and-headless/README.md)). Most organizations should get
the phone experience of the website right first.

## Contents

1. [Website first](#website-first)
2. [The channels at a glance](#the-channels-at-a-glance)
3. [Before you commission an app](#before-you-commission-an-app)
4. [Who does what](#who-does-what)
5. [Keeping it running](#keeping-it-running)

## Website first

A responsive site works on every phone without an install. Check yours regularly:

- [ ] The front page, an article, a form and the search work on a phone.
- [ ] Images are not larger than needed; pages load quickly on a mobile connection.
- [ ] Buttons are large enough and forms are easy to fill in.
- [ ] Text can be zoomed to 200% without sideways scrolling ([Accessibility upkeep](../day-to-day/accessibility-upkeep.md)).

The business test plan is on [Responsive sites](../../businesses/mobile-and-headless/responsive-sites.md). Editors can
work on a phone too: the administration has responsive designs
([admin3 responsive admin](../../../features/6.0/admin3-responsive-admin.md)).

## The channels at a glance

From [Mobile and headless for businesses](../../businesses/mobile-and-headless/README.md):

| Channel | What Exponential offers | For owners |
|---|---|---|
| Visitors on phones | responsive public designs | [Responsive sites](../../businesses/mobile-and-headless/responsive-sites.md) |
| Your own app or front end | the REST content API, read at v1 and v2, write at v2 | [The REST API for owners](rest-api-for-owners.md) |
| Programs and integrations | personal API keys, OAuth2 for apps, remote service tokens | [Apps and API keys](../../businesses/mobile-and-headless/apps-and-api-keys.md) |
| Remote administration, portals, scripts | over a thousand named remote services | [Remote services (expservices)](remote-services-expservices.md) |
| Feed readers and podcast directories | RSS, Atom, OPML and Apple Podcasts exports | [RSS and podcast feeds](rss-and-podcast-feeds.md) |
| Repeat visitors | an optional navigation cache in the browser | [Offline and repeat visits](../../businesses/mobile-and-headless/offline-and-repeat-visits.md) |

## Before you commission an app

| Question | Why |
|---|---|
| What can the app do that the website cannot? | an app is a second product to maintain |
| Does it only read content, or also write (comments, orders, forms)? | writes need v2 of the REST API or remote services, and careful permissions |
| Do its users sign in? | apps whose users sign in use OAuth2; a program acting as one user uses an API key ([Apps and API keys](../../businesses/mobile-and-headless/apps-and-api-keys.md)) |
| Who builds and updates the app for each new phone operating system? | the app is not part of Exponential |
| What happens when the site is in maintenance? | the app depends on the API being available ([App backend reliability](../../businesses/mobile-and-headless/app-backend-reliability.md)) |
| Which content may the app show? | the API applies the same policies as the website |

Plan it like any integration: [Planning an integration](integrations-planning.md).

## Who does what

| Task | Who |
|---|---|
| Decide the channels and what each is for | owner ([Mobile strategy](../../businesses/mobile-and-headless/mobile-strategy.md)) |
| Build and publish the app | app developer or partner |
| Create the app's credentials and limit them | administrator |
| Keep the content right | the editorial team, as for the website |
| Keep the API available, monitored and backed up | host or partner |

## Keeping it running

- Review the app's credentials once a year with the [access review](../../businesses/security/access-reviews.md):
  keys are revoked by the owner or an administrator, and end when they expire or the owner is disabled
  ([Personal API keys](../../../guides/api-keys.md)).
- Tell the app developer before upgrades; ask your partner what changes for API clients
  ([REST API v1 and v2](../../../guides/rest-api-v1-and-v2.md)).
- Use the [Mobile checklist](../../businesses/mobile-and-headless/mobile-checklist.md) before each app release.

## Related pages

- [Mobile and headless for businesses](../../businesses/mobile-and-headless/README.md)
- [Headless with the REST API](../../businesses/mobile-and-headless/headless-with-the-rest-api.md)
- [The REST API for owners](rest-api-for-owners.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
