---
title: exp:upgradelibraries
audience: developer, admin
category: examples
summary: "Upgrade-Stufe 2, Bibliotheken: composer install aus composer.lock (oder ein Update benannter Pakete) mit Sicherheitsprüfung (Probelauf ohne --apply)."
keywords: Konsole, Befehl, exp:upgradelibraries, upgradelibraries, console, command, CLI, upgradelibraries.php, exp:upgrade:libraries
type: generated
generated: exp:help:examples
---
# exp:upgradelibraries

Upgrade-Stufe 2, Bibliotheken: composer install aus composer.lock (oder ein Update benannter Pakete) mit Sicherheitsprüfung (Probelauf ohne --apply).

Skript `bin/php/upgradelibraries.php`, Code in `kernel/private/classes/commands/upgradelibraries.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:upgradelibraries --help
./console exp:upgrade:libraries --help
php bin/php/upgradelibraries.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Upgrade stage 2: the libraries
Makes vendor/ hold what composer.lock names (composer install, with the dev packages only when
vendor/ had them), or with --update composer update of the named packages first. The security
audit (composer audit) runs before and after and reports; it never stops the run. Refused when
Composer reports local changes in vendor/ (--allow-vendor-changes installs over them) and when
the process is not the owner of the installation. Dry run unless --apply.

./console exp:upgrade:libraries
./console exp:upgrade:libraries --apply
./console exp:upgrade:libraries --apply --update=guzzlehttp/guzzle
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
| `--no-caption` |  | Schreiben, ohne vorher einen Sicherungspunkt anzulegen (dasselbe wie --no-backup); auf Produktion ohne --force-no-caption verweigert |
| `--force-no-caption` |  | Mit --no-caption auf einer Produktions-Installation: trotzdem ohne Sicherungspunkt schreiben |
| `--update` | erforderlich | composer update für diese Pakete, mit ihren Abhängigkeiten (Komma-Liste oder wiederholt) |
| `--composer` | erforderlich | Das Composer-Programm oder composer.phar (Standard: composer im PATH, sonst composer.phar in der Wurzel) |
| `--no-audit` |  | Die Sicherheitsprüfung der Bibliotheken (composer audit) vorher und nachher überspringen |
| `--allow-vendor-changes` |  | Die Bibliotheken installieren, obwohl Composer lokale Änderungen in vendor/ meldet |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/upgradelibraries.php`, `kernel/private/classes/commands/upgradelibraries.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
