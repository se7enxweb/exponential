---
title: exp:solr
audience: developer, admin
category: examples
summary: Control the Solr search server, once it is installed and configured.
keywords: Konsole, Befehl, exp:solr, solr, console, command, CLI, solr.php, exp:search
type: generated
generated: exp:help:examples
---
# exp:solr

Control the Solr search server, once it is installed and configured.

Skript `bin/php/solr.php`, Code in `kernel/private/classes/commands/solr.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:solr --help
./console exp:search --help
php bin/php/solr.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--json` |  | Als JSON berichten, für einen Aufrufer, der kein Mensch ist |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[command]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/solr.php`, `kernel/private/classes/commands/solr.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
