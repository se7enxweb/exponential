---
title: audit.ini [AuditReadSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AuditReadSettings] in audit.ini: Reads, SampleRate, Sections, Classes, AlwaysModules"
keywords: Einstellung, audit.ini, AuditReadSettings, INI, setting, Reads, SampleRate, Sections, Classes, AlwaysModules
type: generated
generated: exp:help:examples
---
# audit.ini [AuditReadSettings]

Der Block `[AuditReadSettings]` von `settings/audit.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/audit.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/audit.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/audit.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/audit.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Reads

> Z6: Lesezugriffe sind optional und werden stichprobenartig erfasst.  enabled | disabled  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditReadSettings]
Reads=disabled
*/ ?>
```

## SampleRate

> Anteil der festgehaltenen Lesezugriffe, 0.0 bis 1.0 (0.01 = einer von hundert).  

Standard: `0.01`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditReadSettings]
SampleRate=0.01
*/ ?>
```

## Sections

> Nur Lesezugriffe in diesen Sektionen oder auf diese Klassen (Bezeichner); leer = alle.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditReadSettings]
# ein leeres "Sections[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Sections[]
*/ ?>
```

## Classes

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditReadSettings]
# ein leeres "Classes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Classes[]
*/ ?>
```

## AlwaysModules

> Ansichten dieser Module werden immer festgehalten, ob Stichprobe oder nicht, ob Reads an oder aus  
> (access.view.sensitive).  
> Vorschlag: Schreibzugriffe auf Einstellungen laufen über setup und settings; die Ansichten des Moduls settings  
> sind ebenfalls sensibel.  

Standard: eine Liste mit 5: `setup`, `role`, `user`, `audit`, `settings`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditReadSettings]
# ein leeres "AlwaysModules[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AlwaysModules[]
AlwaysModules[]=setup
AlwaysModules[]=role
AlwaysModules[]=user
AlwaysModules[]=audit
AlwaysModules[]=settings
*/ ?>
```

## Siehe auch

- [Alle Blöcke von audit.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/audit.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
