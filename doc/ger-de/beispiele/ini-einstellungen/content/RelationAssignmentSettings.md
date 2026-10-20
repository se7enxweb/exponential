---
title: content.ini [RelationAssignmentSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [RelationAssignmentSettings] in content.ini: DefaultAssignment, ClassSpecificAssignment"
keywords: Einstellung, content.ini, RelationAssignmentSettings, INI, setting, DefaultAssignment, ClassSpecificAssignment
type: generated
generated: exp:help:examples
---
# content.ini [RelationAssignmentSettings]

Der Block `[RelationAssignmentSettings]` von `settings/content.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/content.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/content.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/content.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/content.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## DefaultAssignment

> Standardplatzierung für neue verknüpfte Objekte, mögliche Werte sind  
> - eine Knoten-ID - alle neuen Objekte unter diesen Knoten legen  
> - ein Knotenpfad - die Platzierung des Elternknotens über einen Pfad, z. B. media/files  
> - root - die Wurzel des Baums  
> - users - die Wurzel des Benutzerbaums  
> - media - die Wurzel des Medienbaums  
> - none - VERALTET  
>
> Mehrere Platzierungen sind nicht erlaubt.  

Standard: `root`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[RelationAssignmentSettings]
DefaultAssignment=root
*/ ?>
```

## ClassSpecificAssignment

> Automatische Platzierungen für bestimmte Klassen.  
>
> Jede Zeile besteht aus drei oder zwei durch Semikolon getrennten Werten.  
> Die ersten beiden Werte sind durch Kommas getrennte Listen von Elementen.  
>
> Der erste Wert ist der Klassenbezeichner, der zweite die Platzierung (Werte  
> siehe oben). Ein optionaler dritter ist der Hauptknoten, falls Sie an viele  
> Platzierungen hochladen. Ist der dritte Parameter nicht gesetzt, wird das erste Element  
> des zweiten Werts als Hauptknoten verwendet.  
>
> Klassen werden mit Bezeichner, ID oder group\_ID angegeben  
> Die Einträge werden der Reihe nach durchsucht, der erste Treffer wird verwendet,  
> Klassen-IDs sollten also vor IDs von Klassengruppen stehen.  
>
> Ist dieselbe Klasse in mehreren Zeilen angegeben, wird der erste  
> Treffer verwendet, bei dem der Benutzer Zugriff auf alle aufgeführten Knoten hat.  

Standard: eine Liste mit 8: `user,user_group;users/guest_accounts`, `image;media/images`, `video;media/multimedia`, `file;media/files`, `quicktime;media/multimedia`, `windows_media;media/multimedia`, `real_video;media/multimedia`, `flash;media/multimedia`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[RelationAssignmentSettings]
# ein leeres "ClassSpecificAssignment[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ClassSpecificAssignment[]
ClassSpecificAssignment[]=user,user_group;users/guest_accounts
ClassSpecificAssignment[]=image;media/images
ClassSpecificAssignment[]=video;media/multimedia
ClassSpecificAssignment[]=file;media/files
ClassSpecificAssignment[]=quicktime;media/multimedia
ClassSpecificAssignment[]=windows_media;media/multimedia
ClassSpecificAssignment[]=real_video;media/multimedia
ClassSpecificAssignment[]=flash;media/multimedia
*/ ?>
```

## Siehe auch

- [Alle Blöcke von content.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/content.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
