---
title: notification.ini [TransportSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [TransportSettings] in notification.ini: DefaultTransport, TransportPluginPath"
keywords: Einstellung, notification.ini, TransportSettings, INI, setting, DefaultTransport, TransportPluginPath
type: generated
generated: exp:help:examples
---
# notification.ini [TransportSettings]

Der Block `[TransportSettings]` von `settings/notification.ini`, mit 2 Einstellung(en).

> AvailableNotificationEventTypes\[\]=ezsimpledigest  

Bearbeiten Sie `settings/notification.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/notification.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/notification.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/notification.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## DefaultTransport

Standard: `mail`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/notification.ini.append.php
[TransportSettings]
DefaultTransport=mail
*/ ?>
```

## TransportPluginPath

> An array of pathes to the transport plugins  
> TransportPluginPath\[\]=path/to/plugin1  
> TransportPluginPath\[\]=path/to/plugin2  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/notification.ini.append.php
[TransportSettings]
# ein leeres "TransportPluginPath[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
TransportPluginPath[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von notification.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/notification.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
