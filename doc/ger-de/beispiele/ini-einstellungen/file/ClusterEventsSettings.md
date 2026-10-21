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

> Aktiviert/deaktiviert einen eigenen Listener für Cluster-Ereignisse  
> Wenn aktiviert, verwendet der Cluster-Datei-Handler ein eigenes Listener-Objekt,  
> um auf Cluster-Ereignisse zu reagieren.  
> Lässt sich verwenden, um eine Cache-Schicht (z. B. Memcached oder Redis) in das Cluster-Backend einzufügen  
> und so die Last der Cluster-Datenbank zu senken.  
> Derzeit nur vom MySQLi-Backend von eZDFSFileHandler unterstützt.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/file.ini.append.php
[ClusterEventsSettings]
ClusterEvents=disabled
*/ ?>
```

## Listener

> Klasse, die als Listener für Cluster-Ereignisse verwendet wird  
> Diese Klasse muss die Schnittstelle eZClusterEventListener implementieren  

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
