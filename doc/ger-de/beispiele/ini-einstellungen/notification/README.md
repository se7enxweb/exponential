---
title: notification.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von notification.ini.
keywords: Beispiele, Referenz, notification.ini, examples, reference
type: generated
generated: exp:help:examples
---
# notification.ini

Die ausgelieferten Einstellungen von `settings/notification.ini`: 6 Blöcke, 17 Einstellungen.

> Exponential configuration file for notification.  
>
>
> NOTE: It is not recommended to edit this files directly, instead  
>       a file in override should be created for setting the  
>       values that is required for your site. Either create  
>       a file called settings/override/notification.ini.append or  
>       settings/override/notification.ini.append.php for more security  
>       in non-virtualhost modes (the .php file may already be present  
>       and can be used for this purpose).  

| Block | Einstellungen |
|---|---|
| [NotificationSettings](NotificationSettings.md) | 3 |
| [RuleSettings](RuleSettings.md) | 5 |
| [NotificationEventTypeSettings](NotificationEventTypeSettings.md) | 3 |
| [NotificationEventHandlerSettings](NotificationEventHandlerSettings.md) | 3 |
| [TransportSettings](TransportSettings.md) | 2 |
| [MailSettings](MailSettings.md) | 1 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/notification.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
