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

> Mit einem Paket ausgelieferte Migrationen (ein Verzeichnis migrations/ oben im Paket, neben seiner package.xml oder  
> package.json): Das Installieren des Pakets (Setup > Pakete, oder ein Paket, das der Setup-Assistent oder der Kickstarter  
> Element für Element installiert) führt sie aus, sobald seine Elemente installiert sind, mit ./console exp:migration:migrate  
> --path=<die Migrationen des Pakets> --apply als der installierende Benutzer (auf der Konsole: \[RunSettings\] RunAsUser). Eine  
> Migration, die scheitert, lässt das Paket nicht installiert, sodass eine erneute Installation ausführt, was noch aussteht.  
> disabled: Die Migrationen des Pakets werden nicht ausgeführt; ./console exp:migration:migrate --apply führt sie mit den anderen aus.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[InstallSettings]
RunOnPackageInstall=enabled
*/ ?>
```

## RunOnKickstart

> Der Kickstarter (php bin/php/kickstarter.php run --apply) führt jede ausstehende Migration der neuen Installation aus  
> (./console exp:migration:migrate --apply), nachdem die Sites angelegt sind, während der Wartungsmodus noch an ist.  
> disabled: Er tut es nicht.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[InstallSettings]
RunOnKickstart=enabled
*/ ?>
```

## RunOptions

> Die Optionen von exp:migration:migrate für diese Läufe: no-caption, no-restore, no-commit, no-push, no-tag,  
> include-uncommitted. Das Installieren schreibt Einstellungs-, Design- und Erweiterungsdateien, die der Lauf sonst als nicht  
> committete Änderungen ablehnen würde, und eine neue Installation hat noch nichts zu pushen.  

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

> Sekunden, die ein Lauf bei der Installation dauern darf, bevor er gestoppt (und als gescheitert gemeldet) wird.  

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
