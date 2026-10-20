---
title: URLs, redirects and moved pages
audience: customer, editor, admin
category: customer-success
order: 18
summary: What a site owner needs to know about page addresses on Exponential - renaming and moving keep old addresses working, removing does not - and which tool to ask for when a page goes, a campaign needs a short address or a relaunch changes many addresses.
keywords: URLs, addresses, URL aliases, redirects, moved pages, renamed pages, wildcards, URL translator, 301, relaunch, broken links
type: help
---
# URLs, redirects and moved pages

This page is for owners and editors who rename, move and remove pages and want visitors and search engines to keep
finding them. Exponential makes readable addresses from content names and keeps old addresses working after most
changes ([Redirects and moved content](../../creators/website-creators/redirects-and-moved-content.md)). The one
case that needs a decision is removing a page.

## Contents

1. [What happens by itself](#what-happens-by-itself)
2. [When a page goes](#when-a-page-goes)
3. [Short addresses for campaigns](#short-addresses-for-campaigns)
4. [A relaunch or a big reorganization](#a-relaunch-or-a-big-reorganization)
5. [Rules for editors](#rules-for-editors)

## What happens by itself

From [Redirects and moved content](../../creators/website-creators/redirects-and-moved-content.md):

| Event | Result |
|---|---|
| An item is renamed | a new address is made; the old one stays and redirects to the new one |
| An item is moved | the same: the old path redirects |
| A translation is renamed | the same, per language |
| An item is removed | the address answers "not found"; nothing redirects |

So editors may rename and move freely. Hiding an item also takes it off the site, with everything below it
([Publishing, versions and the trash](../../publishing-and-versions.md#hide-an-item)).

## When a page goes

Before removing a page that people may have bookmarked or that search engines know, decide where its visitors should
go instead:

| Situation | What to ask for | Tool |
|---|---|---|
| The page has a natural successor | the old address added as an alias of the new page | the node's **URL aliases** tab |
| A whole folder of old pages goes | a wildcard from the old pattern to the new place | **Setup > URL wildcards** |
| Nothing replaces it | let it answer "not found", deliberately | none |

Wildcards are only consulted when no alias matches, and **Try an address** on the wildcards page shows which wildcard
an address matches without changing anything
([URL aliases, wildcards and search statistics](../../../guides/urls-and-aliases.md#4-url-wildcards)).

> **Tip**
> Removed content goes to the trash and can be restored with its address
> ([Archiving and the trash](archiving-and-the-trash.md)). If a removal was a mistake, restoring is better than a
> redirect.

## Short addresses for campaigns

For a printed flyer or a radio spot you may want `/autumn` instead of a long path. Your administrator can make a
**global URL alias** in **Setup > URL translator**, with "Alias should redirect" for a permanent redirect
([Redirects and moved content](../../creators/website-creators/redirects-and-moved-content.md)). Note every short
address in your [Content calendar](content-calendar.md) with the date it may be removed.

## A relaunch or a big reorganization

When many addresses change at once, ask your partner for a redirect plan before the launch
([Redirects and moved content](../../creators/website-creators/redirects-and-moved-content.md)):

1. Export the old addresses (sitemap, the search engine's list of indexed pages, server logs).
2. Decide each new destination: same page, a better page, or none.
3. Patterns become wildcards; single pages become aliases.
4. Every old address is tested before launch for a permanent redirect to the right place.
5. After launch, watch the error log and the search engines' crawl errors.

Your part as owner is step 2: only you know which new page is the right one.

## Rules for editors

- [ ] Choose names with care: the name becomes the address.
- [ ] Rename and move; do not remove and recreate.
- [ ] Before removing a page, ask whether anything replaces it.
- [ ] Link to pages with the link tool of the editor, not by typing addresses; links to content follow moves.
- [ ] Report addresses that answer "not found" with the address and where you found the link.

## Related pages

- [URL aliases and nice URLs](../../creators/website-creators/url-aliases-and-nice-urls.md)
- [URL wildcards and custom aliases](../../creators/website-creators/url-wildcards.md)
- [SEO checks](seo-checks.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
