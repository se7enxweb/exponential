---
title: exp:upgradeverify
audience: developer, admin
category: examples
summary: "Upgrade prüfen: jeden Schritt neu erkennen und Abweichungen melden (ein als erledigt aufgezeichneter Schritt, dessen Wirkung fehlt)."
keywords: Konsole, Befehl, exp:upgradeverify, upgradeverify, console, command, CLI, upgradeverify.php, exp:upgrade:verify
type: generated
generated: exp:help:examples
---
# exp:upgradeverify

Upgrade prüfen: jeden Schritt neu erkennen und Abweichungen melden (ein als erledigt aufgezeichneter Schritt, dessen Wirkung fehlt).

Skript `bin/php/upgradeverify.php`, Code in `kernel/private/classes/commands/upgradeverify.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:upgradeverify --help
./console exp:upgrade:verify --help
php bin/php/upgradeverify.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Upgrade verify
Looks at every step again and lists drift: a step exp:upgrade recorded as done whose effect is
missing now (a table dropped, a policy removed). Exit 1 when there is drift. Changes nothing.

./console exp:upgrade:verify
./console exp:upgrade:verify --format=json
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--stages` | erforderlich | Nur diese Stufen, eine Komma-Liste aus code, libraries, database, scripts, finish (in dieser Reihenfolge) |
| `--format` | erforderlich | text (Standard) oder json (die Maschinenausgabe exp-upgrade/1) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/upgradeverify.php`, `kernel/private/classes/commands/upgradeverify.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
