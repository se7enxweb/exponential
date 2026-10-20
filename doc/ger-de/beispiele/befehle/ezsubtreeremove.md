---
title: exp:ezsubtreeremove
audience: developer, admin
category: examples
summary: Entfernt Teilbäume von Inhalten nach Knoten-ID, in den Papierkorb, außer --ignore-trash ist angegeben.
keywords: Konsole, Befehl, exp:ezsubtreeremove, ezsubtreeremove, console, command, CLI, ezsubtreeremove.php
type: generated
generated: exp:help:examples
---
# exp:ezsubtreeremove

Entfernt Teilbäume von Inhalten nach Knoten-ID, in den Papierkorb, außer --ignore-trash ist angegeben.

Skript `bin/php/ezsubtreeremove.php`, Code in `kernel/private/classes/commands/ezsubtreeremove.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:ezsubtreeremove --help
php bin/php/ezsubtreeremove.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--nodes-id` | erforderlich | IDs der Teilbaumknoten (durch Komma ',' getrennt). |
| `--ignore-trash` |  | Papierkorb übergehen (standardmäßig 'in den Papierkorb verschieben'). |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/ezsubtreeremove.php`, `kernel/private/classes/commands/ezsubtreeremove.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
