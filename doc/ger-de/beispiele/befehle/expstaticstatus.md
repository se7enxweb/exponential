---
title: exp:expstaticstatus
audience: developer, admin
category: examples
summary: "Der Zustand eines Ziels des statischen Veröffentlichens: sein Git-Repository, die Zugangsdaten (nie ihr Wert), die letzten Läufe, wartende Pushes und veraltete Seiten."
keywords: Konsole, Befehl, exp:expstaticstatus, expstaticstatus, console, command, CLI, expstaticstatus.php, exp:static:status
type: generated
generated: exp:help:examples
---
# exp:expstaticstatus

Der Zustand eines Ziels des statischen Veröffentlichens: sein Git-Repository, die Zugangsdaten (nie ihr Wert), die letzten Läufe, wartende Pushes und veraltete Seiten.

Skript `bin/php/expstaticstatus.php`, Code in `kernel/private/classes/commands/expstaticstatus.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expstaticstatus --help
./console exp:static:status --help
php bin/php/expstaticstatus.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
The state of a static publishing target
Shows the target's git repository (branch, last commit, last tag static/<target>/<n>, changes not
published, commits not pushed), whether its credential can be read (never its value), the last
publishes and rollbacks of exp_static_run with their push state, and the pages of exp_static_page
with how many are stale.

./console exp:static:status github-public
./console exp:static:status github-public --limit=30 --format=json
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--limit` | erforderlich | Wie viele der letzten Läufe aufgeführt werden (Standard 10) |
| `--format` | erforderlich | text (Standard) oder json (die Maschinenausgabe exp-static/1) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[target]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expstaticstatus.php`, `kernel/private/classes/commands/expstaticstatus.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
