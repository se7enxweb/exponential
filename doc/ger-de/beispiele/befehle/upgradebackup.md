---
title: exp:upgradebackup
audience: developer, admin
category: examples
summary: "Upgrade-Sicherung: die Sicherung, die exp:upgrade vor dem ersten Schreiben macht, für sich allein (Datenbankabzug und Einstellungs-Overrides)."
keywords: Konsole, Befehl, exp:upgradebackup, upgradebackup, console, command, CLI, upgradebackup.php, exp:upgrade:backup
type: generated
generated: exp:help:examples
---
# exp:upgradebackup

Upgrade-Sicherung: die Sicherung, die exp:upgrade vor dem ersten Schreiben macht, für sich allein (Datenbankabzug und Einstellungs-Overrides).

Skript `bin/php/upgradebackup.php`, Code in `kernel/private/classes/commands/upgradebackup.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:upgradebackup --help
./console exp:upgrade:backup --help
php bin/php/upgradebackup.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Upgrade backup
Makes the backup exp:upgrade --apply makes before its first write, into
var/backup/upgrade/<time>/: SQLite a consistent copy (VACUUM INTO), MySQL/MariaDB mysqldump,
PostgreSQL pg_dump, MongoDB mongodump, and a copy of settings/override. Oracle: make one with
expdp. Passwords never reach a command line.

./console exp:upgrade:backup
./console exp:upgrade:backup --backup-dir=/srv/backup/site --format=json
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--backup-dir` | erforderlich | Wohin die Sicherung geht (Standard var/backup/upgrade); darin wird ein Verzeichnis <Zeit> angelegt |
| `--format` | erforderlich | text (Standard) oder json (die Maschinenausgabe exp-upgrade/1) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/upgradebackup.php`, `kernel/private/classes/commands/upgradebackup.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
