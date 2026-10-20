---
title: exp:clusterpurge
audience: developer, admin
category: examples
summary: Löscht die Cluster-Dateien der angegebenen Bereiche physisch (--scopes=scope1,scope2).
keywords: Konsole, Befehl, exp:clusterpurge, clusterpurge, console, command, CLI, clusterpurge.php
type: generated
generated: exp:help:examples
---
# exp:clusterpurge

Löscht die Cluster-Dateien der angegebenen Bereiche physisch (--scopes=scope1,scope2).

Skript `bin/php/clusterpurge.php`, Code in `kernel/private/classes/commands/clusterpurge.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:clusterpurge --help
php bin/php/clusterpurge.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--dry-run` |  | Testmodus: die Liste der betroffenen Dateien ausgeben, ohne sie zu entfernen |
| `--iteration-sleep` | erforderlich | Sekunden Pause zwischen den Iterationen beim Löschen, auch als Dezimalzahl. Standard ist eine Sekunde. |
| `--iteration-limit` | erforderlich | Anzahl der Einträge, die beim Löschen in jeder Iteration entfernt werden. Standard ist 100. |
| `--memory-monitoring` |  | Erzeugt eine Ausgabe zur Speicherüberwachung. Wenn gesetzt, wird der Speicherverbrauch in var/log/clusterpurge.log protokolliert. |
| `--scopes` | erforderlich | Durch Kommas getrennte Liste der zu löschenden Dateitypen. Mögliche Werte: classattridentifiers, classidentifiers, content, expirycache, statelimitations, user-info-cache, viewcache, wildcard-cache-index, image, binaryfile, media. |
| `--expiry` | erforderlich | Anzahl der Tage, seit die Datei abgelaufen ist. Nur ältere Dateien werden gelöscht. Standard ist 30, Minimum 1. |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/clusterpurge.php`, `kernel/private/classes/commands/clusterpurge.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
