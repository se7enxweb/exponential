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

> Die Locale Ihrer Website  

Standard: `eng-GB`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RegionalSettings]
Locale=eng-GB
*/ ?>
```

## HTTPLocale

> Setzen Sie dies, wenn die HTTP-Locale in der Locale falsch ist  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RegionalSettings]
HTTPLocale=
*/ ?>
```

## SystemLocale

> Setzen Sie dies, wenn Sie das ganze PHP-System in einer bestimmten  
> Locale ausführen wollen; PHP berücksichtigt die Locale dann beim Umgang  
> mit Zeichenketten (z. B. beim Umwandeln von Zeichen in Kleinbuchstaben). (C ist eine  
> besondere Locale und bedeutet 7-Bit-ASCII). Beispiele für diese Einstellung sind  
> "german", "de\_DE.ISO-8859-1"; abhängig von Ihrem Betriebssystem.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RegionalSettings]
SystemLocale=
*/ ?>
```

## ContentObjectLocale

> Die Standardsprache für Inhaltsobjekte  
> Hinweis: Eine Änderung kann katastrophale Folgen haben,  
>       da es eventuell keine Inhaltsobjekte in  
>       der gewählten Sprache gibt. Verwenden Sie stattdessen die Seite content/translations,  
>       um Übersetzungen hinzuzufügen oder zu entfernen.  

Standard: `eng-US`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RegionalSettings]
ContentObjectLocale=eng-US
*/ ?>
```

## ContentObjectFallbackLocale

> Die Inhaltssprache, die als letzte Möglichkeit verwendet wird, wenn weder eine priorisierte  
> Inhaltssprache noch ContentObjectLocale gesetzt ist: Der Setup-Assistent und Kommando-  
> zeilenskripte legen Klassen- und Attributnamen an, bevor es die Sprachen der Website  
> gibt. Leer oder fehlend bedeutet eng-US.  

Standard: `eng-US`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RegionalSettings]
ContentObjectFallbackLocale=eng-US
*/ ?>
```

## ShowUntranslatedObjects

> Wenn aktiviert, erzwingt ShowUntranslatedObjects die Anzeige der Objekte,  
> die in anderen als den in SiteLanguageList angegebenen Sprachen existieren  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RegionalSettings]
ShowUntranslatedObjects=disabled
*/ ?>
```

## SiteLanguageList

> Priorisierte Liste der Sprachen. Nur Objekte, die in diesen  
> Sprachen existieren, werden angezeigt (außer ShowUntranslatedObjects ist aktiviert).  
> Existiert ein Objekt in mehreren Sprachen, wird die zum Rendern verwendet, die in  
> SiteLanguageList zuerst steht.  

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

> Steuert, wie XML in Inhaltsobjekten gespeichert wird.  
> disabled - mit utf8 speichern  
> enabled  - mit dem aktuellen Zeichensatz speichern  
> Jeder andere eingegebene Text gilt als Zeichensatz und wird verwendet  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RegionalSettings]
ContentXMLCharset=enabled
*/ ?>
```

## TextTranslation

> Verwenden Sie disabled, um jede Textübersetzung abzuschalten (schneller), oder enabled.  
> Die Textübersetzung wird automatisch abgeschaltet, wenn die Locale auf eng-GB steht  

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

> Die Cache-Dateien der Übersetzungen werden in einem Verzeichnis außerhalb des  
> VarDir des Siteaccess gespeichert. Ein leerer Wert schaltet das Teilen des Übersetzungs-Caches ab.  
> Nützlich, wenn Sie den Übersetzungs-Cache zwischen Siteaccesses teilen wollen, die sich  
> nur in Inhalt, Datenbank und var-Verzeichnis unterscheiden.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RegionalSettings]
SharedTranslationCacheDir=
*/ ?>
```

## TranslationCheckMTime

> Legt fest, ob beim Prüfen der Gültigkeit des Übersetzungs-Caches  
> die Änderungszeit der ts-Dateien geprüft wird  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RegionalSettings]
TranslationCheckMTime=enabled
*/ ?>
```

## Debug

> Verwenden Sie enabled, um zu sehen, welche Locale-Dateien geladen werden, oder disabled, um das Debugging zu unterdrücken  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RegionalSettings]
Debug=disabled
*/ ?>
```

## DevelopmentMode

> Im Entwicklungsmodus werden alle unübersetzten Texte  
> nach den Bork-Regeln übersetzt.  
> Mehr dazu in lib/ezi18n/classes/ezborktranslator.php.  

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

> HINWEIS: Die Einstellungen des LanguageSwitcher sind nicht festgeschrieben und können sich noch ändern.  
> Die Klasse, die im Modul der Sprachumschaltung Benutzer auf die  
> gewählten Übersetzungs-Siteaccesses weiterleitet.  

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
