---
title: audit.ini [AuditBridgeSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AuditBridgeSettings] in audit.ini: Bridge"
keywords: Einstellung, audit.ini, AuditBridgeSettings, INI, setting, Bridge
type: generated
generated: exp:help:examples
---
# audit.ini [AuditBridgeSettings]

Der Block `[AuditBridgeSettings]` von `settings/audit.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/audit.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/audit.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/audit.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/audit.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Bridge

> ezpEvent bridge (Z3): record an existing kernel event as an audit event.  
> Bridge\[<ezpEvent name>\]=<audit name>  
> The listener records after.args, the event's arguments made scalar (numbers and short strings as they are,  
> objects as class#id); the arguments of session/\* events are session ids and only ever recorded hashed. A  
> filter event's value is passed through unchanged. Extensions add their own events the same way, e.g.  
>   Bridge\[myext/vote\]=content.myext\_poll.vote   (with the branch registered in \[AuditEventSettings\] Branches\[\])  
> Kernel events of the catalogue are raised directly where they happen; the bridge is for the events that exist  
> only as ezpEvents. The two session events below are off by default (Disabled\[\]); switching them on in  
> \[AuditEventSettings\] Enabled\[\] is all it takes.  

Standard: ein Hash mit 3: `session/regenerate=access.session.regenerate`, `session/destroy=access.session.expire`, `session/gc=access.session.expire`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditBridgeSettings]
# ein leeres "Bridge[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Bridge[]
Bridge[session/regenerate]=access.session.regenerate
Bridge[session/destroy]=access.session.expire
Bridge[session/gc]=access.session.expire
*/ ?>
```

## Siehe auch

- [Alle Blöcke von audit.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/audit.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
