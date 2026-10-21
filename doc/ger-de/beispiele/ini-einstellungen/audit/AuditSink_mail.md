---
title: audit.ini [AuditSink_mail]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AuditSink_mail] in audit.ini: Receivers, MinSeverity, Events, Throttle, Transport"
keywords: Einstellung, audit.ini, AuditSink_mail, INI, setting, Receivers, MinSeverity, Events, Throttle, Transport
type: generated
generated: exp:help:examples
---
# audit.ini [AuditSink_mail]

Der Block `[AuditSink_mail]` von `settings/audit.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/audit.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/audit.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/audit.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/audit.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Receivers

> E-Mail bei kritischen Ereignissen (Q7). Wer sie erhält: siehe \[AuditAlertSettings\] Recipients\[\].  
> Receivers\[\] ist der ältere Name dieser Standardliste (gleiche Syntax); er wird nur verwendet,  
> wenn \[AuditAlertSettings\] Recipients\[\] leer ist.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditSink_mail]
# ein leeres "Receivers[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Receivers[]
*/ ?>
```

## MinSeverity

Standard: `critical`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditSink_mail]
MinSeverity=critical
*/ ?>
```

## Events

Standard: eine Liste mit 2: `system.audit.*`, `access.role.assign`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditSink_mail]
# ein leeres "Events[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Events[]
Events[]=system.audit.*
Events[]=access.role.assign
*/ ?>
```

## Throttle

> Höchstens eine Mail pro Regel und Gruppe innerhalb so vieler Sekunden.  

Standard: `900`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditSink_mail]
Throttle=900
*/ ?>
```

## Transport

> Der Mail-Transport: leer = der des Kernels (site.ini \[MailSettings\] Transport, der  
> DebugSending beachtet) oder eine Klasse, die von eZMailTransport erbt. Mails werden nur vom  
> Cronjob-Teil gesendet.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditSink_mail]
Transport=
*/ ?>
```

## Siehe auch

- [Alle Blöcke von audit.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/audit.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
