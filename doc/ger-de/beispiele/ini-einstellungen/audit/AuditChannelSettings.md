---
title: audit.ini [AuditChannelSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AuditChannelSettings] in audit.ini: Channels, Route, DefaultChannel"
keywords: Einstellung, audit.ini, AuditChannelSettings, INI, setting, Channels, Route, DefaultChannel
type: generated
generated: exp:help:examples
---
# audit.ini [AuditChannelSettings]

Der Block `[AuditChannelSettings]` von `settings/audit.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/audit.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/audit.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/audit.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/audit.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Channels

> The channels (Q6): each is one chain, one file per day (and per MaxFileSize part).  
> Proposed: a fifth channel for sampled reads, used only when reads are switched on.  

Standard: eine Liste mit 5: `content`, `access`, `system`, `commerce`, `read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditChannelSettings]
# ein leeres "Channels[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Channels[]
Channels[]=content
Channels[]=access
Channels[]=system
Channels[]=commerce
Channels[]=read
*/ ?>
```

## Route

> Routing by taxonomy pattern, most specific wins. An event no pattern matches goes to  
> DefaultChannel.  

Standard: ein Hash mit 8: `content.*=content`, `content.node.view=read`, `content.search.*=read`, `content.object.download=read`, `access.*=access`, `system.*=system`, `commerce.*=commerce`, `data.*=commerce`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditChannelSettings]
# ein leeres "Route[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Route[]
Route[content.*]=content
Route[content.node.view]=read
Route[content.search.*]=read
Route[content.object.download]=read
Route[access.*]=access
Route[system.*]=system
Route[commerce.*]=commerce
Route[data.*]=commerce
*/ ?>
```

## DefaultChannel

Standard: `system`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditChannelSettings]
DefaultChannel=system
*/ ?>
```

## Siehe auch

- [Alle Blöcke von audit.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/audit.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
