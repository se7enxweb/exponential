---
title: Search engine integration
audience: integrator, developer, admin
category: integration
order: 240
summary: How site search is wired in Exponential and how to connect an external search service - the built-in engine, the ezpSearchEngine plugin interface, Solr through ezfind and exp:solr, delayed indexing, reindexing after imports, feeding an external index from publish hooks, and search over the APIs.
keywords: search, search engine, eZSearchEngine, ezpSearchEngine, SearchSettings, SearchEngine, search plugin, Solr, ezfind, exp:solr, solr.ini, updatesearchindex, DelayedIndexing, indexcontent, external search, Elasticsearch, OpenSearch, expsearch
type: help
---
# Search engine integration

This page is for integrators who must improve or replace the site search, or feed content into a search service the
customer already runs. Search in Exponential is a pluggable engine: the kernel calls `eZSearch`, which hands every
add, remove and query to the engine named in `site.ini [SearchSettings] SearchEngine`.

## Contents

1. [The parts](#the-parts)
2. [Option A: the built-in engine](#option-a-the-built-in-engine)
3. [Option B: Solr through ezfind](#option-b-solr-through-ezfind)
4. [Option C: a search engine plugin of your own](#option-c-a-search-engine-plugin-of-your-own)
5. [Option D: an external index next to the site search](#option-d-an-external-index-next-to-the-site-search)
6. [Indexing after imports](#indexing-after-imports)
7. [Search over the APIs](#search-over-the-apis)

## The parts

| Part | Where |
|---|---|
| The engine setting | `site.ini [SearchSettings] SearchEngine=eZSearchEngine` |
| The interface every engine implements | `ezpSearchEngine` (`kernel/private/interfaces/ezpsearchengine.php`): `addObject()`, `removeObject()`, `removeObjectById()`, `search()`, `supportedSearchTypes()`, `needCommit()`, `needRemoveWithUpdate()`, `commit()` |
| Where engines are looked for | `kernel/search/plugins/<name>/<name>.php`, then `extension/<ext>/search/plugins/<name>/<name>.php` for each extension in `[SearchSettings] ExtensionDirectories[]` (`eZSearch::getEngine()`) |
| Delayed indexing | `[SearchSettings] DelayedIndexing` (`disabled`, `enabled` or `classbased` with `DelayedIndexingClassList[]`); the global cronjob script `indexcontent.php` indexes what was queued |
| Full reindex | `php bin/php/updatesearchindex.php` (`--clean` empties the index first) |

## Option A: the built-in engine

`eZSearchEngine` indexes words in database tables. It needs nothing else to run and is enough for small and
medium sites. Statistics of search phrases are off by default (`LogSearchStats=disabled`), because they can hold
personal data.

## Option B: Solr through ezfind

The `ezfind` extension connects the site search to Apache Solr (facets, relevance tuning, the content of files). It is
**not** in the `require` list of the root `composer.json`; install it separately and check its state before you
promise it. The kernel's `exp:solr` command controls a Solr server once `solr.ini [SolrSettings] Enabled=true`:

```bash
php bin/php/console exp:solr status --allow-root-user
php bin/php/console exp:solr ping --allow-root-user
```

Read [Search with ezfind and Solr](../creators/website-creators/search-with-ezfind-and-solr.md) and
[Server control commands](../../features/6.0/web-server-and-solr-commands.md#expsolr-an-optional-search-server).

## Option C: a search engine plugin of your own

To make the site search use another service (OpenSearch, Elasticsearch, a SaaS search), write a class that
implements `ezpSearchEngine` in your extension:

```text
extension/myintegration/search/plugins/myintegrationsearch/myintegrationsearch.php   class myIntegrationSearch
```

```ini
# settings/override/site.ini.append.php
[SearchSettings]
SearchEngine=myIntegrationSearch
ExtensionDirectories[]=myintegration
```

The directory and file name are the engine name in lower case; the class name is the setting's value. Everything that
calls the search (the search page, `fetch( 'content', 'search', ... )`, the `expsearch` services) then uses your
engine, and every publish and remove calls `addObject()` and `removeObject()`. This is the deepest integration and the
largest piece of work: the search page templates expect the result structure of the built-in engine.

## Option D: an external index next to the site search

Often the customer's enterprise search only needs to **receive** content, while the site keeps its own search. Then
do not replace the engine: send publications to the external index with an outbox
([Workflows and triggers as hooks](workflows-and-triggers-as-hooks.md#the-outbox-pattern-notice-fast-send-later)),
removals with the audit webhook or the same outbox, and let a cronjob part deliver them. A full reload is a
server-side export ([Writing an export](writing-an-export-or-outbound-sync.md)).

## Indexing after imports

| Situation | Do |
|---|---|
| An import created objects through `eZContentFunctions` | Nothing: publishing indexes them (or queues them with delayed indexing) |
| Delayed indexing is on | Make sure the global cronjob part runs (`indexcontent.php`) |
| A package was installed with `ezpm.php` | The installer delays indexing: run `php bin/php/updatesearchindex.php` |
| Data was changed outside the publish operation | Run `php bin/php/updatesearchindex.php`; better, do not change data that way |

## Search over the APIs

The REST API has no search route. `expservices` has `expsearch::search`, `count`, `facets`, `byClass`, `inSubtree`,
`bySection`, `byAttribute`, `suggest` and `engine` (which engine is in use), each checked against `content/read`
([Remote services with expservices](remote-services-with-expservices.md)).

## Related pages

- [Search backends for hosts](../hosting/basics/search-backends.md)
- [Search with ezfind and Solr](../creators/website-creators/search-with-ezfind-and-solr.md)
- [Searching](../searching.md)
- [Caching after imports](caching-after-imports.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
