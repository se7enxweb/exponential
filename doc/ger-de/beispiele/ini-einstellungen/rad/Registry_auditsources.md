---
title: rad.ini [Registry_auditsources]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Registry_auditsources] in rad.ini: Title, Description, Doc, Ini, Section, Entries"
keywords: Einstellung, rad.ini, Registry_auditsources, INI, setting, Title, Description, Doc, Ini, Section, Entries
type: generated
generated: exp:help:examples
---
# rad.ini [Registry_auditsources]

Der Block `[Registry_auditsources]` von `settings/rad.ini`, mit 6 Einstellung(en).

Bearbeiten Sie `settings/rad.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/rad.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/rad.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/rad.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Title

Standard: `Audit console sources`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rad.ini.append.php
[Registry_auditsources]
Title=Audit console sources
*/ ?>
```

## Description

Standard: `What shows an extension's own audit records in the audit console (audit.ini [AuditSourceSettings] Sources[<name>]=, implementing expAuditSource).`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rad.ini.append.php
[Registry_auditsources]
Description=What shows an extension's own audit records in the audit console (audit.ini [AuditSourceSettings] Sources[<name>]=, implementing expAuditSource).
*/ ?>
```

## Doc

Standard: `doc/eng-us/bc/6.0/audit.md`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rad.ini.append.php
[Registry_auditsources]
Doc=doc/eng-us/bc/6.0/audit.md
*/ ?>
```

## Ini

Standard: `audit.ini`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rad.ini.append.php
[Registry_auditsources]
Ini=audit.ini
*/ ?>
```

## Section

Standard: `AuditSourceSettings`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rad.ini.append.php
[Registry_auditsources]
Section=AuditSourceSettings
*/ ?>
```

## Entries

Standard: ein Hash mit 1: `Sources=expAuditSource`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rad.ini.append.php
[Registry_auditsources]
# ein leeres "Entries[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Entries[]
Entries[Sources]=expAuditSource
*/ ?>
```

## Siehe auch

- [Alle Blöcke von rad.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/rad.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
