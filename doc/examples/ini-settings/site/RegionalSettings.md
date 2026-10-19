---
title: site.ini [RegionalSettings]
audience: developer, admin
category: examples
summary: "The settings of [RegionalSettings] in site.ini: Locale, HTTPLocale, SystemLocale, ContentObjectLocale, ContentObjectFallbackLocale, ShowUntranslatedObjects, SiteLanguageList, ContentXMLCharset ..."
keywords: site.ini, RegionalSettings, INI, setting, Locale, HTTPLocale, SystemLocale, ContentObjectLocale, ContentObjectFallbackLocale, ShowUntranslatedObjects, SiteLanguageList, ContentXMLCharset, TextTranslation, TranslationCache, SharedTranslationCacheDir, TranslationCheckMTime, Debug, DevelopmentMode, TranslationRepository, TranslationExtensions, LanguageSwitcherClass
type: generated
generated: exp:help:examples
---
# site.ini [RegionalSettings]

The block `[RegionalSettings]` of `settings/site.ini`, with 17 setting(s).

Never edit `settings/site.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/site.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in an extension (read when the extension is active).

## Locale

> The locale for your site  

Default: `eng-GB`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RegionalSettings]
Locale=eng-GB
*/ ?>
```

## HTTPLocale

> Set this if the http locale in the locale is wrong  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RegionalSettings]
HTTPLocale=
*/ ?>
```

## SystemLocale

> Set this if you want to run the entire PHP system in a given  
> locale, this means that PHP will be locale aware when dealing  
> with strings (e.g making characters in a string into lowercase). (C is a  
> special locale meaning 7-bit ASCII). Examples for this setting include  
> "german", "de\_DE.ISO-8859-1"; depending on your operating system.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RegionalSettings]
SystemLocale=
*/ ?>
```

## ContentObjectLocale

> The default language for content objects  
> Note: Changing this can have catastrophic consequences  
>       since there might not be any content objects with  
>       the selected language. Instead the content/translations  
>       page should be used for adding/removing translations.  

Default: `eng-US`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RegionalSettings]
ContentObjectLocale=eng-US
*/ ?>
```

## ContentObjectFallbackLocale

> The content language used as a last resort, when neither a prioritized  
> content language nor ContentObjectLocale is set: the setup wizard and command  
> line scripts create class and attribute names before the site's languages  
> exist. Empty or missing means eng-US.  

Default: `eng-US`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RegionalSettings]
ContentObjectFallbackLocale=eng-US
*/ ?>
```

## ShowUntranslatedObjects

> If enabled, ShowUntranslatedObjects will force showing the objects  
> existing in other languages than those specified in SiteLanguageList  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RegionalSettings]
ShowUntranslatedObjects=disabled
*/ ?>
```

## SiteLanguageList

> Prioritized list of languages. Only objects existing in these  
> languages will be shown (unless ShowUntranslatedObjects is enabled).  
> If an object exists in more languages, that one which is first in  
> SiteLanguageList will be used to render it.  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RegionalSettings]
# an empty "SiteLanguageList[]" first replaces the shipped list; without it the items are added
SiteLanguageList[]
*/ ?>
```

## ContentXMLCharset

> Controls how xml is stored in content objects.  
> disabled - Store with utf8  
> enabled  - Store with current charset  
> if any other text is entered it is assumed to be a charset and will be used  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RegionalSettings]
ContentXMLCharset=enabled
*/ ?>
```

## TextTranslation

> Use either disabled to disable all text translation (faster) or enabled.  
> Text translation is automatically disabled when the Locale is set to eng-GB  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RegionalSettings]
TextTranslation=enabled
*/ ?>
```

## TranslationCache

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RegionalSettings]
TranslationCache=enabled
*/ ?>
```

## SharedTranslationCacheDir

> Translation cache files will be stored in the directory located outside of the  
> siteaccess VarDir. Empty value disables sharing of translation cache.  
> Useful if you want to share translation cache between siteaccesses that  
> only differes by content, db and var directory.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RegionalSettings]
SharedTranslationCacheDir=
*/ ?>
```

## TranslationCheckMTime

> Sets if modified time should be checked or not on ts files when checking  
> if translation cache is valid  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RegionalSettings]
TranslationCheckMTime=enabled
*/ ?>
```

## Debug

> Use either enabled to see which locale files are loaded or disabled to supress debug  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RegionalSettings]
Debug=disabled
*/ ?>
```

## DevelopmentMode

> By enabling development mode all untranslated texts will be translated  
> using the bork rules.  
> See lib/ezi18n/classes/ezborktranslator.php for more information.  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RegionalSettings]
DevelopmentMode=disabled
*/ ?>
```

## TranslationRepository

Default: `share/translations/`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RegionalSettings]
TranslationRepository=share/translations/
*/ ?>
```

## TranslationExtensions

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RegionalSettings]
# an empty "TranslationExtensions[]" first replaces the shipped list; without it the items are added
TranslationExtensions[]
*/ ?>
```

## LanguageSwitcherClass

> NOTE: The LanguageSwitcher settings are not frozen, meaning they might still change.  
> The class which is used in the language switcher module to forward users to  
> selected translation siteaccesses.  

Default: `ezpLanguageSwitcher`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RegionalSettings]
LanguageSwitcherClass=ezpLanguageSwitcher
*/ ?>
```

## Related

- [All blocks of site.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/site.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
