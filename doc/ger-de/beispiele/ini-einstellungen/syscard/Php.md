---
title: syscard.ini [Php]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Php] in syscard.ini: SecuritySupportEnds, WarnDays"
keywords: Einstellung, syscard.ini, Php, INI, setting, SecuritySupportEnds, WarnDays
type: generated
generated: exp:help:examples
---
# syscard.ini [Php]

Der Block `[Php]` von `settings/syscard.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/syscard.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/syscard.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/syscard.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/syscard.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## SecuritySupportEnds

> The end of security support of each PHP branch (https://www.php.net/supported-versions.php). A branch past its  
> date is an error on the card, one within WarnDays a warning.  

Standard: ein Hash mit 7: `7.4=2022-11-28`, `8.0=2023-11-26`, `8.1=2025-12-31`, `8.2=2026-12-31`, `8.3=2027-12-31`, `8.4=2028-12-31`, `8.5=2029-12-31`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Php]
# ein leeres "SecuritySupportEnds[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SecuritySupportEnds[]
SecuritySupportEnds[7.4]=2022-11-28
SecuritySupportEnds[8.0]=2023-11-26
SecuritySupportEnds[8.1]=2025-12-31
SecuritySupportEnds[8.2]=2026-12-31
SecuritySupportEnds[8.3]=2027-12-31
SecuritySupportEnds[8.4]=2028-12-31
SecuritySupportEnds[8.5]=2029-12-31
*/ ?>
```

## WarnDays

Standard: `180`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Php]
WarnDays=180
*/ ?>
```

## Siehe auch

- [Alle Blöcke von syscard.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/syscard.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
