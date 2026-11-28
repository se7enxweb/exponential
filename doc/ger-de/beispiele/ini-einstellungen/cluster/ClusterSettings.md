---
title: cluster.ini [ClusterSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ClusterSettings] in cluster.ini: HostList, DefaultPhp, ConnectTimeout, Timeout, Mode, AllowRootUser, SshOptions, LogFile"
keywords: Einstellung, cluster.ini, ClusterSettings, INI, setting, HostList, DefaultPhp, ConnectTimeout, Timeout, Mode, AllowRootUser, SshOptions, LogFile
type: generated
generated: exp:help:examples
---
# cluster.ini [ClusterSettings]

Der Block `[ClusterSettings]` von `settings/cluster.ini`, mit 8 Einstellung(en).

Bearbeiten Sie `settings/cluster.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/cluster.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/cluster.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/cluster.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## HostList

> The hosts, in the order the commands visit them. Each one has a block  
> \[Host\_<name>\] below. A name is letters, digits, dot, dash and underscore.  
>
> HostList\[\]  
> HostList\[\]=web  
> HostList\[\]=cluster  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cluster.ini.append.php
[ClusterSettings]
# ein leeres "HostList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
HostList[]
*/ ?>
```

## DefaultPhp

> The PHP binary on a host whose block names none (Php=).  

Standard: `php`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cluster.ini.append.php
[ClusterSettings]
DefaultPhp=php
*/ ?>
```

## ConnectTimeout

> Seconds ssh waits for a host to answer (ssh -o ConnectTimeout).  

Standard: `20`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cluster.ini.append.php
[ClusterSettings]
ConnectTimeout=20
*/ ?>
```

## Timeout

> Seconds one host may take for everything it is asked to do; a host that takes  
> longer is stopped and counts as failed.  

Standard: `1800`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cluster.ini.append.php
[ClusterSettings]
Timeout=1800
*/ ?>
```

## Mode

> Run on the hosts one after the other (sequential, the default) or all at once  
> (parallel). --parallel and --sequential on the command line win.  

Standard: `sequential`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cluster.ini.append.php
[ClusterSettings]
Mode=sequential
*/ ?>
```

## AllowRootUser

> Add --allow-root-user to every console command, so that a host where the ssh  
> login (or User=) is root runs it. A non-root user ignores the option.  
> A host block can set its own AllowRootUser=enabled|disabled.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cluster.ini.append.php
[ClusterSettings]
AllowRootUser=enabled
*/ ?>
```

## SshOptions

> More ssh options for every host, each as ssh -o takes it.  
> SshOptions\[\]=StrictHostKeyChecking=accept-new  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cluster.ini.append.php
[ClusterSettings]
# ein leeres "SshOptions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SshOptions[]
*/ ?>
```

## LogFile

> The log in var/log where every run is written, one line per host  
> (in addition to the audit event system.cluster.run).  

Standard: `cluster.log`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cluster.ini.append.php
[ClusterSettings]
LogFile=cluster.log
*/ ?>
```

## Siehe auch

- [Alle Blöcke von cluster.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/cluster.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
