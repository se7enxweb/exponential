---
title: exp:expmigrationheal
audience: developer, admin
category: examples
summary: "Selbstheilung vor einem Migrationslauf: eine verwaiste Laufsperre, fehlende Migrationsordner, Dateirechte, Caches (was ein Deploy auf einer Gegenstelle korrigieren darf)."
keywords: Konsole, Befehl, exp:expmigrationheal, expmigrationheal, console, command, CLI, expmigrationheal.php, exp:migration:heal
type: generated
generated: exp:help:examples
---
# exp:expmigrationheal

Selbstheilung vor einem Migrationslauf: eine verwaiste Laufsperre, fehlende Migrationsordner, Dateirechte, Caches (was ein Deploy auf einer Gegenstelle korrigieren darf).

Skript `bin/php/expmigrationheal.php`, Code in `kernel/private/classes/commands/expmigrationheal.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expmigrationheal --help
./console exp:migration:heal --help
php bin/php/expmigrationheal.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Self-healing before a migration run
The fixes a deploy may make on a remote before it runs the migrations there ([RemoteSettings] SelfHeal[]
of the source), each only when named: stale-lock (a run lock whose process is gone and that is older than
--stale-lock-age, held on this host by this user), paths (the missing migration folders), modes (the
permissions of [FileSettings] on the migration folders and var/<site>/migration, only added), caches (ini
and template-override). What cannot be fixed safely is only reported. Without --apply it lists what it
would do.

./console exp:migration:heal --fix=stale-lock,paths,modes
./console exp:migration:heal --fix=stale-lock --stale-lock-age=3600 --apply --format=json
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--fix` | erforderlich | Die Korrekturen: stale-lock, paths, modes, caches (durch Kommas getrennt oder wiederholt) |
| `--stale-lock-age` | erforderlich | Sekunden, ab denen eine gehaltene Laufsperre, deren Prozess beendet ist, als verwaist gilt (Vorgabe 7200) |
| `--apply` |  | Die Änderungen vornehmen (ohne die Option ein Probelauf, der sie auflistet und nichts ändert) |
| `--dry-run` |  | Der Standard, angenommen für Skripte, die ihn übergeben: auflisten, was sich ändern würde, nichts ändern |
| `--format` | erforderlich | text (Standard) oder json (die Maschinenausgabe exp-migration/1) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expmigrationheal.php`, `kernel/private/classes/commands/expmigrationheal.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
