---
title: Building a multilingual site
audience: website-creator, admin
category: website-creation
order: 250
summary: Build a site in several languages - add content languages, give each language a siteaccess with SiteLanguageList, translate, set fallbacks and always-available content, and give translators their own role.
keywords: multilingual, languages, translation, SiteLanguageList, ContentObjectLocale, Locale, ShowUntranslatedObjects, always available, translators, content languages, siteaccess per language, website creator
type: help
---
# Building a multilingual site

This page is for website creators who build a site in more than one language. In Exponential every object can hold a
translation per content language, and every siteaccess decides which languages it shows, in which order. The full
reference is [Content languages](../../../guides/content-languages.md); this page is the site builder's path through it.

![The translations overview: the languages of the installation and how many items exist in each](../../images/creators/translations-overview.webp)

## Contents

1. [How languages work](#how-languages-work)
2. [Step by step: adding a second language](#step-by-step-adding-a-second-language)
3. [Fallbacks and untranslated content](#fallbacks-and-untranslated-content)
4. [Translators](#translators)
5. [Templates and interface texts](#templates-and-interface-texts)
6. [Things that delete content](#things-that-delete-content)
7. [Checklist](#checklist)

## How languages work

- **Adding a content language translates nothing and changes no siteaccess.** It only makes the language available
  for translations.
- **A siteaccess shows each object in the first language of its `SiteLanguageList[]` that the object has.**
- **Objects in none of those languages are hidden**, unless they are marked "always available" or the siteaccess has
  `ShowUntranslatedObjects=enabled`.
- **Every object has a main language.**
- Exponential knows a large set of locales, including right-to-left ones ([Languages in Exponential](../../../languages.md)).
  More than 30 content languages need a 64-bit PHP, because the languages of an object are stored as bits of one
  integer ([2.2 PHP](../../../install/02-requirements.md#22-php)).

## Step by step: adding a second language

From [Adding German to an English site](../../../guides/content-languages.md#adding-german-to-an-english-site):

1. **Add the language** in **Setup > Languages > Add language** (for example `ger-DE`).
2. **Give it a siteaccess.** Copy `settings/siteaccess/site` to `settings/siteaccess/site_de` and set:

   ```ini
   # settings/siteaccess/site_de/site.ini.append.php
   [RegionalSettings]
   Locale=ger-DE
   ContentObjectLocale=ger-DE
   SiteLanguageList[]
   SiteLanguageList[]=ger-DE
   SiteLanguageList[]=eng-US
   ShowUntranslatedObjects=disabled
   TextTranslation=enabled
   ```

3. **Register it** in `AvailableSiteAccessList[]` and `RelatedSiteAccessList[]` and set up matching: a path (`/de`) or
   a host ([Language switchers and addresses per language](language-switcher-and-language-urls.md)).
4. **Clear caches**: `php bin/php/ezcache.php --clear-tag=ini,content --allow-root-user` (and the Velocity response
   cache, `./console exp:velocity cache clear --apply`, when Velocity serves the site).
5. **Translate**, starting with the front page and the menu folders.
6. **Mark shared objects always available** (media folders, users) so they show without a translation.
7. **Check** the German address.

## Fallbacks and untranslated content

| Wanted | Setting |
|---|---|
| Show English where German is missing | `SiteLanguageList[]=ger-DE` then `eng-US` |
| Hide what is not translated | `ShowUntranslatedObjects=disabled` and no second language in the list |
| Show everything in its main language when nothing else fits | `ShowUntranslatedObjects=enabled` |
| An object that appears in every language as it is (logo, a PDF) | mark it **always available** |

## Translators

Give translators a role with `content/translate` and `content/edit` limited by **Language** to their language, plus
read access to the content. They then edit only their translation
([Roles and policies](../../../guides/roles-and-policies.md)).

## Templates and interface texts

- Texts written in templates go through `|i18n( 'design/mysite' )` and are translated in `.ts` files of your design
  extension; `TextTranslation=enabled` and `Locale` choose the catalogue (the translations cookbook, `doc/eng-us/examples/cookbook/translations.md`).
- Dates and numbers follow the siteaccess's `Locale`.
- Set `lang` on the `<html>` element from `$site.http_equiv.Content-language`.

## Things that delete content

- **Removing a translation** deletes that translation for good.
- **Removing a language** is only possible when no object and no class uses it, so it never deletes content.
- Changing `ContentObjectLocale` on a site with content needs care; read the warning in `settings/site.ini` first.

## Checklist

- [ ] One siteaccess per language, each with its `SiteLanguageList[]`, `Locale` and `SiteName`.
- [ ] The fallback order decided and written down.
- [ ] Shared objects marked always available.
- [ ] Translators limited to their language.
- [ ] The design's own texts translated.
- [ ] A language switcher on every page ([Language switchers](language-switcher-and-language-urls.md)).
- [ ] Right-to-left languages checked ([Right-to-left sites](right-to-left-sites.md)).

## Related pages

- [Languages (for editors)](../../languages.md)
- [Planning multisite and multilingual projects](../../leadership/multisite-and-multilingual-planning.md)
- [10.14 Multi-language sites](../../../install/10-after-installing.md#1014-multi-language-sites)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
