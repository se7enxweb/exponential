---
title: file.ini [ClusteringSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ClusteringSettings] in file.ini: FileHandler, NonExistantStaleCacheHandling"
keywords: Einstellung, file.ini, ClusteringSettings, INI, setting, FileHandler, NonExistantStaleCacheHandling
type: generated
generated: exp:help:examples
---
# file.ini [ClusteringSettings]

Der Block `[ClusteringSettings]` von `settings/file.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/file.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/file.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/file.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/file.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## FileHandler

> Cluster-Datei-Handler.  
> Seit 4.1 haben sich die Namen der Datei-Handler geändert  
> Sie können wählen zwischen:  
> - eZFSFileHandler  
>   Standard-Datei-Handler  
> - eZDFSFileHandler  
>   Handler für verteilte Dateisysteme mit einer Datenbankschicht. Erforderlich für NFS-basierte  
>   Architekturen  
> Groß- und Kleinschreibung wird beachtet  

Standard: `eZFSFileHandler`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/file.ini.append.php
[ClusteringSettings]
FileHandler=eZFSFileHandler
*/ ?>
```

## NonExistantStaleCacheHandling

> VERALTET  
> Wenn Sie einen eigenen Cluster-Datei-Handler aus einer Erweiterung verwenden wollen,  
> fügen Sie den Namen der Erweiterung der Liste unten hinzu.  
> Dann wird der Handler im folgenden Verzeichnis gesucht:  
> extension/<your\_extension>/clusterfilehandlers  
> ExtensionDirectories\[\]  
> Legt fest, was geschieht, wenn eine angeforderte Cache-Datei gerade erzeugt wird  
> und keine abgelaufene Cache-Datei existiert (zum Beispiel bei neuem Inhalt)  
> Zwei mögliche Werte:  
>   - wait: lässt den Prozess für begrenzte Zeit in einer Warteschleife, bis die Datei  
>           fertig erzeugt ist. Das ist der Standard  
>   - generate: lässt den anfragenden Prozess seine eigenen Daten erzeugen, ohne das  
>               Ergebnis zu speichern  
> Der Schlüssel dieses Arrays legt die Art des betroffenen Caches fest. Drei  
> Cache-Arten sind hier erlaubt: viewcache, cacheblock und misc (jeder Cache, der  
> weder viewcache noch cacheblock ist).  

Standard: ein Hash mit 3: `viewcache=wait`, `cacheblock=wait`, `misc=wait`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/file.ini.append.php
[ClusteringSettings]
# ein leeres "NonExistantStaleCacheHandling[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
NonExistantStaleCacheHandling[]
NonExistantStaleCacheHandling[viewcache]=wait
NonExistantStaleCacheHandling[cacheblock]=wait
NonExistantStaleCacheHandling[misc]=wait
*/ ?>
```

## Siehe auch

- [Alle Blöcke von file.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/file.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
