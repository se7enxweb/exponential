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

> Exponential-Konfigurationsdatei für Benachrichtigungen.  
>
>
> HINWEIS: Es wird nicht empfohlen, diese Datei direkt zu bearbeiten; legen Sie stattdessen  
>       eine Datei in override an, um die Werte zu setzen,  
>       die Ihre Website braucht. Legen Sie entweder  
>       eine Datei settings/override/notification.ini.append oder  
>       settings/override/notification.ini.append.php an, für mehr Sicherheit  
>       im Modus ohne virtuellen Host (die .php-Datei ist eventuell schon vorhanden  
>       und kann dafür verwendet werden).  

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
