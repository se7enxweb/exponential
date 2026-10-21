---
title: site.ini [LinkCheck]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [LinkCheck] in site.ini: ConnectTimeout"
keywords: Einstellung, site.ini, LinkCheck, INI, setting, ConnectTimeout
type: generated
generated: exp:help:examples
---
# site.ini [LinkCheck]

Der Block `[LinkCheck]` von `settings/site.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ConnectTimeout

> Vom Cronjob linkcheck verwendet; ConnectTimeout gibt die Grenze (in Sekunden) an, die auf eine gültige Verbindung gewartet wird.  

Standard: `3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[LinkCheck]
ConnectTimeout=3
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
