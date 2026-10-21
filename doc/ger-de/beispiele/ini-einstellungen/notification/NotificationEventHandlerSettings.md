---
title: notification.ini [NotificationEventHandlerSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [NotificationEventHandlerSettings] in notification.ini: RepositoryDirectories, ExtensionDirectories, AvailableNotificationEventTypes"
keywords: Einstellung, notification.ini, NotificationEventHandlerSettings, INI, setting, RepositoryDirectories, ExtensionDirectories, AvailableNotificationEventTypes
type: generated
generated: exp:help:examples
---
# notification.ini [NotificationEventHandlerSettings]

Der Block `[NotificationEventHandlerSettings]` von `settings/notification.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/notification.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/notification.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/notification.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/notification.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## RepositoryDirectories

Standard: eine Liste mit 1: `kernel/classes/notification/handler/`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/notification.ini.append.php
[NotificationEventHandlerSettings]
# ein leeres "RepositoryDirectories[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RepositoryDirectories[]
RepositoryDirectories[]=kernel/classes/notification/handler/
*/ ?>
```

## ExtensionDirectories

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/notification.ini.append.php
[NotificationEventHandlerSettings]
# ein leeres "ExtensionDirectories[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExtensionDirectories[]
*/ ?>
```

## AvailableNotificationEventTypes

Standard: eine Liste mit 3: `ezgeneraldigest`, `ezcollaborationnotification`, `ezsubtree`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/notification.ini.append.php
[NotificationEventHandlerSettings]
# ein leeres "AvailableNotificationEventTypes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AvailableNotificationEventTypes[]
AvailableNotificationEventTypes[]=ezgeneraldigest
AvailableNotificationEventTypes[]=ezcollaborationnotification
AvailableNotificationEventTypes[]=ezsubtree
*/ ?>
```

## Siehe auch

- [Alle Blöcke von notification.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/notification.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
