---
title: Search statistics and privacy
audience: government, admin, editor
category: public-sector
order: 80
summary: How to use Setup > Search statistics to learn what the public looks for and does not find, how to switch recording on per siteaccess, and how to handle the personal data search phrases can contain.
keywords: search statistics, LogSearchStats, search phrases, zero results, Setup Search statistics, search/stats, privacy, personal data, reset statistics
type: help
---
# Search statistics and privacy

This page is for administrators and editors who want to improve a public site from what people search for. Exponential
records search phrases only where switched on, and shows them in **Setup > Search statistics** (`/search/stats`)
([Search statistics](../../../../guides/urls-and-aliases.md#5-search-statistics)).

## Contents

1. [Switching it on](#switching-it-on)
2. [Reading the page](#reading-the-page)
3. [Acting on it](#acting-on-it)
4. [Privacy](#privacy)

## Switching it on

```ini
# settings/siteaccess/<public siteaccess>/site.ini.append.php
[SearchSettings]
LogSearchStats=enabled
```

It is `disabled` by default. Switch it on per public siteaccess (per language). The page says which siteaccesses
record searches, or warns that none does.

## Reading the page

- Figures: different phrases, all searches, phrases that found nothing, and their share.
- **Find a phrase**, **Show** (all or only those that found nothing), **Order** (**Most searched**, **Phrase A to Z**,
  **Fewest results**).
- **Search now** runs a phrase in the administration search, to see what it finds today.

## Acting on it

| Finding | Action |
|---|---|
| A frequent phrase finds nothing | add the content, or the words people use, to an existing page |
| A phrase finds the wrong page first | improve titles and summaries; with Solr, elevate the right page (see "Elevation and synonyms") |
| Many searches for one service | link it from the front page or a "top tasks" list |

## Privacy

Phrases are what visitors typed. They can contain names, addresses or case numbers. The privacy standard lists stored
search phrases among the known gaps the site must handle ([Privacy standards](../../../../standards/privacy.md#known-gaps)):

- give the page only to staff who need it (it needs Setup access);
- mention the recording in the privacy notice;
- **Reset statistics** removes all phrases for good after a confirmation; do it on a schedule that fits your
  retention rule;
- export or copy phrases only as aggregates without personal data.

## Related pages

- [Links, URL aliases, wildcards and search statistics](../../../../guides/urls-and-aliases.md)
- [Elevation and synonyms](elevation-and-synonyms.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
