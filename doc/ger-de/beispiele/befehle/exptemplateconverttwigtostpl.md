---
title: exp:exptemplateconverttwigtostpl
audience: developer, admin
category: examples
summary: Konvertiert die Twig-Templates (.twig) eines Designs in Smarty-Templates (.stpl); --dry-run, --file, --force, --override-ini.
keywords: Konsole, Befehl, exp:exptemplateconverttwigtostpl, exptemplateconverttwigtostpl, console, command, CLI, exptemplateconverttwigtostpl.php, exp:template:convert:twig-to-stpl
type: generated
generated: exp:help:examples
---
# exp:exptemplateconverttwigtostpl

Konvertiert die Twig-Templates (.twig) eines Designs in Smarty-Templates (.stpl); --dry-run, --file, --force, --override-ini.

Skript `bin/php/exptemplateconverttwigtostpl.php`, Code in `kernel/private/classes/commands/exptemplateconverttwigtostpl.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:exptemplateconverttwigtostpl --help
./console exp:template:convert:twig-to-stpl --help
php bin/php/exptemplateconverttwigtostpl.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Convert Twig templates (.twig) to Smarty templates (.stpl)
Every *.twig below --from becomes the *.stpl at the same place below --to (templates/ and
override/templates/ alike), so that it prints what the .twig printed.
A .html.twig is read as the template of its name without .html. The .twig is converted to Exponential
templates and then to Smarty, in memory, in one run.
--override-ini writes the override rules that make the -s siteaccess use the .stpl.
The same as exp:template:convert --from-engine=twig --to-engine=stpl.

./console exp:template:convert:twig-to-stpl --from=extension/<ext>/design/<design> --to=var/tmp/<design>
./console exp:template:convert:twig-to-stpl --from=extension/<ext>/design/<design> --dry-run
./console exp:template:convert:twig-to-stpl --file=design/<design>/templates/<name>.twig
./console exp:template:convert:twig-to-stpl -s <siteaccess> --from=<design> --to=<design> --override-ini=<file>

Every written file ends with a marker comment ({* exp:template:convert twig->stpl md5:... from ... *});
a target that has no marker, or was changed after it was written, is skipped (SKIP) unless --force
is given. The result is checked before it is written: a .stpl must compile with Smarty 5 (when
installed). Prints WRITE or REPLACE for every file written, NOTE for every construct that could not
be converted exactly, ERROR for a template that could not be converted, then PASS or FAIL with the counts.
Exit code: 0 PASS; 1 FAIL (a template could not be read, converted, checked or written); 2 wrong options;
3 the conversion is not available (the engine's library or converter is missing).
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--from` | erforderlich | Design-Verzeichnis mit den .twig-Templates (jede .twig darunter wird konvertiert) |
| `--to` | erforderlich | Design-Verzeichnis für die .stpl-Templates (Standard: das --from-Verzeichnis, neben den Quellen) |
| `--file` | erforderlich | nur diese eine .twig konvertieren (unter --from, oder eine eigene Datei: dann ist --to die Zieldatei) |
| `--override-ini` | erforderlich | die override.ini.append.php, die für das --to-Design geschrieben wird (die Override-Regeln des Siteaccess von -s, MatchFile zeigt auf die konvertierte .stpl) |
| `--dry-run` |  | zeigt, was geschrieben würde; nichts wird geschrieben |
| `--force` |  | ersetzt auch eine .stpl, die von Hand geändert oder geschrieben wurde (keine Markierung, oder sie passt nicht mehr) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/exptemplateconverttwigtostpl.php`, `kernel/private/classes/commands/exptemplateconverttwigtostpl.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
