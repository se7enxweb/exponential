---
title: Redirect old addresses after a reorganization
audience: government, editor, admin
category: public-sector
order: 370
summary: For editors and web officers - keep old addresses working after pages were renamed, moved, merged or removed - what Exponential redirects by itself, aliases for single pages, URL wildcards for groups with Try an address, and a test list.
keywords: redirect, old addresses, reorganization, relaunch, URL alias, URL wildcards, Redirecting URL, Try an address, 301, moved content, removed pages, broken links
type: help
---
# Redirect old addresses after a reorganization

This page is for editors and web officers after a restructuring: departments merged, a section renamed, old pages
retired. Bookmarks, printed leaflets and other sites point to the old addresses. Exponential keeps redirects for
renamed and moved pages by itself; removed pages and whole patterns need you
([Redirects and moved content](../../../creators/website-creators/redirects-and-moved-content.md),
[URLs and aliases, 4](../../../../guides/urls-and-aliases.md#4-url-wildcards)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check the result](#check-the-result)
4. [Pitfalls](#pitfalls)

## Before you start

| Change | Redirect needed from you? |
|---|---|
| A page renamed | no: the old alias stays and redirects |
| A page moved | no: the old path redirects |
| A page removed | yes: the address answers "not found" |
| A group of pages moved to another site | yes: a wildcard to the other site |

Make a list of old addresses that matter (from the old sitemap, search statistics or the server's logs) with their new
destination.

## Steps

**A single removed page:**

1. Open the replacement page, its **URL aliases** page, **Create new alias** with the old path as **URL alias name**,
   **Place alias on the site root** if the old path started at the root, **Alias should redirect to its destination**
   ticked, **Create** ([Give a page a short address](give-a-page-a-short-address.md)).

**A group of addresses:**

1. **Setup > URL wildcards**.
2. **New URL wildcard**: the pattern, for example `old-department/*`; **Destination**: `new-department/{1}`; tick
   **Redirecting URL** (a 301 redirect). Create it.
3. **Try an address**: type an old address, for example `old-department/contact`; the page says which wildcard it
   matches and what it becomes, without changing anything.

**An address that should lead to a module page** (search, a form view): **Setup > URL translator**, a global alias with
redirect.

## Check the result

- [ ] For each address on your list: open it signed out (or `curl -sI <address>`): a 301 to the right place.
- [ ] Watch the error log and search engines' crawl reports for misses in the weeks after.

## Pitfalls

| Pitfall | Avoid it |
|---|---|
| A wildcard that catches more than intended | wildcards are tried only when no alias matches, in creation order; test with **Try an address** |
| Redirect chains (old to older to new) | point directly to the final address |
| Redirecting everything to the front page | visitors lose their way; send them to the nearest relevant page |
| Removing pages without a plan | decide the destination before removing |

## Related pages

- [Publishing tasks](README.md)
- [Move a page](move-a-page.md)
- [URL wildcards](../../../creators/website-creators/url-wildcards.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
