---
title: exp:expmigrationremotelist
audience: developer, admin
category: examples
summary: Die Gegenstellen, auf die Migrationen übertragen werden können (migration.ini [RemoteSettings]), mit ihren Gruppen und jedem Problem ihrer Einstellungen; verbindet sich mit keiner.
keywords: Konsole, Befehl, exp:expmigrationremotelist, expmigrationremotelist, console, command, CLI, expmigrationremotelist.php, exp:migration:remote:list
type: generated
generated: exp:help:examples
---
# exp:expmigrationremotelist

Die Gegenstellen, auf die Migrationen übertragen werden können (migration.ini [RemoteSettings]), mit ihren Gruppen und jedem Problem ihrer Einstellungen; verbindet sich mit keiner.

Skript `bin/php/expmigrationremotelist.php`, Code in `kernel/private/classes/commands/expmigrationremotelist.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expmigrationremotelist --help
./console exp:migration:remote:list --help
php bin/php/expmigrationremotelist.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
The remotes of migration deploys
Every remote of migration.ini ([RemoteSettings] RemoteList[], one [Remote_<id>] each): environment,
transport, address, credential (by name, never its value), groups, and what is wrong with its
settings. Connects to no remote and reads no secret.

./console exp:migration:remote:list
./console exp:migration:remote:list --remotes=production --format=json
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--remotes` | erforderlich | Die Gegenstellen: IDs oder Gruppennamen aus [RemoteSettings] RemoteList[] in migration.ini, oder all (durch Kommas getrennt oder wiederholt) |
| `--format` | erforderlich | text (Standard) oder json (die Maschinenausgabe exp-migration/1) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expmigrationremotelist.php`, `kernel/private/classes/commands/expmigrationremotelist.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
