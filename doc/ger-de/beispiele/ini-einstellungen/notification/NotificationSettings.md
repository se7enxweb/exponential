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

> enabled zeigt „Ohne Benachrichtigung veröffentlichen“ neben der Schaltfläche Veröffentlichen von  
> content/edit und der Versionsvorschau: Die Version wird veröffentlicht, aber kein  
> Benachrichtigungsereignis erzeugt, die Abonnenten ihrer Platzierung erhalten also keine Mail  
> (der Filter content/notification/create erhält false). disabled blendet die Schaltfläche aus  
> und veröffentlicht jede Version mit Benachrichtigung.  
> Die Schaltfläche wird Benutzern angezeigt, deren Rolle die Richtlinienfunktion  
> content/publish\_without\_notification hat (eine Richtlinie content/\* schließt  
> sie ein), und gilt nur für sie. Für alle anderen und mit deaktivierter Einstellung veröffentlicht eine abgeschickte Schaltfläche  
> mit Benachrichtigung. Nur das Ereignis ezpublish der Version entfällt:  
> Benachrichtigungen zu Freigaben (Zusammenarbeit) werden wie bisher gesendet. Setzen Sie es pro  
> Siteaccess (zum Beispiel nur im Administrations-Siteaccess), um es nur dort anzubieten.  
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

> enabled erzeugt Benachrichtigungsereignisse (die Mails und Einträge der Zusammenfassungen der  
> Abonnenten einer Platzierung) nur für Veröffentlichungen der Klassen in  
> IncludeClasses\[\] (Klassenbezeichner; eine Klassen-ID nennt ihre Klasse ebenfalls); eine  
> Veröffentlichung jeder anderen Klasse gilt als ohne Benachrichtigung veröffentlicht  
> (der Filter content/notification/create erhält false).  
> Bei einer leeren Liste erzeugt nichts ein Ereignis. disabled (Standard): jede Klasse.  
> Benachrichtigungen zu Freigaben (Zusammenarbeit) sind nicht betroffen.  
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
