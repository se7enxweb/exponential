---
title: exp:upgraderollback
audience: developer, admin
category: examples
summary: "Upgrade zurückrollen: Datenbank, Speicher, Einstellungen, Bibliotheken und Code auf einen Sicherungspunkt zurücksetzen (Probelauf ohne --apply)."
keywords: Konsole, Befehl, exp:upgraderollback, upgraderollback, console, command, CLI, upgraderollback.php, exp:upgrade:rollback
type: generated
generated: exp:help:examples
---
# exp:upgraderollback

Upgrade zurückrollen: Datenbank, Speicher, Einstellungen, Bibliotheken und Code auf einen Sicherungspunkt zurücksetzen (Probelauf ohne --apply).

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
Without a caption id: the captions, newest first (as exp:upgrade:captions). With one: what a
rollback to it restores, and with --apply: a safety caption of the current state first (so the
rollback can be undone), then the parts: the database (tables made later are dropped), the
storage (var/), settings/override, composer.lock with composer install, the git checkouts
(git reset --keep), then the verification of exp:upgrade:verify --after. Never automatic.
Afterwards run ./console exp:upgrade:finish --apply.

./console exp:upgrade:rollback
./console exp:upgrade:rollback 2026-10-29_10-15-00
./console exp:upgrade:rollback 2026-10-29_10-15-00 --apply --parts=database,settings
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--apply` |  | Die Änderungen vornehmen (ohne die Option ein Probelauf, der sie auflistet und nichts ändert) |
| `--dry-run` |  | Der Standard, angenommen für Skripte, die ihn übergeben: auflisten, was sich ändern würde, nichts ändern |
| `--parts` | erforderlich | Nur diese Teile, eine Komma-Liste aus database, storage, settings, libraries, code (Standard: was der Sicherungspunkt enthält) |
| `--backup-dir` | erforderlich | Wohin die Sicherung geht (Standard var/backup/upgrade); darin wird ein Verzeichnis <Zeit> angelegt |
| `--format` | erforderlich | text (Standard) oder json (die Maschinenausgabe exp-upgrade/1) |
| `--no-caption` |  | Schreiben, ohne vorher einen Sicherungspunkt anzulegen (dasselbe wie --no-backup); auf Produktion ohne --force-no-caption verweigert |
| `--force-no-caption` |  | Mit --no-caption auf einer Produktions-Installation: trotzdem ohne Sicherungspunkt schreiben |
| `--no-verify` |  | Die Prüfung nach dem Upgrade überspringen (exp:upgrade:verify --after) |
| `--url` | erforderlich | Die Adressen, die die Rauchprobe nach dem Deploy abruft (Komma-Liste oder wiederholt); Standard die Startseite von SiteURL |
| `--no-tests` |  | Die Testsuite upgrade-smoke aus der Prüfung weglassen |
| `--allow-vendor-changes` |  | Die Bibliotheken installieren, obwohl Composer lokale Änderungen in vendor/ meldet |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[CAPTION]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/upgraderollback.php`, `kernel/private/classes/commands/upgraderollback.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
