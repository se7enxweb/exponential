---
title: The language switcher
audience: government, website-creator, developer
category: public-sector
order: 80
summary: How to put a language switcher on every page of a multilingual public site with Exponential (TranslationSA, the language_switcher operator, switchlanguage/to), make it accessible, and add hreflang links, which the shipped designs do not print.
keywords: language switcher, language_switcher, TranslationSA, LanguageSA, switchlanguage, hreflang, lang attribute, aria-current, accessibility, multilingual navigation
type: help
---
# The language switcher

This page is for site builders. A public site in several languages needs a visible way to change language on every
page, leading to the same page in the other language. Exponential builds it from `[RegionalSettings] TranslationSA[]`
and the kernel operator `language_switcher`
([Language switchers and addresses per language](../../creators/website-creators/language-switcher-and-language-urls.md#the-language-switcher)).

## Contents

1. [Configure the siteaccesses](#configure-the-siteaccesses)
2. [The template](#the-template)
3. [Making it accessible](#making-it-accessible)
4. [hreflang links for search engines](#hreflang-links-for-search-engines)
5. [When a page is not translated](#when-a-page-is-not-translated)

## Configure the siteaccesses

```ini
# in each public siteaccess, or once in settings/override/site.ini.append.php
[RegionalSettings]
TranslationSA[]
TranslationSA[site_en]=English
TranslationSA[site_fr]=Français
```

Every siteaccess listed must also be in `[SiteAccessSettings] RelatedSiteAccessList[]`. Write each language's name
in that language. `LanguageSA[]` maps a language to a siteaccess when names do not follow the convention.

## The template

```text
{def $avail_translation = language_switcher( $site.uri.original_uri )}
<nav aria-label="{'Language'|i18n( 'design/mysite' )}">
<ul class="languages">
{foreach $avail_translation as $siteaccess => $lang}
  <li><a href={$lang.url|ezurl}{if $siteaccess|eq( $access_type.name )} aria-current="true"{/if}
         lang="{$siteaccess|wash}">{$lang.text|wash}</a></li>
{/foreach}
</ul>
</nav>
{undef $avail_translation}
```

Each link goes through the module `switchlanguage/to`, which finds the same page in the other siteaccess. Set the
`lang` attribute to the language code (for example `fr`), not the siteaccess name. Clear the INI and template caches
afterwards.

## Making it accessible

- Each link text is the language's own name ("Français", not "French"), with a `lang` attribute, so screen readers
  pronounce it correctly ([WCAG 2.2, Language of Parts](https://www.w3.org/TR/WCAG22/)).
- Mark the current language (`aria-current`).
- Do not use flags alone: flags stand for countries, not languages.
- Place it in the same spot on every page, reachable by keyboard.

## hreflang links for search engines

Exponential does not print `hreflang` alternates by itself; build them in the pagelayout's head from the same
`language_switcher` result, with full addresses: `<link rel="alternate" hreflang="fr" href="https://www.example.gov/fr/...">`.

## When a page is not translated

The switcher leads to the same node in the other siteaccess. What that siteaccess shows depends on its
`SiteLanguageList[]`: the page in a fallback language, or nothing if the object is in none of its languages and not
always available (see "Fallback rules for untranslated content"). Test the switcher on a page that exists in only one
language.

## Related pages

- [Language switchers and addresses per language](../../creators/website-creators/language-switcher-and-language-urls.md)
- [Accessibility for site builders](../../creators/website-creators/accessibility-for-site-builders.md)
- [SEO basics](../../creators/website-creators/seo-basics.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
