---
title: notification.ini [NotificationEventTypeSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [NotificationEventTypeSettings] in notification.ini: RepositoryDirectories, ExtensionDirectories, AvailableNotificationEventTypes"
keywords: Einstellung, notification.ini, NotificationEventTypeSettings, INI, setting, RepositoryDirectories, ExtensionDirectories, AvailableNotificationEventTypes
type: generated
generated: exp:help:examples
---
# notification.ini [NotificationEventTypeSettings]

Der Block `[NotificationEventTypeSettings]` von `settings/notification.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/notification.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/notification.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/notification.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/notification.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## RepositoryDirectories

Standard: eine Liste mit 1: `kernel/classes/notification/event/`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/notification.ini.append.php
[NotificationEventTypeSettings]
# ein leeres "RepositoryDirectories[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RepositoryDirectories[]
RepositoryDirectories[]=kernel/classes/notification/event/
*/ ?>
```

## ExtensionDirectories

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/notification.ini.append.php
[NotificationEventTypeSettings]
# ein leeres "ExtensionDirectories[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExtensionDirectories[]
*/ ?>
```

## AvailableNotificationEventTypes

Standard: eine Liste mit 4: `ezpublish`, `eznewcontent`, `ezcurrenttime`, `ezcollaboration`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/notification.ini.append.php
[NotificationEventTypeSettings]
# ein leeres "AvailableNotificationEventTypes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AvailableNotificationEventTypes[]
AvailableNotificationEventTypes[]=ezpublish
AvailableNotificationEventTypes[]=eznewcontent
AvailableNotificationEventTypes[]=ezcurrenttime
AvailableNotificationEventTypes[]=ezcollaboration
*/ ?>
```

## Siehe auch

- [Alle Blöcke von notification.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/notification.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
