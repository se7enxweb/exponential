---
title: exp:exptemplateconvert
audience: developer, admin
category: examples
summary: "Konvertiert die Templates eines Designs von einer Template-Engine in eine andere (--from-engine, --to-engine: tpl, stpl, twig); --dry-run, --file, --force, --override-ini, --twig-suffix."
keywords: Konsole, Befehl, exp:exptemplateconvert, exptemplateconvert, console, command, CLI, exptemplateconvert.php, exp:template:convert
type: generated
generated: exp:help:examples
---
# exp:exptemplateconvert

Konvertiert die Templates eines Designs von einer Template-Engine in eine andere (--from-engine, --to-engine: tpl, stpl, twig); --dry-run, --file, --force, --override-ini, --twig-suffix.

Skript `bin/php/exptemplateconvert.php`, Code in `kernel/private/classes/commands/exptemplateconvert.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:exptemplateconvert --help
./console exp:template:convert --help
php bin/php/exptemplateconvert.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Convert the templates of a design from one template engine to another
Engines: tpl (Exponential templates, .tpl; also exp, exponential), stpl (Smarty templates, .stpl;
also smarty) and twig (Twig templates, .twig and .html.twig). Every template of the --from-engine
below --from becomes the template of the --to-engine at the same place below --to (templates/ and
override/templates/ alike), so that it prints what the source printed. Smarty and Twig convert to
each other through Exponential templates, in memory, in one run. The named commands do the same
with the engines fixed: exp:template:convert:tpl-to-stpl, :stpl-to-tpl, :tpl-to-twig, :twig-to-tpl,
:stpl-to-twig and :twig-to-stpl.

./console exp:template:convert --from-engine=tpl --to-engine=stpl --from=extension/<ext>/design/<design> --to=var/tmp/<design>
./console exp:template:convert --from-engine=stpl --to-engine=tpl --from=var/tmp/<design> --to=var/tmp/<design>-tpl
./console exp:template:convert --from-engine=tpl --to-engine=stpl --from=extension/<ext>/design/<design> --dry-run
./console exp:template:convert --from-engine=tpl --to-engine=stpl --file=design/<design>/templates/<name>.tpl
./console exp:template:convert --from-engine=tpl --to-engine=twig --from=extension/<ext>/design/<design> --to=var/tmp/<design> --twig-suffix=.html.twig

Every written file ends with a marker comment in the target engine's comment syntax
({* exp:template:convert tpl->stpl md5:... from ... *}, {# exp:template:convert tpl->twig ... #});
a target that has no marker, or was changed after it was written, is skipped (SKIP) unless --force
is given. The result is checked before it is written: a .stpl must compile with Smarty 5 and a .twig
with Twig 3 (when installed), a .tpl must parse. Prints WRITE or REPLACE for every file written, NOTE for every
construct that could not be converted exactly, ERROR for a template that could not be converted,
then PASS or FAIL with the counts.
Exit code: 0 PASS; 1 FAIL (a template could not be read, converted, checked or written); 2 wrong
options; 3 the conversion between the two engines is not available.
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--from-engine` | erforderlich | die Engine der zu konvertierenden Templates: tpl, stpl oder twig |
| `--to-engine` | erforderlich | die Engine, in die konvertiert wird: tpl, stpl oder twig |
| `--from` | erforderlich | Design-Verzeichnis mit den zu konvertierenden Templates (jedes Template der --from-engine darunter) |
| `--to` | erforderlich | Design-Verzeichnis für die konvertierten Templates (Standard: das --from-Verzeichnis, neben den Quellen) |
| `--file` | erforderlich | nur dieses eine Template konvertieren (unterhalb von --from, oder eine einzelne Datei: dann ist --to die Zieldatei) |
| `--override-ini` | erforderlich | die override.ini.append.php, die für das --to-Design geschrieben wird (die Override-Regeln des Siteaccess von -s, MatchFile zeigt auf die konvertierten Templates) |
| `--twig-suffix` | erforderlich | die Endung geschriebener Twig-Templates: .twig (Standard) oder .html.twig |
| `--dry-run` |  | zeigt, was geschrieben würde; nichts wird geschrieben |
| `--force` |  | ersetzt auch ein Ziel, das von Hand geändert oder geschrieben wurde (keine Markierung, oder sie passt nicht mehr) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/exptemplateconvert.php`, `kernel/private/classes/commands/exptemplateconvert.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
