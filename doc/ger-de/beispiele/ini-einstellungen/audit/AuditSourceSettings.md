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

> Optionale Quellen der Audit-Konsole (doc/eng-us/bc/6.0/audit.md, "An extension's source in the  
> console"): Eine Erweiterung, die eigene Ereignisse festhält, sagt der Konsole, wie sie anzuzeigen sind,  
> mit einer Klasse, die expAuditSource implementiert (am besten: von expAuditSourceBase erbt). Das Dashboard  
> zeigt ihr Panel, die Ansichten console, recent, charts, alerts und export ihre Links, die Ansicht charts  
> ihre Reihen, audit/event die Felder und Links ihrer Einträge, audit/settings ihren Zustand.  
> Von der Erweiterung in ihrer eigenen settings/audit.ini.append.php registriert, sodass eine Erweiterung, die  
> nicht aktiv ist, nichts ändert. Ihr Kanal muss in \[AuditChannelSettings\] Channels\[\] stehen.  
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
