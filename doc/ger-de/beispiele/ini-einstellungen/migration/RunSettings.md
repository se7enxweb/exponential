---
title: migration.ini [RunSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [RunSettings] in migration.ini: RunAsUser, StopOnFailure, Throttle, ClearCaches, Reindex"
keywords: Einstellung, migration.ini, RunSettings, INI, setting, RunAsUser, StopOnFailure, Throttle, ClearCaches, Reindex
type: generated
generated: exp:help:examples
---
# migration.ini [RunSettings]

Der Block `[RunSettings]` von `settings/migration.ini`, mit 5 Einstellung(en).

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

> Eine Pause zwischen Migrationen, damit ein langer Lauf den Server nicht belastet. Leer: keine.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[RunSettings]
Throttle=
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
