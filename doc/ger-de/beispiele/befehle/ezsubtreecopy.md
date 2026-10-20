---
title: exp:ezsubtreecopy
audience: developer, admin
category: examples
summary: Copy a content subtree to a new location (--src-node-id, --dst-node-id).
keywords: Konsole, Befehl, exp:ezsubtreecopy, ezsubtreecopy, console, command, CLI, ezsubtreecopy.php
type: generated
generated: exp:help:examples
---
# exp:ezsubtreecopy

Copy a content subtree to a new location (--src-node-id, --dst-node-id).

Skript `bin/php/ezsubtreecopy.php`, Code in `kernel/private/classes/commands/ezsubtreecopy.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:ezsubtreecopy --help
php bin/php/ezsubtreecopy.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--src-node-id` | erforderlich | Source subtree parent node ID. |
| `--dst-node-id` | erforderlich | Destination node ID. |
| `--all-versions` |  | Copy all versions for each contentobject being copied. |
| `--keep-creator` |  | Do not change the creator (user) for the copied content objects. |
| `--keep-time` |  | Do not change the creation and modification time of the copied content objects. |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/ezsubtreecopy.php`, `kernel/private/classes/commands/ezsubtreecopy.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
