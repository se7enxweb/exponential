---
title: Translations and languages
audience: cms-developer
category: cms-development
order: 18
summary: For CMS developers building multi-language sites. It covers content languages against interface languages, translations as part of one object, translatable and shared fields, SiteLanguageList and fallbacks, always available objects, one siteaccess per language, language switchers and translating your own template strings.
keywords: translations, languages, multi-language, multilingual, content language, interface language, SiteLanguageList, ShowUntranslatedObjects, ContentObjectLocale, always available, main language, language mask, translatable attribute, language_switcher, i18n, translation.ts
type: help
---
# Translations and languages

This page is for developers who build a site in more than one language. In Exponential a translation is not a copy
of a page: it is another language of the **same object**, so relations, locations, permissions and the tree are
shared by every language, and only the texts differ.

## Contents

1. [Two kinds of language](#two-kinds-of-language)
2. [Translations belong to the object](#translations-belong-to-the-object)
3. [Which fields are translated](#which-fields-are-translated)
4. [Which language a visitor sees](#which-language-a-visitor-sees)
5. [Always available objects](#always-available-objects)
6. [One siteaccess per language](#one-siteaccess-per-language)
7. [Template strings](#template-strings)
8. [Translations from code](#translations-from-code)
9. [Caches after language changes](#caches-after-language-changes)

## Two kinds of language

| | Content language | Interface language |
|---|---|---|
| What | the language objects are written in | the texts of templates and the administration |
| Stored | in the database, per object | in `.ts` translation files (`share/translations/<locale>/`, an extension's `translations/`) |
| Chosen by | `site.ini [RegionalSettings] SiteLanguageList[]` of the siteaccess | `[RegionalSettings] Locale` of the siteaccess |

They are independent: a siteaccess can show German content with an English interface
([Content languages, section 1](../../guides/content-languages.md#1-content-languages-and-interface-languages)).

## Translations belong to the object

Content languages are added on **Setup** > **Languages** before anyone translates into them. Each language is one bit
in a mask (2 for the first, 4 for the second and so on), and every object records its languages in its own mask, so
"objects in German" is one bit test on any database. Each object has a **main language**, the one it was created in,
which cannot be removed while it is the main one. How the storage works and the limit of 62 languages:
[Content languages, section 2](../../guides/content-languages.md#2-how-languages-are-stored).

Because all translations are versions of one object, publishing a German translation creates a new version of the
object, and the English text in that version is unchanged
([Versions, drafts and publishing](versions-drafts-and-publishing.md)).

## Which fields are translated

Each class attribute has a **translatable** flag (`can_translate`). Untick it for values that must be equal in every
language: dates, prices, e-mail addresses, images that contain no text, relations to shared objects. Translators then
see the value of the main language and cannot change it per language. Classes have translatable names and
descriptions too, which editors see in the create menu ([Content classes and attributes](content-classes-and-attributes.md)).

## Which language a visitor sees

Per siteaccess, in `settings/siteaccess/<name>/site.ini.append.php`:

```ini
[RegionalSettings]
Locale=ger-DE
ContentObjectLocale=ger-DE
SiteLanguageList[]
SiteLanguageList[]=ger-DE
SiteLanguageList[]=eng-US
ShowUntranslatedObjects=disabled
TextTranslation=enabled
```

An object is shown in the first language of `SiteLanguageList[]` it has. With `ShowUntranslatedObjects=disabled` (the
shipped value) objects in none of the listed languages are hidden, unless they are always available. The empty
`SiteLanguageList[]` line clears what other files set. Every setting:
[Content languages, section 5](../../guides/content-languages.md#5-showing-a-language-sitelanguagelist-and-siteaccesses).

Fetches follow the same rules, so a German list shows German titles, falls back to English, and leaves out what has
neither. The `list` fetch has `language` and `only_translated` parameters for lists that must differ
([Fetching content in templates](fetching-content-in-templates.md)).

## Always available objects

**Always available** is a flag per object (bit 0 of its mask): such an object is shown in its main language where no
listed language exists. Folders, users, user groups and media are usually always available, so a German site still
finds the image folder and the author of an article. A class sets the default for new objects (`always_available`)
([Content languages, section 6](../../guides/content-languages.md#6-main-language-and-always-available)).

## One siteaccess per language

A multi-language site usually has one public siteaccess per language, matched by a URL element (`/de/`) or a host
name, sharing one database and listed in `[SiteAccessSettings] RelatedSiteAccessList[]`. Each language has its own
URL aliases for the same node ([URL aliases and routing](url-aliases-and-routing.md)). For links between the language
versions of a page, `[RegionalSettings] TranslationSA[]` names the translation siteaccesses, and the template operator
`language_switcher` with the module `switchlanguage/to` builds the links. Setup in the installation book:
[10.14 Multi-language sites](../../install/10-after-installing.md#1014-multi-language-sites), and
[Siteaccesses and multisite](siteaccesses-and-multisite.md).

## Template strings

Fixed texts in your templates are translated with the `i18n` operator and a context:

```smarty
<a href={'/content/search'|ezurl}>{'Search'|i18n( 'extension/exp_mysite' )}</a>
```

The translations live in your extension's `translations/<locale>/translation.ts`, and the extension is listed in
`site.ini [RegionalSettings] TranslationExtensions[]`
([Translating template strings](../designers/templates/translating-template-strings.md),
[Translations in an extension](../extension-development/part-3-extension-developer/translations-in-an-extension.md)).

## Translations from code

`eZContentFunctions::updateAndPublishObject()` takes a `language` parameter, so an import adds a translation to an
existing object instead of creating a second object. Import the main language first, then each translation to the same
object, found by its remote ID ([Content from PHP](content-from-php.md),
[Mapping content: translations](../../install/migration/content-model-mapping.md#3-objects-versions-and-translations)).

## Caches after language changes

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user        # after changing [RegionalSettings]
php bin/php/ezcache.php --clear-tag=content --allow-root-user    # lists and pages drawn again
php bin/php/ezcache.php --clear-tag=i18n --allow-root-user       # after changing a .ts file
```

With Velocity, also `./console exp:velocity cache clear`. The full list, including URL alias rebuilds:
[Content languages, section 10](../../guides/content-languages.md#10-commands-for-the-shell).

## Related pages

- [Content languages](../../guides/content-languages.md): the complete guide
- [Languages](../languages.md): the editor's view
- [Right-to-left](../../features/6.0/right-to-left.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
