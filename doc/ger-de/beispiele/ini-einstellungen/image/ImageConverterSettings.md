---
title: image.ini [ImageConverterSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ImageConverterSettings] in image.ini: ImageConverters, LockTimeout"
keywords: Einstellung, image.ini, ImageConverterSettings, INI, setting, ImageConverters, LockTimeout
type: generated
generated: exp:help:examples
---
# image.ini [ImageConverterSettings]

Der Block `[ImageConverterSettings]` von `settings/image.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/image.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/image.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/image.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/image.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ImageConverters

> Liste der zu verwendenden Konverter; jeder Eintrag ist der Name des Konverters,  
> der in einem INI-Block definiert ist.  
> Standardmäßig können Sie die Handler ImageMagick und GD verwenden.  
> Die Reihenfolge der Konverter ist wichtig, weil der erste  
> passende Konverter verwendet wird. Der letzte Handler ist  
> meist ein Ausweichkonverter, der die meisten Formate beherrscht.  
>
> GD steht zuerst: Es ist die in PHP eingebaute Bilderweiterung, es  
> wandelt also im Prozess um, ohne für jedes Bild ein externes Programm  
> zu starten, und braucht außer PHP nichts Installiertes. Es liest und  
> schreibt JPEG, PNG, GIF und WBMP und unterstützt jeden Geometriefilter,  
> den die Standard-Aliase verwenden.  
> ImageMagick bleibt als Ausweichlösung in der Liste. Es wird für die  
> Formate verwendet, die GD nicht lesen kann (zum Beispiel PSD, TIFF, PDF oder WebP), und für  
> die Filter, die nur es unterstützt (zum Beispiel flatten, resize,  
> filter/swirl oder filter/noise), und es übernimmt ganz, wenn die  
> GD-Erweiterung fehlt oder \[GD\] IsEnabled=false ist.  
> Um wieder ImageMagick zu bevorzugen, überschreiben Sie die Liste in  
> settings/override/image.ini.append.php:  
>   ImageConverters\[\]  
>   ImageConverters\[\]=ImageMagick  
>   ImageConverters\[\]=GD  
> Die Liste darf auch Konverter enthalten, die auf dem installierten  
> Server nicht funktionieren; der Konverter sollte das prüfen  
> und dem Bildsystem sagen, ihn nicht zu verwenden.  
>
> Für jeden anderen Konverter sorgen Sie für einen INI-Abschnitt  
> mit demselben Namen wie Ihr Konverter  
>
> Zum Beispiel:  
> ImageConverters\[\]=MyCustomConverter  
> \[MyCustomConverter\]  
> mit denselben INI-Variablen wie die INI-Abschnitte GD oder ImageMagick  

Standard: eine Liste mit 2: `GD`, `ImageMagick`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[ImageConverterSettings]
# ein leeres "ImageConverters[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ImageConverters[]
ImageConverters[]=GD
ImageConverters[]=ImageMagick
*/ ?>
```

## LockTimeout

> Wartezeit für Prozesse in der Warteschlange, um eine exklusive Schreibsperre  
> zum Schreiben eines bestimmten Bild-Alias zu erhalten.  
>
> Wartet ein Prozess länger, wird der Prozess, der die Sperre hält,  
> beendet, und der wartende Prozess macht weiter und erzeugt den Bild-Alias.  

Standard: `60`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[ImageConverterSettings]
LockTimeout=60
*/ ?>
```

## Siehe auch

- [Alle Blöcke von image.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/image.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
