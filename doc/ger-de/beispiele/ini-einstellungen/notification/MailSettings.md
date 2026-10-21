---
title: notification.ini [MailSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [MailSettings] in notification.ini: EmailSender"
keywords: Einstellung, notification.ini, MailSettings, INI, setting, EmailSender
type: generated
generated: exp:help:examples
---
# notification.ini [MailSettings]

Der Block `[MailSettings]` von `settings/notification.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/notification.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/notification.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/notification.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/notification.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## EmailSender

> Welche E-Mail-Adresse beim Senden von Benachrichtigungsmails verwendet wird  
> Wenn leer, wird site.ini/MailSettings/AdminEmail verwendet.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/notification.ini.append.php
[MailSettings]
EmailSender=
*/ ?>
```

## Siehe auch

- [Alle Blöcke von notification.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/notification.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
