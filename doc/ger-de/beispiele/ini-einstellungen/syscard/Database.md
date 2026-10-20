---
title: syscard.ini [Database]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Database] in syscard.ini: AskVersion"
keywords: Einstellung, syscard.ini, Database, INI, setting, AskVersion
type: generated
generated: exp:help:examples
---
# syscard.ini [Database]

Der Block `[Database]` von `settings/syscard.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/syscard.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/syscard.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/syscard.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/syscard.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## AskVersion

> enabled: connect once and ask the server for its version; disabled: show the type and host from site.ini only  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Database]
AskVersion=enabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von syscard.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/syscard.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
