---
title: solr.ini [SolrSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [SolrSettings] in solr.ini: Enabled, Url, BinaryPath, Host, Port, DataDir, RunDir, LogDir ..."
keywords: Einstellung, solr.ini, SolrSettings, INI, setting, Enabled, Url, BinaryPath, Host, Port, DataDir, RunDir, LogDir, StartTimeout
type: generated
generated: exp:help:examples
---
# solr.ini [SolrSettings]

Der Block `[SolrSettings]` von `settings/solr.ini`, mit 9 Einstellung(en).

Bearbeiten Sie `settings/solr.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/solr.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/solr.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/solr.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> Hauptschalter. Solange false, meldet exp:solr „nicht konfiguriert“ und weigert sich zu  
> starten, egal was unten sonst gesetzt ist.  

Standard: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/solr.ini.append.php
[SolrSettings]
Enabled=false
*/ ?>
```

## Url

> Ein laufendes Solr, mit dem gesprochen wird, Basis-URL mit Schema und Port, ohne nachfolgenden  
> Pfad, z. B. http://127.0.0.1:8983 . Wenn gesetzt, pingt exp:solr status es an, und  
> start/stop melden, dass die Instanz extern verwaltet wird (ein entferntes Solr zu starten oder zu stoppen  
> ist nicht Sache dieses Befehls).  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/solr.ini.append.php
[SolrSettings]
Url=
*/ ?>
```

## BinaryPath

> Die Steuerdatei von Solr für ein auf DIESEM Rechner installiertes Solr, z. B.  
> /opt/solr/bin/solr . Wenn gesetzt (und Url leer ist), verwalten exp:solr start/stop/restart  
> diesen lokalen Prozess mit dessen eigenen Unterbefehlen start/stop.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/solr.ini.append.php
[SolrSettings]
BinaryPath=
*/ ?>
```

## Host

> Host und Port, an die die lokale Instanz bindet (mit BinaryPath verwendet und um eine  
> Ping-URL zu bilden, wenn Url leer ist).  

Standard: `127.0.0.1`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/solr.ini.append.php
[SolrSettings]
Host=127.0.0.1
*/ ?>
```

## Port

Standard: `8983`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/solr.ini.append.php
[SolrSettings]
Port=8983
*/ ?>
```

## DataDir

> Solr-Home-/Datenverzeichnis der lokalen Instanz. Wenn leer, gilt der eigene  
> Standard von Solr.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/solr.ini.append.php
[SolrSettings]
DataDir=
*/ ?>
```

## RunDir

> Wo exp:solr die PID, die Logs und (optional) die Daten des Solr dieser Installation hält,  
> nach dem Vorbild der Struktur von Velocity auf der Festplatte (var/vc/<engine>/...).  

Standard: `var/vc/solr/run`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/solr.ini.append.php
[SolrSettings]
RunDir=var/vc/solr/run
*/ ?>
```

## LogDir

Standard: `var/vc/solr/log`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/solr.ini.append.php
[SolrSettings]
LogDir=var/vc/solr/log
*/ ?>
```

## StartTimeout

> Wie lange (Sekunden) nach einem Start gewartet wird, bis der Port antwortet.  

Standard: `60`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/solr.ini.append.php
[SolrSettings]
StartTimeout=60
*/ ?>
```

## Siehe auch

- [Alle Blöcke von solr.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/solr.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
