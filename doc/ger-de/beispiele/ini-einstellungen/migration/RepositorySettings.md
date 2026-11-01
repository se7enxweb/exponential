---
title: migration.ini [RepositorySettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [RepositorySettings] in migration.ini: Mode, Path, Remote, Branch, Commit, Tag, TagPattern, Push"
keywords: Einstellung, migration.ini, RepositorySettings, INI, setting, Mode, Path, Remote, Branch, Commit, Tag, TagPattern, Push
type: generated
generated: exp:help:examples
---
# migration.ini [RepositorySettings]

Der Block `[RepositorySettings]` von `settings/migration.ini`, mit 8 Einstellung(en).

Bearbeiten Sie `settings/migration.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/migration.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/migration.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/migration.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Mode

> Wo die Aufzeichnung der Läufe unter Versionskontrolle liegt: die Statusdateien (status/<environment>.json) und die  
> Laufprotokolle (runs/<run-id>.log).  
>   site      Path ist ein Ordner des eigenen Git-Repositorys der Installation  
>   separate  Path ist ein eigenes Git-Repository  

Standard: `site`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RepositorySettings]
Mode=site
*/ ?>
```

## Path

> Der Ordner, relativ zur Wurzel der Installation oder absolut.  

Standard: `migrations`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RepositorySettings]
Path=migrations
*/ ?>
```

## Remote

> Das Remote, zu dem ein Lauf pusht (ein beliebiges Git-Remote: GitHub, GitLab, ein eigener Server). Leer: kein Push.  

Standard: `origin`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RepositorySettings]
Remote=origin
*/ ?>
```

## Branch

> Der Branch, auf dem ein Lauf committet. Leer: der Branch, auf dem das Repository steht. In ein Repository auf  
> einem anderen Branch wird nicht committet (eine Warnung sagt das).  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RepositorySettings]
Branch=
*/ ?>
```

## Commit

> enabled: Statusdatei und Laufprotokoll werden nach jedem Lauf committet, auch nach gescheiterten.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RepositorySettings]
Commit=enabled
*/ ?>
```

## Tag

> enabled: der Commit wird getaggt (ein Lauf, eine Revision: Commit und Tag). Ein Tag wird nie verschoben oder ersetzt.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RepositorySettings]
Tag=enabled
*/ ?>
```

## TagPattern

> Der Name des Tags: <environment> (default, wenn die Installation keine nennt), <run-id>, <date> (YYYYMMDD).  

Standard: `migrations/<environment>/<run-id>`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RepositorySettings]
TagPattern=migrations/<environment>/<run-id>
*/ ?>
```

## Push

> enabled: der Commit und sein Tag werden zu Remote gepusht. Ein gescheiterter Push bleibt lokal und wird vom  
> Cronjob-Teil migrationpush erneut versucht (php runcronjobs.php migrationpush).  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RepositorySettings]
Push=enabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von migration.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
