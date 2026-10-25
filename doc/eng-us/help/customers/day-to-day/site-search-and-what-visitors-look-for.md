---
title: Site search and what visitors look for
audience: customer, editor, admin
category: customer-success
order: 22
summary: How a site owner uses the site's own search as a source of ideas and a quality check - switching on search statistics, reading phrases that found nothing, fixing content rather than search, what the built-in engine and Solr do, and a monthly routine.
keywords: site search, search statistics, search phrases, no results, findability, built-in search, Solr, ezfind, searchable attributes, content gaps
type: help
---
# Site search and what visitors look for

This page is for the owner and editor-in-chief. The phrases visitors type into your site's search box are the most
direct feedback you get: they say in the visitors' own words what they expect to find. Exponential can record them
and show which ones found nothing ([URL aliases, wildcards and search statistics](../../../guides/urls-and-aliases.md#5-search-statistics)).
How editors search inside the administration is a different topic: [Searching](../../searching.md).

## Contents

1. [How the site's search works](#how-the-sites-search-works)
2. [Switching on search statistics](#switching-on-search-statistics)
3. [Reading the phrases](#reading-the-phrases)
4. [Fix the content, not the search](#fix-the-content-not-the-search)
5. [When to ask for more](#when-to-ask-for-more)
6. [A monthly routine](#a-monthly-routine)

## How the site's search works

- The built-in engine keeps its index in the database and indexes the fields marked **Searchable** in each class when
  content is published. Visitors only find what they may read
  ([Site search](../../creators/website-creators/site-search.md)).
- Large sites, or sites that need facets and relevance tuning, use Solr through `ezfind`
  ([Search with ezfind and Solr](../../creators/website-creators/search-with-ezfind-and-solr.md)).
- Hidden content and content in a section visitors may not read is not found by visitors.

## Switching on search statistics

Search statistics are **off by default** (`site.ini [SearchSettings] LogSearchStats=disabled`), because phrases can be
personal data ([Site search](../../creators/website-creators/site-search.md)). To use them:

1. Decide with whoever is responsible for data protection whether, and for how long, you keep phrases. This is not
   legal advice.
2. Ask your administrator to set `LogSearchStats=enabled` on the **public** siteaccess.
3. Note in your privacy information that search phrases are recorded.

**Setup > Search statistics** (`/search/stats`) then shows every phrase, how often it was searched and the average
number of results. It filters the phrases that found nothing, orders by count, phrase or fewest results, and runs a
phrase again with one click. When no siteaccess records searches, the page says so
([URL aliases, wildcards and search statistics](../../../guides/urls-and-aliases.md)).

![Setup, Search statistics: the number of phrases and searches, the phrases that found nothing, and the list ordered by count](../../images/creators/search-statistics.webp)

## Reading the phrases

| What you see | What it usually means | What to do |
|---|---|---|
| A frequent phrase with no results | the content is missing | write it, or link to where it is answered |
| A phrase with no results, but you have the page | visitors use other words | use their words in the title or intro |
| A phrase with many results, searched often | visitors cannot find it in the menu | add a link to the menu or front page |
| Product or person names | people expect a directory or a catalog page | consider one |
| Misspellings | normal | add the common spelling to the text where natural |

## Fix the content, not the search

Most search problems are content problems: titles that use internal words, intros left empty, information only in a
PDF. Before asking for search settings to change, change the words. Editors control titles and intros; the site
builder decides which fields are searchable ([Site search](../../creators/website-creators/site-search.md)).

> **Tip**
> The search statistics are also a good list of topics for the [Content calendar](content-calendar.md).

## When to ask for more

Ask your partner when:

- important pages are found, but far down the list (relevance tuning needs Solr);
- visitors need filters such as type, date or category (facets);
- search is slow on a large site;
- a newly published page cannot be found at all: the index may need rebuilding, or indexing may be delayed by cron
  (`DelayedIndexing`) ([Site search](../../creators/website-creators/site-search.md)).

## A monthly routine

- [ ] Open **Setup > Search statistics**, filter phrases without results, order by count.
- [ ] Take the top ten; for each, decide: write, rename, link or ignore.
- [ ] Run each fixed phrase again with one click to confirm it now finds the page.
- [ ] Note the actions in the [SEO monthly checklist](seo-monthly-checklist.md) record.

## Related pages

- [Site search](../../creators/website-creators/site-search.md)
- [Searching (for editors)](../../searching.md)
- [Intranet search](../../businesses/intranets/intranet-search.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
