---
title: toolbar.ini [Toolbar_top]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Toolbar_top] in toolbar.ini: Tool"
keywords: Einstellung, toolbar.ini, Toolbar_top, INI, setting, Tool
type: generated
generated: exp:help:examples
---
# toolbar.ini [Toolbar_top]

Der Block `[Toolbar_top]` von `settings/toolbar.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/toolbar.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/toolbar.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/toolbar.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/toolbar.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Tool

Standard: eine Liste mit 2: `login`, `searchbox`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/toolbar.ini.append.php
[Toolbar_top]
# ein leeres "Tool[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Tool[]
Tool[]=login
Tool[]=searchbox
*/ ?>
```

## Siehe auch

- [Alle Blöcke von toolbar.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/toolbar.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
