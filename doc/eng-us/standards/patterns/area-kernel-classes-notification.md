---
title: Patterns in kernel/classes/notification
audience: developer
category: standards
order: 504
summary: Every class, view, fetch function, operator and plugin point of kernel/classes/notification with its design patterns.
keywords: kernel/classes/notification, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/classes/notification

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/classes/notification` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (19)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expNotificationMailCategoryHandler` | class implements expMailCategoryHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `kernel/classes/notification/expnotificationmailcategoryhandler.php:25` | The categories "content" and "collaboration" of the e-mail preferences read the notification system's own data, so nothing has to be migrated and the… |
| `eZCollaborationEventType` | class extends eZNotificationEventType | – | `kernel/classes/notification/event/ezcollaboration/ezcollaborationtype.php:18` | – |
| `eZCollaborationNotificationHandler` | class extends eZNotificationEventHandler | [Strategy / handler](strategy-handler.md) | `kernel/classes/notification/handler/ezcollaborationnotification/ezcollaborationnotificationhandler.php:18` | – |
| `eZCollaborationNotificationRule` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/notification/handler/ezcollaborationnotification/ezcollaborationnotificationrule.php:17` | – |
| `eZCurrentTimeType` | class extends eZNotificationEventType | – | `kernel/classes/notification/event/ezcurrenttime/ezcurrenttimetype.php:17` | – |
| `eZGeneralDigestHandler` | class extends eZNotificationEventHandler | [Strategy / handler](strategy-handler.md) | `kernel/classes/notification/handler/ezgeneraldigest/ezgeneraldigesthandler.php:17` | – |
| `eZGeneralDigestUserSettings` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/notification/handler/ezgeneraldigest/ezgeneraldigestusersettings.php:18` | – |
| `eZMailNotificationTransport` | class extends eZNotificationTransport | [Strategy / handler](strategy-handler.md) | `kernel/classes/notification/ezmailnotificationtransport.php:18` | – |
| `eZNotificationCollection` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/notification/eznotificationcollection.php:17` | – |
| `eZNotificationCollectionItem` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `kernel/classes/notification/eznotificationcollectionitem.php:18` | – |
| `eZNotificationEvent` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/notification/eznotificationevent.php:17` | – |
| `eZNotificationEventFilter` | class | [Decorator / filter](decorator.md), [Utility (static helper)](utility.md) | `kernel/classes/notification/eznotificationeventfilter.php:17` | – |
| `eZNotificationEventHandler` | class | [Strategy / handler](strategy-handler.md) | `kernel/classes/notification/eznotificationeventhandler.php:18` | – |
| `eZNotificationEventType` | class | [Factory and static factory methods](factory.md), [Registry](registry.md) | `kernel/classes/notification/eznotificationeventtype.php:18` | – |
| `eZNotificationSchedule` | class | [Utility (static helper)](utility.md) | `kernel/classes/notification/eznotificationschedule.php:18` | – |
| `eZNotificationTransport` | class | [Singleton / instance()](singleton.md), [Strategy / handler](strategy-handler.md) | `kernel/classes/notification/eznotificationtransport.php:17` | – |
| `eZPublishType` | class extends eZNotificationEventType | – | `kernel/classes/notification/event/ezpublish/ezpublishtype.php:18` | – |
| `eZSubTreeHandler` | class extends eZNotificationEventHandler | [Strategy / handler](strategy-handler.md) | `kernel/classes/notification/handler/ezsubtree/ezsubtreehandler.php:18` | – |
| `eZSubtreeNotificationRule` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `kernel/classes/notification/handler/ezsubtree/ezsubtreenotificationrule.php:17` | – |
