---
title: exp:upgradefinish
audience: developer, admin
category: examples
summary: "Upgrade-Stufe 5, Abschluss: die Änderungen bereitstellen (exp:velocity deploy --kernel) und prüfen, dass die Site antwortet (Probelauf ohne --apply)."
keywords: Konsole, Befehl, exp:upgradefinish, upgradefinish, console, command, CLI, upgradefinish.php, exp:upgrade:finish
type: generated
generated: exp:help:examples
---
# exp:upgradefinish

Upgrade-Stufe 5, Abschluss: die Änderungen bereitstellen (exp:velocity deploy --kernel) und prüfen, dass die Site antwortet (Probelauf ohne --apply).

Skript `bin/php/upgradefinish.php`, Code in `kernel/private/classes/commands/upgradefinish.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:upgradefinish --help
./console exp:upgrade:finish --help
php bin/php/upgradefinish.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Upgrade stage 5: finish
Makes the changes live with ./console exp:velocity deploy --kernel (autoloads, INI and template
caches, PHP-FPM, Velocity, the output caches, the help content cache), then requests the front
page (or each --url) and fails when it does not answer 200 to 399. --no-deploy: autoloads and
caches only. Run on its own it always has work; inside exp:upgrade only when the run changed
something. Dry run unless --apply.

./console exp:upgrade:finish --apply
./console exp:upgrade:finish --apply --no-deploy
./console exp:upgrade:finish --apply --url=https://www.example.com/,https://admin.example.com/
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
| `--no-deploy` |  | Ohne exp:velocity deploy abschließen: nur die Autoloads der Erweiterungen und die INI-, Template- und Inhalts-Caches |
| `--url` | erforderlich | Die Adressen, die die Rauchprobe nach dem Deploy abruft (Komma-Liste oder wiederholt); Standard die Startseite von SiteURL |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/upgradefinish.php`, `kernel/private/classes/commands/upgradefinish.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
