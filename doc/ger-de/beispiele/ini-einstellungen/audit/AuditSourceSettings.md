---
title: audit.ini [AuditSourceSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AuditSourceSettings] in audit.ini: Sources"
keywords: Einstellung, audit.ini, AuditSourceSettings, INI, setting, Sources
type: generated
generated: exp:help:examples
---
# audit.ini [AuditSourceSettings]

Der Block `[AuditSourceSettings]` von `settings/audit.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/audit.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/audit.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/audit.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/audit.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Sources

> Optional sources of the audit console (doc/eng-us/bc/6.0/audit.md, "An extension's source in the  
> console"): an extension that records events of its own tells the console how to show them,  
> with a class that implements expAuditSource (best: extends expAuditSourceBase). The dashboard  
> shows its panel, the console, recent, charts, alerts and export views its links, the charts  
> view its series, audit/event the fields and links of its records, audit/settings its state.  
> Registered by the extension in its own settings/audit.ini.append.php, so an extension that is  
> not active changes nothing. Its channel must be in \[AuditChannelSettings\] Channels\[\].  
> Sources\[myext\]=myExtAuditSource  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditSourceSettings]
# ein leeres "Sources[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Sources[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von audit.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/audit.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
