---
title: Emergency alert readiness
audience: government, admin, editor, hosting
category: public-sector
order: 190
summary: How a public body prepares its Exponential site to publish urgent information fast and keep it available under load - who publishes, a prepared alert content area, cache clearing, Velocity's response cache, load and fallback - using only features Exponential has, and naming what it does not have.
keywords: emergency, alert, urgent information, crisis communication, banner, publishing fast, cache clearing, load, traffic spike, Velocity, static cache, maintenance page, public sector
type: help
---
# Emergency alert readiness

This checklist is for the communications lead, the site administrator and the host of a public body that may need to
publish urgent information: a storm warning, a water advisory, a closure. It uses features Exponential has.

Exponential has **no dedicated emergency alert feature**: no alert banner type, no push to phones, no link to public
warning systems. What it offers is fast publishing, cache control and an engine that can take load. Plan around that.

## Contents

1. [People](#people)
2. [Prepared content](#prepared-content)
3. [Publishing fast](#publishing-fast)
4. [Making the change visible](#making-the-change-visible)
5. [Staying up under load](#staying-up-under-load)
6. [Practice](#practice)

## People

- [ ] Named people who may publish an alert at any hour, with accounts and two-step sign-in set up ([Two-step sign-in](../../../guides/two-factor-login.md)).
- [ ] Their role lets them publish to the alert area without waiting for an approval workflow, or an approver is always reachable ([Workflows](../../../guides/workflows.md)).
- [ ] The site administrator and the host reachable outside office hours ([Incident readiness](incident-readiness.md)).

## Prepared content

- [ ] A content area for alerts, shown prominently by the design (for example a block on the home page or in the shared header) ([Shared header and footer](../../creators/website-creators/shared-header-and-footer.md), [Landing pages with layouts](../../creators/website-creators/landing-pages-with-layouts.md)).
- [ ] Templates for common alerts written in plain language and translated in advance ([Plain language checklist](plain-language-checklist.md)).
- [ ] Alerts as text, not images, so screen readers and slow connections get them.
- [ ] An RSS feed of alerts for partners who republish them ([RSS feeds for your site](../../creators/website-creators/rss-feeds-for-your-site.md)).

## Publishing fast

- [ ] The alert editors have practiced publishing on the training installation.
- [ ] They know how to update and then remove the alert, and the versions page to see what was said when ([Versions of an object](../../../guides/content-history.md)).
- [ ] Notifications to subscribers, if your site uses them ([Notifications](../../../guides/notifications-administrator.md)); newsletters are not instant ([Sending newsletters](../../creators/website-creators/sending-newsletters.md)).

## Making the change visible

- [ ] Editors know publishing clears the view cache of the page; the administrator knows how to clear more when the alert appears in shared parts: **Setup > Cache management** or `php bin/php/ezcache.php --clear-id=template-block,content --allow-root-user` ([Caches](../../../guides/caches.md)).
- [ ] If the site uses Velocity's response cache, clear it too: `exp:velocity cache clear` ([Velocity response cache](../../../features/6.0/velocity-response-cache.md)).
- [ ] If a CDN or reverse proxy is in front, its purge procedure is written down ([HTTP caching and CDNs](../../creators/website-creators/http-caching-and-cdns.md)).
- [ ] If static cache is used, its regeneration is known ([Static cache and Velocity](../../creators/website-creators/static-cache-and-velocity.md)).

## Staying up under load

- [ ] The host has estimated a traffic spike and knows the caching layers ([Caching layers](../../hosting/performance/caching-layers.md)).
- [ ] Caches preloaded for the key pages ([Preloading caches](../../../guides/preloading-caches.md)).
- [ ] Measured with a benchmark, not guessed ([Benchmarking](../../../guides/benchmarking.md)).
- [ ] A fallback: if the site goes down, maintenance mode can show a message with the essential facts; it is a plain page served before the database is touched ([Maintenance mode](../../../guides/maintenance-mode.md)).
- [ ] Other channels (social media, phone line) carry the same message.

## Practice

- [ ] An exercise once a year: publish a test alert on staging, clear caches, remove it, time each step.
- [ ] Lessons recorded and this checklist updated.

## Related pages

- [Government checklists](README.md)
- [Incident readiness](incident-readiness.md)
- [Performance and caching](../../creators/website-creators/performance-and-caching.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
