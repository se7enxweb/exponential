---
title: Multilingual offers
audience: reseller, partner, sales-marketing
category: partnership
order: 230
summary: How to sell multilingual Exponential sites honestly - translations per content object, a siteaccess per language, 268 locale files and 33 administration translations, right-to-left support, the limit of 62 content languages on 64-bit PHP, what to check before you promise a language, and what to price.
keywords: multilingual, languages, translation, locale, content language, administration translation, siteaccess per language, right to left, RTL, 62 content languages, 64-bit, machine-assisted translations, pricing
type: help
---
# Multilingual offers

This page is for resellers who sell sites in several languages, or sell into markets whose editors work in a
language other than English. Exponential is strong here, and the honest details make the difference between a
happy customer and a surprised one.

## Contents

1. [What Exponential provides](#what-exponential-provides)
2. [Three questions before you promise a language](#three-questions-before-you-promise-a-language)
3. [Right-to-left markets](#right-to-left-markets)
4. [What to price](#what-to-price)
5. [Offer checklist](#offer-checklist)

## What Exponential provides

| Feature | What it means for the customer | Source |
|---|---|---|
| Translations per content object | Each page can exist in several languages, edited separately | [Content languages](../../../guides/content-languages.md) |
| A siteaccess per language | Each language can have its own address, such as `example.com/ger` or `example.de` | [Siteaccess by host prefix, and the redirect to the language](../../../features/6.0/siteaccess-host-and-language-matching.md) |
| Locale per site | Dates, numbers and currency follow `site.ini [RegionalSettings] Locale` | [Languages and locales](../../businesses/multilingual/languages-and-locales.md) |
| 268 locale files, 32 scripts | Content in almost any written language | [Languages](../../languages.md) |
| 33 administration translations, nineteen complete | Editors can work in their own language, for the complete ones | same |
| 40 right-to-left locales | Hebrew, Arabic, Persian, Urdu and others; the administration turns around | [Right-to-left languages](../../../features/6.0/right-to-left.md) |
| Fallback of interface translations | An Austrian or Swiss team gets the German administration without extra work | [Languages and locales](../../businesses/multilingual/languages-and-locales.md) |

Figures as counted for Exponential 6.0.15, October 2026.

## Three questions before you promise a language

1. **Do visitors need it, or editors too?** Content in a language needs only a locale file. An administration in
   that language needs a translation. Check the language's support level: full, partial interface, or content only.
2. **Is the administration translation reviewed?** All complete translations except `eng-US` and `ger-DE` are
   machine-assisted, at least in part; a review by a native speaker is needed before a site relies on them. Budget it
   or say so in the offer.
3. **How many content languages over the years?** An installation holds at most **62 content languages on 64-bit
   PHP** (30 on 32-bit PHP). A language added and never used still takes a place until it is removed. Use 64-bit PHP.

Source: [Languages and locales](../../businesses/multilingual/languages-and-locales.md).

## Right-to-left markets

When the interface language is written right to left, the administration sets `dir="rtl"`, the side columns change
places and icons point the other way; content in a right-to-left content language is marked the same way where it
is shown and edited ([Right-to-left languages](../../../features/6.0/right-to-left.md)). Your **public design** is
your own work: test it right to left before you promise it
([Right-to-left markets](../../businesses/multilingual/right-to-left-markets.md)).

## What to price

| Item | Note |
|---|---|
| Setup per language | Locale, siteaccess, host name or path, certificate, testing |
| Design work per writing direction | Right-to-left layouts need their own testing |
| Translation workflow setup | Roles, states, review steps ([Translation workflow](../../businesses/multilingual/translation-workflow.md)) |
| Review of a machine-assisted administration translation | Only if editors need it |
| Support per language | Who answers editors in that language? |
| Translation of content | Usually the customer's or a translation agency's work; say who does it |

Exponential Intelligence can assist translation where the customer chooses it; see
[Assisted translation with Exponential Intelligence](../../businesses/multilingual/assisted-translation-with-exponential-intelligence.md)
before you include it, and mind the data protection questions it raises.

## Offer checklist

- [ ] Languages listed, with the locale code of each.
- [ ] Support level of each language checked ([Languages](../../languages.md)).
- [ ] Who translates content, who reviews it, who approves it.
- [ ] Address scheme per language decided (path or host name).
- [ ] Right-to-left design tested, if any language needs it.
- [ ] Content language count within the limit for the next years.
- [ ] Support language for editors stated in the offer.

## Related pages

- [Multilingual planning](../../businesses/multilingual/multilingual-planning.md)
- [Multilingual checklist](../../businesses/multilingual/multilingual-checklist.md)
- [Multi-site offers](multi-site-offers.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
