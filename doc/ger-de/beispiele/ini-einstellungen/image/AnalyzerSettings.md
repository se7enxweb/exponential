---
title: image.ini [AnalyzerSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AnalyzerSettings] in image.ini: RepositoryList, ExtensionList, ImageAnalyzers, ImageAnalyzerAlias, AnalyzerMIMEList"
keywords: Einstellung, image.ini, AnalyzerSettings, INI, setting, RepositoryList, ExtensionList, ImageAnalyzers, ImageAnalyzerAlias, AnalyzerMIMEList
type: generated
generated: exp:help:examples
---
# image.ini [AnalyzerSettings]

Der Block `[AnalyzerSettings]` von `settings/image.ini`, mit 5 Einstellung(en).

> -------- Einstellungen der Bildanalyse --------  

Bearbeiten Sie `settings/image.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/image.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/image.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/image.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## RepositoryList

> Wo nach Analysatoren gesucht wird  

Standard: eine Liste mit 1: `lib/ezimage/classes`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AnalyzerSettings]
# ein leeres "RepositoryList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RepositoryList[]
RepositoryList[]=lib/ezimage/classes
*/ ?>
```

## ExtensionList

> Eine Liste der Erweiterungen, die Analysatoren haben; gesucht wird im Unterverzeichnis imageanalyzer.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AnalyzerSettings]
# ein leeres "ExtensionList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExtensionList[]
*/ ?>
```

## ImageAnalyzers

> Eine Liste der aktiven Analysatoren  

Standard: eine Liste mit 2: `GIF`, `EXIF`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AnalyzerSettings]
# ein leeres "ImageAnalyzers[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ImageAnalyzers[]
ImageAnalyzers[]=GIF
ImageAnalyzers[]=EXIF
*/ ?>
```

## ImageAnalyzerAlias

> Eine Liste von Aliasen für Analysatoren; damit lassen sich bestimmte  
> Analysatoren auf eigene umleiten.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AnalyzerSettings]
# ein leeres "ImageAnalyzerAlias[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ImageAnalyzerAlias[]
*/ ?>
```

## AnalyzerMIMEList

> Eine Liste der Formate, die eine zusätzliche Analyse brauchen  
> GIF-Bilder müssen analysiert werden, um  
> animierte GIFs zu erkennen; animierte GIFs lassen sich schwer  
> in andere Formate umwandeln.  

Standard: eine Liste mit 1: `image/gif`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AnalyzerSettings]
# ein leeres "AnalyzerMIMEList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AnalyzerMIMEList[]
AnalyzerMIMEList[]=image/gif
*/ ?>
```

## Siehe auch

- [Alle Blöcke von image.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/image.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
