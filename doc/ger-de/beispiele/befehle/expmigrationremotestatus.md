---
title: exp:expmigrationremotestatus
audience: developer, admin
category: examples
summary: Der Migrationsstand der Gegenstellen, parallel gelesen (erreichbar, Version, Sperre, ausgeführt, ausstehend, gescheitert), ob sie im Gleichstand sind, und die letzten Deploys.
keywords: Konsole, Befehl, exp:expmigrationremotestatus, expmigrationremotestatus, console, command, CLI, expmigrationremotestatus.php, exp:migration:remote:status
type: generated
generated: exp:help:examples
---
# exp:expmigrationremotestatus

Der Migrationsstand der Gegenstellen, parallel gelesen (erreichbar, Version, Sperre, ausgeführt, ausstehend, gescheitert), ob sie im Gleichstand sind, und die letzten Deploys.

Skript `bin/php/expmigrationremotestatus.php`, Code in `kernel/private/classes/commands/expmigrationremotestatus.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expmigrationremotestatus --help
./console exp:migration:remote:status --help
php bin/php/expmigrationremotestatus.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
The migration state of remotes
Reads exp:migration:status of every selected remote in parallel and lists, per remote, whether it
was reached, its kernel version, the run lock, how many migrations are done, pending and failed, and
its latest run; then whether the remotes are in sync (every migration done on all of them or on
none) and the latest deploys of this installation. Changes nothing.

./console exp:migration:remote:status
./console exp:migration:remote:status --remotes=production --format=json
./console exp:migration:remote:status --history=0
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--remotes` | erforderlich | Die Gegenstellen: IDs oder Gruppennamen aus [RemoteSettings] RemoteList[] in migration.ini, oder all (durch Kommas getrennt oder wiederholt) |
| `--format` | erforderlich | text (Standard) oder json (die Maschinenausgabe exp-migration/1) |
| `--parallel` | erforderlich | An wie vielen Gegenstellen gleichzeitig gearbeitet wird (Vorgabe [RemoteSettings] Parallel) |
| `--timeout` | erforderlich | Sekunden, die jede Gegenstelle für eine Phase brauchen darf (Vorgabe [RemoteSettings] Timeout) |
| `--history` | erforderlich | Wie viele der letzten Deploys aufgelistet werden (Vorgabe 10, 0 für keine) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expmigrationremotestatus.php`, `kernel/private/classes/commands/expmigrationremotestatus.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
