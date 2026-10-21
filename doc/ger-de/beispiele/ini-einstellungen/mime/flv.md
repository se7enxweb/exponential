---
title: mime.ini [flv]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [flv] in mime.ini: Types"
keywords: Einstellung, mime.ini, flv, INI, setting, Types
type: generated
generated: exp:help:examples
---
# mime.ini [flv]

Der Block `[flv]` von `settings/mime.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/mime.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/mime.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/mime.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/mime.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Types

Standard: eine Liste mit 1: `video/x-flv`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mime.ini.append.php
[flv]
# ein leeres "Types[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Types[]
Types[]=video/x-flv
*/ ?>
```

## Siehe auch

- [Alle Blöcke von mime.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/mime.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
