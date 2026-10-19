---
title: Site search
audience: website-creator, admin
category: website-creation
order: 560
summary: Give a site a search - the built-in search engine, the search box and results templates, the settings that limit and tune it, delayed indexing, rebuilding the index, and search statistics.
keywords: search, site search, eZSearchEngine, content/search, advancedsearch, search box, search results, SearchSettings, DelayedIndexing, updatesearchindex, search statistics, website creator
type: help
---
# Site search

This page is for website creators who add search to a site. Exponential has a built-in search engine that keeps its
index in the database; it is enough for most sites. Large sites or sites that need facets and relevance tuning move to
Solr ([Search with ezfind and Solr](search-with-ezfind-and-solr.md)).

![Search statistics: what visitors searched for and how many results they got](../../images/creators/search-statistics.webp)

![The search page of the public site with the results for running](../../images/creators/site-search-results.webp)

## Contents

1. [The built-in engine](#the-built-in-engine)
2. [The search box](#the-search-box)
3. [The results page](#the-results-page)
4. [Settings that matter](#settings-that-matter)
5. [Indexing](#indexing)
6. [Search statistics](#search-statistics)

## The built-in engine

`site.ini [SearchSettings] SearchEngine=eZSearchEngine` indexes the attributes marked **Searchable** in each class when
content is published. Visitors only find what they may read.

## The search box

A form that sends `SearchText` to `content/search`:

```text
<form role="search" method="get" action={'content/search'|ezurl}>
  <label for="site-search">{'Search'|i18n( 'design/mysite' )}</label>
  <input type="search" id="site-search" name="SearchText">
  <button type="submit">{'Search'|i18n( 'design/mysite' )}</button>
</form>
```

`content/advancedsearch` offers more fields (phrase, class, date). Both are disallowed for crawlers in the shipped
`robots.txt`.

## The results page

Override `content/search.tpl` in your design. It receives the results and the search text; show each hit with its line
view, the number of results and paging. Tell the visitor when nothing was found and what to try instead.

## Settings that matter

`site.ini [SearchSettings]`:

| Key | Shipped | Meaning |
|---|---|---|
| `MaximumSearchLimit` | `30` | the most results one page may ask for |
| `MaximumSearchTextWords`, `MaximumSearchTextLength` | `16`, `1024` | longer search texts are cut |
| `AllowEmptySearch` | `disabled` | an empty search is refused (costly) |
| `EnableWildcard`, `MinCharacterWildcard` | `false`, `3` | `term*` searches; costly on large sites |
| `StopWordThresholdPercent` | `60` | words in more than this share of objects are ignored |
| `DelayedIndexing` | `disabled` | `enabled` or `classbased`: index by cron instead of on publish |
| `LogSearchStats` | `disabled` | record search phrases |

Mark only useful attributes **Searchable**: titles, intros, bodies; not internal codes.

## Indexing

- Content is indexed on publish, or by the default cron part (`indexcontent.php`) with delayed indexing
  ([10.8 Search](../../../install/10-after-installing.md#108-search-indexing-and-image-handling)).
- Rebuild the whole index after an import or a database move:

  ```bash
  php bin/php/updatesearchindex.php --clean --allow-root-user
  ```

## Search statistics

With `LogSearchStats=enabled` on the public siteaccess, **Setup > Search statistics** (`/search/stats`) shows every
phrase, how often it was searched and the average number of results; phrases that found nothing show what content is
missing ([5. Search statistics](../../../guides/urls-and-aliases.md#5-search-statistics)). Phrases can be personal data,
which is why it is off by default.

## Related pages

- [Search with ezfind and Solr](search-with-ezfind-and-solr.md)
- [Searching (for editors)](../../searching.md)
- [Tagging content with eztags](tagging-with-eztags.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
