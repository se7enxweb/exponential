---
title: exp:services
audience: developer, admin
category: examples
summary: Compile, list, show and check the kernel service container (verbs compile, list, show, check).
keywords: Konsole, Befehl, exp:services, services, console, command, CLI, services.php, exp:services:compile, exp:services:list, exp:services:show, exp:services:check
type: generated
generated: exp:help:examples
---
# exp:services

Compile, list, show and check the kernel service container (verbs compile, list, show, check).

Skript `bin/php/services.php`, Code in `kernel/private/classes/commands/services.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:services --help
./console exp:services:compile --help
./console exp:services:list --help
./console exp:services:show --help
./console exp:services:check --help
php bin/php/services.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Exponential kernel services

The service container of the kernel (settings/kernelservices.ini) and its compiled form
(var/cache/kernelservices/).

  ./console exp:services:compile [--only=site,admin]
  ./console exp:services:list [--tag=<tag>] [-s <siteaccess>]
  ./console exp:services:show <id> [-s <siteaccess>]
  ./console exp:services:check
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--json` |  | Das Ergebnis als JSON ausgeben. |
| `--tag` | erforderlich | list: only the services with this tag. |
| `--only` | erforderlich | compile: only these siteaccesses (comma separated); the files of the others are kept. |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[verb][id]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/services.php`, `kernel/private/classes/commands/services.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
