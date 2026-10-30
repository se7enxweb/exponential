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

> Eine Liste der Verzeichnisse, in denen nach Workflow-Ereignistypen gesucht wird  

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

> Eine Liste der Erweiterungen, die Workflow-Ereignisse haben  
> Üblicherweise legt man in der Erweiterung eine Datei settings/workflow.ini.append  
> an und trägt den Namen der Erweiterung ein, um automatisch  
> die Workflow-Ereignisse der Erweiterung zu erhalten, wenn sie eingeschaltet ist.  

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

> Eine Liste von Ereignissen, jeder Eintrag besteht aus der Gruppe  
> und dem Namen mit einem \_ als Trennzeichen  
> Schreibt das veröffentlichte Objekt in die Tabellen der Synchronisationen der Datentabellen (doc/eng-us/features/6.0/data-tables.md)  
> Approve user accounts: ein neues Konto in einer gewählten Benutzergruppe wartet auf einen Freigebenden (doc/eng-us/bc/6.0/user-account-approval.md)  

Standard: eine Liste mit 8: `event_ezapprove`, `event_ezwaituntildate`, `event_ezsimpleshipping`, `event_ezmultiplexer`, `event_ezpaymentgateway`, `event_ezfinishuserregister`, `event_expdatatablemirror`, `event_expuserapprove`.

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
AvailableEventTypes[]=event_expdatatablemirror
AvailableEventTypes[]=event_expuserapprove
*/ ?>
```

## Siehe auch

- [Alle Blöcke von workflow.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/workflow.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
