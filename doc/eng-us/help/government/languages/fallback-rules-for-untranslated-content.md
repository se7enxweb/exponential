---
title: Fallback rules for untranslated content
audience: government, admin, project-manager
category: public-sector
order: 90
summary: What a visitor of one language version sees when a page is not translated yet, how SiteLanguageList order, ShowUntranslatedObjects and always available decide it, and the policy choices a public body makes for each official language.
keywords: fallback, untranslated, SiteLanguageList, ShowUntranslatedObjects, always available, missing translation, language policy, official languages, partial translation
type: help
---
# Fallback rules for untranslated content

This page is for administrators and managers who decide what happens when content is not available in a visitor's
language. In Exponential each siteaccess shows an object in the first language of its `SiteLanguageList[]` that the
object has; objects in none of them are hidden unless they are always available or the siteaccess shows untranslated
objects ([Content languages, sections 5 and 6](../../../guides/content-languages.md#5-showing-a-language-sitelanguagelist-and-siteaccesses)).

## Contents

1. [The three settings](#the-three-settings)
2. [Policy choices](#policy-choices)
3. [Telling visitors](#telling-visitors)
4. [Search and lists](#search-and-lists)
5. [Checking what a siteaccess shows](#checking-what-a-siteaccess-shows)

## The three settings

| Wanted | Setting |
|---|---|
| Show the main language where the translation is missing | `SiteLanguageList[]=fre-FR` then `eng-US` |
| Hide what is not translated | `ShowUntranslatedObjects=disabled` and no second language |
| Show everything in its main language when nothing else fits | `ShowUntranslatedObjects=enabled` |
| One object appears in every language as it is (logo, a PDF) | mark it **always available** |

## Policy choices

| Policy | Configuration | Consequence |
|---|---|---|
| Full parity: every page exists in every official language | one language per list, no fallback | untranslated pages are invisible in that language; publication waits for translation |
| Fallback to the main language | two languages in each list | visitors see some pages in another language; mark them |
| Core in all languages, the rest in one | fallback, plus a rule which sections must be translated | needs a translation backlog and review |

Some jurisdictions require certain information in every official language; decide the policy with your legal and
language services, and write it down per section.

## Telling visitors

When a page falls back, tell visitors in their language that it is not yet available in it. A template can compare
the language shown with the siteaccess's first language; mark the content with its own `lang` attribute (the
standard design's `parts/exp_content_direction.tpl` helps for direction; the `lang` value comes from the object's
language), so screen readers switch pronunciation ([HTML language tags and accessibility](html-language-tags-and-accessibility.md)).

## Search and lists

Lists and search results follow the same rules: an object hidden in a siteaccess is not listed there. With Solr,
`ezfind.ini [LanguageSearch] SearchMainLanguageOnly` decides whether results follow the fallback (see "Multilingual
search").

## Checking what a siteaccess shows

- **Setup > Languages** shows each siteaccess with its `SiteLanguageList` in order.
- An object's **Translations** tab lists its languages, its main language and whether it is always available.
- Open the same page in each language address signed out.

## Related pages

- [Always available content](always-available-content.md)
- [Content languages](../../../guides/content-languages.md)
- [Building a multilingual site](../../creators/website-creators/multilingual-sites.md#fallbacks-and-untranslated-content)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
