---
title: exp:upgradestatus
audience: developer, admin
category: examples
summary: "Upgrade-Status: jeder Upgrade-Schritt mit seinem Zustand (done, pending, not-needed, failed, unknown)."
keywords: Konsole, Befehl, exp:upgradestatus, upgradestatus, console, command, CLI, upgradestatus.php, exp:upgrade:status
type: generated
generated: exp:help:examples
---
# exp:upgradestatus

Upgrade-Status: jeder Upgrade-Schritt mit seinem Zustand (done, pending, not-needed, failed, unknown).

Skript `bin/php/upgradestatus.php`, Code in `kernel/private/classes/commands/upgradestatus.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:upgradestatus --help
./console exp:upgrade:status --help
php bin/php/upgradestatus.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Upgrade status
Every upgrade step, stage by stage, with its state as the installation shows it: done (applied
by exp:upgrade), not-needed (in place without it, or not for this installation), pending, failed
or unknown, and how it was told. Changes nothing, takes no lock.

./console exp:upgrade:status
./console exp:upgrade:status --stages=database --format=json
./console exp:upgrade:status --fail-on-pending     exit 1 when something is pending, for CI
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--stages` | erforderlich | Nur diese Stufen, eine Komma-Liste aus code, libraries, database, scripts, finish (in dieser Reihenfolge) |
| `--only` | erforderlich | Nur diese Schritte (Schritt-IDs, als Komma-Liste oder wiederholt); ein optionaler Schritt läuft nur, wenn er hier genannt ist |
| `--format` | erforderlich | text (Standard) oder json (die Maschinenausgabe exp-upgrade/1) |
| `--fail-on-pending` |  | Exit-Code 1, wenn noch Schritte offen sind (für CI) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/upgradestatus.php`, `kernel/private/classes/commands/upgradestatus.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
