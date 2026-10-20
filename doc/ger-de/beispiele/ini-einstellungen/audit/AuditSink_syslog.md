---
title: audit.ini [AuditSink_syslog]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AuditSink_syslog] in audit.ini: Transport, Host, Port, Facility, AppName, Events, MinSeverity"
keywords: Einstellung, audit.ini, AuditSink_syslog, INI, setting, Transport, Host, Port, Facility, AppName, Events, MinSeverity
type: generated
generated: exp:help:examples
---
# audit.ini [AuditSink_syslog]

Der Block `[AuditSink_syslog]` von `settings/audit.ini`, mit 7 Einstellung(en).

Bearbeiten Sie `settings/audit.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/audit.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/audit.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/audit.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Transport

> RFC 5424 with structured data, one message per record.  
> local     the system log: journald's native socket where the system runs journald  
>           (journalctl -t <AppName> finds the records; rsyslog reading the journal  
>           gets them too), else /dev/log; written at flush time  
> journald  journald's native socket only  
> devlog    /dev/log with the RFC 5424 line as is (journald 252 does not parse its header)  
> udp       Host:Port, header and structured data only (RFC 5426)  
> tcp|tls   Host:Port, the whole record, octet counting (RFC 6587, RFC 5425)  
> udp, tcp and tls go through the spool and are sent by the cronjob part.  

Standard: `local`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditSink_syslog]
Transport=local
*/ ?>
```

## Host

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditSink_syslog]
Host=
*/ ?>
```

## Port

Standard: `514`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditSink_syslog]
Port=514
*/ ?>
```

## Facility

> Facility: auth | authpriv | daemon | local0 … local7  

Standard: `authpriv`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditSink_syslog]
Facility=authpriv
*/ ?>
```

## AppName

> The APP-NAME, and the identifier in journald (journalctl -t exponential).  

Standard: `exponential`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditSink_syslog]
AppName=exponential
*/ ?>
```

## Events

> Which events: patterns; empty = everything routed to this sink.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditSink_syslog]
# ein leeres "Events[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Events[]
*/ ?>
```

## MinSeverity

Standard: `info`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditSink_syslog]
MinSeverity=info
*/ ?>
```

## Siehe auch

- [Alle Blöcke von audit.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/audit.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
