---
title: audit.ini [AuditSinkSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AuditSinkSettings] in audit.ini: SinkClasses, SpoolDir"
keywords: Einstellung, audit.ini, AuditSinkSettings, INI, setting, SinkClasses, SpoolDir
type: generated
generated: exp:help:examples
---
# audit.ini [AuditSinkSettings]

Der Block `[AuditSinkSettings]` von `settings/audit.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/audit.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/audit.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/audit.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/audit.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## SinkClasses

> Sinks besides the file (which is always written). name => class implementing  
> expAuditSink. Extensions add their own (RAD survey registry "auditsinks").  

Standard: ein Hash mit 3: `syslog=expAuditSyslogSink`, `webhook=expAuditWebhookSink`, `mail=expAuditMailSink`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditSinkSettings]
# ein leeres "SinkClasses[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SinkClasses[]
SinkClasses[syslog]=expAuditSyslogSink
SinkClasses[webhook]=expAuditWebhookSink
SinkClasses[mail]=expAuditMailSink
*/ ?>
```

## SpoolDir

> Undeliverable batches wait here and are retried by the cronjob part.  

Standard: `log/audit/spool`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditSinkSettings]
SpoolDir=log/audit/spool
*/ ?>
```

## Siehe auch

- [Alle Blöcke von audit.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/audit.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
