---
title: migration.ini [InstallSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [InstallSettings] in migration.ini: RunOnPackageInstall, RunOnKickstart, RunOptions, Timeout"
keywords: Einstellung, migration.ini, InstallSettings, INI, setting, RunOnPackageInstall, RunOnKickstart, RunOptions, Timeout
type: generated
generated: exp:help:examples
---
# migration.ini [InstallSettings]

Der Block `[InstallSettings]` von `settings/migration.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/migration.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/migration.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/migration.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/migration.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## RunOnPackageInstall

> Migrations shipped with a package (a directory migrations/ at the top of the package, next to its package.xml or  
> package.json): installing the package (Setup > Packages, or a package the setup wizard or the kickstarter installs  
> item by item) runs them once its items are installed, with ./console exp:migration:migrate  
> --path=<the package's migrations> --apply as the user who installs it (the console: \[RunSettings\] RunAsUser). A  
> migration that fails leaves the package not installed, so installing it again runs what is still pending.  
> disabled: the package's migrations are not run; ./console exp:migration:migrate --apply runs them with the others.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[InstallSettings]
RunOnPackageInstall=enabled
*/ ?>
```

## RunOnKickstart

> The kickstarter (php bin/php/kickstarter.php run --apply) runs every pending migration of the new installation  
> (./console exp:migration:migrate --apply) after the sites are created, while maintenance mode is still on.  
> disabled: it does not.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[InstallSettings]
RunOnKickstart=enabled
*/ ?>
```

## RunOptions

> The options of exp:migration:migrate for these runs: no-caption, no-restore, no-commit, no-push, no-tag,  
> include-uncommitted. Installing writes settings, design and extension files the run would otherwise refuse as  
> changes that are not committed, and a new installation has nothing to push yet.  

Standard: eine Liste mit 2: `include-uncommitted`, `no-push`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[InstallSettings]
# ein leeres "RunOptions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RunOptions[]
RunOptions[]=include-uncommitted
RunOptions[]=no-push
*/ ?>
```

## Timeout

> Seconds a run at installation may take before it is stopped (and reported as failed).  

Standard: `3600`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[InstallSettings]
Timeout=3600
*/ ?>
```

## Siehe auch

- [Alle Blöcke von migration.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
