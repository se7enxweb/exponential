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

> FallbackLanguages legt die verfügbaren Übersetzungs-Fallbacks  
> der QT-Übersetzungsdateien (.ts) fest. Gibt es keine Übersetzung  
> für z. B. ger-CH, wird die Übersetzungsdatei ger-DE angefordert.  
> Das wird für den Kernel und für jede Erweiterung einzeln nachgeschlagen: esl-MX hat eine  
> eigene Kernel-Übersetzung und nimmt esl-ES nur für die Erweiterungen, die  
> keine für esl-MX haben. Jedes Land einer Sprache mit einer Übersetzung fällt auf sie zurück,  
> sodass seine Administration in dieser Sprache ist (getestet in expUISwitchLanguageProviderTest).  
> Ein Fallback kann selbst einen Fallback haben: FallbackLanguages\[esl-AR\]=esl-MX mit  
> FallbackLanguages\[esl-MX\]=esl-ES ergibt die Kette esl-AR, esl-MX, esl-ES, in  
> dieser Reihenfolge für jede Übersetzungswurzel versucht. eng-GB und untranslated bedeuten die Quell-  
> texte und beenden eine Kette. ./console exp:i18n fallbacks zeigt jede Kette, und  
> ./console exp:i18n check findet Zyklen und fehlende Ziele (doc/eng-us/languages.md).  
>
> FallbackLanguages\[\] entscheidet immer zuerst. Dann die Klassen aus FallbackRules\[\]  
> (die expI18nFallbackRuleInterface implementieren, für Erweiterungen), dann, wenn aktiviert,  
> der automatische Fallback.  

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

> AutomaticLanguageFallback: Eine Locale ohne eigene Übersetzung und ohne  
> Eintrag in FallbackLanguages\[\] fällt auf die vollständigste Übersetzung derselben  
> Sprache zurück (fre-NC auf fre-FR), eine neue Länder-Locale braucht hier also keine Zeile.  
> enabled (Standard) oder disabled.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/i18n.ini.append.php
[TranslationSettings]
AutomaticLanguageFallback=enabled
*/ ?>
```

## AutomaticFallbackExclude

> Sprachen (eng) oder Locales (chi-MO), die nie automatisch zurückfallen. Englisch  
> ist die Sprache der Quelltexte.  

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

> Die meisten Schritte einer Kette (1 bis 16); eine längere wird abgeschnitten und gemeldet.  

Standard: `4`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/i18n.ini.append.php
[TranslationSettings]
FallbackMaxDepth=4
*/ ?>
```

## FallbackMode

> first (Standard): Jede Übersetzungswurzel lädt die erste Datei der Kette, die sie hat.  
> merge: Sie lädt jede Datei der Kette, die allgemeinste zuerst, sodass die Meldungen,  
> die einer Übersetzung fehlen (esl-MX), aus der nächsten genommen werden (esl-ES).  

Standard: `first`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/i18n.ini.append.php
[TranslationSettings]
FallbackMode=first
*/ ?>
```

## FallbackLanguages

Standard: ein Hash mit 106: `ger-CH=ger-DE`, `ger-AT=ger-DE`, `eng-US=eng-GB`, `eng-CA=eng-GB`, `fre-CA=fre-FR`, `fre-BE=fre-FR`, `hin-IN=untranslated`, `hun-HU=untranslated`, `ind-ID=untranslated`, `kor-KR=untranslated`, `por-MZ=por-PT`, `slk-SK=untranslated` ....

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
FallbackLanguages[por-MZ]=por-PT
FallbackLanguages[slk-SK]=untranslated
FallbackLanguages[tur-TR]=untranslated
FallbackLanguages[ukr-UA]=untranslated
FallbackLanguages[fre-CD]=fre-FR
FallbackLanguages[fre-CH]=fre-FR
FallbackLanguages[fre-CI]=fre-FR
FallbackLanguages[fre-CM]=fre-FR
FallbackLanguages[fre-MA]=fre-FR
FallbackLanguages[fre-SN]=fre-FR
# ... 86 weitere in der ausgelieferten Datei
*/ ?>
```

## Siehe auch

- [Alle Blöcke von i18n.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/i18n.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
