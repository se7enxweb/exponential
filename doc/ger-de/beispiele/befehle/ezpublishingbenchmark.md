---
title: exp:ezpublishingbenchmark
audience: developer, admin
category: examples
summary: Benchmark parallel publishing in concurrent batches (--batches-count, -l level).
keywords: Konsole, Befehl, exp:ezpublishingbenchmark, ezpublishingbenchmark, console, command, CLI, ezpublishingbenchmark.php
type: generated
generated: exp:help:examples
---
# exp:ezpublishingbenchmark

Benchmark parallel publishing in concurrent batches (--batches-count, -l level).

Skript `bin/php/ezpublishingbenchmark.php`, Code in `kernel/private/classes/commands/ezpublishingbenchmark.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:ezpublishingbenchmark --help
php bin/php/ezpublishingbenchmark.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Parallel publishing benchmark

Runs concurrent batches of content publishing to measure how the installation copes with it.

Example: ./bin/php/ezpublishingbenchmark.php --concurrency-level=20 --batches-count=1
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `-b` | erforderlich |  |
| `--batches-count` | erforderlich | How many times a concurrent batch should be started [default: 1] |
| `-c` | erforderlich |  |
| `--content-class` | erforderlich | Identifier of the content class used for testing [default: article] |
| `-l` | erforderlich |  |
| `--concurrency-level` | erforderlich | Parallel processes to use [default: 20] |
| `-p` | erforderlich |  |
| `--parent-node` | erforderlich | Container content should be created in [default: 2] |
| `-g` |  |  |
| `--generate-content` |  | Wether content should  be generated or not (not fully supported yet) [default: off] |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/ezpublishingbenchmark.php`, `kernel/private/classes/commands/ezpublishingbenchmark.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
