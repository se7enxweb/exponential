---
title: expservices.ini [Writes]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Writes] in expservices.ini (expservices): RequireToken, Audit"
keywords: Einstellung, expservices.ini, Writes, INI, setting, RequireToken, Audit
type: generated
generated: exp:help:examples
---
# expservices.ini [Writes]

Der Block `[Writes]` von `extension/expservices/settings/expservices.ini` (Erweiterung expservices), mit 2 Einstellung(en).

Bearbeiten Sie `extension/expservices/settings/expservices.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/expservices.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/expservices.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/expservices.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## RequireToken

> Schreibzugriffe brauchen POST und das Formular-Token; nur für Tests auf disabled setzen  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/expservices.ini.append.php
[Writes]
RequireToken=enabled
*/ ?>
```

## Audit

> Audit-Ereignis, das für jeden erfolgreichen Schreibzugriff geschrieben wird (expAudit): service.<domain>.<method>  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/expservices.ini.append.php
[Writes]
Audit=enabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von expservices.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/expservices/settings/expservices.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
