---
title: workflow.ini [EventSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [EventSettings] in workflow.ini: RepositoryDirectories, ExtensionDirectories, AvailableEventTypes"
keywords: Einstellung, workflow.ini, EventSettings, INI, setting, RepositoryDirectories, ExtensionDirectories, AvailableEventTypes
type: generated
generated: exp:help:examples
---
# workflow.ini [EventSettings]

Der Block `[EventSettings]` von `settings/workflow.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/workflow.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/workflow.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/workflow.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/workflow.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## RepositoryDirectories

> A list of directories to check for workflow eventtypes  

Standard: eine Liste mit 1: `kernel/classes/workflowtypes`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/workflow.ini.append.php
[EventSettings]
# ein leeres "RepositoryDirectories[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RepositoryDirectories[]
RepositoryDirectories[]=kernel/classes/workflowtypes
*/ ?>
```

## ExtensionDirectories

> A list of extensions which have workflow events  
> It's common to create a settings/workflow.ini.append file  
> in your extension and add the extension name to automatically  
> get workflow events from the extension when it's turned on.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/workflow.ini.append.php
[EventSettings]
# ein leeres "ExtensionDirectories[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExtensionDirectories[]
*/ ?>
```

## AvailableEventTypes

> A list of events, each entry consists of the group  
> and the name with a \_ as separator  

Standard: eine Liste mit 6: `event_ezapprove`, `event_ezwaituntildate`, `event_ezsimpleshipping`, `event_ezmultiplexer`, `event_ezpaymentgateway`, `event_ezfinishuserregister`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/workflow.ini.append.php
[EventSettings]
# ein leeres "AvailableEventTypes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AvailableEventTypes[]
AvailableEventTypes[]=event_ezapprove
AvailableEventTypes[]=event_ezwaituntildate
AvailableEventTypes[]=event_ezsimpleshipping
AvailableEventTypes[]=event_ezmultiplexer
AvailableEventTypes[]=event_ezpaymentgateway
AvailableEventTypes[]=event_ezfinishuserregister
*/ ?>
```

## Siehe auch

- [Alle Blöcke von workflow.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/workflow.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
