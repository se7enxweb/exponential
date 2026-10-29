---
title: exp:upgradecode
audience: developer, admin
category: examples
summary: "Upgrade-Stufe 1, Code: die Git-Checkouts auf das Ziel-Release gebracht, nur Fast-Forward, über git_manager (Probelauf ohne --apply)."
keywords: Konsole, Befehl, exp:upgradecode, upgradecode, console, command, CLI, upgradecode.php, exp:upgrade:code
type: generated
generated: exp:help:examples
---
# exp:upgradecode

Upgrade-Stufe 1, Code: die Git-Checkouts auf das Ziel-Release gebracht, nur Fast-Forward, über git_manager (Probelauf ohne --apply).

Skript `bin/php/upgradecode.php`, Code in `kernel/private/classes/commands/upgradecode.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:upgradecode --help
./console exp:upgrade:code --help
php bin/php/upgradecode.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Upgrade stage 1: the code
Moves the installation's git checkout (and each --repos checkout) to --to (a tag, a branch or a
commit) or to the upstream of its branch, fast-forward only, through git_manager; the submodules
follow. Never over local changes, never when the checkout has commits the target lacks, and only
as the owner of the checkout. git_manager's backup is made first (unless --no-backup), and the
commits before the move are kept for exp:upgrade:rollback. The dry run fetches (remote-tracking
refs only). Dry run unless --apply.

./console exp:upgrade:code --to=v6.0.16
./console exp:upgrade:code --apply --to=v6.0.16 --repos=extension/myext
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
| `--to` | erforderlich | Die Code-Stufe bringt die Checkouts auf diesen Tag, Branch oder Commit (Standard: den Upstream des Branches) |
| `--repos` | erforderlich | Weitere Git-Checkouts, die die Code-Stufe bewegt, Pfade in der Installation (Komma-Liste oder wiederholt) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/upgradecode.php`, `kernel/private/classes/commands/upgradecode.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
