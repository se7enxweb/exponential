---
title: Refresh a page that shows old content
audience: government, editor
category: public-sector
order: 590
summary: For editors - when a published change does not show, check what you see, clear the cache of the page from the administration's Clear cache box (Content - node or subtree), and know which caches only the web team can clear.
keywords: cache, old content, page not updated, Clear cache, Content - node, Content - subtree, view cache, static cache, Velocity, CDN, browser cache
type: help
---
# Refresh a page that shows old content

This page is for editors who published a change and still see the old page. Exponential clears the view cache of the
object and related pages when you publish, but lists in cached blocks, the static cache, Velocity's response cache, a
CDN or your own browser can still show the old version for a while ([Caches](../../../../guides/caches.md)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check the result](#check-the-result)
4. [Pitfalls](#pitfalls)

## Before you start

- Make sure the change is published: **Manage versions** shows your version as *Published* (not *Draft* or *Pending*).
- Look in a private browser window, signed out: your own browser may show a stored copy.

## Steps

1. Open the page in the administration.
2. In the **Clear cache** box of the administration's side column, choose:
   - **Content - node** for this page;
   - **Content - subtree** for this page and everything below it (for example a folder whose list is old);
3. press **Clear**.
4. Reload the page signed out in a private window.
5. Still old: the copy is in a cache in front of the CMS (static cache, Velocity's response cache, a CDN). Ask the web
   team; they can clear those (for example `./console exp:velocity cache clear --apply` for Velocity).

The other choices of the box (**Template**, **Ini settings**, **Static**, **All caches**) are for administrators; they
affect the whole site.

## Check the result

- [ ] The page shows the new content signed out.
- [ ] Lists on other pages (front page, menus) refresh with their own expiry; check them later or ask the web team.

## Pitfalls

| Pitfall | Avoid it |
|---|---|
| The version is still pending | an approval holds it ([Send a page for approval](send-a-page-for-approval.md)) |
| Clearing **All caches** on a busy site | slows the whole site; leave it to administrators |
| A cached file (PDF) is old | replace the file properly ([Replace a document with a new version](replace-a-document-with-a-new-version.md)) |
| The change was made in another language | check the language siteaccess |

## Related pages

- [Publishing tasks](README.md)
- [View cache and cache blocks](../../../creators/website-creators/view-cache-and-cache-blocks.md)
- [When something goes wrong](../../../when-something-goes-wrong.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
