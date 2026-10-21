---
title: logviewer.ini [HealthSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [HealthSettings] in logviewer.ini: DiskBudget"
keywords: Einstellung, logviewer.ini, HealthSettings, INI, setting, DiskBudget
type: generated
generated: exp:help:examples
---
# logviewer.ini [HealthSettings]

Der Block `[HealthSettings]` von `settings/logviewer.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/logviewer.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/logviewer.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/logviewer.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/logviewer.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## DiskBudget

> Setup > Logdateien > Zustand (setup/logshealth): der Speicherplatz, den alle Logdateien und ihre Archive zusammen belegen dürfen, in Bytes.  
> Die Seite warnt ab 80 % davon und markiert die Logs darüber; 0 setzt kein Budget. Standard 268435456 (256 MB).  

Standard: `268435456`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[HealthSettings]
DiskBudget=268435456
*/ ?>
```

## Siehe auch

- [Alle Blöcke von logviewer.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/logviewer.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
