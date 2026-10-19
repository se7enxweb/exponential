---
title: Solr with ezfind
audience: government, admin, website-creator
category: public-sector
order: 60
summary: When a public body should move its site search to Solr with the ezfind extension, what Solr adds (relevance, facets, per-language cores, autocomplete, spell check), what it costs to run, and the steps to switch it on with exp:solr.
keywords: Solr, ezfind, eZ Find, facets, relevance, exp:solr, solr.ini, SearchEngine ezsolr, autocomplete, spell check, MoreLikeThis, search server
type: help
---
# Solr with ezfind

This page is for administrators and site builders whose site outgrows the built-in search. The `ezfind` extension
connects Exponential to an Apache Solr server; Solr is optional and nothing changes until you switch it on
([Search with ezfind and Solr](../../../creators/website-creators/search-with-ezfind-and-solr.md)).

## Contents

1. [What Solr adds](#what-solr-adds)
2. [What it costs](#what-it-costs)
3. [Switching it on](#switching-it-on)
4. [Settings worth knowing](#settings-worth-knowing)

## What Solr adds

| Feature | ezfind setting or source |
|---|---|
| Relevance ranking and boosting | `ezfind.ini [IndexBoost]`, `[QueryBoost]` |
| Facets (filter results by class, tag, date) | `[FacetSettings]`, the extension's fetch functions |
| Per-language behavior | `[LanguageSearch] SearchMainLanguageOnly`, `MultiCore`, `LanguagesCoresMap` |
| Autocomplete | `[AutoCompleteSettings]` (off unless enabled; extra load) |
| Spell check | `[SpellCheck] SpellCheck=enabled` |
| "More like this" | `[MoreLikeThis]` |
| Highlighting | `[HighLighting]` |
| Elevation of chosen results | the `ezfind/elevate` module (see "Elevation and synonyms") |

## What it costs

Solr is a separate Java server to install, run, monitor, secure and back up; plan memory for it, and an operator
who knows it. For a small site the built-in engine is fine.

## Switching it on

1. Install a Solr server (locally with `BinaryPath`, or a remote one with `Url`).
2. Set `[SolrSettings] Enabled=true` and the address in `settings/override/solr.ini.append.php`.
3. Check: `./console exp:solr status`, then `./console exp:solr ping`.
4. Activate `ezfind`; its `site.ini.append.php` switches `[SearchSettings] SearchEngine` to `ezsolr`.
5. Clear the INI cache and build the index: `php bin/php/updatesearchindex.php --clean`.

`exp:solr` has the actions `status`, `start`, `stop`, `restart` and `ping`
([Server control commands](../../../../features/6.0/web-server-and-solr-commands.md)).

## Settings worth knowing

- `SearchMainLanguageOnly=enabled` (shipped) returns results only in the siteaccess's first language.
- `AutoComplete` is best enabled per siteaccess where it is used.
- With delayed indexing, moves and visibility changes queue actions for Solr; keep the indexing cronjob running.

## Related pages

- [Search with ezfind and Solr](../../../creators/website-creators/search-with-ezfind-and-solr.md)
- [Search hosting backends](../../../hosting/basics/search-backends.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
