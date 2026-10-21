---
title: site.ini [RSSSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [RSSSettings] in site.ini: ActiveExtensions, NumberOfObjectsList, NumberOfObjectsDefault, CacheTime, AvailableVersionList, OPMLMaxOutlines, DefaultVersion, DefaultFeedItemClasses"
keywords: Einstellung, site.ini, RSSSettings, INI, setting, ActiveExtensions, NumberOfObjectsList, NumberOfObjectsDefault, CacheTime, AvailableVersionList, OPMLMaxOutlines, DefaultVersion, DefaultFeedItemClasses
type: generated
generated: exp:help:examples
---
# site.ini [RSSSettings]

Der Block `[RSSSettings]` von `settings/site.ini`, mit 8 Einstellung(en).

> Einstellungen für RSS  

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ActiveExtensions

> Einstellungen der Erweiterungen für RSS-Import-Handler  
> Muss in <extension directory>/<active extension>/rss/ezrssimporthandler.php liegen  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RSSSettings]
# ein leeres "ActiveExtensions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ActiveExtensions[]
*/ ?>
```

## NumberOfObjectsList

> Liste der Anzahlen von Objekten, die in den RSS-Feed aufgenommen werden.  
> Diese Liste wird in der Bearbeitungsoberfläche des RSS-Exports in der Administration angezeigt.  

Standard: eine Liste mit 11: `5`, `10`, `20`, `30`, `40`, `50`, `100`, `250`, `350`, `500`, `1000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RSSSettings]
# ein leeres "NumberOfObjectsList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
NumberOfObjectsList[]
NumberOfObjectsList[]=5
NumberOfObjectsList[]=10
NumberOfObjectsList[]=20
NumberOfObjectsList[]=30
NumberOfObjectsList[]=40
NumberOfObjectsList[]=50
NumberOfObjectsList[]=100
NumberOfObjectsList[]=250
NumberOfObjectsList[]=350
NumberOfObjectsList[]=500
NumberOfObjectsList[]=1000
*/ ?>
```

## NumberOfObjectsDefault

> In der Bearbeitungsoberfläche des RSS-Exports standardmäßig ausgewählt.  

Standard: `20`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RSSSettings]
NumberOfObjectsDefault=20
*/ ?>
```

## CacheTime

> Cache-Zeit in Sekunden  
>
> Die Zeitspanne in Sekunden, die ein RSS-Feed aus dem Cache ausgeliefert wird.  
> Nach Ablauf der Zeitspanne wird der RSS-Feed neu erzeugt und  
> wieder aus dem Cache ausgeliefert.  
>
> Das bedeutet, dass Änderungen an den eingespeisten Inhalten spätestens  
> nach der maximalen Cache-Zeit erscheinen.  
>
> "CacheTime = 0" schaltet das Caching ab.  

Standard: `1200`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RSSSettings]
CacheTime=1200
*/ ?>
```

## AvailableVersionList

> Die Liste der verfügbaren RSS-Versionen.  
> OPML ist eine Liste von Feeds statt einer Liste von Artikeln: Ein darauf gesetzter Export  
> hat keine Inhaltsquelle und listet stattdessen die anderen Exporte dieser Installation oder  
> Inhaltsknoten auf. Siehe kernel/classes/ezrssexportopmlitem.php.  
> Ein Apple-Podcasts-Feed: RSS 2.0 mit dem iTunes-Namespace, den Apple für alles liest,  
> wofür RSS keinen Platz hat: Autor, Eigentümer, Bild,  
> Kategorie und Einstufung der Sendung. Die Folgen kommen wie bei jedem anderen  
> Feed aus Inhaltsquellen, und bei jeder muss das Enclosure einem Medien- oder Dateiattribut zugeordnet sein.  
> Siehe eZRSSExport::generateITunes().  

Standard: eine Liste mit 5: `1.0`, `2.0`, `ATOM`, `OPML`, `ITUNES`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RSSSettings]
# ein leeres "AvailableVersionList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AvailableVersionList[]
AvailableVersionList[]=1.0
AvailableVersionList[]=2.0
AvailableVersionList[]=ATOM
AvailableVersionList[]=OPML
AvailableVersionList[]=ITUNES
*/ ?>
```

## OPMLMaxOutlines

> Die meisten Gliederungspunkte, die ein OPML-Dokument enthält. Ein Dokument, das groß genug ist,  
> um den Speicher zu erschöpfen, wird von einer öffentlichen Adresse ausgeliefert und würde daher die Website  
> lahmlegen statt nur sich selbst. Begrenzt auf 50000, egal was hier steht.  

Standard: `5000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RSSSettings]
OPMLMaxOutlines=5000
*/ ?>
```

## DefaultVersion

> In der Bearbeitungsoberfläche des RSS-Exports standardmäßig ausgewählt.  

Standard: `2.0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RSSSettings]
DefaultVersion=2.0
*/ ?>
```

## DefaultFeedItemClasses

> Standardzuordnung von Quelle zu Unterelement  
> DefaultFeedItemClasses\[<source\_class>\]=<child\_class>\[;<child\_class\_2>\]  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RSSSettings]
# ein leeres "DefaultFeedItemClasses[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
DefaultFeedItemClasses[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
