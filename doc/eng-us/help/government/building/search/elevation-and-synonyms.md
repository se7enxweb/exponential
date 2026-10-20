---
title: Elevation and synonyms
audience: government, admin, editor
category: public-sector
order: 70
summary: How a public body puts the right page first for common searches and makes different words find the same content: ezfind's elevation module (Solr only), Solr synonyms, eztags synonyms, and what the built-in engine offers instead.
keywords: elevation, elevate, best bets, promoted results, synonyms, synonyms.txt, ezfind elevate, eztags synonyms, keywords, search tuning
type: help
---
# Elevation and synonyms

This page is for administrators and editors who tune search: "parking permit" should lead to the permit page first,
and "rubbish", "trash" and "waste" should find the same service. What is possible depends on the engine.

## Contents

1. [Elevation with ezfind](#elevation-with-ezfind)
2. [Synonyms in Solr](#synonyms-in-solr)
3. [Synonyms with eztags](#synonyms-with-eztags)
4. [With the built-in engine](#with-the-built-in-engine)
5. [Finding what to tune](#finding-what-to-tune)

## Elevation with ezfind

The `ezfind` extension has an elevation module (`ezfind/elevate`, views `elevate`, `elevation_detail`,
`remove_elevation`, policy `ezfind/elevate`). Its page explains: "Elevating an object" for a given search query means
that this object will be returned among the very first search results when a search is triggered using this search
query. You choose the object (**Browse**), the search query, the **Language**, and whether it is a **Fuzzy match**,
then **Elevate**; existing configurations can be listed **By object** or **By search query** and cancelled.

It works only when `ezfind` is active with a Solr server; on an installation without it the address answers that the
module is not available.

## Synonyms in Solr

Solr reads synonyms from its configuration (`synonyms.txt` in the core's `conf` directory). The file shipped with
`ezfind`'s Solr configuration holds only test mappings, so you write your own, per language core if you use one core
per language, and reload Solr. This is a server configuration change; record it in your installation documentation.

## Synonyms with eztags

With `eztags`, a tag can have synonyms; content tagged with the main tag is found from its tag page under any synonym
([Tagging with eztags](../../../creators/website-creators/tagging-with-eztags.md)). This helps browsing by theme on
any engine.

## With the built-in engine

There is no elevation or synonym list. Instead:

- add the words people use to the page's keywords field (marked searchable);
- write a short "Also known as" line in the summary;
- link a "Top tasks" list from the search page.

## Finding what to tune

**Setup > Search statistics** lists phrases by how often they were searched and the average number of results; the
phrases that found nothing are the first candidates ([Search statistics](../../../../guides/urls-and-aliases.md#5-search-statistics)).

## Related pages

- [Solr with ezfind](solr-with-ezfind.md)
- [Search statistics and privacy](search-statistics-and-privacy.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
