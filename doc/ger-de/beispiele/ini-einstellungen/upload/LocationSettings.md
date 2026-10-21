---
title: upload.ini [LocationSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [LocationSettings] in upload.ini: ClassList, RootNode, MaxDepth, MaxItems"
keywords: Einstellung, upload.ini, LocationSettings, INI, setting, ClassList, RootNode, MaxDepth, MaxItems
type: generated
generated: exp:help:examples
---
# upload.ini [LocationSettings]

Der Block `[LocationSettings]` von `settings/upload.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/upload.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/upload.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/upload.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/upload.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ClassList

> Eine Liste der Klassenbezeichner, die in das Auswahlfeld für den Ort aufgenommen werden  

Standard: eine Liste mit 1: `folder`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/upload.ini.append.php
[LocationSettings]
# ein leeres "ClassList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ClassList[]
ClassList[]=folder
*/ ?>
```

## RootNode

> Von welchem Wurzelknoten die Orte geholt werden  
> Verwenden Sie eine Knoten-ID oder einen Knotenpfad (z. B. media )  
> - content - die Inhaltsstruktur  
> - users   - der Benutzerbaum  
> - media   - der Medienbaum für Bilder, Dateien und Medien  

Standard: `media`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/upload.ini.append.php
[LocationSettings]
RootNode=media
*/ ?>
```

## MaxDepth

> Die Höchstzahl der Ebenen, in denen nach Objekten gesucht wird  
> Eine Tiefe von 1 bedeutet direkte Nachkommen  

Standard: `3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/upload.ini.append.php
[LocationSettings]
MaxDepth=3
*/ ?>
```

## MaxItems

> Die Höchstzahl der Elemente im Auswahlfeld  
> Ein zu hoher Wert kann das System verlangsamen und das Auswahlfeld schwer bedienbar machen.  
> Eine Zahl zwischen 10 und 30 ist in Ordnung.  

Standard: `30`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/upload.ini.append.php
[LocationSettings]
MaxItems=30
*/ ?>
```

## Siehe auch

- [Alle Blöcke von upload.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/upload.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
