---
title: notification.ini [NotificationSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [NotificationSettings] in notification.ini: PublishWithoutNotification, NotificationFilterByClassIdentifier, IncludeClasses"
keywords: Einstellung, notification.ini, NotificationSettings, INI, setting, PublishWithoutNotification, NotificationFilterByClassIdentifier, IncludeClasses
type: generated
generated: exp:help:examples
---
# notification.ini [NotificationSettings]

Der Block `[NotificationSettings]` von `settings/notification.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/notification.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/notification.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/notification.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/notification.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## PublishWithoutNotification

> enabled shows "Publish without notification" next to the publish button of  
> content/edit and of the version preview: the version is published, but no  
> notification event is made, so the subscribers of its location get no mail  
> (the filter content/notification/create gets false). disabled hides the button  
> and publishes every version with notification.  
> The button is shown to, and counts for, users whose role has the policy  
> function content/publish\_without\_notification (a content/\* policy includes  
> it). For anybody else, and with the setting disabled, a posted button  
> publishes with notification. Only the ezpublish event of the version is left  
> out: approval (collaboration) notifications are sent as before. Set it per  
> siteaccess (the admin siteaccess only, for example) to offer it there only.  
> doc/eng-us/features/6.0/publish-without-notification.md  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/notification.ini.append.php
[NotificationSettings]
PublishWithoutNotification=disabled
*/ ?>
```

## NotificationFilterByClassIdentifier

> enabled makes notification events (the mails and digest items of the  
> subscribers of a location) only for publications of the classes in  
> IncludeClasses\[\] (class identifiers; a class ID names its class too); a  
> publication of any other class counts as published without notification  
> (the filter content/notification/create gets false).  
> With an empty list nothing makes an event. disabled (default): every class.  
> Approval (collaboration) notifications are not concerned.  
> doc/eng-us/features/6.0/publish-without-notification.md  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/notification.ini.append.php
[NotificationSettings]
NotificationFilterByClassIdentifier=disabled
*/ ?>
```

## IncludeClasses

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/notification.ini.append.php
[NotificationSettings]
# ein leeres "IncludeClasses[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
IncludeClasses[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von notification.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/notification.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
