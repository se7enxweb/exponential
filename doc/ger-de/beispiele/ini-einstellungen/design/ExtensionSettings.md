---
title: design.ini [ExtensionSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ExtensionSettings] in design.ini: DesignExtensions"
keywords: Einstellung, design.ini, ExtensionSettings, INI, setting, DesignExtensions
type: generated
generated: exp:help:examples
---
# design.ini [ExtensionSettings]

Der Block `[ExtensionSettings]` von `settings/design.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/design.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/design.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/design.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/design.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## DesignExtensions

> A list of extensions which have design data  
> It's common to create a settings/design.ini.append file  
> in your extension and add the extension name to automatically  
> get design from the extension when it's turned on.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/design.ini.append.php
[ExtensionSettings]
# ein leeres "DesignExtensions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
DesignExtensions[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von design.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/design.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
