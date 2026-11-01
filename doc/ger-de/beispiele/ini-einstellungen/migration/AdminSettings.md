---
title: migration.ini [AdminSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AdminSettings] in migration.ini: PhpCliPath, JobHistory, LogBytes"
keywords: Einstellung, migration.ini, AdminSettings, INI, setting, PhpCliPath, JobHistory, LogBytes
type: generated
generated: exp:help:examples
---
# migration.ini [AdminSettings]

Der Block `[AdminSettings]` von `settings/migration.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/migration.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/migration.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/migration.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/migration.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## PhpCliPath

> Setup > Migrations (module migration) starts a run or a rollback as a console process of its own, which goes on when  
> the page is closed; its files are in var/<site>/migration/jobs/.  
>
> The php command line binary those processes run with. Empty: cronjob.ini \[AdminSettings\] PhpCliPath, else the  
> usual places (the bin directory of this php, /usr/bin/php, /usr/local/bin/php). Never the php-fpm binary.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[AdminSettings]
PhpCliPath=
*/ ?>
```

## JobHistory

> How many runs started there keep their files (the newest ones; a run going on is always kept).  

Standard: `50`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[AdminSettings]
JobHistory=50
*/ ?>
```

## LogBytes

> How much of the end of a run log a page shows, in bytes.  

Standard: `262144`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[AdminSettings]
LogBytes=262144
*/ ?>
```

## Siehe auch

- [Alle Blöcke von migration.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
