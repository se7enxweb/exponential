---
title: exp:exptemplateconverttwigtotpl
audience: developer, admin
category: examples
summary: Konvertiert die Twig-Templates (.twig) eines Designs in Exponential-Templates (.tpl); --dry-run, --file, --force, --override-ini.
keywords: Konsole, Befehl, exp:exptemplateconverttwigtotpl, exptemplateconverttwigtotpl, console, command, CLI, exptemplateconverttwigtotpl.php, exp:template:convert:twig-to-tpl
type: generated
generated: exp:help:examples
---
# exp:exptemplateconverttwigtotpl

Konvertiert die Twig-Templates (.twig) eines Designs in Exponential-Templates (.tpl); --dry-run, --file, --force, --override-ini.

Skript `bin/php/exptemplateconverttwigtotpl.php`, Code in `kernel/private/classes/commands/exptemplateconverttwigtotpl.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:exptemplateconverttwigtotpl --help
./console exp:template:convert:twig-to-tpl --help
php bin/php/exptemplateconverttwigtotpl.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Convert Twig templates (.twig) to Exponential templates (.tpl)
Every *.twig below --from becomes the *.tpl at the same place below --to (templates/ and
override/templates/ alike), so that it prints what the .twig printed.
A .html.twig is read as the template of its name without .html (x.html.twig -> x.tpl).
--override-ini writes the override rules that make the -s siteaccess use the .tpl.
The same as exp:template:convert --from-engine=twig --to-engine=tpl.

./console exp:template:convert:twig-to-tpl --from=extension/<ext>/design/<design> --to=var/tmp/<design>
./console exp:template:convert:twig-to-tpl --from=extension/<ext>/design/<design> --dry-run
./console exp:template:convert:twig-to-tpl --file=design/<design>/templates/<name>.twig
./console exp:template:convert:twig-to-tpl -s <siteaccess> --from=<design> --to=<design> --override-ini=<file>

Every written file ends with a marker comment ({* exp:template:convert twig->tpl md5:... from ... *});
a target that has no marker, or was changed after it was written, is skipped (SKIP) unless --force
is given. The result is checked before it is written: a .tpl must parse in the template
system's parser. Prints WRITE or REPLACE for every file written, NOTE for every construct that could not
be converted exactly, ERROR for a template that could not be converted, then PASS or FAIL with the counts.
Exit code: 0 PASS; 1 FAIL (a template could not be read, converted, checked or written); 2 wrong options;
3 the conversion is not available (the engine's library or converter is missing).
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--from` | erforderlich | Design-Verzeichnis mit den .twig-Templates (jede .twig darunter wird konvertiert) |
| `--to` | erforderlich | Design-Verzeichnis für die .tpl-Templates (Standard: das --from-Verzeichnis, neben den Quellen) |
| `--file` | erforderlich | nur diese eine .twig konvertieren (unter --from, oder eine eigene Datei: dann ist --to die Zieldatei) |
| `--override-ini` | erforderlich | die override.ini.append.php, die für das --to-Design geschrieben wird (die Override-Regeln des Siteaccess von -s, MatchFile zeigt auf die konvertierte .tpl) |
| `--dry-run` |  | zeigt, was geschrieben würde; nichts wird geschrieben |
| `--force` |  | ersetzt auch eine .tpl, die von Hand geändert oder geschrieben wurde (keine Markierung, oder sie passt nicht mehr) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/exptemplateconverttwigtotpl.php`, `kernel/private/classes/commands/exptemplateconverttwigtotpl.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
