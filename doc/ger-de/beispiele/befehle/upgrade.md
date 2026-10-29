---
title: exp:upgrade
audience: developer, admin
category: examples
summary: "Aktualisiert die Installation in einem Aufruf: jede Stufe der Reihe nach (Probelauf ohne --apply)."
keywords: Konsole, Befehl, exp:upgrade, upgrade, console, command, CLI, upgrade.php
type: generated
generated: exp:help:examples
---
# exp:upgrade

Aktualisiert die Installation in einem Aufruf: jede Stufe der Reihe nach (Probelauf ohne --apply).

Skript `bin/php/upgrade.php`, Code in `kernel/private/classes/commands/upgrade.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:upgrade --help
php bin/php/upgrade.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Upgrade the installation in one call
Runs the stages in their fixed order: code, libraries, database, scripts, finish. Without --apply
it changes nothing and lists, step by step, what it would do. With --apply it makes a backup,
applies the pending steps in order, records each one and stops at the first failure; the finish
stage then deploys the changes and checks that the site answers.

./console exp:upgrade                                  dry run of every stage
./console exp:upgrade --apply
./console exp:upgrade --apply --stages=database,scripts,finish
./console exp:upgrade --format=json                    the machine output exp-upgrade/1

Exit codes: 0 done or nothing pending, 1 a step failed (or pending with --fail-on-pending),
2 usage error, 3 database not reachable, 4 internal error.
Guide: doc/eng-us/guides/upgrading-with-the-console.md
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--stages` | erforderlich | Nur diese Stufen, eine Komma-Liste aus code, libraries, database, scripts, finish (in dieser Reihenfolge) |
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
| `--no-deploy` |  | Ohne exp:velocity deploy abschließen: nur die Autoloads der Erweiterungen und die INI-, Template- und Inhalts-Caches |
| `--url` | erforderlich | Die Adressen, die die Rauchprobe nach dem Deploy abruft (Komma-Liste oder wiederholt); Standard die Startseite von SiteURL |
| `--to` | erforderlich | Die Code-Stufe bringt die Checkouts auf diesen Tag, Branch oder Commit (Standard: den Upstream des Branches) |
| `--repos` | erforderlich | Weitere Git-Checkouts, die die Code-Stufe bewegt, Pfade in der Installation (Komma-Liste oder wiederholt) |
| `--update` | erforderlich | composer update für diese Pakete, mit ihren Abhängigkeiten (Komma-Liste oder wiederholt) |
| `--composer` | erforderlich | Das Composer-Programm oder composer.phar (Standard: composer im PATH, sonst composer.phar in der Wurzel) |
| `--no-audit` |  | Die Sicherheitsprüfung der Bibliotheken (composer audit) vorher und nachher überspringen |
| `--allow-vendor-changes` |  | Die Bibliotheken installieren, obwohl Composer lokale Änderungen in vendor/ meldet |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/upgrade.php`, `kernel/private/classes/commands/upgrade.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
