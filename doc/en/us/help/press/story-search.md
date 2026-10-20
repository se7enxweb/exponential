---
title: "Story: search"
audience: press
category: press-media
order: 66
summary: A feature story about search in Exponential - the built-in search engine, Solr as an option, the content finder for ids and paths, the hardened administration search and the instant search of the help - with printable facts, sources and the claims to avoid.
keywords: story, search, built-in search engine, Solr, eZ Find, content finder, administration search, help search, press
type: help
---
# Story: search

This page is for journalists and reviewers who write about how people find content in Exponential: visitors on a
site, editors in the administration, and administrators chasing an id from a log file. It gives the story, the
facts with sources and the claims to leave out.

## Contents

1. [The story in one paragraph](#the-story-in-one-paragraph)
2. [What is new, and when](#what-is-new-and-when)
3. [Facts you can print](#facts-you-can-print)
4. [What not to claim](#what-not-to-claim)
5. [Angles and headline ideas](#angles-and-headline-ideas)
6. [Who to ask](#who-to-ask)
7. [Related pages](#related-pages)

## The story in one paragraph

Exponential has a "Built in search engine" ([README, Main Exponential features](https://github.com/se7enxweb/exponential/blob/main/README.md#main-exponential-features))
and needs no search server: "Exponential searches with its built-in index ... until Solr is installed and switched on"
([Server control commands](../../features/6.0/web-server-and-solr-commands.md#expsolr-an-optional-search-server)).
The 6.0.15 development line added the **content finder**, which finds the content behind an id, a URL alias, a page
address or a file path, and hardened the administration's search so that malformed or very long queries no longer
break it ([6.0.15 changelog](../../changelogs/6.0/6.0.15.md#14-october-2026-the-search-of-the-administration)). The
help system has its own instant search.

![The search of the administration](../images/help/pr-search.webp)

## What is new, and when

All in the 6.0.15 line, which is in development and not released.

| What | When | Source |
|---|---|---|
| `exp:solr`, a command for an optional Solr server | 16 to 30 September 2026 | [6.0.15 changelog](../../changelogs/6.0/6.0.15.md#16-to-30-september-2026) |
| The content finder | 10 October 2026 | [6.0.15 changelog](../../changelogs/6.0/6.0.15.md#10-october-2026-the-content-finder) |
| The content finder by the address a visitor sees, on every database engine | 11 October 2026 | [6.0.15 changelog](../../changelogs/6.0/6.0.15.md#11-october-2026-the-content-finder-by-the-address-a-visitor-sees-on-every-database-engine) |
| The search of the administration hardened | 14 October 2026 | [6.0.15 changelog](../../changelogs/6.0/6.0.15.md#14-october-2026-the-search-of-the-administration) |
| The help system with instant search | 18 October 2026 | [6.0.15 changelog](../../changelogs/6.0/6.0.15.md#18-october-2026-the-help-system-in-the-administration) |

## Facts you can print

| Fact | Source |
|---|---|
| Solr is optional; without it the built-in index is used. | [Server control commands](../../features/6.0/web-server-and-solr-commands.md#expsolr-an-optional-search-server) |
| Editors press `/` anywhere in the administration to jump to the search field. | [Searching, The quick search](../searching.md#the-quick-search) |
| Visitors find only published, visible items they may read. | [Searching, Searching the site](../searching.md#searching-the-site) |
| The content finder finds an object from an object, node, attribute or version id, a remote id, a URL alias, a page URL or a file path. | [The content finder, In short](../../features/6.0/content-finder.md#in-short) |
| A search text is cut to 16 words and 1024 characters by default, with a note on the page. | [6.0.15 changelog](../../changelogs/6.0/6.0.15.md#14-october-2026-the-search-of-the-administration) |
| The help's search shows matching pages as you type, with audience and topic filters. | [Searching, The help pages](../searching.md#the-help-pages) |

## What not to claim

- "Exponential includes Solr." Solr is an optional, separate search server.
- "Search finds everything instantly." The help page says new items are "usually found within moments; on some sites
  the search index is updated in the background and takes a few minutes".
- "AI search" or "semantic search": the sources describe neither.

## Angles and headline ideas

```text
"No search server required: Exponential's built-in index"
"From a number in a log file to the page behind it: the content finder"
"Why a CMS limits your search to 16 words - and tells you so"
```

## Who to ask

| Question | Ask | Where |
|---|---|---|
| Search design and Solr | 7x | info@se7enx.com, or <https://exponential.earth/about/contact-us> |
| Search on real sites | the community | <https://share.exponential.earth/forums> |

## Related pages

- [Searching](../searching.md)
- [Story: the help system](story-the-help-system.md)
- [Story: operations tools for administrators](story-maintenance-mode-and-operations.md)
- [Feature stories: index](feature-stories-index.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
