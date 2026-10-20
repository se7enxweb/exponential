---
title: Offline and repeat visits
audience: businesses, admin, website-creator
category: business-solutions
order: 90
summary: The optional browser navigation cache of Exponential (the service worker /index.js) explained for businesses - faster repeat page visits, the strict rules about what it never stores, the "second view" effect after publishing, its off switch, and what it does not do.
keywords: service worker, navigation cache, index.js, sw.js, repeat visits, offline, stale-while-revalidate, Velocity, response cache, second view, kill switch, business
type: help
---
# Offline and repeat visits

This page is for owners, support staff and administrators of sites where visitors come back often: news, catalogs,
member sites, store front pages. Exponential can let the visitor's own browser answer a repeat page visit at once and
refresh the page in the background. This page explains what that gives you, the rules that keep it safe, the one
effect your support team must know, and how to switch it off. It is optional; nothing on your site depends on it.

## Contents

1. [What it does](#what-it-does)
2. [Why it helps](#why-it-helps)
3. [What it never stores](#what-it-never-stores)
4. [The "second view" effect](#the-second-view-effect)
5. [Switching it off](#switching-it-off)
6. [What it is not](#what-it-is-not)
7. [Questions for your team](#questions-for-your-team)

## What it does

The site's service worker is a small script, `/index.js`, "the one service worker script of an Exponential site"
(file header of `index.js`). Its first and, so far, only feature is the **navigation cache**: "repeat page visits
answered from the browser, refreshed in the background". `/sw.js` was its earlier name and remains as a one-line
script that loads `/index.js`, so browsers registered under the old name run the same code
([6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md)).

The worker is registered "by the site design after page load"; the header of `index.js` names the media theme
(`extension/sevenx_themes_media`) as the design that does so. The hosting documentation describes it as part of the
setup "under Velocity" ([Velocity response cache: the browser navigation cache](../../hosting/performance/velocity-response-cache.md#the-browser-navigation-cache)).
Whether your site uses it therefore depends on your design and server: ask your team.

## Why it helps

The source explains it with two measured numbers from one installation
([The response cache, and getting out of the network's way](../../../bc/6.0/response-cache-and-navigation.md#added-a-navigation-cache-in-the-browser-indexjs)):

| Measurement | Value |
|---|---|
| Network round trip to a real browser connection | 12.47 ms |
| The server's answer to the same request | 0.36 ms |

"A round trip cannot be shortened, only skipped." So the page is answered from the browser's own store, and the
network is asked afterwards. These figures are the source's measurements on one server, not a promise for your site;
the point for a business is that a repeat visit no longer waits for the network.

## What it never stores

From the same source, the rules:

- only same-origin GET navigations (assets are already cached by the browser);
- never a page fetched with a session cookie, so a signed-in visitor's page is never kept;
- never anything but a successful page (200);
- never `/admin`, `/user`, `/explayouts_ui` or `/api`.

"If a session cookie appears, the page-side script unregisters the worker and clears its cache, rather than leaving
it to serve one visitor another's page."

For a business this means: customer accounts, baskets, checkout and the administration are never answered from this
cache.

## The "second view" effect

The worker shows the stored copy first and refreshes it in the background, so "a published change appears on the
**second** view, not the first". Tell your support team and editors:

| Question | Answer |
|---|---|
| "I published a correction and still see the old page" | Reload once more; the first view refreshed the copy |
| "A customer still sees the old price" | Their next visit shows the new one; signed-in customers are not affected |
| "It must change immediately for everyone" | Ask the administrator about the response cache and, if needed, the off switch |

The hosting checklist includes: "Support knows the navigation cache's 'second view' behavior."

> **Warning**
> For time-critical changes (a recalled product, a wrong price), plan with your administrator how to clear Velocity's
> response cache, and accept that a repeat visitor may see the old page once
> ([Velocity response cache](../../hosting/performance/velocity-response-cache.md)).

## Switching it off

The documented **kill switch**: serve `index.js` as a script that unregisters itself. "Every browser drops it on its
next update check, within 24 hours, because `index.js` is `no-cache`." The file `/sw.js` must be kept until no browser
can still hold the old registration; removing it early "would leave those browsers on their last copy of the old
worker for good" (file header of `sw.js`).

## What it is not

- **Not an offline mode for the whole site.** The sources describe a cache of pages a browser has already visited, not
  a site that works without a connection. Pages never visited are not there.
- **Not an app.** It needs no installation by the visitor and offers no notifications.
- **Not a replacement for server caches.** The response cache and HTTP caching still do the main work
  ([Caching for availability](../reliability/caching-for-availability.md)).

## Questions for your team

- [ ] Does our design register the service worker, and on which siteaccesses?
- [ ] Is `/index.js` served with revalidation (the source lists it among paths that are "always revalidated")?
- [ ] Does support know about the second view?
- [ ] Who can switch it off, and how quickly?
- [ ] Is `/sw.js` still in place for older registrations?

## Related pages

- [App backend reliability](app-backend-reliability.md)
- [Responsive sites](responsive-sites.md)
- [Static and response cache](../reliability/static-and-response-cache.md)
- [Velocity response cache](../../hosting/performance/velocity-response-cache.md)
- [The response cache, and getting out of the network's way](../../../bc/6.0/response-cache-and-navigation.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
