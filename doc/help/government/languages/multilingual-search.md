---
title: Multilingual search
audience: government, website-creator, admin
category: public-sector
order: 250
summary: How search behaves on a multilingual Exponential site: the built-in engine indexes every translation of an object together and filters results by the siteaccess's languages, while Solr through ezfind offers per-language behavior (SearchMainLanguageOnly, one core per language); with search statistics per language.
keywords: multilingual search, search per language, built-in search, ezfind, Solr, SearchMainLanguageOnly, MultiCore, LanguagesCoresMap, stemming, search statistics
type: help
---
# Multilingual search

This page is for site builders and administrators of sites in several languages. Visitors expect the search on the
French site to find French pages. How close you get depends on the engine.

## Contents

1. [The built-in engine](#the-built-in-engine)
2. [Solr through ezfind](#solr-through-ezfind)
3. [Choosing](#choosing)
4. [Search statistics per language](#search-statistics-per-language)
5. [Checking](#checking)

## The built-in engine

`site.ini [SearchSettings] SearchEngine=eZSearchEngine` keeps its index in the database
([Site search](../../creators/website-creators/site-search.md)). Checked in the kernel
(`kernel/search/plugins/ezsearchengine/ezsearchengine.php`):

- When an object is indexed, the searchable attributes of **all its translations** go into one index for the object;
  the word index has no language column.
- Results are limited to objects the siteaccess shows in its languages (`eZContentLanguage::languagesSQLFilter()`), and
  each result is shown in the siteaccess's preferred translation.

So on the French site a word that appears only in the English translation of a page can still find that page, which
is then shown in French (or in the fallback). For two official languages with distinct vocabularies this is often
acceptable; for strict separation use Solr.

The built-in engine does no stemming per language; `EnableWildcard` allows `term*` searches at a cost.

## Solr through ezfind

The `ezfind` extension connects a Solr server ([Search with ezfind and Solr](../../creators/website-creators/search-with-ezfind-and-solr.md)).
Its `ezfind.ini [LanguageSearch]` settings:

| Setting | Shipped | Meaning |
|---|---|---|
| `SearchMainLanguageOnly` | `enabled` | only results in the first language of the siteaccess's `SiteLanguageList[]` |
| (same) | `disabled` | results follow the fallback of `SiteLanguageList[]` and the always available flag |
| `MultiCore` | `disabled` | `enabled`: each language indexed in its own Solr core for language-specific analysis |
| `LanguagesCoresMap[<locale>]` | empty | which core holds which language; unmapped languages are not indexed |

Language-specific analysis (stemming, stop words) is configured in Solr itself, per core.

## Choosing

| Need | Engine |
|---|---|
| A small bilingual site | built-in |
| Results strictly in the visitor's language | Solr with `SearchMainLanguageOnly=enabled` |
| Good matching of word forms per language | Solr with one core per language |
| Facets by language, theme, date | Solr |

## Search statistics per language

`LogSearchStats=enabled` is set per siteaccess, so you can switch it on for each language siteaccess and see what
people search in each language in **Setup > Search statistics** ([Search statistics](../../../guides/urls-and-aliases.md#5-search-statistics)).
Phrases can contain personal data.

## Checking

Search on each language site for a word that exists in only one language's text, and check what is found and in
which language it is shown.

## Related pages

- [Site search](../../creators/website-creators/site-search.md)
- [Search with ezfind and Solr](../../creators/website-creators/search-with-ezfind-and-solr.md)
- [Fallback rules for untranslated content](fallback-rules-for-untranslated-content.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
