---
title: site.ini [RegionalSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [RegionalSettings] in site.ini: Locale, HTTPLocale, SystemLocale, ContentObjectLocale, ContentObjectFallbackLocale, ShowUntranslatedObjects, SiteLanguageList, ContentXMLCharset ..."
keywords: Einstellung, site.ini, RegionalSettings, INI, setting, Locale, HTTPLocale, SystemLocale, ContentObjectLocale, ContentObjectFallbackLocale, ShowUntranslatedObjects, SiteLanguageList, ContentXMLCharset, TextTranslation, TranslationCache, SharedTranslationCacheDir, TranslationCheckMTime, Debug, DevelopmentMode, TranslationRepository, TranslationExtensions, LanguageSwitcherClass
type: generated
generated: exp:help:examples
---
# site.ini [RegionalSettings]

Der Block `[RegionalSettings]` von `settings/site.ini`, mit 17 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Locale

> The locale for your site  

Standard: `eng-GB`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RegionalSettings]
Locale=eng-GB
*/ ?>
```

## HTTPLocale

> Set this if the http locale in the locale is wrong  

Standard: leer.

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

Standard: leer.

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

Standard: `eng-US`.

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

Standard: `eng-US`.

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

Standard: `disabled`.

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

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RegionalSettings]
# ein leeres "SiteLanguageList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SiteLanguageList[]
*/ ?>
```

## ContentXMLCharset

> Controls how xml is stored in content objects.  
> disabled - Store with utf8  
> enabled  - Store with current charset  
> if any other text is entered it is assumed to be a charset and will be used  

Standard: `enabled`.

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

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RegionalSettings]
TextTranslation=enabled
*/ ?>
```

## TranslationCache

Standard: `enabled`.

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

Standard: leer.

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

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RegionalSettings]
TranslationCheckMTime=enabled
*/ ?>
```

## Debug

> Use either enabled to see which locale files are loaded or disabled to supress debug  

Standard: `disabled`.

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

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RegionalSettings]
DevelopmentMode=disabled
*/ ?>
```

## TranslationRepository

Standard: `share/translations/`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RegionalSettings]
TranslationRepository=share/translations/
*/ ?>
```

## TranslationExtensions

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RegionalSettings]
# ein leeres "TranslationExtensions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
TranslationExtensions[]
*/ ?>
```

## LanguageSwitcherClass

> NOTE: The LanguageSwitcher settings are not frozen, meaning they might still change.  
> The class which is used in the language switcher module to forward users to  
> selected translation siteaccesses.  

Standard: `ezpLanguageSwitcher`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RegionalSettings]
LanguageSwitcherClass=ezpLanguageSwitcher
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
