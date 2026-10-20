---
title: Search phrases
audience: government, admin
category: public-sector
order: 270
summary: For administrators of public sites - Exponential's search statistics store what visitors type in the search, which can contain names or case numbers; they are off by default, switched on per siteaccess, and should be reset when no longer needed.
keywords: search statistics, search phrases, LogSearchStats, Setup Search statistics, reset statistics, personal data, minimization, retention, public sector
type: help
---
# Search phrases

This page is for administrators. Search statistics tell you what visitors look for and do not find, which is valuable
for a public site. They also store exactly what visitors typed, and people type names, addresses and case numbers into
search boxes ([URLs and search statistics](../../../guides/urls-and-aliases.md#5-search-statistics)).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Off by default](#off-by-default)
2. [Switching on](#switching-on)
3. [Using and resetting](#using-and-resetting)
4. [Related pages](#related-pages)

## Off by default

`site.ini [SearchSettings] LogSearchStats=disabled`. The page **Setup > Search statistics** says which siteaccesses
record searches, or warns that none does. Phrases are stored without a user or address.

## Switching on

Only on the public siteaccess where you need it:

```ini
# settings/siteaccess/<public siteaccess>/site.ini.append.php
[SearchSettings]
LogSearchStats=enabled
```

Mention in the privacy notice that search terms are recorded for improving the site.

## Using and resetting

- The page shows the phrases, how often each was searched and the average number of results; **Show** filters the
  phrases that found nothing.
- Act on it: add missing content or the words people use.
- **Reset statistics** removes all phrases for good after a confirmation in place. Reset on a schedule (for example
  quarterly, after you have drawn your conclusions), and whenever you notice personal data in the list.
- Give access to the page only to people who need it.

## Related pages

- [Links, URL aliases, wildcards and search statistics](../../../guides/urls-and-aliases.md)
- [Data minimization](data-minimization.md)
- [Site search](../../creators/website-creators/site-search.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
