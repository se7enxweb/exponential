---
title: exp:upgradedatabase
audience: developer, admin
category: examples
summary: "Upgrade-Stufe 3, Datenbank: die Schema- und Datenschritte auf jeder Datenbank-Engine (Probelauf ohne --apply)."
keywords: Konsole, Befehl, exp:upgradedatabase, upgradedatabase, console, command, CLI, upgradedatabase.php, exp:upgrade:database
type: generated
generated: exp:help:examples
---
# exp:upgradedatabase

Upgrade-Stufe 3, Datenbank: die Schema- und Datenschritte auf jeder Datenbank-Engine (Probelauf ohne --apply).

Skript `bin/php/upgradedatabase.php`, Code in `kernel/private/classes/commands/upgradedatabase.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:upgradedatabase --help
./console exp:upgrade:database --help
php bin/php/upgradedatabase.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Upgrade stage 3: the database
The tables, columns, indexes and rows the release needs, on MySQL/MariaDB, PostgreSQL, SQLite,
Oracle (ezoracle) and MongoDB, written from the kernel schema (share/db_schema.dba). Each step
looks at the database first, so what the SQL update files already did counts. Dry run unless
--apply.

./console exp:upgrade:database
./console exp:upgrade:database --apply
./console exp:upgrade:database --apply --only=6.0.15-user-audit-table
Guide: doc/eng-us/guides/upgrading-with-the-console.md
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--apply` |  | Die Änderungen vornehmen (ohne die Option ein Probelauf, der sie auflistet und nichts ändert) |
| `--dry-run` |  | Der Standard, angenommen für Skripte, die ihn übergeben: auflisten, was sich ändern würde, nichts ändern |
| `--only` | erforderlich | Nur diese Schritte (Schritt-IDs, als Komma-Liste oder wiederholt); ein optionaler Schritt läuft nur, wenn er hier genannt ist |
| `--skip` | erforderlich | Diese Schritte nie (Schritt-IDs, als Komma-Liste oder wiederholt) |
| `--format` | erforderlich | text (Standard) oder json (die Maschinenausgabe exp-upgrade/1) |
| `--no-backup` |  | Schreiben, ohne vorher eine Sicherung zu machen |
| `--backup-dir` | erforderlich | Wohin die Sicherung geht (Standard var/backup/upgrade); darin wird ein Verzeichnis <Zeit> angelegt |
| `--force-order` |  | Eine Stufe ausführen, obwohl eine frühere Stufe offene Schritte hat |
| `--fail-on-pending` |  | Exit-Code 1, wenn noch Schritte offen sind (für CI) |
| `--force-no-backup` |  | Mit --no-backup auf einer Produktions-Installation: trotzdem ohne Sicherung schreiben |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/upgradedatabase.php`, `kernel/private/classes/commands/upgradedatabase.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
