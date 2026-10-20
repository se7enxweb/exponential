---
title: No version queries on asset links
audience: designer
category: theme-design
order: 250
summary: Why designs of Exponential never add ?v= or similar version queries to stylesheet and script links, how new files reach browsers instead, and what to do when a browser still shows an old file.
keywords: version query, cache busting, ?v=, query string, stylesheet link, script link, browser cache, hard reload, AppendLastModifiedTime, stale css
type: help
---
# No version queries on asset links

This page is for designers who are used to "cache busting" with `style.css?v=3`. In Exponential designs this is not
done, and not needed.

## Contents

1. [The rule](#the-rule)
2. [How new files reach browsers](#how-new-files-reach-browsers)
3. [Why not add a query anyway](#why-not-add-a-query-anyway)
4. [A browser still shows the old file](#a-browser-still-shows-the-old-file)

## The rule

Do not add `?v=`, `?ver=`, a date or a hash as a query string to `<link>` or `<script>` URLs in templates, and do not
propose it in reviews. This is a project rule for every design.

## How new files reach browsers

Stylesheets and scripts go through the packer, which names each packed file after the list of its sources and the
**newest modification time** of those sources (`ezjscore.ini [Packer] AppendLastModifiedTime=enabled`). Edit a file
and the next page links a file with a different name. Browsers have never seen it, so they fetch it. See
[The packer](the-packer.md).

Images and fonts referenced from CSS are fetched by URL as well; give a replaced image a new file name when it must
change at once.

## Why not add a query anyway

- It duplicates what the packer already does, and goes stale when someone forgets to raise it.
- Hand-written links with queries bypass the packer and the design cascade.
- Some caches and proxies treat URLs with queries differently.

## A browser still shows the old file

1. Check that the page links the new packed file (view the source). If it links the old one, a cached template still
   holds the link: clear `template-block` (and `content` for full views); see [The packer cache and deploys](packer-cache-and-deploy.md).
2. If the link is new but the style is old, hard-reload the browser.
3. On sites with a service worker (the media design registers one), the worker may serve an older page; reload twice or
   clear site data in the browser's developer tools.

## Related pages

- [The packer](the-packer.md)
- [Loading stylesheets](loading-stylesheets.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
