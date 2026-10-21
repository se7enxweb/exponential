---
title: "Search: the database engine and Solr"
audience: dxp-developer
category: dxp-development
order: 31
summary: The two search engines of the DXP - the default database engine and Solr 8 - how to choose, how to switch with SEARCH_ENGINE, how to create the Solr core without a console command, and what full-text search can and cannot do on SQLite.
keywords: search, search engine, SEARCH_ENGINE, legacy, Solr, ibexa/solr, ibexa/core-search, ibexa/search, Solr core, SOLR_DSN, full text, reindex
type: help
---
# Search: the database engine and Solr

This page is for developers who choose and run the search engine of a DXP project. The DXP ships the database search
engine (named `legacy` in the configuration) and the Solr engine (`ibexa/solr`). Your code uses the same search API
with either.

## Contents

1. [Choosing an engine](#choosing-an-engine)
2. [Switching to Solr](#switching-to-solr)
3. [Switching back](#switching-back)
4. [Searching from code and templates](#searching-from-code-and-templates)
5. [Limits to know](#limits-to-know)

## Choosing an engine

| Engine | Setting | Good for |
|---|---|---|
| Database (default) | `SEARCH_ENGINE=legacy` | Small and medium sites, development, no extra service |
| Solr 8 (8.11.x recommended) | `SEARCH_ENGINE=solr` | Large content sets, relevance ranking, facets, heavy full-text use |

The name `legacy` means "the search engine that queries the database"; it has nothing to do with the Exponential 6
kernel.

## Switching to Solr

From the skeleton's guide (section 18):

1. Set `SEARCH_ENGINE=solr`, `SOLR_DSN` and `SOLR_CORE` in `.env.local`.
2. `php bin/console cache:clear`.
3. Create the core with Solr's own HTTP API ("no console command exists in v5"):

   ```bash
   curl "http://localhost:8983/solr/admin/cores?action=CREATE&name=default&configSet=exponential"
   ```

   The config set named in the request must exist on your Solr server.
4. Index everything: `php bin/console exponential:reindex` ([Reindexing](reindexing.md)).

## Switching back

```bash
SEARCH_ENGINE=legacy
php bin/console cache:clear
```

## Searching from code and templates

In PHP, inject `Ibexa\Contracts\Core\Repository\SearchService` and run a query with criteria and sort clauses; the
same query runs on both engines. For lists in templates, the content query field type runs a query as a field
([The field types it ships](the-field-types-it-ships.md#structured-fields)).

## Limits to know

- On SQLite there is no FULLTEXT index; full-text search falls back to `LIKE` queries
  ([SQLite for development](sqlite-for-development.md#limits)).
- Changing a content type's searchable fields needs a reindex.
- With Solr, plan for the Solr server like any other service: monitoring, backups of its configuration, and a reindex
  after restoring it.

## Related pages

- [Reindexing](reindexing.md)
- [Environment variables](environment-variables.md#search-cache-mail-and-the-rest)
- [Searching in Exponential 6](../searching.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
