---
title: Offline and performance for apps
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 600
summary: The landing page of the offline and performance pages for app developers - where answers are cached (the REST answer cache, Velocity, your back end, the app), how fresh content is, syncing for offline reading, and the browser rules (CORS, CSP, the service worker) for web apps.
keywords: performance, caching, offline, sync, freshness, REST answer cache, Velocity, CORS, CSP, service worker, web app, PWA, mobile app
type: help
---
# Offline and performance for apps

This folder is for app developers who want an app that feels fast on a slow network, keeps working in a tunnel, and
does not load the server more than it must. It explains the caches between the app and the content, and what the app
has to do itself.

## Contents

1. [The layers](#the-layers)
2. [Pages in this folder](#pages-in-this-folder)

## The layers

| Layer | Who runs it | Holds | Read |
|---|---|---|---|
| The app's own store | you | answers, images, queued writes | [Caching in the app](caching-in-the-app.md), [Syncing and offline reading](syncing-and-offline-reading.md) |
| Your back end, if any | you | shared answers for every phone | [A back end for your app](../auth/a-backend-for-your-app.md) |
| The REST answer cache | the server, `rest.ini [CacheSettings]` | `GET` answers per user, ten minutes by default | [Caching on the server](caching-on-the-server.md) |
| Velocity's response cache | the server, when Velocity serves the site | anonymous pages; never requests with an `Authorization` header | same |
| The browser navigation cache | the site's service worker | site pages for repeat visits; not app data | [Web apps, CORS and CSP](web-apps-cors-and-csp.md) |

## Pages in this folder

| Page | Answers |
|---|---|
| [Caching on the server](caching-on-the-server.md) | How fresh REST answers are and why |
| [Caching in the app](caching-in-the-app.md) | What to cache, keys, freshness with `dateModified`, rate limits |
| [Syncing and offline reading](syncing-and-offline-reading.md) | Keeping a folder on the device, queued writes |
| [Web apps, CORS and CSP](web-apps-cors-and-csp.md) | Browser apps: origins, CORS, Content Security Policy, the service worker |

## Related pages

- [Headless Exponential: caching and performance](../../creators/app-creators/headless-exponential.md#caching-and-performance)
- [App backend reliability (for businesses)](../../businesses/mobile-and-headless/app-backend-reliability.md)
- [Offline and repeat visits (for businesses)](../../businesses/mobile-and-headless/offline-and-repeat-visits.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
