---
title: exp:clusterpurge
audience: developer, admin
category: examples
summary: Physically purge the cluster files of the given scopes (--scopes=scope1,scope2).
keywords: Konsole, Befehl, exp:clusterpurge, clusterpurge, console, command, CLI, clusterpurge.php
type: generated
generated: exp:help:examples
---
# exp:clusterpurge

Physically purge the cluster files of the given scopes (--scopes=scope1,scope2).

Skript `bin/php/clusterpurge.php`, Code in `kernel/private/classes/commands/clusterpurge.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:clusterpurge --help
php bin/php/clusterpurge.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--dry-run` |  | Test mode, output the list of affected files without removing them |
| `--iteration-sleep` | erforderlich | Amount of seconds to sleep between each iteration when performing a purge operation, can be a float. Default is one second. |
| `--iteration-limit` | erforderlich | Amount of items to remove in each iteration when performing a purge operation. Default is 100. |
| `--memory-monitoring` |  | Generates memory monitoring output. If set, memory usage will be logged in var/log/clusterpurge.log. |
| `--scopes` | erforderlich | Comma separated list of file types to purge. Possible values are: classattridentifiers, classidentifiers, content, expirycache, statelimitations, user-info-cache, viewcache, wildcard-cache-index, image, binaryfile, media. |
| `--expiry` | erforderlich | Number of days since the file was expired. Only files older than this will be purged. Default is 30, minimum is 1. |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/clusterpurge.php`, `kernel/private/classes/commands/clusterpurge.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
