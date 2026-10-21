---
title: Caching in the app
audience: mobile-app-developer, developer
category: mobile-apps
order: 620
summary: Cache Exponential content in an app so it starts fast and saves data - what to cache, cache keys per user, version and language, freshness with dateModified and childrenCount, invalidation after writes, images, the API key rate limit and request budgets.
keywords: cache, app cache, cache key, dateModified, childrenCount, freshness, stale-while-revalidate, invalidation, images, rate limit, 429, Retry-After, request budget, performance, mobile app
type: help
---
# Caching in the app

This page is for app developers who design the data layer of an app. The server does not send HTTP caching headers
the app could rely on for API answers ([Caching on the server](caching-on-the-server.md)), so the app decides what to
keep and for how long. The advice is general practice, tied to the fields Exponential gives you.

## Contents

1. [What to cache](#what-to-cache)
2. [Cache keys](#cache-keys)
3. [Deciding freshness](#deciding-freshness)
4. [After a write](#after-a-write)
5. [Request budgets and rate limits](#request-budgets-and-rate-limits)

## What to cache

| Data | Cache? | Notes |
|---|---|---|
| Lists of a folder (first page) | yes | show it at once on start, refresh in the background |
| Detail answers | yes | keyed by node id |
| Image files | yes | your platform's image cache, keyed by URL |
| Alias URLs from `expimage::url` | yes | with the item |
| The site's languages, the alias list | yes, for a day | they change rarely |
| Credentials | only in secure storage | [Storing secrets on devices](../auth/storing-secrets-on-devices.md) |
| Answers of another user | never | a shared device signs out, the cache goes |

## Cache keys

Include everything that changes the answer:

```text
<installation base URL> | <user id> | <route> | <ResponseGroups> | <Translation> | <offset>/<limit>/<sort>
```

The user matters because the server answers per user: two users can see different children of the same folder. The
language matters because the same node has a different body per `Translation`.

## Deciding freshness

- **Stale while revalidate.** Show the cached copy at once, request a fresh one, and replace it if it changed. This
  is the pattern that makes an app feel instant.
- **`dateModified`** of an item (Unix time) tells whether a cached detail is still current: compare it with the value
  in the newest list answer before re-reading the detail.
- **`metadata.childrenCount`** of a list changes when items are added or removed; a cheap
  `GET .../childrenCount` tells whether a folder changed in count.
- **Sort by `modified`** (`.../list/offset/0/limit/20/sort/modified/desc`) to see the most recently changed items
  first, and stop reading when you reach items older than your last sync.
- Remember that the server's own answer cache may be up to `DefaultCacheTTL` (600 seconds by default) old.

## After a write

- Insert the item the write returned into the local list at once (optimistic update), marked as pending until a read
  confirms it.
- Drop the cached detail of an updated or removed item, and the first page of its parent list.
- Do not read the list again immediately to confirm: the server's answer cache can still return the old list
  ([Caching on the server](caching-on-the-server.md#freshness-after-publishing)).

## Request budgets and rate limits

- A personal API key is limited to `RateLimitPerMinute` requests per minute (120 by default) and answers `429` with
  `Retry-After` above it ([Personal API keys for tools](../auth/personal-api-keys-for-tools.md#limits)). A back end
  that serves many phones with one key must cache, or it will hit the limit.
- Batch where the API allows: one list with `ResponseGroups=Fields` instead of one detail call per row;
  `expnode::getMany` reads several nodes by id in one call.
- Do not poll in the background more often than the content changes; refresh on app start, on pull-to-refresh and
  when a screen opens.

## Related pages

- [Syncing and offline reading](syncing-and-offline-reading.md)
- [Listing children and pagination](../rest-api/listing-children-and-pagination.md)
- [Image aliases for screen sizes](../media-and-images/image-aliases-for-screen-sizes.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
