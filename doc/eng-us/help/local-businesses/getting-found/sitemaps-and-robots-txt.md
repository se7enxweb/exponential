---
title: Sitemaps and robots.txt for a local business
audience: local-business, businesses, customer
category: business-solutions
order: 23
summary: What an XML sitemap and robots.txt do for a small business site, how Exponential produces them with bcgooglesitemaps and the shipped robots.txt, what to ask your builder for, and how to check both yourself in a browser.
keywords: sitemap, XML sitemap, bcgooglesitemaps, robots.txt, cron job, search engines, webmaster tools, crawl, local business
type: help
---
# Sitemaps and robots.txt for a local business

This page is for the owner of a small business site who wants search engines to know every page. Two small files do
this: the **sitemap** lists your pages, and **robots.txt** tells crawlers where not to go and where the sitemap is.

## Contents

1. [The sitemap](#the-sitemap)
2. [robots.txt](#robotstxt)
3. [What to ask your builder](#what-to-ask-your-builder)
4. [Check it yourself](#check-it-yourself)
5. [Common problems](#common-problems)

## The sitemap

Exponential generates sitemaps with one of two extensions, both in the standard `composer.json`:

| Extension | Cron job part | When |
|---|---|---|
| `bcgooglesitemaps` | `googlesitemaps`, `googlesitemapsmultilingual` | a plain sitemap; the usual choice |
| `xrowmetadata` | `sitemap` and others | when `xrowmetadata` is used for page metadata anyway |

Source: [XML sitemaps](../../creators/website-creators/sitemaps.md) and
[bcgooglesitemaps](../../../features/6.0/extensions/bcgooglesitemaps.md).

The sitemap is a file the server writes when the cron job runs, usually once a night. A new page appears in it after
the next run. For a site in two languages, the multilingual part writes one sitemap per language.

Settings worth knowing (in `bcgooglesitemaps.ini`):

| Setting | What it does | For a local business |
|---|---|---|
| `SitemapRootNodeID` | where the sitemap starts | usually the front page |
| `ClassFilterType`, `ClassFilterArray[]` | include or exclude classes | exclude images, files and banners, which are not pages |
| `Main_Node_Only` | list each page once | `true` if pages appear in two places |

## robots.txt

A `robots.txt` ships at the web root of every installation
([robots.txt for your site](../../creators/website-creators/robots-txt.md)). It already keeps crawlers out of the
site search results, print views and similar pages (for example `Disallow: /content/search/` in the shipped file).
Your builder adds one line with the sitemap address:

```text
Sitemap: https://www.example.com/sitemap.xml
```

robots.txt is a polite request, not a lock. Content that must stay private needs permissions, not robots.txt.

## What to ask your builder

1. Switch on `bcgooglesitemaps` and set it to exclude classes that are not pages.
2. Add the sitemap cron job to the schedule ([Cronjobs](../../../guides/cronjobs.md)).
3. Add the `Sitemap:` line to `robots.txt`.
4. Register the sitemap in the webmaster tools of the search engines you care about. Those tools are outside
   Exponential; the account should be in the business's name.

## Check it yourself

| Check | How | Good result |
|---|---|---|
| The sitemap exists | open `https://<your site>/sitemap.xml` in a browser | a list of addresses |
| New pages appear | publish a page, wait for the next night, look again | the new address is listed |
| Only real pages | read the list | no image or file objects, no test pages |
| robots.txt names it | open `https://<your site>/robots.txt` | a `Sitemap:` line with your address |
| Addresses use HTTPS and your real host | read the list | `https://www.example.com/...`, not a test host |

## Common problems

| Problem | Likely cause | Who fixes it |
|---|---|---|
| Sitemap never changes | the cron job does not run | host or builder |
| Sitemap shows a test address | the protocol or host setting is wrong | builder |
| Members-only pages appear | permissions or class filter | builder; check with a page you know is restricted |
| Search engine says "blocked by robots.txt" | a `Disallow:` line too wide | builder |

## Related pages

- [Local SEO basics](local-seo-basics.md)
- [XML sitemaps](../../creators/website-creators/sitemaps.md)
- [robots.txt feature](../../../features/6.0/robots-txt.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
