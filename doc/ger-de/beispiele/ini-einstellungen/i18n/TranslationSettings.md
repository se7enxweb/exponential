---
title: i18n.ini [TranslationSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [TranslationSettings] in i18n.ini: FallbackRules, AutomaticLanguageFallback, AutomaticFallbackExclude, FallbackMaxDepth, FallbackMode, FallbackLanguages"
keywords: Einstellung, i18n.ini, TranslationSettings, INI, setting, FallbackRules, AutomaticLanguageFallback, AutomaticFallbackExclude, FallbackMaxDepth, FallbackMode, FallbackLanguages
type: generated
generated: exp:help:examples
---
# i18n.ini [TranslationSettings]

Der Block `[TranslationSettings]` von `settings/i18n.ini`, mit 6 Einstellung(en).

Bearbeiten Sie `settings/i18n.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/i18n.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/i18n.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/i18n.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

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

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/i18n.ini.append.php
[TranslationSettings]
# ein leeres "FallbackRules[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
FallbackRules[]
*/ ?>
```

## AutomaticLanguageFallback

> AutomaticLanguageFallback: a locale with no translation of its own and no  
> FallbackLanguages\[\] entry falls back to the most complete translation of the  
> same language (fre-NC to fre-FR), so a new country locale needs no line here.  
> enabled (default) or disabled.  

Standard: `enabled`.

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

Standard: eine Liste mit 1: `eng`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/i18n.ini.append.php
[TranslationSettings]
# ein leeres "AutomaticFallbackExclude[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AutomaticFallbackExclude[]
AutomaticFallbackExclude[]=eng
*/ ?>
```

## FallbackMaxDepth

> The most steps of a chain (1 to 16); a longer one is cut and reported.  

Standard: `4`.

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

Standard: `first`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/i18n.ini.append.php
[TranslationSettings]
FallbackMode=first
*/ ?>
```

## FallbackLanguages

Standard: ein Hash mit 99: `ger-CH=ger-DE`, `ger-AT=ger-DE`, `eng-US=eng-GB`, `eng-CA=eng-GB`, `fre-CA=fre-FR`, `fre-BE=fre-FR`, `hin-IN=untranslated`, `hun-HU=untranslated`, `ind-ID=untranslated`, `kor-KR=untranslated`, `por-MZ=por-BR`, `por-PT=por-BR` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/i18n.ini.append.php
[TranslationSettings]
# ein leeres "FallbackLanguages[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
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
# ... 79 weitere in der ausgelieferten Datei
*/ ?>
```

## Siehe auch

- [Alle Blöcke von i18n.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/i18n.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
