---
title: debug.ini [DebugSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [DebugSettings] in debug.ini: ConditionDebug"
keywords: Einstellung, debug.ini, DebugSettings, INI, setting, ConditionDebug
type: generated
generated: exp:help:examples
---
# debug.ini [DebugSettings]

Der Block `[DebugSettings]` von `settings/debug.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/debug.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/debug.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/debug.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/debug.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ConditionDebug

> Hauptschalter für bedingtes Debugging; wenn deaktiviert,  
> werden keine Bedingungen geprüft.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[DebugSettings]
ConditionDebug=enabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von debug.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/debug.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
