---
title: exp:maintenance
audience: developer, admin
category: examples
summary: "Nimmt die Website für Wartungsarbeiten vom Netz und wieder zurück: on, off oder status."
keywords: Konsole, Befehl, exp:maintenance, maintenance, console, command, CLI, maintenance.php
type: generated
generated: exp:help:examples
---
# exp:maintenance

Nimmt die Website für Wartungsarbeiten vom Netz und wieder zurück: on, off oder status.

Skript `bin/php/maintenance.php`, Code in `kernel/private/classes/commands/maintenance.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:maintenance --help
php bin/php/maintenance.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Maintenance mode: take the site offline and back

  maintenance.php on|off|status [options]
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--message` | erforderlich | Was die Seite sagt, statt des Standardtextes |
| `--until` | erforderlich | Wann die Website zurück sein soll: 30m, 2h oder ein Datum mit Uhrzeit |
| `--allow-ip` | erforderlich | Adressen, die die Website weiterhin sehen, durch Kommas getrennt |
| `--allow-admin` |  | Die Administration (/admin) erreichbar lassen |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[action]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/maintenance.php`, `kernel/private/classes/commands/maintenance.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
