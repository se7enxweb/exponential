---
title: exp:upgradecaptions
audience: developer, admin
category: examples
summary: "Upgrade-Sicherungspunkte: die sicheren Punkte zum Zurückrollen (von git_manager und von exp:upgrade), geprüft mit --verify, aufgeräumt mit --prune."
keywords: Konsole, Befehl, exp:upgradecaptions, upgradecaptions, console, command, CLI, upgradecaptions.php, exp:upgrade:captions
type: generated
generated: exp:help:examples
---
# exp:upgradecaptions

Upgrade-Sicherungspunkte: die sicheren Punkte zum Zurückrollen (von git_manager und von exp:upgrade), geprüft mit --verify, aufgeräumt mit --prune.

Skript `bin/php/upgradecaptions.php`, Code in `kernel/private/classes/commands/upgradecaptions.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:upgradecaptions --help
./console exp:upgrade:captions --help
php bin/php/upgradecaptions.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Upgrade captions
Every caption, newest first: id, kind (pre, post, safety, manual; git_manager for its own), time,
size, engine, label, and the command that rolls back to it. A caption is git_manager's full backup
(database, var/ storage) with what exp:upgrade adds: settings, composer.lock, the git commits and
checksums. --verify=<id> checks the checksums and restores the dump into a throwaway database.
--prune lists the captions of exp:upgrade that [UpgradeSettings] CaptionKeep, CaptionKeepDays and
CaptionKeepUpgrades let go, and removes them with --apply.

./console exp:upgrade:captions
./console exp:upgrade:captions --verify=2026-10-29_10-15-00
./console exp:upgrade:captions --prune --apply
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--format` | erforderlich | text (Standard) oder json (die Maschinenausgabe exp-upgrade/1) |
| `--verify` | erforderlich | Diesen Sicherungspunkt prüfen: seine Prüfsummen und das Einspielen seines Abzugs in eine Wegwerf-Datenbank |
| `--prune` |  | Die Sicherungspunkte von exp:upgrade jenseits von [UpgradeSettings] CaptionKeep, CaptionKeepDays, CaptionKeepUpgrades entfernen |
| `--apply` |  | Die Änderungen vornehmen (ohne die Option ein Probelauf, der sie auflistet und nichts ändert) |
| `--dry-run` |  | Der Standard, angenommen für Skripte, die ihn übergeben: auflisten, was sich ändern würde, nichts ändern |
| `--backup-dir` | erforderlich | Wohin die Sicherung geht (Standard var/backup/upgrade); darin wird ein Verzeichnis <Zeit> angelegt |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/upgradecaptions.php`, `kernel/private/classes/commands/upgradecaptions.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
