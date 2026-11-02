---
title: migration.ini [RunSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [RunSettings] in migration.ini: RunAsUser, StopOnFailure, Throttle, MaxMigrations, MaxTime, Nice, ClearCaches, Reindex"
keywords: Einstellung, migration.ini, RunSettings, INI, setting, RunAsUser, StopOnFailure, Throttle, MaxMigrations, MaxTime, Nice, ClearCaches, Reindex
type: generated
generated: exp:help:examples
---
# migration.ini [RunSettings]

Der Block `[RunSettings]` von `settings/migration.ini`, mit 8 Einstellung(en).

Bearbeiten Sie `settings/migration.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/migration.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/migration.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/migration.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## RunAsUser

> Der Benutzer, als der eine Migration läuft, wenn niemand angemeldet ist: Konsole und unbeaufsichtigte Läufe. Ein  
> Lauf in der Administration läuft als der angemeldete Benutzer. Das Login, nie eine ID: IDs unterscheiden sich  
> zwischen Installationen.  

Standard: `admin`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RunSettings]
RunAsUser=admin
*/ ?>
```

## StopOnFailure

> enabled: ein Lauf hält bei der ersten scheiternden Migration an; die danach laufen nicht.  
> disabled: der Lauf geht mit den Migrationen weiter, die die gescheiterte nicht verlangen.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RunSettings]
StopOnFailure=enabled
*/ ?>
```

## Throttle

> A pause between migrations, in seconds (0.5 is half a second, at most 60), to keep a long run from loading the  
> server; PHP migrations that work in batches (expMigrationPhp::batches()) pause as long between their batches.  
> Empty: none. --throttle=<seconds> of a run wins.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RunSettings]
Throttle=
*/ ?>
```

## MaxMigrations

> A long run in parts. MaxMigrations: apply at most this many migrations in one run; MaxTime: start no further migration  
> after this many seconds (the one running is finished). The rest stay pending: the next run (or exp:migration:resume)  
> goes on with them, and a PHP migration that works in batches goes on from its checkpoint. Empty: no limit.  
> --max-migrations=<n> and --max-time=<seconds> of a run win.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RunSettings]
MaxMigrations=
*/ ?>
```

## MaxTime

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RunSettings]
MaxTime=
*/ ?>
```

## Nice

> A lower priority for runs on the command line (and the administration's background runs), 1 to 19 as nice(1).  
> Empty: unchanged. --nice=<n> of a run wins.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RunSettings]
Nice=
*/ ?>
```

## ClearCaches

> enabled: nach einem Lauf, der Migrationen angewendet (oder einem Zurücknehmen, das Migrationen rückgängig gemacht) hat, werden die  
> Caches geleert, die ihre Schritte berührt haben, mit ihren Namen in ezcache, und nur diese (jeder Schritttyp nennt seine Caches;  
> der Schritttyp cache fügt weitere hinzu). Der Bericht listet sie, mit dem Deploy, das genau sie auch auf dem Server leert  
> (./console exp:velocity deploy --caches=<Liste> --apply). disabled: die Caches bleiben, wie sie sind.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RunSettings]
ClearCaches=enabled
*/ ?>
```

## Reindex

> Was nach dem Lauf mit dem Suchindex der Objekte geschieht, die die Schritte eines Laufs angelegt oder geändert haben:  
> queue: für den Cronjob indexcontent vorgemerkt (die ausstehenden Aktionen der verzögerten Indizierung); now: sofort  
> indiziert, bevor der Lauf endet; disabled: nichts. Der Schritttyp search indiziert für sich, auch alles neu.  

Standard: `queue`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RunSettings]
Reindex=queue
*/ ?>
```

## Siehe auch

- [Alle Blöcke von migration.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
