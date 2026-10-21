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

> RFC 5424 mit strukturierten Daten, eine Nachricht pro Eintrag.  
> local     das Systemlog: der native Socket von journald, wo das System journald verwendet  
>           (journalctl -t <AppName> findet die Einträge; ein rsyslog, das das Journal liest,  
>           erhält sie auch), sonst /dev/log; beim Flush geschrieben  
> journald  nur der native Socket von journald  
> devlog    /dev/log mit der RFC-5424-Zeile unverändert (journald 252 parst ihren Header nicht)  
> udp       Host:Port, nur Header und strukturierte Daten (RFC 5426)  
> tcp|tls   Host:Port, der ganze Eintrag, Octet Counting (RFC 6587, RFC 5425)  
> udp, tcp und tls laufen über den Spool und werden vom Cronjob-Teil gesendet.  

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

> Der APP-NAME und der Bezeichner in journald (journalctl -t exponential).  

Standard: `exponential`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditSink_syslog]
AppName=exponential
*/ ?>
```

## Events

> Welche Ereignisse: Muster; leer = alles, was diesem Ziel zugeordnet ist.  

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
