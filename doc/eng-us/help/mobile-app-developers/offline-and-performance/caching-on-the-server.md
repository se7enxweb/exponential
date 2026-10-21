---
title: Caching on the server
audience: mobile-app-developer, app-creator, developer, hosting
category: mobile-apps
order: 610
summary: How the server caches what an app reads - the REST answer cache (per user, GET and HEAD only, 600 seconds by default, refusals never cached), its settings and cache group, what this means for freshness after publishing, Velocity's response cache and authenticated requests, and the headers expservices answers carry.
keywords: REST answer cache, ApplicationCache, DefaultCacheTTL, CacheTTL, rest cache, content tag, freshness, publish, Velocity response cache, Authorization, no-cache, server cache, mobile app
type: help
---
# Caching on the server

This page is for app developers who wonder why an item published a minute ago does not show in the app yet, and for
the server team who tunes it. It explains the caches on the server that touch API answers.

## Contents

1. [The REST answer cache](#the-rest-answer-cache)
2. [Freshness after publishing](#freshness-after-publishing)
3. [Tuning it](#tuning-it)
4. [Velocity and API requests](#velocity-and-api-requests)
5. [The remote services](#the-remote-services)

## The REST answer cache

The REST framework keeps answers in an application cache (`rest.ini [CacheSettings]`):

| Setting | Default | Meaning |
|---|---|---|
| `ApplicationCache` | `enabled` | the global switch |
| `ApplicationCacheDefault` | `enabled` | cache every controller action unless a block says otherwise |
| `DefaultCacheTTL` | `600` | seconds an answer is kept |

Since 6.0.15 it keeps one answer per user and only for `GET` and `HEAD`; a refusal is never cached, and a cache file
that cannot be read back is generated again ([Permissions of the REST interface](../../../guides/api-keys.md#32-permissions-of-the-rest-interface)).
The cache is the group `rest` of `site.ini` ("REST Application cache"), tagged `content` and `rest`, so clearing the
content caches clears it too.

## Freshness after publishing

With the defaults, a read may return an answer up to ten minutes old. For an app that means:

- After an editor publishes, the app may see the change only when the cached answer expires.
- After the app itself writes, its own next read may still be the cached answer. Show the result of the write from
  the write's answer (or a fresh read through the remote services) instead of re-reading the list at once.
- If the business needs faster updates, the server team lowers the TTL for the actions the app uses.

## Tuning it

A block per controller action, or per controller, sets its own rules; the shipped `rest.ini` comments show the
pattern for the old controller name. For the current controller:

```ini
# settings/override/rest.ini.append.php
[ezp7xRestContentController_list_CacheSettings]
ApplicationCache=enabled
CacheTTL=120
```

The name is `<controllerClass>_<action>_CacheSettings` (the actions are `viewContent`, `list`, `viewFields`,
`viewField`, `countChildren`); without such a block the system looks for `<controllerClass>_CacheSettings`. After a
change to `rest.ini`: clear the INI cache, reload PHP-FPM and restart Velocity
([REST API v1 and v2: configuration](../../../guides/rest-api-v1-and-v2.md#5-configuration)). The example values are
for illustration; choose them with the site owner.

## Velocity and API requests

When the site is served by Velocity, its response cache keeps rendered anonymous pages, and never a request with an
`Authorization` header ([Velocity response cache: what it never stores](../../hosting/performance/velocity-response-cache.md#what-it-never-stores)).
Authenticated REST calls therefore always reach PHP (and the REST answer cache). Anonymous calls to public remote
services are ordinary `GET` requests; whether they are cached depends on the headers they are sent with.

## The remote services

The ezjscore call answers seen on the demonstration server carried `Cache-Control: no-cache, must-revalidate` and an
`Expires` date in the past, so neither browsers nor proxies keep them. Cache them in the app or your back end
([Caching in the app](caching-in-the-app.md)).

## Related pages

- [Caches: what each one holds and how to clear it](../../../guides/caches.md)
- [Caching in applications](../../creators/app-creators/caching-in-applications.md)
- [HTTP caching, proxies and CDNs](../../creators/website-creators/http-caching-and-cdns.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
