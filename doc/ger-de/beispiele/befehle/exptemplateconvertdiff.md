---
title: exp:exptemplateconvertdiff
audience: developer, admin
category: examples
summary: Gibt die Konvertierung eines Templates in eine andere Engine auf stdout aus, ohne etwas zu schreiben (--file, --to-engine, --from-engine).
keywords: Konsole, Befehl, exp:exptemplateconvertdiff, exptemplateconvertdiff, console, command, CLI, exptemplateconvertdiff.php, exp:template:convert:diff
type: generated
generated: exp:help:examples
---
# exp:exptemplateconvertdiff

Gibt die Konvertierung eines Templates in eine andere Engine auf stdout aus, ohne etwas zu schreiben (--file, --to-engine, --from-engine).

Skript `bin/php/exptemplateconvertdiff.php`, Code in `kernel/private/classes/commands/exptemplateconvertdiff.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:exptemplateconvertdiff --help
./console exp:template:convert:diff --help
php bin/php/exptemplateconvertdiff.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Print the conversion of one template to another engine, without writing anything
The engine of --file follows from its ending unless --from-engine is given. The converted text goes
to stdout, the NOTE lines of what could not be converted exactly to stderr; with --against=<file> the
converted text is compared with that file (a target written before and perhaps edited by hand) and
the differences are printed as a unified diff.

./console exp:template:convert:diff --file=design/<design>/templates/<name>.tpl --to-engine=twig
./console exp:template:convert:diff --file=<name>.tpl --to-engine=twig --against=<name>.twig

Exit code: 0 converted (no differences with --against); 1 notes, a check failed, or differences;
2 wrong options; 3 the conversion is not available.
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--file` | erforderlich | das zu konvertierende Template |
| `--from-engine` | erforderlich | seine Engine: tpl, stpl oder twig (Standard: aus der Dateiendung) |
| `--to-engine` | erforderlich | die Engine, in die es konvertiert wird: tpl, stpl oder twig |
| `--against` | erforderlich | die Konvertierung mit dieser Datei vergleichen und einen Unified Diff ausgeben |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/exptemplateconvertdiff.php`, `kernel/private/classes/commands/exptemplateconvertdiff.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
