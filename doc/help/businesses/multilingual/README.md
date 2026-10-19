---
title: Selling in several languages and markets
audience: businesses, project-manager, sales-marketing
category: business-solutions
order: 1
summary: The landing page for businesses that publish or sell in more than one language or market with Exponential - what the section covers, every page in it, and a suggested reading order.
keywords: multilingual, languages, locales, translation, markets, international, currencies, dates, right to left, Exponential Intelligence, business
type: help
---
# Selling in several languages and markets

This section is for owners, managers and marketing leads of organizations that reach customers in more than one
language or country: exporters, online stores with international customers, associations with members in several
countries, tourism businesses, and any company whose staff or partners read different languages. It explains what
Exponential supports, what each language costs you in work, and how to keep translations correct and up to date.
The technical details are on the pages linked from each topic.

## Contents

1. [What this section covers](#what-this-section-covers)
2. [The figures in one place](#the-figures-in-one-place)
3. [Pages in this section](#pages-in-this-section)
4. [Suggested reading order](#suggested-reading-order)
5. [Neighboring sections](#neighboring-sections)

## What this section covers

"Advanced multi-lingual support", "Full support for Unicode" and "Localization and internationalization libraries"
are listed among the main features in the project README. In practice that means:

- every content object can hold one translation per content language;
- every site address (siteaccess) decides which languages it shows, and in which order;
- dates, numbers and currency amounts follow the locale of the site;
- right-to-left languages are marked so browsers display them correctly;
- the writing assistant, Exponential Intelligence, can prepare translation drafts for a person to review.

The pages here turn those facts into business decisions.

## The figures in one place

From [Languages in Exponential](../../../languages.md), the reference page for every language figure (counted on
15 October 2026 for the locales and on 18 October 2026 for the administration translations, Exponential 6.0.15):

| What | Figure |
|---|---|
| Locale files that can be chosen as content languages and site locales | 268 (261 languages and countries, the test language Lorem Ipsum and 6 variations) |
| Writing systems (scripts) covered by those locales | 32 |
| Locales written right to left | 40 |
| Administration translations | 33, nineteen of them complete |
| Content languages one installation can hold at the same time | at most 62 on 64-bit PHP, 30 on 32-bit PHP |

> **Note**
> The project README's feature summary still names an earlier figure of 219 locales. The counts above come from
> `doc/languages.md`, which is kept up to date with the files in `share/locale/`. Quote that page.

## Pages in this section

| Page | What it answers |
|---|---|
| [Multilingual planning](multilingual-planning.md) | Which languages, in which order, with which fallback, and who translates |
| [Languages and locales](languages-and-locales.md) | What a locale, a content language and an interface translation are, and what each language level gives you |
| [Translation workflow](translation-workflow.md) | How translators work, how they are limited to their language, and how translations are approved |
| [Currencies, dates and formats](currencies-dates-and-formats.md) | How prices, dates and numbers follow each market, and what the shop does with currencies |
| [Right-to-left markets](right-to-left-markets.md) | Selling in Arabic, Hebrew, Persian, Urdu and other right-to-left languages |
| [Assisted translation with Exponential Intelligence](assisted-translation-with-exponential-intelligence.md) | Machine-prepared translation drafts that a person reviews before anything is published |
| [Multilingual checklist](multilingual-checklist.md) | What to check before a new language goes live, and afterwards |

## Suggested reading order

1. [Multilingual planning](multilingual-planning.md) for the decisions.
2. [Languages and locales](languages-and-locales.md) to check that your languages are supported at the level you
   need.
3. [Translation workflow](translation-workflow.md) and, if you want help from a language model,
   [Assisted translation with Exponential Intelligence](assisted-translation-with-exponential-intelligence.md).
4. [Currencies, dates and formats](currencies-dates-and-formats.md) if you sell, and
   [Right-to-left markets](right-to-left-markets.md) if one of your markets reads right to left.
5. The [Multilingual checklist](multilingual-checklist.md) before every launch of a language.

## Neighboring sections

- [Several brands on one installation](../multisite/README.md): a language is often one more siteaccess
- [Stores](../stores/README.md), especially [prices and currencies](../stores/prices-and-currencies.md)
- [Mobile apps, headless use and networks](../mobile-and-headless/README.md)
- Businesses start here
- Public sector organizations: Governments start here

## Related pages

- [Languages in Exponential](../../../languages.md)
- [Content languages](../../../guides/content-languages.md)
- [Planning multisite and multilingual projects](../../leadership/multisite-and-multilingual-planning.md)
- [Building a multilingual site](../../creators/website-creators/multilingual-sites.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
