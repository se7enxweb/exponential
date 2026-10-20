---
title: "FAQ: search"
audience: government, editor, admin, project-manager
category: public-sector
order: 160
summary: Answers to the search questions public bodies ask about Exponential - built-in site search, Solr with ezfind, searching documents, search in several languages, search statistics and privacy, and searching in the administration.
keywords: search FAQ, site search, Solr, ezfind, facets, document search, search statistics, privacy, administration search, content finder, public sector
type: help
---
# FAQ: search

This page answers the search questions public bodies ask about Exponential. The answers come from
[Site search](../../creators/website-creators/site-search.md),
[Search with ezfind and Solr](../../creators/website-creators/search-with-ezfind-and-solr.md) and [Searching](../../searching.md).

## Contents

1. [Does the site have search?](#does-the-site-have-search)
2. [When do we need Solr?](#when-do-we-need-solr)
3. [Can visitors search inside documents?](#can-visitors-search-inside-documents)
4. [Can we see what people search for?](#can-we-see-what-people-search-for)
5. [How do editors find content?](#how-do-editors-find-content)
6. [Is there training?](#is-there-training)

## Does the site have search?

Yes. Exponential has a built-in search engine with a search box and results templates, settings to limit and tune it,
delayed indexing and index rebuilding ([Site search](../../creators/website-creators/site-search.md)).

## When do we need Solr?

When you need facets, better relevance, the content of files, or more scale. The ezfind extension switches site search
to Apache Solr; `exp:solr` controls it ([Search with ezfind and Solr](../../creators/website-creators/search-with-ezfind-and-solr.md),
[Search backends](../../hosting/basics/search-backends.md)). Solr is a separate service your host must run.

## Can visitors search inside documents?

With Solr through ezfind, file contents can be indexed ([Search with ezfind and Solr](../../creators/website-creators/search-with-ezfind-and-solr.md)).
Check with your supplier which file types your configuration indexes.

## Can we see what people search for?

Yes: **Setup > Search statistics** lists search phrases ([Links, URL aliases, wildcards and search statistics](../../../guides/urls-and-aliases.md)).
Search phrases can contain personal data; include them in your retention rules ([Privacy standards](../../../standards/privacy.md#known-gaps)).

## How do editors find content?

The administration has a quick search, a search scope, advanced search and opening an item by its number or address
([Searching](../../searching.md)); **Setup > Content finder** finds content across the tree
([Content finder](../../../features/6.0/content-finder.md)).

## Is there training?

Yes: the [Search course](../../trainers/search/README.md), 20 lessons, about 10 hours, for administrators and developers.

## Related pages

- [Government FAQ](README.md)
- [FAQ: integrations and APIs](integrations-faq.md)
- [Site search](../../creators/website-creators/site-search.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
