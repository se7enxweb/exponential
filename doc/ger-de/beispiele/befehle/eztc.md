---
title: exp:eztc
audience: developer, admin
category: examples
summary: Compile the templates of a siteaccess into the template cache (--force recompiles all).
keywords: Konsole, Befehl, exp:eztc, eztc, console, command, CLI, eztc.php
type: generated
generated: exp:help:examples
---
# exp:eztc

Compile the templates of a siteaccess into the template cache (--force recompiles all).

Skript `bin/php/eztc.php`, Code in `kernel/private/classes/commands/eztc.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:eztc --help
php bin/php/eztc.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--compile-directory` | erforderlich | Where to place compiled files, default is template/compiled in current cache directory |
| `--www-dir` | erforderlich | The part before the index.php in your URL, you should supply this if you are running in non-virtualhost mode |
| `--index-file` | erforderlich | The name of your index.php if you are running in non-virtualhost mode |
| `--access-path` | erforderlich | Extra access path |
| `--force` |  | Force compilation of template whether it has changed or not |
| `--full-url` |  | Makes sure generated urls have http:// in them (i.e. global), used mainly by sites that include the Exponential HTML (e.g payment gateways) |
| `--no-full-url` |  | Makes sure generated urls are relative to the site. (default) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/eztc.php`, `kernel/private/classes/commands/eztc.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
