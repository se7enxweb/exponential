---
title: Caches explained
audience: non-technical, editor, customer
category: plain-language
order: 15
summary: Why a site keeps ready-made copies of its pages, why that sometimes shows an old page after you published, what you can safely do about it from the browser, and when to ask somebody else - with a bakery comparison.
keywords: cache, old page, stale page, clear cache, refresh, reload, browser cache, CDN, speed, explained
type: help
---
# Caches explained

## Contents

1. [The comparison: a bakery](#the-comparison-a-bakery)
2. [Most of the time you do nothing](#most-of-the-time-you-do-nothing)
3. [When you see an old page](#when-you-see-an-old-page)
4. [Clearing a cache yourself](#clearing-a-cache-yourself)
5. [When to ask somebody else](#when-to-ask-somebody-else)

## The comparison: a bakery

A bakery does not bake each loaf when a customer walks in: it bakes in the morning and keeps the bread on the shelf,
so customers are served at once. A website does the same with pages. Building a page takes work; keeping a ready-made
copy (a **cache**) makes the site fast. When the recipe changes, the old loaves are taken off the shelf and new ones
are baked.

## Most of the time you do nothing

Exponential renews its caches when content is published, so clearing by hand is the exception
([Which cache do I clear, and how?](../../manual/which-cache-do-i-clear.md)). When you publish an article, the pages
that show it are rebuilt by themselves.

## When you see an old page

If you published and still see the old version, go through this list in order:

1. **Was it really published?** Look in **My drafts** on the dashboard. If your draft is there, open it and press
   **Send for publishing** ([Drafts, versions and publishing explained](drafts-versions-and-publishing-explained.md)).
2. **Is it waiting for approval?** Look in **My pending items**.
3. **Is it your browser?** Your own browser keeps copies too. Reload the page while holding the Shift key, or open it
   in a private window.
4. **Is it a cache of the site?** Wait a minute and reload. If it is still old, clear the content cache (below) or ask.

## Clearing a cache yourself

Only if your role allows it (the permission is called `setup/managecache`):

1. In the right toolbar of the administration, use **Clear cache**: choose **Content** in the list and click
   **Clear**.
2. Reload the page in a private window.

Clear the smallest cache that solves the problem. **Clear all caches** works too, but the site is slow for a while
afterwards because every page is built again on its first visit, so avoid it at busy times
([Which cache do I clear, and how?](../../manual/which-cache-do-i-clear.md#what-can-go-wrong)).

## When to ask somebody else

| Situation | Who |
|---|---|
| The page is still old after clearing the content cache | Your host: a cache outside Exponential (a proxy, a CDN or the web server's response cache) may hold it |
| A design or settings change does not show | The developer who made it: templates and settings have their own caches, and settings may need a server reload |
| The whole site is slow after a clear | Wait a few minutes; if it stays slow, tell your host ([Speed and caching](../../customers/healthy/speed-and-caching.md)) |

## Related pages

- [Words explained](README.md)
- [Which cache do I clear, and how?](../../manual/which-cache-do-i-clear.md)
- [Speed and caching](../../customers/healthy/speed-and-caching.md)
- [Something looks wrong](../when-stuck/something-looks-wrong.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
