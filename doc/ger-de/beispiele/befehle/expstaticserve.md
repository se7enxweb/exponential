---
title: exp:expstaticserve
audience: developer, admin
category: examples
summary: "Den statischen Build eines Ziels lokal ansehen: der eingebaute Webserver von PHP liefert seinen Ordner unterhalb des Basispfads des Ziels aus, wie GitHub Pages eine Projekt-Website ausliefert."
keywords: Konsole, Befehl, exp:expstaticserve, expstaticserve, console, command, CLI, expstaticserve.php, exp:static:serve
type: generated
generated: exp:help:examples
---
# exp:expstaticserve

Den statischen Build eines Ziels lokal ansehen: der eingebaute Webserver von PHP liefert seinen Ordner unterhalb des Basispfads des Ziels aus, wie GitHub Pages eine Projekt-Website ausliefert.

Skript `bin/php/expstaticserve.php`, Code in `kernel/private/classes/commands/expstaticserve.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expstaticserve --help
./console exp:static:serve --help
php bin/php/expstaticserve.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Preview a static build
Serves the target's folder with PHP's built-in web server, below the target's base path (/my-site/)
the way GitHub Pages serves a project site: a folder answers with its index.html, a missing page
with 404.html. Listens on this computer only unless --host says otherwise; stop it with Ctrl+C.

./console exp:static:serve github-public                    http://127.0.0.1:8090/my-site/
./console exp:static:serve github-public --base-path=/      at the root, as with a custom domain
./console exp:static:serve github-public --port=9000
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--port` | erforderlich | Der Port des Vorschauservers (Standard 8090) |
| `--host` | erforderlich | Die Adresse, auf der der Vorschauserver lauscht (Standard 127.0.0.1, nur dieser Rechner) |
| `--base-path` | erforderlich | Den Ordner unterhalb dieses Pfads ausliefern (Standard BasePath des Ziels; / für die Wurzel) |
| `--format` | erforderlich | text (Standard) oder json (die Maschinenausgabe exp-static/1) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[target]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expstaticserve.php`, `kernel/private/classes/commands/expstaticserve.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
