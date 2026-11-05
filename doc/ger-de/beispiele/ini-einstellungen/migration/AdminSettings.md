---
title: migration.ini [AdminSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AdminSettings] in migration.ini: PhpCliPath, JobHistory, Background, QueueJobType, LogBytes"
keywords: Einstellung, migration.ini, AdminSettings, INI, setting, PhpCliPath, JobHistory, Background, QueueJobType, LogBytes
type: generated
generated: exp:help:examples
---
# migration.ini [AdminSettings]

Der Block `[AdminSettings]` von `settings/migration.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/migration.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/migration.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/migration.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/migration.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## PhpCliPath

> Einrichtung > Migrationen (Modul migration) startet einen Lauf oder eine Rücknahme als eigenen Konsolenprozess, der weiterläuft, wenn  
> die Seite geschlossen wird; seine Dateien liegen in var/<site>/migration/jobs/.  
>
> Das PHP-Kommandozeilenprogramm, mit dem diese Prozesse laufen. Leer: cronjob.ini \[AdminSettings\] PhpCliPath, sonst die  
> üblichen Orte (das bin-Verzeichnis dieses PHP, /usr/bin/php, /usr/local/bin/php). Nie das php-fpm-Programm.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[AdminSettings]
PhpCliPath=
*/ ?>
```

## JobHistory

> Wie viele dort gestartete Läufe ihre Dateien behalten (die neuesten; ein laufender Lauf wird immer behalten).  

Standard: `50`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[AdminSettings]
JobHistory=50
*/ ?>
```

## Background

> Wie ein dort gestarteter Lauf in den Hintergrund geht:  
>   process  ein eigener Konsolenprozess, sofort gestartet (der Standard)  
>   queue    ein Job von expqueue (der Erweiterung), von ihrem Worker ausgeführt, wenn expqueue aktiv ist, seine Tabellen installiert sind,  
>            expqueue.ini den Job-Typ unten hat und ein Worker läuft; sonst ein Prozess wie oben  
> Der Job-Typ, in der expqueue.ini.append.php der Site:  
>   \[JobType\_migration.run\]  
>   Handler=expMigrationQueueHandler  
>   Class=bulk  
>   Timeout=86400  
>   Description=A migration run started in Setup > Migrations  

Standard: `process`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[AdminSettings]
Background=process
*/ ?>
```

## QueueJobType

> Der expqueue-Job-Typ von Background=queue.  

Standard: `migration.run`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[AdminSettings]
QueueJobType=migration.run
*/ ?>
```

## LogBytes

> Wie viel vom Ende eines Laufprotokolls eine Seite zeigt, in Byte.  

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
