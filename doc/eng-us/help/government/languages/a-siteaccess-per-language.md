---
title: A siteaccess per language
audience: government, admin, website-creator
category: public-sector
order: 50
summary: How each official language of a public site gets its own siteaccess in Exponential: the RegionalSettings that decide content, interface and fallback, registering and matching the siteaccess, sharing one design, and clearing caches.
keywords: siteaccess, language siteaccess, SiteLanguageList, Locale, ContentObjectLocale, ShowUntranslatedObjects, TextTranslation, RelatedSiteAccessList, AvailableSiteAccessList, ExtensionSettingsSiteAccess
type: help
---
# A siteaccess per language

This page is for administrators and site builders. A multilingual site usually has one public siteaccess per
language, all sharing one database. Each decides which content languages it shows and in which order, its interface
language and its address ([Content languages, section 5](../../../guides/content-languages.md#5-showing-a-language-sitelanguagelist-and-siteaccesses)).

## Contents

1. [The settings of one language siteaccess](#the-settings-of-one-language-siteaccess)
2. [Step by step](#step-by-step)
3. [Sharing the look of the main site](#sharing-the-look-of-the-main-site)
4. [Checking](#checking)

## The settings of one language siteaccess

```ini
# settings/siteaccess/site_fr/site.ini.append.php
[RegionalSettings]
Locale=fre-FR
ContentObjectLocale=fre-FR
SiteLanguageList[]
SiteLanguageList[]=fre-FR
SiteLanguageList[]=eng-US
ShowUntranslatedObjects=disabled
TextTranslation=enabled
```

| Setting | What it does |
|---|---|
| `SiteLanguageList[]` | the content languages shown, most preferred first; the empty first line clears inherited values |
| `ShowUntranslatedObjects` | `disabled`: objects in none of the languages are hidden unless always available |
| `ContentObjectLocale` | default language for new content created in this siteaccess |
| `Locale` | interface language and date and number formats |
| `TextTranslation` | `enabled` to translate interface texts into `Locale` |

Do not change `ContentObjectLocale` on a site with content without reading the warning in `settings/site.ini`.

## Step by step

1. Add the content language (see "Adding a content language").
2. Copy `settings/siteaccess/site` to `settings/siteaccess/site_fr` and set the block above.
3. In `settings/override/site.ini.append.php` add `site_fr` to `[SiteAccessSettings] AvailableSiteAccessList[]` and
   `RelatedSiteAccessList[]`, and set up matching by path or host (see "Language addresses: path or host").
4. Clear the caches: `php bin/php/ezcache.php --clear-tag=ini,content`, and `./console exp:velocity cache clear --apply`
   when Velocity serves the site.
5. Translate, starting with the front page and the menu.

## Sharing the look of the main site

A translation siteaccess that should look exactly like the main site gets
`[SiteAccessSettings] ExtensionSettingsSiteAccess=<main siteaccess>`, so the design extension's settings for the main
siteaccess apply ([Translations and languages](../../../features/6.0/translations-and-languages.md#a-translation-siteaccess-that-looks-like-the-main-site)).
Give each siteaccess its own `SiteName` in the language.

## Checking

**Setup > Languages** shows, below the language cards, every siteaccess of `AvailableSiteAccessList` with its
`SiteLanguageList` in order; a locale that is not a content language is marked in red. Open the new address signed
out and check a translated and an untranslated page.

## Related pages

- [Building a multilingual site](../../creators/website-creators/multilingual-sites.md)
- [10.14 Multi-language sites](../../../install/10-after-installing.md#1014-multi-language-sites)
- [Extension loading order](../../../features/6.0/extension-loading-order.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
