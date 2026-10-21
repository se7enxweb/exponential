---
title: exp:clusterize
audience: developer, admin
category: examples
summary: Verschiebt die Dateien aus var in den Cluster-Speicher der Datenbank (-u verschiebt sie zurück).
keywords: Konsole, Befehl, exp:clusterize, clusterize, console, command, CLI, clusterize.php
type: generated
generated: exp:help:examples
---
# exp:clusterize

Verschiebt die Dateien aus var in den Cluster-Speicher der Datenbank (-u verschiebt sie zurück).

Skript `bin/php/clusterize.php`, Code in `kernel/private/classes/commands/clusterize.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:clusterize --help
php bin/php/clusterize.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `-u` |  | Aus dem Cluster zurückholen |
| `--skip-binary-files` |  | Binärdateien nicht kopieren |
| `--skip-media-files` |  | Mediendateien nicht kopieren |
| `--skip-images` |  | Bilder nicht kopieren |
| `-r` |  | Dateien nach dem Kopieren entfernen |
| `-n` |  | Nicht warten |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/clusterize.php`, `kernel/private/classes/commands/clusterize.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
