---
title: SEO monthly checklist
audience: customer, editor, businesses
category: customer-success
order: 17
summary: A one-hour monthly checklist for the owner or marketing lead of an Exponential site - search phrases without results, broken links, broken relations, removed pages, new content quality, sitemap and webmaster tools - with a record line to keep.
keywords: SEO checklist, monthly, search statistics, broken links, link management, redirects, sitemap, titles, descriptions, webmaster tools
type: help
---
# SEO monthly checklist

This page is a **checklist** for one hour a month. It uses the tools that come with Exponential and assumes the
answers from [SEO checks](seo-checks.md) are known (whether there is a sitemap, which field is the description).
Some steps need Setup pages; do them with your administrator if you do not have access.

## Contents

1. [Visitors and search](#visitors-and-search)
2. [Links and addresses](#links-and-addresses)
3. [New content of the month](#new-content-of-the-month)
4. [Technical signals](#technical-signals)
5. [Record](#record)

## Visitors and search

- [ ] Open **Setup > Search statistics** (`/search/stats`). Filter the phrases that found nothing and order by count
      ([URL aliases, wildcards and search statistics](../../../guides/urls-and-aliases.md#5-search-statistics)).
- [ ] For each of the top phrases without results: is the content missing (write it), named differently (add the
      word to a title or intro), or hidden (check its visibility)?
- [ ] If the page says no siteaccess records searches, decide whether to ask for it
      ([Site search and what visitors look for](site-search-and-what-visitors-look-for.md)).

## Links and addresses

- [ ] **Setup > Link management** (`/url/list`): show **invalid** links; fix or remove each one in the content that
      uses it. The link check runs in the `infrequent` cron part
      ([URL aliases, wildcards and search statistics](../../../guides/urls-and-aliases.md)).
- [ ] **Broken relations** from the content root: fix each source
      ([The content checks](../../../features/6.0/content-checks.md)).
- [ ] Pages removed this month: each one has a replacement address (an alias on the new page, or a wildcard), or is
      deliberately gone ([URLs, redirects and moved pages](urls-redirects-and-moved-pages.md)).
- [ ] Test two or three old addresses you know were moved: they should land on the new page.

## New content of the month

Pick five items published this month (the dashboard lists recent content):

- [ ] Title short and clear; the address reads well.
- [ ] Intro or description filled in.
- [ ] One main heading; headings in order.
- [ ] Images have alternative texts.
- [ ] Link texts say where they lead.
- [ ] Each translation has its own title, not the source language's
      ([Translation routine](translation-routine.md)).

Tell the writers what you found, kindly and specifically. The editor rules are in
[Accessibility checklist for editors](accessibility-checklist-for-editors.md), which overlaps with good SEO.

## Technical signals

- [ ] The search engines' webmaster tools: new crawl errors, pages excluded from the index.
- [ ] The sitemap, if your site has one: it opens, lists this month's new pages and does not list hidden or
      members-only pages ([XML sitemaps](../../creators/website-creators/sitemaps.md)).
- [ ] `robots.txt` still lists the sitemap and was not changed by accident
      ([robots.txt for your site](../../creators/website-creators/robots-txt.md)).
- [ ] The site opens on one host name with HTTPS; the other spellings redirect.
- [ ] Anything slow or broken goes to the host or partner with the address and the time.

## Record

```text
SEO MONTH: __________   Checked by: ______________   Time spent: ______
Top search phrases without results: ______________________________________
Content written or renamed because of them: ______________________________
Invalid links fixed: ____   Broken relations fixed: ____   Redirects added: ____
Crawl errors in webmaster tools: ____   Sent to partner/host: ______________
```

## Related pages

- [SEO checks](seo-checks.md)
- [Weekly owner routine](weekly-owner-routine.md)
- [Monthly operations checklist](../../businesses/templates/monthly-operations-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
