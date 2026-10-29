---
title: exp:upgradeverify
audience: developer, admin
category: examples
summary: "Upgrade prüfen: jeden Schritt neu erkennen und Abweichungen melden; --after: die ganze Prüfung nach einem Upgrade (Schritte, Rauchprobe-Adressen, Tests, Fehlerprotokoll, Autoloads)."
keywords: Konsole, Befehl, exp:upgradeverify, upgradeverify, console, command, CLI, upgradeverify.php, exp:upgrade:verify
type: generated
generated: exp:help:examples
---
# exp:upgradeverify

Upgrade prüfen: jeden Schritt neu erkennen und Abweichungen melden; --after: die ganze Prüfung nach einem Upgrade (Schritte, Rauchprobe-Adressen, Tests, Fehlerprotokoll, Autoloads).

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
With --after, the check exp:upgrade --apply runs at its end: the steps (no drift, none failed),
the smoke addresses ([UpgradeSettings] SmokeURL[], else velocity.ini WarmUpURL[], else the front
page and the sign-in page: Apache and Velocity), the test suite upgrade-smoke, no new fatal line in
the error logs since --since, and the autoloads. Exit 1 when a check fails.

./console exp:upgrade:verify
./console exp:upgrade:verify --after
./console exp:upgrade:verify --after --url=https://www.example.com/ --no-tests --format=json
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--stages` | erforderlich | Nur diese Stufen, eine Komma-Liste aus code, libraries, database, scripts, finish (in dieser Reihenfolge) |
| `--format` | erforderlich | text (Standard) oder json (die Maschinenausgabe exp-upgrade/1) |
| `--after` |  | Die Prüfung nach einem Upgrade: Schritte, Rauchprobe-Adressen, Tests, Fehlerprotokoll, Autoloads |
| `--url` | erforderlich | Die Adressen, die die Rauchprobe nach dem Deploy abruft (Komma-Liste oder wiederholt); Standard die Startseite von SiteURL |
| `--no-tests` |  | Die Testsuite upgrade-smoke aus der Prüfung weglassen |
| `--since` | erforderlich | Das Fehlerprotokoll wird ab diesem Zeitpunkt gelesen: eine Unix-Zeit (Standard vor einer Stunde) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/upgradeverify.php`, `kernel/private/classes/commands/upgradeverify.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
