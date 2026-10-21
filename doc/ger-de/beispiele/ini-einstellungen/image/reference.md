---
title: image.ini [reference]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [reference] in image.ini: Filters"
keywords: Referenz, Einstellung, image.ini, reference, INI, setting, Filters
type: generated
generated: exp:help:examples
---
# image.ini [reference]

Der Block `[reference]` von `settings/image.ini`, mit 1 Einstellung(en).

> AliasList\[\]=rssitem  
> AliasList\[\]=gray  
> AliasList\[\]=zoom  
> Beispiel eines Bild-Alias; die Gruppe heißt so wie der Alias  
> \[myalias\]  
>
> Sagt dem System, den Alias original als Referenzbild zu verwenden. Das Referenz-  
> bild ist die Grundlage für Umwandlung und Filter. original ist der Standard und  
> kann weggelassen werden  
> Reference=reference  
>
> Der MIME-Typ des Ausgabebildes. Kann angegeben werden, wenn Sie für den Alias  
> ein bestimmtes Format wollen; sonst lassen Sie es weg und das Bildsystem entscheidet.  
> Eine sinnvolle Anwendung sind Bildschirmfotos: Für ein Bildschirmfoto will man  
> normalerweise nicht image/jpeg, sondern eher image/png oder image/gif.  
> MIMEType=image/png  
>
> Qualität nach MIME-Typ nur für diesen Alias überschreiben  
> Folgt dem Muster der globalen Einstellungen in \[MIMETypeSettings\]  
> Quality\[\]  
> Quality\[\]=image/jpeg;80  
>
> Eine Liste auszuführender Filter; jeder Filter muss von den aktiven Bildkonvertern unterstützt werden  
> Filters\[\]=geometry/scale=300;300  
> reference, small, medium und large zur Rückwärtskompatibilität  
> Sie können zum Beispiel reference entfernen, wenn Sie den zusätzlichen Speicherplatz nicht wollen  
> Wenn Sie den Alias 'original' definieren und Filter setzen  
> oder den MIME-Typ ändern, können Sie sogar eine Umwandlung  
> des ursprünglich hochgeladenen Bildes erzwingen.  
> Das ist nützlich, um Platz zu sparen oder sicherzustellen, dass das Bild in einem bestimmten Format vorliegt  
> \[original\]  
> Filters\[\]  
> Filters\[\]=colorspace/gray  
> \[original\]  
> Filters\[\]  
> Filters\[\]=filter/swirl=210  
> # MIMEType=image/png  

Bearbeiten Sie `settings/image.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/image.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/image.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/image.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Filters

Standard: eine Liste mit 1: `geometry/scaledownonly=600;600`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[reference]
# ein leeres "Filters[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Filters[]
Filters[]=geometry/scaledownonly=600;600
*/ ?>
```

## Siehe auch

- [Alle Blöcke von image.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/image.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
