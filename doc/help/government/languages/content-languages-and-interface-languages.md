---
title: Content languages and interface languages
audience: government, admin, editor
category: public-sector
order: 30
summary: The two kinds of language in Exponential, explained for public bodies: content languages (what pages are written in) and interface languages (what the administration and the site's fixed texts speak), where each is set, and how they combine.
keywords: content language, interface language, locale, SiteLanguageList, Locale, TextTranslation, translation.ts, administration language, site texts
type: help
---
# Content languages and interface languages

This page is for administrators and lead editors who set up a site in several languages. Exponential has two
independent kinds of language, set in different places. Mixing them up is the most common cause of confusion
([Content languages, section 1](../../../guides/content-languages.md#1-content-languages-and-interface-languages)).

## Contents

1. [The two kinds](#the-two-kinds)
2. [Locales, the third piece](#locales-the-third-piece)
3. [Typical combinations](#typical-combinations)
4. [Where the fixed texts of a public site come from](#where-the-fixed-texts-of-a-public-site-come-from)

## The two kinds

| | Content language | Interface language |
|---|---|---|
| What it is | a language objects (pages, documents, users) can be written in | the language of buttons, menus and messages |
| Where the texts are | in the database, one translation per object and language | in translation files, `share/translations/<locale>/translation.ts` and each extension's `translations/` |
| Where it is managed | **Setup > Languages** (`content/translations`) | `site.ini [RegionalSettings] Locale` per siteaccess, and the administrator's own choice |
| What chooses it for a page | `SiteLanguageList[]` of the siteaccess | `Locale` of the siteaccess, with `TextTranslation=enabled` |

A siteaccess can show German content with an English interface and the other way round.

## Locales, the third piece

A **locale** (`share/locale/<code>.ini`) holds the names of a language and country, the HTTP language tag, the text
direction, date and number formats. A content language is a locale code, so only a locale with a file can be added;
the siteaccess's `Locale` decides how dates and numbers are written ([Languages in Exponential, section 1](../../../languages.md#1-locales-translations-content-languages-and-siteaccesses)).

## Typical combinations

| Situation | Public siteaccess | Administration |
|---|---|---|
| Bilingual country site | one siteaccess per official language, each with its `Locale` and `SiteLanguageList[]` | one administration; staff choose their interface language |
| Staff work in a language without a full interface translation | | the interface shows English for untranslated strings, while content is edited in the staff's language |
| A minority language site section | its own siteaccess listing the minority language first and the main language second | |

## Where the fixed texts of a public site come from

Texts written into templates ("Search", "Contact us", footer labels) are interface texts: they go through
`|i18n( 'design/<your design>' )` and are translated in `.ts` files of your design extension. Texts editors write
(menus, footer content kept as objects) are content and are translated in the editor
([Building a multilingual site](../../creators/website-creators/multilingual-sites.md#templates-and-interface-texts)).
Keeping as much fixed text as possible in content lets editors translate it without a developer.

## Related pages

- [Content languages](../../../guides/content-languages.md)
- [Translations and languages](../../../features/6.0/translations-and-languages.md)
- [Languages (for editors)](../../languages.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
