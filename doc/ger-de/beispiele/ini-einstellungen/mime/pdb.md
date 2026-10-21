---
title: mime.ini [pdb]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [pdb] in mime.ini: Types"
keywords: Einstellung, mime.ini, pdb, INI, setting, Types
type: generated
generated: exp:help:examples
---
# mime.ini [pdb]

Der Block `[pdb]` von `settings/mime.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/mime.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/mime.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/mime.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/mime.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Types

Standard: eine Liste mit 1: `chemical/x-pdb`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mime.ini.append.php
[pdb]
# ein leeres "Types[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Types[]
Types[]=chemical/x-pdb
*/ ?>
```

## Siehe auch

- [Alle Blöcke von mime.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/mime.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
