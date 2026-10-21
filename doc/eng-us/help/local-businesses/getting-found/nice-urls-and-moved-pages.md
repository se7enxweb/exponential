---
title: Nice URLs and moved pages
audience: local-business, businesses, customer, editor
category: business-solutions
order: 24
summary: How Exponential makes readable page addresses from page names, what happens to the old address when a page is renamed or moved, short addresses for flyers and signs, and the care to take when replacing an old website.
keywords: nice URLs, URL alias, address, redirect, renamed page, moved page, short URL, flyer, QR code, old website, wildcards, local business
type: help
---
# Nice URLs and moved pages

This page is for the editor or owner of a small business site who wants readable, stable page addresses. Exponential
handles most of this by itself.

## Contents

1. [Addresses come from names](#addresses-come-from-names)
2. [Renaming and moving pages](#renaming-and-moving-pages)
3. [Short addresses for print](#short-addresses-for-print)
4. [Replacing an old website](#replacing-an-old-website)
5. [Checklist](#checklist)

## Addresses come from names

When you publish a page called "Opening hours" in a folder called "Visit us", Exponential gives it the address
`/Visit-us/Opening-hours`. This is called a **URL alias** and is built in
([URL aliases and nice URLs](../../creators/website-creators/url-aliases-and-nice-urls.md)).

So choose names with care:

- short and clear: "Menu", not "Our wonderful menu for 2026";
- no dates in folder names that will change;
- the same language as the visitors who will read it (a second language has its own addresses:
  [Multilingual for tourist areas](../marketing/multilingual-for-tourist-areas.md)).

## Renaming and moving pages

When a page is renamed or moved, the old address keeps working and sends visitors to the new one. Exponential keeps
**history aliases** for this by default
([Redirects and moved content](../../creators/website-creators/redirects-and-moved-content.md)). Links in old
newsletters, printed QR codes and search engine results do not break.

What does break the old address:

- deleting the page (moving it to the trash);
- an administrator removing history aliases.

If a page is no longer needed, consider hiding it or pointing to a replacement page instead of deleting it. Routine:
[URLs, redirects and moved pages](../../customers/day-to-day/urls-redirects-and-moved-pages.md).

## Short addresses for print

A flyer or a sign in the window needs a short address, such as `example.com/menu` or `example.com/offer`. An
administrator can create **custom aliases** and **wildcards** that send a short address to any page
([URL wildcards and custom aliases](../../creators/website-creators/url-wildcards.md)). Good uses:

| Short address | Goes to |
|---|---|
| `/menu` | `/Food-and-drink/Menu` |
| `/hours` | `/Visit-us/Opening-hours` |
| `/offer` | this month's special, changed each month |

Keep a list of the short addresses you have printed, so nobody removes one that is still on a sign.

## Replacing an old website

When a new Exponential site replaces an old site, the old addresses are in search results, bookmarks and other
people's pages. Before the switch:

1. Make a list of the old addresses that matter (the menu, the contact page, the most visited pages).
2. Ask your builder to send each to its new page with a permanent redirect (wildcards or web server rules).
3. After the switch, open each old address and check it lands on the right new page.

## Checklist

- [ ] Folder and page names are short and will not change often.
- [ ] Old addresses of a replaced site redirect to the new pages.
- [ ] Printed short addresses are on a list and tested.
- [ ] Nobody deletes pages that have printed or well-known addresses without a replacement.

## Related pages

- [Local SEO basics](local-seo-basics.md)
- [Structuring the content tree](../../creators/website-creators/structuring-the-content-tree.md)
- [URL aliases, wildcards and search statistics](../../../guides/urls-and-aliases.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
