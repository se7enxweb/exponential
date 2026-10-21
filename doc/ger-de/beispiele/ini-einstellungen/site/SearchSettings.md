---
title: site.ini [SearchSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [SearchSettings] in site.ini: SearchEngine, SearchViewHandling, LogSearchStats, MaximumSearchLimit, MaximumSearchTextWords, MaximumSearchTextLength, AllowEmptySearch, EnableWildcard ..."
keywords: Einstellung, site.ini, SearchSettings, INI, setting, SearchEngine, SearchViewHandling, LogSearchStats, MaximumSearchLimit, MaximumSearchTextWords, MaximumSearchTextLength, AllowEmptySearch, EnableWildcard, MinCharacterWildcard, StopWordThresholdValue, StopWordThresholdPercent, DelayedIndexing, DelayedIndexingClassList, ExtensionDirectories
type: generated
generated: exp:help:examples
---
# site.ini [SearchSettings]

Der Block `[SearchSettings]` von `settings/site.ini`, mit 14 Einstellung(en).

> Einstellungen für das Suchmaschinen-Framework von Exponential  

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## SearchEngine

Standard: `eZSearchEngine`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SearchSettings]
SearchEngine=eZSearchEngine
*/ ?>
```

## SearchViewHandling

> Wie die Seiten Suche und erweiterte Suche arbeiten;  
> standardmäßig führt die Suchansicht die Suche aus, sie lässt sich aber auch  
> rein im Template-Code ausführen.  
> Mögliche Werte sind:  
> default - Der Code der Ansicht führt die Suche aus und übergibt das Ergebnis an das Template  
> template - Das Template führt die Suche aus und gibt das Ergebnis an den Code der Ansicht zurück  

Standard: `default`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SearchSettings]
SearchViewHandling=default
*/ ?>
```

## LogSearchStats

> Ob Suchstatistiken protokolliert werden  
> Die Suchstatistik speichert jeden Suchbegriff, den ein Besucher eingegeben hat, was  
> personenbezogene Daten enthalten kann. Standardmäßig deaktiviert; aktivieren Sie sie in einem Override, wenn die  
> Statistik in der Administrationsoberfläche gebraucht wird.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SearchSettings]
LogSearchStats=disabled
*/ ?>
```

## MaximumSearchLimit

> Der Höchstwert für Suchgrenzen, die aus HTTP-POST-Variablen geholt werden  
> Nötig, damit Benutzer einer Website keine lächerlich großen Grenzen angeben  

Standard: `30`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SearchSettings]
MaximumSearchLimit=30
*/ ?>
```

## MaximumSearchTextWords

> Die Suchansichten (content/search und content/advancedsearch) suchen nach höchstens so vielen Wörtern  
> und Zeichen eines Suchtextes und kürzen einen längeren; jedes Wort ist eine Klausel der Abfrage der Suchmaschine,  
> und bei zu vielen davon scheitert die Suche, statt Ergebnisse zu liefern (eZ Find: too many clauses)  

Standard: `16`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SearchSettings]
MaximumSearchTextWords=16
*/ ?>
```

## MaximumSearchTextLength

Standard: `1024`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SearchSettings]
MaximumSearchTextLength=1024
*/ ?>
```

## AllowEmptySearch

> Steuert, ob leere Suchen erlaubt sind; standardmäßig deaktiviert,  
> weil solche Suchen viele Ressourcen brauchen.  
> Ist die leere Suche aktiviert, müssen Sie sicherstellen, dass Ihr Template  
> genug Einschränkungen an die Suche übergibt, sonst müssen Sie damit rechnen, dass  
> Ihre Website deutlich langsamer wird  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SearchSettings]
AllowEmptySearch=disabled
*/ ?>
```

## EnableWildcard

> Platzhalter in der Suche aktivieren oder deaktivieren. Beachten Sie, dass die Platzhaltersuche  
> die Leistung des Systems stark beeinflussen kann.  

Standard: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SearchSettings]
EnableWildcard=false
*/ ?>
```

## MinCharacterWildcard

> Mindestzahl der Zeichen in einer Platzhaltersuche setzen  

Standard: `3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SearchSettings]
MinCharacterWildcard=3
*/ ?>
```

## StopWordThresholdValue

> Stoppwörter nur verwenden, wenn die Gesamtzahl der Objekte größer als StopWordThresholdValue ist  

Standard: `100`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SearchSettings]
StopWordThresholdValue=100
*/ ?>
```

## StopWordThresholdPercent

> Wörter, die in mehr als StopWordThresholdPercent % aller Objekte vorkommen, nicht suchen  

Standard: `60`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SearchSettings]
StopWordThresholdPercent=60
*/ ?>
```

## DelayedIndexing

> Steuert, ob Objekte beim Veröffentlichen indiziert werden oder verzögert  
> und später von einem Cronjob verarbeitet; lässt sich mit 'classbased' nach Klassen setzen  
> Dann sollte DelayedIndexingClassList mit Klassenbezeichnern gesetzt sein  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SearchSettings]
DelayedIndexing=disabled
*/ ?>
```

## DelayedIndexingClassList

> Liste der Klassenbezeichner, für die die verzögerte Indizierung verwendet wird.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SearchSettings]
# ein leeres "DelayedIndexingClassList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
DelayedIndexingClassList[]
*/ ?>
```

## ExtensionDirectories

> Liste der Pfade von Such-Erweiterungen. Fügen Sie dieser Liste den Namen der Erweiterung hinzu, in der das Such-Plugin  
> liegt.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SearchSettings]
# ein leeres "ExtensionDirectories[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExtensionDirectories[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
