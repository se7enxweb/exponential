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

> Master switch. While false, exp:solr reports "not configured" and refuses to  
> start, whatever else is set below.  

Standard: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/solr.ini.append.php
[SolrSettings]
Enabled=false
*/ ?>
```

## Url

> A running Solr to talk to, base URL including scheme and port, no trailing  
> path, e.g. http://127.0.0.1:8983 . When set, exp:solr status pings it and  
> start/stop report that the instance is managed externally (a remote Solr is  
> not this command's to start or stop).  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/solr.ini.append.php
[SolrSettings]
Url=
*/ ?>
```

## BinaryPath

> The Solr control binary for a Solr installed on THIS machine, e.g.  
> /opt/solr/bin/solr . When set (and Url is empty) exp:solr start/stop/restart  
> manage that local process with its own start/stop subcommands.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/solr.ini.append.php
[SolrSettings]
BinaryPath=
*/ ?>
```

## Host

> The local instance's bind host and port (used with BinaryPath, and to build a  
> ping URL when Url is empty).  

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

> Solr home / data directory for the local instance. Left empty, Solr's own  
> default applies.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/solr.ini.append.php
[SolrSettings]
DataDir=
*/ ?>
```

## RunDir

> Where exp:solr keeps this installation's Solr pid, logs and (optional) data,  
> mirroring the Velocity on-disk layout (var/vc/<engine>/...).  

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

> How long to wait (seconds) for the port to answer after a start.  

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
