---
title: exp:expmigrationrollback
audience: developer, admin
category: examples
summary: "Nimmt Migrationen zurück: einen Lauf (sein Sicherungspunkt wird wiederhergestellt), eine Migration (ihr down()) oder, für Fachleute, jeden Sicherungspunkt; ein Probelauf ohne --apply."
keywords: Konsole, Befehl, exp:expmigrationrollback, expmigrationrollback, console, command, CLI, expmigrationrollback.php, exp:migration:rollback
type: generated
generated: exp:help:examples
---
# exp:expmigrationrollback

Nimmt Migrationen zurück: einen Lauf (sein Sicherungspunkt wird wiederhergestellt), eine Migration (ihr down()) oder, für Fachleute, jeden Sicherungspunkt; ein Probelauf ohne --apply.

Skript `bin/php/expmigrationrollback.php`, Code in `kernel/private/classes/commands/expmigrationrollback.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expmigrationrollback --help
./console exp:migration:rollback --help
php bin/php/expmigrationrollback.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Take migrations back
The argument names what to take back, found in this order:
  a run id         the caption taken before the run is restored (exp:upgrade's rollback: a safety
                   caption of the current state first, then database, storage and settings)
  a migration id   its down(), when it has one; the record of it is removed
  a caption id     any earlier caption, for experts: --expert --confirm=<the caption id>
Without --apply it lists what would be restored or undone. Oracle has no built-in restore: the
command prints the steps to restore by hand.

./console exp:migration:rollback 20261031-120000-a1b2c3
./console exp:migration:rollback 20261031-120000-a1b2c3 --apply
./console exp:migration:rollback 20261031120000_add_news_class --apply
./console exp:migration:rollback 2026-10-29_10-15-00 --expert --confirm=2026-10-29_10-15-00 --apply
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--apply` |  | Die Änderungen vornehmen (ohne die Option ein Probelauf, der sie auflistet und nichts ändert) |
| `--dry-run` |  | Der Standard, angenommen für Skripte, die ihn übergeben: auflisten, was sich ändern würde, nichts ändern |
| `--format` | erforderlich | text (Standard) oder json (die Maschinenausgabe exp-migration/1) |
| `--source` | erforderlich | Die Quelle: site oder extension:<Name> (generate: wohin die Datei kommt; rollback: welche Migration, wenn zwei Quellen ihre ID haben) |
| `--environment` | erforderlich | Die Umgebung (Standard die der Installation: [AdminEnvironment] Level, oder EXP_ENV bei Level=auto; check: jede Umgebung); nur die Migrationen für sie zählen |
| `--login` | erforderlich | Als dieser Benutzer ausführen (Standard: [RunSettings] RunAsUser in migration.ini) |
| `--expert` |  | Einen Sicherungspunkt wiederherstellen, der nicht der vor einem Lauf angelegte ist (mit --confirm) |
| `--confirm` | erforderlich | Die ID des Sicherungspunkts, noch einmal eingegeben, um sie mit --expert wiederherzustellen |
| `--parts` | erforderlich | Nur diese Teile des Sicherungspunkts, eine Komma-Liste aus database, storage, settings, libraries, code (Standard [CaptionSettings] RestoreParts) |
| `--backup-dir` | erforderlich | Wo die ohne git_manager angelegten Sicherungspunkte liegen (Standard var/backup/upgrade) |
| `--no-safety-caption` |  | Wiederherstellen, ohne zuerst einen Sicherungspunkt des aktuellen Zustands anzulegen; auf einer Produktivinstallation ohne --force-no-caption verweigert |
| `--force-no-caption` |  | Mit --no-safety-caption auf einer Produktivinstallation: trotzdem ohne Sicherungspunkt wiederherstellen |
| `--no-verify` |  | Die Prüfung nach der Wiederherstellung auslassen |
| `--progress` | erforderlich | Den Fortschritt des Laufs an diese Datei anhängen, ein JSON-Objekt pro Zeile (was die Administration zeigt, während der Lauf vor sich geht) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[target]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expmigrationrollback.php`, `kernel/private/classes/commands/expmigrationrollback.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
