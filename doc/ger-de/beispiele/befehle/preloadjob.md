---
title: exp:preloadjob
audience: developer, admin
category: examples
summary: Der Befehl exp:preloadjob (bin/php/preloadjob.php).
keywords: Konsole, Befehl, exp:preloadjob, preloadjob, console, command, CLI, preloadjob.php
type: generated
generated: exp:help:examples
---
# exp:preloadjob


Skript `bin/php/preloadjob.php`, Code in `kernel/private/classes/commands/preloadjob.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:preloadjob --help
php bin/php/preloadjob.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Runs one preload for Setup > Preload and writes its events and status.
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--id` | erforderlich | die Auftrags-ID (hex), von setup/preloadjob gewählt |
| `--target` | erforderlich | der Siteaccess, dessen Website vorgewärmt wird (Standard: DefaultAccess) |
| `--max-pages` | erforderlich | höchstens so viele Seiten abrufen (Standard 250) |
| `--max-depth` | erforderlich | höchstens so viele Links von einer Startseite aus folgen (Standard 3) |
| `--images` |  | auch die Bilder anfordern, die die vorgewärmten Seiten zeigen, und die fehlenden melden |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/preloadjob.php`, `kernel/private/classes/commands/preloadjob.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
