---
title: Translations, locales and right-to-left
audience: web-framework-developer, developer
category: web-frameworks
order: 300
summary: Internationalization in Exponential for developers used to Symfony Translation, Laravel's lang files, gettext in Django and Rails I18n - interface strings in Qt Linguist .ts catalogues, the i18n operator and ezpI18n::tr, locales and the l10n and datetime operators, translated content with one version per language, language siteaccesses, and right-to-left pages.
keywords: i18n, l10n, translation, internationalization, localization, ezpI18n, i18n operator, .ts, Qt Linguist, TranslationExtensions, locale, eZLocale, datetime, l10n operator, SiteLanguageList, right-to-left, RTL, dir, gettext, Symfony Translation, web framework developer
type: help
---
# Translations, locales and right-to-left

This page is for developers who translate an application with message catalogues and format dates and numbers by
locale. Exponential separates two things that frameworks often mix: the **interface** (texts in templates and PHP,
translated with catalogues) and the **content** (translated by editors, each object version holding one set of
values per language).

## Contents

1. [Interface texts](#interface-texts)
2. [Locales and formatting](#locales-and-formatting)
3. [Content in several languages](#content-in-several-languages)
4. [Right-to-left](#right-to-left)
5. [Compared with other frameworks](#compared-with-other-frameworks)

## Interface texts

| Where | How |
|---|---|
| Templates | `{'Latest items'\|i18n( 'extension/expdemo' )}`; placeholders: `{'%count items'\|i18n( 'extension/expdemo',, hash( '%count', $count ) )}` |
| PHP | `ezpI18n::tr( 'extension/expdemo', 'Latest items' )` (`kernel/common/ezpi18n.php`) |
| Catalogues | `extension/<ext>/translations/<locale>/translation.ts`, Qt Linguist XML; the kernel's in `share/translations/<locale>/translation.ts` |
| Registration | `site.ini [RegionalSettings] TranslationExtensions[]=<ext>` |

The first argument is the **context**, which groups strings the way a translation domain does. The source text is the
key, as in gettext ([View results: translated texts](../creators/app-creators/view-results-and-the-pagelayout.md#translated-texts),
[i18n operator](../designers/templates/i18n-operator.md), [Translation catalogues](../../standards/internationalization.md#translation-catalogues-qt-linguist-ts)).
The interface language of a siteaccess is `site.ini [RegionalSettings] Locale`.

## Locales and formatting

Locales are INI files in `share/locale/` (`eng-GB.ini`, `ger-DE.ini`, `ara-SA.ini` ...), read by `eZLocale`. They hold
date, time, number and currency formats and the text direction. Templates format with the locale operators
`l10n`, `datetime` and `locale` ([Dates, numbers and l10n](../designers/templates/dates-numbers-and-l10n.md)):

```text
{$node.object.published|l10n( 'shortdate' )}
{$price|l10n( 'currency' )}
```

Locale codes are the three-letter language plus country form (`eng-US`, `ger-DE`), not `en_US`; the standard page
explains how they relate to BCP 47 tags ([Language tags](../../standards/internationalization.md#language-tags-bcp-47-and-iso-639)).

## Content in several languages

- Each content object can be translated into the languages of the installation; every version holds one set of
  attribute values per language. Editors translate in the administration; there is no translation table to design.
- A siteaccess shows content in the languages of `site.ini [RegionalSettings] SiteLanguageList[]`, in order, falling
  back to the next one ([Content languages](../../guides/content-languages.md)).
- A typical multilingual site uses one siteaccess per language, matched by path or host
  ([Siteaccesses](siteaccesses-hosts-and-tenants.md), [Multilingual sites](../creators/website-creators/multilingual-sites.md)).
- URL aliases exist per translation ([Pretty URLs](url-aliases-and-wildcards.md)).
- In PHP, `$object->fetchDataMap( false, 'ger-DE' )` reads one language.

## Right-to-left

The direction comes only from the locale file (`[LanguageSettings] TextDirection=rtl`); `eZLocale::isRightToLeft()` and
`eZLocale::textDirectionOf( 'heb-IL' )` answer it in PHP, and `$site.text_direction` in templates. Print it on `<html>`
in your pagelayout:

```text
<html lang="{$site.http_equiv.Content-language|wash}"{if eq( first_set( $site.text_direction, 'ltr' ), 'rtl' )} dir="rtl"{/if}>
```

Write stylesheets with logical properties (`margin-inline-start`, `text-align: start`) so one stylesheet serves both
directions ([Right-to-left sites](../creators/website-creators/right-to-left-sites.md),
[Right-to-left languages in the administration](../../features/6.0/right-to-left.md)).

## Compared with other frameworks

| Framework | Interface strings | Content translations | Exponential |
|---|---|---|---|
| Symfony | Translation component, XLIFF or YAML | separate bundles | `.ts` catalogues; translations built into content |
| Laravel | `lang/` files, `__()` | packages | the same |
| Django | gettext `.po`, `gettext()` | packages such as model translation apps | the same |
| Rails | I18n YAML, `t()` | gems | the same |
| Spring | `MessageSource`, properties | your own design | the same |

## Related pages

- [Internationalization standard](../../standards/internationalization.md)
- [Translations and languages](../../features/6.0/translations-and-languages.md)
- [Languages](../languages.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
