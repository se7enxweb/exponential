---
title: exp:preload
audience: developer, admin
category: examples
summary: "Die Seiten-Caches einer Site vorwärmen: ihre Abschnittsseiten abrufen, dann ihren Links folgen und die defekten melden (Probelauf, außer mit --apply)."
keywords: Konsole, Befehl, exp:preload, preload, console, command, CLI, preload.php
type: generated
generated: exp:help:examples
---
# exp:preload

Die Seiten-Caches einer Site vorwärmen: ihre Abschnittsseiten abrufen, dann ihren Links folgen und die defekten melden (Probelauf, außer mit --apply).

Skript `bin/php/preload.php`, Code in `kernel/private/classes/commands/preload.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:preload --help
php bin/php/preload.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Exponential cache preloader

Requests the section pages of a site, then follows its links, so the caches are warm
before a visitor arrives, and reports the broken links with the pages that link to them.
The site is the one of --siteaccess (or --target). One preload runs at a time; the run is
listed in Setup > Preload.
Without --apply nothing is requested: the script prints the address, the starting pages and
the limits. --dry-run means the same as no flag; --apply together with --dry-run is a usage
error (exit 2).

Usage: php bin/php/preload.php --siteaccess=<name> [--max-pages=<n>] [--max-depth=<n>] [--images] [--apply]

Example: php bin/php/preload.php --siteaccess=site
         php bin/php/preload.php --siteaccess=site --apply
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--apply` |  | die Seiten abrufen (Standard ist ein Probelauf, der den Plan ausgibt und nichts abruft) |
| `--dry-run` |  |  |
| `--target` | erforderlich | der Siteaccess, dessen Website vorgewärmt wird (Standard: der von --siteaccess) |
| `--max-pages` | erforderlich | höchstens so viele Seiten anfordern (Standard  |
| `--max-depth` | erforderlich |  |
| `--images` |  |  |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/preload.php`, `kernel/private/classes/commands/preload.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
