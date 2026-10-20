---
title: i18n.ini [TranslationSettings]
audience: developer, admin
category: examples
summary: "The settings of [TranslationSettings] in i18n.ini: FallbackRules, AutomaticLanguageFallback, AutomaticFallbackExclude, FallbackMaxDepth, FallbackMode, FallbackLanguages"
keywords: i18n.ini, TranslationSettings, INI, setting, FallbackRules, AutomaticLanguageFallback, AutomaticFallbackExclude, FallbackMaxDepth, FallbackMode, FallbackLanguages
type: generated
generated: exp:help:examples
---
# i18n.ini [TranslationSettings]

The block `[TranslationSettings]` of `settings/i18n.ini`, with 6 setting(s).

Never edit `settings/i18n.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/i18n.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/i18n.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/i18n.ini.append.php` in an extension (read when the extension is active).

## FallbackRules

> FallbackLanguages defines the available translation fallbacks  
> of the QT ( .ts ) translation files. If the this no translation available  
> for e.g. ger-CH the ger-DE translation file will be requested for the translation.  
> It is looked up for the kernel and for every extension on its own: esl-MX has a  
> kernel translation of its own, and takes esl-ES only for the extensions that have  
> no esl-MX one. Every country of a language with a translation falls back to it,  
> so its administration is in that language (tested in expUISwitchLanguageProviderTest).  
> A fallback can have a fallback of its own: FallbackLanguages\[esl-AR\]=esl-MX with  
> FallbackLanguages\[esl-MX\]=esl-ES makes the chain esl-AR, esl-MX, esl-ES, tried in  
> that order for each translation root. eng-GB and untranslated mean the source  
> texts and end a chain. ./console exp:i18n fallbacks shows every chain, and  
> ./console exp:i18n check finds cycles and missing targets (doc/eng-us/languages.md).  
>
> FallbackLanguages\[\] always decides first. Then the classes of FallbackRules\[\]  
> (implementing expI18nFallbackRuleInterface, for extensions), then, when enabled,  
> the automatic fallback.  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/i18n.ini.append.php
[TranslationSettings]
# an empty "FallbackRules[]" first replaces the shipped list; without it the items are added
FallbackRules[]
*/ ?>
```

## AutomaticLanguageFallback

> AutomaticLanguageFallback: a locale with no translation of its own and no  
> FallbackLanguages\[\] entry falls back to the most complete translation of the  
> same language (fre-NC to fre-FR), so a new country locale needs no line here.  
> enabled (default) or disabled.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/i18n.ini.append.php
[TranslationSettings]
AutomaticLanguageFallback=enabled
*/ ?>
```

## AutomaticFallbackExclude

> Languages (eng) or locales (chi-MO) that never fall back automatically. English  
> is the language of the source texts.  

Default: a list of 1: `eng`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/i18n.ini.append.php
[TranslationSettings]
# an empty "AutomaticFallbackExclude[]" first replaces the shipped list; without it the items are added
AutomaticFallbackExclude[]
AutomaticFallbackExclude[]=eng
*/ ?>
```

## FallbackMaxDepth

> The most steps of a chain (1 to 16); a longer one is cut and reported.  

Default: `4`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/i18n.ini.append.php
[TranslationSettings]
FallbackMaxDepth=4
*/ ?>
```

## FallbackMode

> first (default): each translation root loads the first file of the chain it has.  
> merge: it loads every file of the chain, the most general first, so the messages  
> a translation lacks (esl-MX) are taken from the next one (esl-ES).  

Default: `first`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/i18n.ini.append.php
[TranslationSettings]
FallbackMode=first
*/ ?>
```

## FallbackLanguages

Default: a hash of 99: `ger-CH=ger-DE`, `ger-AT=ger-DE`, `eng-US=eng-GB`, `eng-CA=eng-GB`, `fre-CA=fre-FR`, `fre-BE=fre-FR`, `hin-IN=untranslated`, `hun-HU=untranslated`, `ind-ID=untranslated`, `kor-KR=untranslated`, `por-MZ=por-BR`, `por-PT=por-BR` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/i18n.ini.append.php
[TranslationSettings]
# an empty "FallbackLanguages[]" first replaces the shipped list; without it the items are added
FallbackLanguages[]
FallbackLanguages[ger-CH]=ger-DE
FallbackLanguages[ger-AT]=ger-DE
FallbackLanguages[eng-US]=eng-GB
FallbackLanguages[eng-CA]=eng-GB
FallbackLanguages[fre-CA]=fre-FR
FallbackLanguages[fre-BE]=fre-FR
FallbackLanguages[hin-IN]=untranslated
FallbackLanguages[hun-HU]=untranslated
FallbackLanguages[ind-ID]=untranslated
FallbackLanguages[kor-KR]=untranslated
FallbackLanguages[por-MZ]=por-BR
FallbackLanguages[por-PT]=por-BR
FallbackLanguages[slk-SK]=untranslated
FallbackLanguages[tur-TR]=untranslated
FallbackLanguages[ukr-UA]=untranslated
FallbackLanguages[fre-CD]=fre-FR
FallbackLanguages[fre-CH]=fre-FR
FallbackLanguages[fre-CI]=fre-FR
FallbackLanguages[fre-CM]=fre-FR
FallbackLanguages[fre-MA]=fre-FR
# ... 79 more in the shipped file
*/ ?>
```

## Related

- [All blocks of i18n.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/i18n.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
