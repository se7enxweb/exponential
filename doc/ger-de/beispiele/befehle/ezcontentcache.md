---
title: exp:ezcontentcache
audience: developer, admin
category: examples
summary: Clear content caches of given nodes or subtrees (--clear-node, --clear-subtree).
keywords: Konsole, Befehl, exp:ezcontentcache, ezcontentcache, console, command, CLI, ezcontentcache.php
type: generated
generated: exp:help:examples
---
# exp:ezcontentcache

Clear content caches of given nodes or subtrees (--clear-node, --clear-subtree).

Skript `bin/php/ezcontentcache.php`, Code in `kernel/private/classes/commands/ezcontentcache.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:ezcontentcache --help
php bin/php/ezcontentcache.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--clear-node` | erforderlich |  |
| `--clear-subtree` | erforderlich |  |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/ezcontentcache.php`, `kernel/private/classes/commands/ezcontentcache.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
