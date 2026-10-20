---
title: exp:ezsubtreecopy
audience: developer, admin
category: examples
summary: Kopiert einen Teilbaum von Inhalten an einen neuen Ort (--src-node-id, --dst-node-id).
keywords: Konsole, Befehl, exp:ezsubtreecopy, ezsubtreecopy, console, command, CLI, ezsubtreecopy.php
type: generated
generated: exp:help:examples
---
# exp:ezsubtreecopy

Kopiert einen Teilbaum von Inhalten an einen neuen Ort (--src-node-id, --dst-node-id).

Skript `bin/php/ezsubtreecopy.php`, Code in `kernel/private/classes/commands/ezsubtreecopy.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:ezsubtreecopy --help
php bin/php/ezsubtreecopy.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--src-node-id` | erforderlich | ID des Elternknotens des Quell-Teilbaums. |
| `--dst-node-id` | erforderlich | ID des Zielknotens. |
| `--all-versions` |  | Alle Versionen jedes kopierten Inhaltsobjekts kopieren. |
| `--keep-creator` |  | Den Ersteller (Benutzer) der kopierten Inhaltsobjekte nicht ändern. |
| `--keep-time` |  | Die Zeitpunkte der Erstellung und Änderung der kopierten Inhaltsobjekte nicht ändern. |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/ezsubtreecopy.php`, `kernel/private/classes/commands/ezsubtreecopy.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
