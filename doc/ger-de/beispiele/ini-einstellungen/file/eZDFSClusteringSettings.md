---
title: file.ini [eZDFSClusteringSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [eZDFSClusteringSettings] in file.ini: MountPointPath, DBBackend, DFSBackend, DBHost, DBPort, DBSocket, DBName, DBUser ..."
keywords: Einstellung, file.ini, eZDFSClusteringSettings, INI, setting, MountPointPath, DBBackend, DFSBackend, DBHost, DBPort, DBSocket, DBName, DBUser, DBPassword, DBConnectRetries, DBExecuteRetries, MaxCopyRetries
type: generated
generated: exp:help:examples
---
# file.ini [eZDFSClusteringSettings]

Der Block `[eZDFSClusteringSettings]` von `settings/file.ini`, mit 12 Einstellung(en).

> DFS Cluster Handler settings  

Bearbeiten Sie `settings/file.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/file.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/file.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/file.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## MountPointPath

> Path to the NFS mount point  
> Can be relative to the Exponential root, or absolute  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/file.ini.append.php
[eZDFSClusteringSettings]
MountPointPath=
*/ ?>
```

## DBBackend

> Database backend  
> Available options:  
> - MySQLi: eZDFSFileHandlerMySQLiBackend  
> - PostgreSQL: eZDFSFileHandlerPostgresqlBackend  
> - Oracle: eZDFSFileHandlerOracleBackend (requires the ezoracle extension)  

Standard: `eZDFSFileHandlerMySQLiBackend`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/file.ini.append.php
[eZDFSClusteringSettings]
DBBackend=eZDFSFileHandlerMySQLiBackend
*/ ?>
```

## DFSBackend

Standard: `eZDFSFileHandlerDFSBackend`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/file.ini.append.php
[eZDFSClusteringSettings]
DFSBackend=eZDFSFileHandlerDFSBackend
*/ ?>
```

## DBHost

Standard: `dbhost`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/file.ini.append.php
[eZDFSClusteringSettings]
DBHost=dbhost
*/ ?>
```

## DBPort

Standard: `3306`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/file.ini.append.php
[eZDFSClusteringSettings]
DBPort=3306
*/ ?>
```

## DBSocket

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/file.ini.append.php
[eZDFSClusteringSettings]
DBSocket=
*/ ?>
```

## DBName

Standard: `cluster`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/file.ini.append.php
[eZDFSClusteringSettings]
DBName=cluster
*/ ?>
```

## DBUser

Standard: `root`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/file.ini.append.php
[eZDFSClusteringSettings]
DBUser=root
*/ ?>
```

## DBPassword

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/file.ini.append.php
[eZDFSClusteringSettings]
DBPassword=
*/ ?>
```

## DBConnectRetries

Standard: `3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/file.ini.append.php
[eZDFSClusteringSettings]
DBConnectRetries=3
*/ ?>
```

## DBExecuteRetries

Standard: `20`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/file.ini.append.php
[eZDFSClusteringSettings]
DBExecuteRetries=20
*/ ?>
```

## MaxCopyRetries

> Max number of times a dfs file is tried to be copied.  
> Increase this value if you find files like expiryXYZtmp.php with 0 byte size  

Standard: `5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/file.ini.append.php
[eZDFSClusteringSettings]
MaxCopyRetries=5
*/ ?>
```

## Siehe auch

- [Alle Blöcke von file.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/file.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
