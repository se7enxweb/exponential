---
title: exp:setupfiles
audience: developer, admin
category: examples
summary: Kopiert .htaccess_root (oder .htaccess_root_static) nach .htaccess und config.php-RECOMMENDED nach config.php, mit Diff, Sicherung und --force für eine vorhandene Datei (Probelauf ohne --apply).
keywords: Konsole, Befehl, exp:setupfiles, setupfiles, console, command, CLI, setupfiles.php, exp:setup:htaccess, exp:setup:config, exp:setup:status
type: generated
generated: exp:help:examples
---
# exp:setupfiles

Kopiert .htaccess_root (oder .htaccess_root_static) nach .htaccess und config.php-RECOMMENDED nach config.php, mit Diff, Sicherung und --force für eine vorhandene Datei (Probelauf ohne --apply).

Skript `bin/php/setupfiles.php`, Code in `kernel/private/classes/commands/setupfiles.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:setupfiles --help
./console exp:setup:htaccess --help
./console exp:setup:config --help
./console exp:setup:status --help
php bin/php/setupfiles.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Put .htaccess and config.php into place from the templates of the installation

  ./console exp:setup:htaccess [--static] [--force] [--apply]   .htaccess_root (--static: .htaccess_root_static) -> .htaccess
  ./console exp:setup:config [--force] [--apply]               config.php-RECOMMENDED -> config.php
  ./console exp:setup:status                                  which template .htaccess and config.php come from

Nothing is written without --apply: the run shows what would be copied. --dry-run is the default
and may be given; --apply with --dry-run is a usage error (exit 2). An existing file that
differs from the template is shown as a diff (40 lines, -v all) and left alone unless --force; with --force --apply
it is backed up to var/backup/setup/<time>/ (mode and owner kept) and replaced.
A new file gets the template's mode and, run as root, the template's owner and group (as cp -a),
an existing one keeps its own; --mode and --owner set them. -v prints each copy as cp -v does.
Prints PASS or FAIL at the end; exit 1 when a file was refused or could not be written.
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--apply` |  | Schreiben: die Datei kopieren (ohne die Option wird nur gezeigt, was kopiert würde) |
| `--dry-run` |  | Der Standard, erhalten für Skripte, die die Option übergeben: zeigen, was kopiert würde, nichts ändern |
| `--force` |  | Eine vorhandene Datei ersetzen, die von der Vorlage abweicht (sie wird vorher gesichert) |
| `--static` |  | htaccess: .htaccess_root_static kopieren, die Regeln, die den statischen Cache ausliefern, statt .htaccess_root |
| `--root` | erforderlich | Das Wurzelverzeichnis der Installation (Standard: die Installation, zu der der Befehl gehört) |
| `--owner` | erforderlich | Eigentümer der geschriebenen Datei, benutzer oder benutzer:gruppe (nur root kann sie einem anderen Benutzer geben) |
| `--mode` | erforderlich | Modus der geschriebenen Datei, etwa 0644 (Standard: eine vorhandene Datei behält ihren Modus, eine neue bekommt den der Vorlage) |
| `--show-secrets` |  | config: die Werte von Passwörtern, Geheimnissen, Schlüsseln und Tokens im Diff anzeigen |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[verb]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/setupfiles.php`, `kernel/private/classes/commands/setupfiles.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
