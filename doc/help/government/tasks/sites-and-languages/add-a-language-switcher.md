---
title: Add a language switcher
audience: government, website-creator
category: public-sector
order: 40
summary: Task for site builders: put an accessible language switcher on every page of a multilingual public site with TranslationSA and the language_switcher operator.
keywords: language switcher, TranslationSA, language_switcher, switchlanguage, pagelayout, accessibility, lang attribute
type: help
---
# Add a language switcher

This task is for site builders. Background: [The language switcher](../../languages/the-language-switcher.md).

## Before you start

- One siteaccess per language, all listed in `[SiteAccessSettings] RelatedSiteAccessList[]`.
- Access to your design extension's pagelayout.

## Steps

1. Name the siteaccesses of the switcher, each language in its own name:

   ```ini
   # settings/override/site.ini.append.php
   [RegionalSettings]
   TranslationSA[]
   TranslationSA[site_en]=English
   TranslationSA[site_fr]=Français
   ```

2. In the pagelayout, where the header is, add:

   ```text
   {def $avail_translation = language_switcher( $site.uri.original_uri )}
   <nav aria-label="{'Language'|i18n( 'design/mysite' )}"><ul class="languages">
   {foreach $avail_translation as $siteaccess => $lang}
     <li><a href={$lang.url|ezurl}{if $siteaccess|eq( $access_type.name )} aria-current="true"{/if} lang="{$siteaccess|wash}">{$lang.text|wash}</a></li>
   {/foreach}
   </ul></nav>
   {undef $avail_translation}
   ```

   Replace `lang="{$siteaccess|wash}"` with the language code your design maps for each siteaccess (for example `fr`).
3. Clear the INI and template caches: `php bin/php/ezcache.php --clear-tag=ini,template`.

## Check

- On a translated page, each link leads to the same page in the other language.
- The current language is marked; links are reachable by keyboard.
- On a page that exists in one language only, the other link shows what that siteaccess's fallback rules say.

## Problems

| Problem | Fix |
|---|---|
| Links lead to the start page | the siteaccess is missing in `RelatedSiteAccessList[]` |
| The switcher is empty | `TranslationSA[]` not set for the siteaccess, or INI cache |

## Related pages

- [Language switchers and addresses per language](../../../creators/website-creators/language-switcher-and-language-urls.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
