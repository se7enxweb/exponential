---
title: dbschema.ini [postgresql]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [postgresql] in dbschema.ini: ColumnTypeTranslation"
keywords: Einstellung, dbschema.ini, postgresql, INI, setting, ColumnTypeTranslation
type: generated
generated: exp:help:examples
---
# dbschema.ini [postgresql]

Der Block `[postgresql]` von `settings/dbschema.ini`, mit 1 Einstellung(en).

> Make sure this table has delayed key write set when dumping SQL  

Bearbeiten Sie `settings/dbschema.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/dbschema.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/dbschema.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/dbschema.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ColumnTypeTranslation

Standard: ein Hash mit 3: `ezprest_clients.version=int(1);int`, `ezpublishingqueueprocesses.pid=int(8);int`, `ezpublishingqueueprocesses.status=int(2);int`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dbschema.ini.append.php
[postgresql]
# ein leeres "ColumnTypeTranslation[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ColumnTypeTranslation[]
ColumnTypeTranslation[ezprest_clients.version]=int(1);int
ColumnTypeTranslation[ezpublishingqueueprocesses.pid]=int(8);int
ColumnTypeTranslation[ezpublishingqueueprocesses.status]=int(2);int
*/ ?>
```

## Siehe auch

- [Alle Blöcke von dbschema.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/dbschema.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
