---
title: template.ini [ControlSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ControlSettings] in template.ini: DisplayWarnings, AllowUserVariables, MaxLevel"
keywords: Einstellung, template.ini, ControlSettings, INI, setting, DisplayWarnings, AllowUserVariables, MaxLevel
type: generated
generated: exp:help:examples
---
# template.ini [ControlSettings]

Der Block `[ControlSettings]` von `settings/template.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/template.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/template.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/template.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/template.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## DisplayWarnings

> Ob Warnungen der Template-Engine auf der Seite  
> angezeigt werden.  
> Hinweis: Debug-Warnungen werden bei eingeschaltetem Debugging immer angezeigt.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/template.ini.append.php
[ControlSettings]
DisplayWarnings=disabled
*/ ?>
```

## AllowUserVariables

> Benutzerdefinierter Parameter.  
> Bsp.: http://example.com/developer/(poll\_option)/5  
> speichert poll\_option 5 im Template in $view\_parameters.poll\_option  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/template.ini.append.php
[ControlSettings]
AllowUserVariables=true
*/ ?>
```

## MaxLevel

> Höchste Verschachtelungstiefe von Templates, um endlose Rekursion zu vermeiden  

Standard: `40`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/template.ini.append.php
[ControlSettings]
MaxLevel=40
*/ ?>
```

## Siehe auch

- [Alle Blöcke von template.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/template.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
