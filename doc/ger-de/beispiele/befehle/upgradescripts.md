---
title: exp:upgradescripts
audience: developer, admin
category: examples
summary: "Upgrade-Stufe 4, Skripte: die PHP-Upgrade-Schritte, Richtlinien und Reparaturen (Probelauf ohne --apply)."
keywords: Konsole, Befehl, exp:upgradescripts, upgradescripts, console, command, CLI, upgradescripts.php, exp:upgrade:scripts
type: generated
generated: exp:help:examples
---
# exp:upgradescripts

Upgrade-Stufe 4, Skripte: die PHP-Upgrade-Schritte, Richtlinien und Reparaturen (Probelauf ohne --apply).

Skript `bin/php/upgradescripts.php`, Code in `kernel/private/classes/commands/upgradescripts.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:upgradescripts --help
./console exp:upgrade:scripts --help
php bin/php/upgradescripts.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Upgrade stage 4: the scripts
The PHP upgrade steps: the scripts of update/common/scripts/ and the setup commands (editor
policies, member management, user approval, ...), run through the same classes with their
default options. Refused while the database stage has pending steps (--force-order runs
anyway). Dry run unless --apply.

./console exp:upgrade:scripts
./console exp:upgrade:scripts --apply --skip=6.0.15-editor-policies
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

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/upgradescripts.php`, `kernel/private/classes/commands/upgradescripts.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
