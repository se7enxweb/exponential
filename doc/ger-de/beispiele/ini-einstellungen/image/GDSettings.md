---
title: image.ini [GDSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [GDSettings] in image.ini: HasGD2"
keywords: Einstellung, image.ini, GDSettings, INI, setting, HasGD2
type: generated
generated: exp:help:examples
---
# image.ini [GDSettings]

Der Block `[GDSettings]` von `settings/image.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/image.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/image.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/image.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/image.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## HasGD2

> Wenn true, sind erweiterte Funktionen verfügbar,  
> die es in GD2 gibt. Dafür muss GD2 in das  
> PHP-Modul einkompiliert sein.  
> Dies entscheidet nur, ob die Ebenen von texttoimage als  
> True-Color- oder Palettenbilder gezeichnet werden. Es betrifft nicht den  
> Bildkonverter GD (\[GD\] unten), der den Farbtyp des  
> umgewandelten Bildes beibehält.  

Standard: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[GDSettings]
HasGD2=false
*/ ?>
```

## Siehe auch

- [Alle Blöcke von image.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/image.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
