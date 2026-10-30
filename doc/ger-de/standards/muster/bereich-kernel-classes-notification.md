---
title: Muster in kernel/classes/notification
audience: developer
category: standards
order: 505
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/classes/notification mit den Entwurfsmustern.
keywords: kernel/classes/notification, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/classes/notification

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/classes/notification` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (19)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expNotificationMailCategoryHandler` | class implements expMailCategoryHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `kernel/classes/notification/expnotificationmailcategoryhandler.php:25` | Die Kategorien „content“ und „collaboration“ der E-Mail-Präferenzen lesen die eigenen Daten des Benachrichtigungssystems, sodass nichts migriert werden muss und die… |
| `eZCollaborationEventType` | class extends eZNotificationEventType | – | `kernel/classes/notification/event/ezcollaboration/ezcollaborationtype.php:18` | – |
| `eZCollaborationNotificationHandler` | class extends eZNotificationEventHandler | [Strategie / Handler](strategie-handler.md) | `kernel/classes/notification/handler/ezcollaborationnotification/ezcollaborationnotificationhandler.php:18` | – |
| `eZCollaborationNotificationRule` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/notification/handler/ezcollaborationnotification/ezcollaborationnotificationrule.php:17` | – |
| `eZCurrentTimeType` | class extends eZNotificationEventType | – | `kernel/classes/notification/event/ezcurrenttime/ezcurrenttimetype.php:17` | – |
| `eZGeneralDigestHandler` | class extends eZNotificationEventHandler | [Strategie / Handler](strategie-handler.md) | `kernel/classes/notification/handler/ezgeneraldigest/ezgeneraldigesthandler.php:17` | – |
| `eZGeneralDigestUserSettings` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/notification/handler/ezgeneraldigest/ezgeneraldigestusersettings.php:18` | – |
| `eZMailNotificationTransport` | class extends eZNotificationTransport | [Strategie / Handler](strategie-handler.md) | `kernel/classes/notification/ezmailnotificationtransport.php:18` | – |
| `eZNotificationCollection` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/notification/eznotificationcollection.php:17` | – |
| `eZNotificationCollectionItem` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/notification/eznotificationcollectionitem.php:18` | – |
| `eZNotificationEvent` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/notification/eznotificationevent.php:17` | – |
| `eZNotificationEventFilter` | class | [Dekorierer / Filter](dekorierer.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/notification/eznotificationeventfilter.php:17` | – |
| `eZNotificationEventHandler` | class | [Strategie / Handler](strategie-handler.md) | `kernel/classes/notification/eznotificationeventhandler.php:18` | – |
| `eZNotificationEventType` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Registry](registry.md) | `kernel/classes/notification/eznotificationeventtype.php:18` | – |
| `eZNotificationSchedule` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/notification/eznotificationschedule.php:18` | – |
| `eZNotificationTransport` | class | [Singleton / instance()](singleton.md), [Strategie / Handler](strategie-handler.md) | `kernel/classes/notification/eznotificationtransport.php:17` | – |
| `eZPublishType` | class extends eZNotificationEventType | – | `kernel/classes/notification/event/ezpublish/ezpublishtype.php:18` | – |
| `eZSubTreeHandler` | class extends eZNotificationEventHandler | [Strategie / Handler](strategie-handler.md) | `kernel/classes/notification/handler/ezsubtree/ezsubtreehandler.php:18` | – |
| `eZSubtreeNotificationRule` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/notification/handler/ezsubtree/ezsubtreenotificationrule.php:17` | – |
