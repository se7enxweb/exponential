---
title: exp:upgraderollback
audience: developer, admin
category: examples
summary: "Upgrade zurückrollen: Datenbank, Einstellungen, Bibliotheken und Code auf eine Sicherung von exp:upgrade zurücksetzen (Probelauf ohne --apply)."
keywords: Konsole, Befehl, exp:upgraderollback, upgraderollback, console, command, CLI, upgraderollback.php, exp:upgrade:rollback
type: generated
generated: exp:help:examples
---
# exp:upgraderollback

Upgrade zurückrollen: Datenbank, Einstellungen, Bibliotheken und Code auf eine Sicherung von exp:upgrade zurücksetzen (Probelauf ohne --apply).

Skript `bin/php/upgraderollback.php`, Code in `kernel/private/classes/commands/upgraderollback.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:upgraderollback --help
./console exp:upgrade:rollback --help
php bin/php/upgraderollback.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Upgrade rollback
Without a backup name: the backups exp:upgrade made (var/backup/upgrade/). With one: what a
rollback to it restores, and with --apply restores it: the database (SQLite copy, MySQL/MariaDB
and PostgreSQL dump; tables made later are dropped), settings/override, composer.lock with
composer install, and the git checkouts moved back to their commits (git reset --keep).
Never automatic. Afterwards run ./console exp:upgrade:finish --apply.

./console exp:upgrade:rollback
./console exp:upgrade:rollback 20261029-101500
./console exp:upgrade:rollback 20261029-101500 --apply --parts=database,settings
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--apply` |  | Die Änderungen vornehmen (ohne die Option ein Probelauf, der sie auflistet und nichts ändert) |
| `--dry-run` |  | Der Standard, angenommen für Skripte, die ihn übergeben: auflisten, was sich ändern würde, nichts ändern |
| `--parts` | erforderlich | Nur diese Teile, eine Komma-Liste aus database, settings, libraries, code (Standard: was die Sicherung enthält) |
| `--backup-dir` | erforderlich | Wohin die Sicherung geht (Standard var/backup/upgrade); darin wird ein Verzeichnis <Zeit> angelegt |
| `--format` | erforderlich | text (Standard) oder json (die Maschinenausgabe exp-upgrade/1) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[BACKUP]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/upgraderollback.php`, `kernel/private/classes/commands/upgraderollback.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
