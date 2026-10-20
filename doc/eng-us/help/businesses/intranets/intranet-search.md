---
title: Intranet search
audience: businesses, admin, website-creator
category: business-solutions
order: 40
summary: How staff find pages and documents on an Exponential intranet - the built-in search engine, what it respects, when Solr through ezfind is worth running, searching inside files, tags, and using search statistics to find missing content.
keywords: intranet search, site search, eZSearchEngine, ezfind, Solr, facets, permissions, search statistics, LogSearchStats, file contents, tags, eztags, updatesearchindex
type: help
---
# Intranet search

This page is for intranet owners who want staff to find the right page or document in seconds. It explains what the
search that comes with Exponential does, when a larger search server pays off, and how to learn from what staff
search for. The technical setup is in the [Site search](../../creators/website-creators/site-search.md) and
[Search with ezfind and Solr](../../creators/website-creators/search-with-ezfind-and-solr.md) pages.

![Search statistics: what people searched for and found](../../images/help/bu-search-stats.webp)

## Contents

1. [Search shows only what a person may read](#search-shows-only-what-a-person-may-read)
2. [The built-in engine](#the-built-in-engine)
3. [When Solr is worth it](#when-solr-is-worth-it)
4. [Searching inside documents](#searching-inside-documents)
5. [Helping search with structure and tags](#helping-search-with-structure-and-tags)
6. [Learning from search statistics](#learning-from-search-statistics)
7. [Editors and administrators](#editors-and-administrators)
8. [Checklist](#checklist)
9. [Related pages](#related-pages)

## Search shows only what a person may read

The most important property for an intranet: search results follow permissions. A person finds only what they may
read ([Site search, The built-in engine](../../creators/website-creators/site-search.md#the-built-in-engine)). The HR
department's confidential pages do not appear in a sales employee's results, and the search page needs no extra
permission of its own ([What visitors may see, Giving visitors more](../../creators/website-creators/visitor-permissions.md#giving-visitors-more)).

For the business this means one search box can serve the whole company. You do not need a separate search per
department, and you do not risk leaking titles of restricted documents through results.

## The built-in engine

Every installation has a search engine that keeps its index in the database
(`site.ini [SearchSettings] SearchEngine=eZSearchEngine`). It indexes the fields marked **Searchable** in each class
when content is published. It needs no extra server, so it costs nothing more to run.

Settings the intranet owner should know about (`site.ini [SearchSettings]`, shipped values from the Site search page):

| Setting | Shipped | What it means for staff |
|---|---|---|
| `MaximumSearchLimit` | `30` | at most 30 results on one page |
| `EnableWildcard` | `false` | `polic*` does not find "policy" unless switched on; costly on large sites |
| `AllowEmptySearch` | `disabled` | an empty search is refused |
| `DelayedIndexing` | `disabled` | new content is searchable at once; `enabled` indexes by cronjob instead |
| `LogSearchStats` | `disabled` | search phrases are not recorded |

## When Solr is worth it

`ezfind` connects Exponential to an Apache Solr server. It is listed in `composer.json` and stays inactive until
switched on. The comparison from the ezfind page:

| Need | Built-in engine | Solr |
|---|---|---|
| Find pages by words | yes | yes |
| Relevance ranking and boosting | basic | yes |
| Facets (filter results by class, tag, date) | no | yes |
| Very large numbers of objects | slows down | built for it |

Solr is a separate Java server to install, run, monitor and back up
([Search with ezfind and Solr](../../creators/website-creators/search-with-ezfind-and-solr.md#when-solr-is-worth-it)).
The console command `exp:solr` reports its status and starts or stops it
([Server control commands](../../../features/6.0/web-server-and-solr-commands.md)).

A practical rule for the decision: start with the built-in engine. Move to Solr when staff need filters ("only PDF
files from HR in 2026"), when the number of objects grows large, or when search inside many documents matters. The
change does not alter the search address (`content/search` keeps working).

## Searching inside documents

Text inside PDF and Word files can be added to the index. `settings/binaryfile.ini` maps PDF to the `ezpdf` handler
(external tool `pstotext`) and Word to `ezword` (external tool `wvWare`); these programs must be installed on the
server ([Files and downloads, Searching inside files](../../creators/website-creators/files-and-downloads.md#searching-inside-files)).
With Solr, file contents can be indexed as well.

## Helping search with structure and tags

- Mark only useful fields **Searchable**: titles, intros, bodies; not internal codes.
- Give documents clear titles; the title is what appears in results.
- Use tags across departments with `eztags`; with Solr, tag clouds can be built from search
  ([Tagging with eztags](../../creators/website-creators/tagging-with-eztags.md)).
- Keep retired documents out of reading sections, so search does not offer outdated versions.

## Learning from search statistics

With `LogSearchStats=enabled`, **Setup > Search statistics** (`/search/stats`) lists every phrase, how often it was
searched and the average number of results. Phrases that found nothing show what content staff miss
([Site search, Search statistics](../../creators/website-creators/site-search.md#search-statistics)).

> **Note**
> Search phrases can be personal data, which is why recording them is off by default. Decide with your data
> protection contact whether to switch it on, and for how long
> ([GDPR basics](../security/gdpr-basics.md)).

## Editors and administrators

Editors search in the administration as well ([Searching, for editors](../../searching.md)). Administrators can find
the object behind an object, node or version ID, a remote ID or a URL alias with the content finder
([The content finder](../../../features/6.0/content-finder.md)).

After a migration or a database move, the whole index is rebuilt with
`php bin/php/updatesearchindex.php --clean` ([Site search, Indexing](../../creators/website-creators/site-search.md#indexing)).

## Checklist

- [ ] Decided: built-in engine or Solr.
- [ ] Searchable fields reviewed for each class used on the intranet.
- [ ] Search tested while signed in as members of two different departments.
- [ ] Text extraction tools installed if file contents must be searchable.
- [ ] Decision on search statistics recorded, with a review date.
- [ ] Index rebuild planned after the document migration.

## Related pages

- [Site search](../../creators/website-creators/site-search.md)
- [Search with ezfind and Solr](../../creators/website-creators/search-with-ezfind-and-solr.md)
- [Documents and file libraries](documents-and-file-libraries.md)
- [Search hosting backends](../../hosting/basics/search-backends.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
