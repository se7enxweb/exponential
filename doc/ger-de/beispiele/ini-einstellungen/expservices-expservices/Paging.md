---
title: expservices.ini [Paging]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Paging] in expservices.ini (expservices): DefaultLimit, MaxLimit"
keywords: Einstellung, expservices.ini, Paging, INI, setting, DefaultLimit, MaxLimit
type: generated
generated: exp:help:examples
---
# expservices.ini [Paging]

Der Block `[Paging]` von `extension/expservices/settings/expservices.ini` (Erweiterung expservices), mit 2 Einstellung(en).

> Domänen sind die Blöcke \[ezjscServer\_exp<domain>\] von ezjscore.ini, deren Class von expServiceBase erbt.  

Bearbeiten Sie `extension/expservices/settings/expservices.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/expservices.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/expservices.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/expservices.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## DefaultLimit

Standard: `25`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/expservices.ini.append.php
[Paging]
DefaultLimit=25
*/ ?>
```

## MaxLimit

Standard: `200`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/expservices.ini.append.php
[Paging]
MaxLimit=200
*/ ?>
```

## Siehe auch

- [Alle Blöcke von expservices.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/expservices/settings/expservices.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
