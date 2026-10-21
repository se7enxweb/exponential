---
title: site.ini [eZINISettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [eZINISettings] in site.ini: ReadonlySettingList, DynamicTemplateMode"
keywords: Einstellung, site.ini, eZINISettings, INI, setting, ReadonlySettingList, DynamicTemplateMode
type: generated
generated: exp:help:examples
---
# site.ini [eZINISettings]

Der Block `[eZINISettings]` von `settings/site.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ReadonlySettingList

> Mit 'ReadonlySettingList' setzen Sie schreibgeschützte Einstellungen.  
> Verwendung:  
> für eine bestimmte Einstellung:  
>   ini-filename/section\_name/setting\_name  
> für alle Einstellungen eines Abschnitts:  
>   ini-filename/section\_name/\*  
> für alle Einstellungen einer INI-Datei:  
>   ini-filename/\*  

Standard: eine Liste mit 3: `template.ini/PHP/PHPOperatorList`, `image.ini/ImageMagick/ExecutablePath`, `image.ini/ImageMagick/Executable`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[eZINISettings]
# ein leeres "ReadonlySettingList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ReadonlySettingList[]
ReadonlySettingList[]=template.ini/PHP/PHPOperatorList
ReadonlySettingList[]=image.ini/ImageMagick/ExecutablePath
ReadonlySettingList[]=image.ini/ImageMagick/Executable
*/ ?>
```

## DynamicTemplateMode

> Dynamischer INI-Template-Operator  
> Ändert die globale Einstellung der Template-Operatoren ezini und ezini\_hasvariable  
> für den Standardwert des Parameters 'dynamic'  
> Wenn aktiviert, werden diese Aufrufe nicht als INI-Werte in die  
> Templates kompiliert, sondern dynamisch gelesen  
> Hinweis: nur während der Template-Kompilierung gelesen  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[eZINISettings]
DynamicTemplateMode=disabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
