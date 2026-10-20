---
title: file.ini [ClusterEventsSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ClusterEventsSettings] in file.ini: ClusterEvents, Listener"
keywords: Einstellung, file.ini, ClusterEventsSettings, INI, setting, ClusterEvents, Listener
type: generated
generated: exp:help:examples
---
# file.ini [ClusterEventsSettings]

Der Block `[ClusterEventsSettings]` von `settings/file.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/file.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/file.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/file.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/file.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ClusterEvents

> Enables/Disables dedicated cluster events listening  
> If enabled, the cluster file handler will use a dedicated listener object  
> to respond to cluster events.  
> Can be used to add a cache layer (e.g. Memcached or Redis) in the cluster backend  
> and thus reduce cluster database load.  
> Only supported by eZDFSFileHandler MySQLi backend at the moment.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/file.ini.append.php
[ClusterEventsSettings]
ClusterEvents=disabled
*/ ?>
```

## Listener

> Class to use as cluster events listener  
> This class must implement eZClusterEventListener interface  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/file.ini.append.php
[ClusterEventsSettings]
Listener=
*/ ?>
```

## Siehe auch

- [Alle Blöcke von file.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/file.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
