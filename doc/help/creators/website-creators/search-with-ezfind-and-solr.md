---
title: Search with ezfind and Solr
audience: website-creator, admin
category: website-creation
order: 570
summary: When to move site search to Solr, how the ezfind extension and the solr.ini settings switch it on, controlling Solr with exp:solr, reindexing, and what Solr adds (facets, relevance, file contents, tag clouds).
keywords: Solr, ezfind, ezsolr, search engine, facets, relevance, solr.ini, exp:solr, reindex, updatesearchindex, tag cloud, website creator
type: help
---
# Search with ezfind and Solr

This page is for website creators whose site outgrows the built-in search. The `ezfind` extension connects Exponential
to an Apache Solr server; Solr is optional and nothing changes until you switch it on
([exp:solr](../../../features/6.0/web-server-and-solr-commands.md#expsolr-an-optional-search-server)).

## Contents

1. [When Solr is worth it](#when-solr-is-worth-it)
2. [The parts](#the-parts)
3. [Switching it on](#switching-it-on)
4. [Indexing](#indexing)
5. [What changes for templates](#what-changes-for-templates)

## When Solr is worth it

| Need | Built-in engine | Solr |
|---|---|---|
| Find pages by words | yes | yes |
| Relevance ranking and boosting | basic | yes |
| Facets (filter results by class, tag, date) | no | yes |
| Very large numbers of objects | slows down | built for it |
| Tag clouds from search (`eztags` `TagCloudOverSolr`) | no | yes |

Solr is a separate server (Java) to install, run, monitor and back up. For a small site the built-in engine is fine.

## The parts

| Part | What |
|---|---|
| `extension/ezfind` | the extension: `ezfind.ini`, `solr.ini`, and `site.ini.append.php` with `SearchEngine=ezsolr` |
| `settings/solr.ini` `[SolrSettings]` | the kernel's Solr control: `Enabled`, `Url`, `BinaryPath`, `Host`, `Port`, `DataDir` |
| `exp:solr` | the console command: `status`, `start`, `stop`, `restart`, `ping` |

## Switching it on

1. Install a Solr server (locally with `BinaryPath`, or a remote one with `Url`).
2. Set `[SolrSettings] Enabled=true` and the address in `settings/override/solr.ini.append.php`.
3. Check: `./console exp:solr status --allow-root-user`, then `ping`.
4. Activate `ezfind`; its `site.ini.append.php` switches `[SearchSettings] SearchEngine` to `ezsolr`.
5. Clear the INI cache and build the index (below).

## Indexing

```bash
php bin/php/updatesearchindex.php --clean --allow-root-user
```

With delayed indexing, moves and visibility changes queue `index_moved_node` actions for Solr, because Solr keeps paths
([10.8 Search](../../../install/10-after-installing.md#108-search-indexing-and-image-handling)). Keep the indexing cron
part running.

## What changes for templates

`content/search` keeps working; results come from Solr. Facets and filters are available through ezfind's fetch
functions and its own search templates; read the extension's documentation in `extension/ezfind/` for the exact names in
your version before writing facet templates. The online editor's search dialogs also use Solr when it is active
([4. The online editor](../../../guides/content-model-and-editing.md#4-the-online-editor-and-tinymce-8)).

## Related pages

- [Site search](site-search.md)
- [Server control commands](../../../features/6.0/web-server-and-solr-commands.md)
- Scaling applications
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
