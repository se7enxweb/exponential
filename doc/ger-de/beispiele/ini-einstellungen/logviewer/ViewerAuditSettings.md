---
title: logviewer.ini [ViewerAuditSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ViewerAuditSettings] in logviewer.ini: ReadInterval"
keywords: Einstellung, logviewer.ini, ViewerAuditSettings, INI, setting, ReadInterval
type: generated
generated: exp:help:examples
---
# logviewer.ini [ViewerAuditSettings]

Der Block `[ViewerAuditSettings]` von `settings/logviewer.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/logviewer.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/logviewer.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/logviewer.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/logviewer.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ReadInterval

> Record system.log.read when a file is opened in the viewer (once per file and session within this many  
> seconds), so following a file does not write one record per poll.  

Standard: `600`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[ViewerAuditSettings]
ReadInterval=600
*/ ?>
```

## Siehe auch

- [Alle Blöcke von logviewer.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/logviewer.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
