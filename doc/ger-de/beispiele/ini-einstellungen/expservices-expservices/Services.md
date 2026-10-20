---
title: expservices.ini [Services]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Services] in expservices.ini (expservices): Enabled"
keywords: Einstellung, expservices.ini, Services, INI, setting, Enabled
type: generated
generated: exp:help:examples
---
# expservices.ini [Services]

Der Block `[Services]` von `extension/expservices/settings/expservices.ini` (Erweiterung expservices), mit 1 Einstellung(en).

Bearbeiten Sie `extension/expservices/settings/expservices.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/expservices.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/expservices.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/expservices.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> Hauptschalter: wenn deaktiviert, antwortet jeder Dienst mit 403  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/expservices.ini.append.php
[Services]
Enabled=enabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von expservices.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/expservices/settings/expservices.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
