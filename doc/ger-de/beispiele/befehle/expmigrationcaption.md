---
title: exp:expmigrationcaption
audience: developer, admin
category: examples
summary: Legt jetzt einen Sicherungspunkt (Caption) der Installation an (Datenbank, Speicher, Einstellungen, die Commits des Codes) oder listet die Sicherungspunkte; ein Probelauf ohne --apply.
keywords: Konsole, Befehl, exp:expmigrationcaption, expmigrationcaption, console, command, CLI, expmigrationcaption.php, exp:migration:caption
type: generated
generated: exp:help:examples
---
# exp:expmigrationcaption

Legt jetzt einen Sicherungspunkt (Caption) der Installation an (Datenbank, Speicher, Einstellungen, die Commits des Codes) oder listet die Sicherungspunkte; ein Probelauf ohne --apply.

Skript `bin/php/expmigrationcaption.php`, Code in `kernel/private/classes/commands/expmigrationcaption.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expmigrationcaption --help
./console exp:migration:caption --help
php bin/php/expmigrationcaption.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Take a caption now
A caption of the kind manual, under the installation lock: git_manager's full backup (database and
var/ storage) where it is installed, else exp:upgrade's own dump, with settings/override and the
commits of the code. It is a point exp:migration:rollback --expert and exp:upgrade:rollback can go
back to. --list lists the captions, newest first, with the command that restores each.

./console exp:migration:caption --list
./console exp:migration:caption --apply
./console exp:migration:caption --label="before the news import" --apply
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--apply` |  | Die Änderungen vornehmen (ohne die Option ein Probelauf, der sie auflistet und nichts ändert) |
| `--dry-run` |  | Der Standard, angenommen für Skripte, die ihn übergeben: auflisten, was sich ändern würde, nichts ändern |
| `--format` | erforderlich | text (Standard) oder json (die Maschinenausgabe exp-migration/1) |
| `--list` |  | Die Sicherungspunkte auflisten, statt einen anzulegen |
| `--label` | erforderlich | Die Bezeichnung des Sicherungspunkts oder der Aufzeichnung (ein Sicherungspunkt ohne eine: "manual caption" mit der Uhrzeit) |
| `--backup-dir` | erforderlich | Wo die ohne git_manager angelegten Sicherungspunkte liegen (Standard var/backup/upgrade) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expmigrationcaption.php`, `kernel/private/classes/commands/expmigrationcaption.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
