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

> ezpEvent-Brücke (Z3): ein vorhandenes Kernel-Ereignis als Audit-Ereignis festhalten.  
> Bridge\[<ezpEvent name>\]=<audit name>  
> Der Listener hält after.args fest, die Argumente des Ereignisses als Skalare (Zahlen und kurze Zeichenketten unverändert,  
> Objekte als class#id); die Argumente der Ereignisse session/\* sind Sitzungs-IDs und werden nur gehasht festgehalten. Der  
> Wert eines Filter-Ereignisses wird unverändert durchgereicht. Erweiterungen fügen ihre eigenen Ereignisse auf dieselbe Weise hinzu, z. B.  
>   Bridge\[myext/vote\]=content.myext\_poll.vote   (with the branch registered in \[AuditEventSettings\] Branches\[\])  
> Kernel-Ereignisse des Katalogs werden direkt dort ausgelöst, wo sie geschehen; die Brücke ist für die Ereignisse, die es  
> nur als ezpEvents gibt. Die beiden Sitzungsereignisse unten sind standardmäßig aus (Disabled\[\]); um sie einzuschalten, genügt es,  
> sie in \[AuditEventSettings\] Enabled\[\] aufzunehmen.  

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
