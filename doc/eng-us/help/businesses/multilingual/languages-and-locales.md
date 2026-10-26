---
title: Languages and locales
audience: businesses, project-manager, admin
category: business-solutions
order: 20
summary: What Exponential supports for languages, explained for decision makers - locales, content languages, administration translations and site locales, the three support levels, the 62-language limit, and how to check a language before you commit to a market.
keywords: languages, locales, content languages, administration translation, interface language, support levels, 268 locales, 54 translations, limit of 62 languages, fallback, business
type: help
---
# Languages and locales

This page is for managers who need to know whether Exponential supports the languages of their markets, and at what
level, before they promise a launch. It explains four terms that are easily mixed up, the three levels of support,
the limit on the number of content languages, and how to check a specific language. All figures come from
[Languages in Exponential](../../../languages.md), the reference page for languages.

## Contents

1. [Four things that are easily mixed up](#four-things-that-are-easily-mixed-up)
2. [What is available](#what-is-available)
3. [The three support levels](#the-three-support-levels)
4. [When a language has no interface translation of its own](#when-a-language-has-no-interface-translation-of-its-own)
5. [The limit on content languages](#the-limit-on-content-languages)
6. [Checking a language before you commit](#checking-a-language-before-you-commit)

## Four things that are easily mixed up

| Term | What it is, in business terms | Technical home |
|---|---|---|
| **Locale** | The rules of a language in a country: its name, text direction, date and time formats, day and month names, first day of the week, number and currency formats | `share/locale/<code>.ini` |
| **Content language** | A language your content can be written in, such as German for your product pages | Setup > Languages |
| **Administration translation** | The words of the editing interface: buttons, menus, messages | `share/translations/<code>/` |
| **Site locale** | The locale one site address formats with, and the interface translation it uses | `site.ini [RegionalSettings] Locale` |

Source: [Languages, section 1](../../../languages.md#1-locales-translations-content-languages-and-siteaccesses).
Use these words exactly in plans and contracts; it avoids most misunderstandings with the technical team.

A content language needs only a locale file. Adding a locale file makes a language selectable; it does not add a
content language, change a site, or use up one of the content language places.

## What is available

As counted in [Languages in Exponential](../../../languages.md) (Exponential 6.0.15, October 2026):

- **268 locale files**: 261 languages and countries, the test language Lorem Ipsum and 6 variations.
- **32 scripts**, from Latin, Cyrillic and Greek to Arabic, Hebrew, Devanagari, Chinese and many more.
- **40 locales written right to left** ([Right-to-left markets](right-to-left-markets.md)).
- **54 administration translations**, 51 of them complete (25 October 2026), listed in
  [Languages in Exponential](../../../languages.md#in-short).
- **No PHP extension needed at run time**: locales are plain INI files
  ([Languages, section 10](../../../languages.md#10-no-php-extension-at-run-time)).

> **Note**
> All complete translations except `eng-US` and `ger-DE` are machine-assisted, at least in part. The reference page
> says a review by a native speaker is needed before a site relies on them. Budget that review if your editors will
> work in one of these languages.

## The three support levels

From [Languages, section 2](../../../languages.md#2-support-levels):

| Level | What works | Plan for |
|---|---|---|
| **Full: interface and content** | The administration speaks the language completely; content, dates, numbers and currency follow the locale | Editors can work in their own language |
| **Partial interface, full content** | An older administration translation covers part of today's strings; the rest shows in English | Editors who read some English, or a translation project |
| **Content only** | No administration translation: the interface stays English; content and formats follow the locale | Visitors get their language; editors work in English |

For most businesses the decisive question is not "is our customers' language supported?" (content in almost any
written language is) but "do our editors need the administration in their language?".

## When a language has no interface translation of its own

A locale without its own translation falls back to another one of the same language where one exists: every
German-, French- and Spanish-speaking country to `ger-DE`, `fre-FR` and `esl-ES`, the Arabic countries to `ara-SA`,
and so on. Chains of fallbacks are possible, and new country locales of a translated language fall back
automatically ([Languages, section 13](../../../languages.md#13-translation-fallbacks)).

For a business that means an Austrian or Swiss team gets the German administration without extra work.

## The limit on content languages

An installation holds at most **62 content languages on 64-bit PHP** (30 on 32-bit PHP). Each content language is
one bit of a number every object stores. Setup > Languages shows how many are in use and how many can still be
added, and refuses a language beyond the limit
([Languages, section 11](../../../languages.md#11-the-limit-of-62-content-languages)).

The reference page explains why the limit is kept: raising it would change the database schema of every installation
"to serve a case no known site has reached". Two consequences for planning:

- A language added and never used still takes a place until it is removed.
- 64-bit PHP is recommended: `composer.json` suggests it "for support of more than 30 languages"
  ([Operating system support](../../os-support.md)).

## Checking a language before you commit

- [ ] Find the locale in [Languages, section 3](../../../languages.md#3-every-locale) and note its code.
- [ ] Check its support level in [section 2](../../../languages.md#2-support-levels).
- [ ] If editors need the interface in that language and it is not complete, decide: work in English, or fund a
      translation ([section 8](../../../languages.md#8-adding-or-completing-an-administration-translation)).
- [ ] If the language is written right to left, read [Right-to-left markets](right-to-left-markets.md).
- [ ] If your language is not listed, [section 6](../../../languages.md#6-common-languages-still-missing) lists the
      common languages still missing and [section 7](../../../languages.md#7-adding-a-locale) how a locale is added.
- [ ] Count the content languages you will need over the next years against the limit.

## Related pages

- [Multilingual planning](multilingual-planning.md)
- [Currencies, dates and formats](currencies-dates-and-formats.md)
- [Languages in Exponential](../../../languages.md)
- [Translations and languages](../../../features/6.0/translations-and-languages.md)
- [Content languages](../../../guides/content-languages.md)
- [Languages for editors](../../languages.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
