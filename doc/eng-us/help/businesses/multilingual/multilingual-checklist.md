---
title: Multilingual checklist
audience: businesses, project-manager, website-creator
category: business-solutions
order: 70
summary: A checklist for adding a language or market to an Exponential site or store - decisions, setup, translation, formats and prices, right to left, search engines, launch and the routine afterwards.
keywords: checklist, multilingual, new language, launch, translation, SiteLanguageList, language switcher, hreflang, currencies, right to left, business
type: help
---
# Multilingual checklist

This page is for project leads, language owners and administrators who add a language or a market. Copy it into your
project tracker and tick it off; each item links to the page that explains it. It assumes the decisions on
[Multilingual planning](multilingual-planning.md) are taken.

## Contents

1. [Decisions](#decisions)
2. [Setup](#setup)
3. [Translation](#translation)
4. [Formats, prices and legal texts](#formats-prices-and-legal-texts)
5. [Right to left](#right-to-left)
6. [Search engines and links](#search-engines-and-links)
7. [Launch](#launch)
8. [After launch](#after-launch)

## Decisions

- [ ] The market, the language and its locale code are written down
      ([Languages and locales](languages-and-locales.md#checking-a-language-before-you-commit)).
- [ ] The support level of the language is known, and it is decided whether editors work in it or in English.
- [ ] The address scheme is chosen: path, host or both
      ([Addresses per language](multilingual-planning.md#addresses-per-language)).
- [ ] Fallback and untranslated content are decided per siteaccess
      ([What visitors see when a translation is missing](multilingual-planning.md#what-visitors-see-when-a-translation-is-missing)).
- [ ] Translators, approvers and a language owner are named.
- [ ] The number of content languages in use stays well below the limit of 62
      ([Languages, section 11](../../../languages.md#11-the-limit-of-62-content-languages)).

## Setup

- [ ] The content language is added under Setup > Languages.
- [ ] A siteaccess for the language exists with `Locale`, `SiteLanguageList[]` and site name
      ([Step by step: adding a second language](../../creators/website-creators/multilingual-sites.md#step-by-step-adding-a-second-language)).
- [ ] It is registered, matched and listed with its sister siteaccesses.
- [ ] Shared folders, users and media are marked always available.
- [ ] Translator roles are limited by language (and by subtree where needed).
- [ ] Approval for the language is in place if wanted
      ([Approval per language](translation-workflow.md#approval-per-language)).
- [ ] The design's own interface texts are translated.

## Translation

- [ ] The start page, menu folders and key pages are translated first.
- [ ] Product pages, basket and checkout texts are translated for a store.
- [ ] Machine-prepared drafts, if used, are reviewed by a reader of the language
      ([Assisted translation with Exponential Intelligence](assisted-translation-with-exponential-intelligence.md#a-review-routine)).
- [ ] Mail texts (order confirmation, registration, newsletter) exist in the language.

## Formats, prices and legal texts

- [ ] Dates, numbers and amounts look right on product pages, the basket and confirmation mail
      ([Currencies, dates and formats](currencies-dates-and-formats.md)).
- [ ] The currency charged, prices per currency or exchange rates, and rounding are decided and tested.
- [ ] VAT rules for the market are configured and confirmed by a tax adviser.
- [ ] Terms, returns policy, privacy notice and imprint are translated and checked by someone qualified.
- [ ] Shipping countries and costs for the market are set ([Shipping and handling](../stores/shipping-and-handling.md)).

## Right to left

Only if the language is written right to left ([Right-to-left markets](right-to-left-markets.md)):

- [ ] The page frame prints `dir="rtl"` for the siteaccess.
- [ ] Stylesheets use logical properties; icons along the line are flipped.
- [ ] Mixed text and forms were checked by a native reader, also at phone width.
- [ ] The left-to-right sites are unchanged.

## Search engines and links

- [ ] The language switcher links each page to the same page in the other languages
      ([The language switcher](../../creators/website-creators/language-switcher-and-language-urls.md#the-language-switcher)).
- [ ] `hreflang` alternates are built in the pagelayout, if wanted
      ([Telling search engines](../../creators/website-creators/language-switcher-and-language-urls.md#telling-search-engines)).
- [ ] The language has its own sitemap entries ([Sitemaps](../../creators/website-creators/sitemaps.md)).
- [ ] Feeds of the language point to its own address ([Feeds and syndication](../mobile-and-headless/feeds-and-syndication.md)).
- [ ] Cross-brand links stay in the visitor's language where possible
      ([Links between your sites](../multisite/cross-site-links.md#how-a-link-finds-its-site)).

## Launch

- [ ] Caches cleared after the last settings change (INI and content; Velocity's response cache when Velocity serves
      the site).
- [ ] Every address of the language answers over HTTPS.
- [ ] A test order, sign-up or contact form works in the language.
- [ ] The other languages show exactly what they showed before.

## After launch

- [ ] Setup > Languages is checked monthly for languages with few objects or no siteaccess.
- [ ] Changed source pages have their translations updated
      ([Keeping translations up to date](translation-workflow.md#keeping-translations-up-to-date)).
- [ ] Search queries and contact requests in the language are reviewed for missing content.
- [ ] Removing a translation is only done by people who know it cannot be undone.

## Related pages

- [Selling in several languages and markets](README.md)
- [Multi-brand checklist](../multisite/multi-brand-checklist.md)
- [Planning multisite and multilingual projects: checklists](../../leadership/multisite-and-multilingual-planning.md#checklists)
- [Building a multilingual site: checklist](../../creators/website-creators/multilingual-sites.md#checklist)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
