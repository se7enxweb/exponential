---
title: exp:maintenance
audience: developer, admin
category: examples
summary: "Take the site offline for maintenance and back: on, off or status."
keywords: Konsole, Befehl, exp:maintenance, maintenance, console, command, CLI, maintenance.php
type: generated
generated: exp:help:examples
---
# exp:maintenance

Take the site offline for maintenance and back: on, off or status.

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
| `--message` | erforderlich | What the page says, instead of the default text |
| `--until` | erforderlich | When the site is expected back: 30m, 2h, or a date and time |
| `--allow-ip` | erforderlich | Addresses that still see the site, comma separated |
| `--allow-admin` |  | Keep the administration (/admin) reachable |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[action]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/maintenance.php`, `kernel/private/classes/commands/maintenance.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
