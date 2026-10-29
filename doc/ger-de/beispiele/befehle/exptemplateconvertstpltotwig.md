---
title: exp:exptemplateconvertstpltotwig
audience: developer, admin
category: examples
summary: Konvertiert die Smarty-Templates (.stpl) eines Designs in Twig-Templates (.twig); --dry-run, --file, --force, --override-ini, --twig-suffix.
keywords: Konsole, Befehl, exp:exptemplateconvertstpltotwig, exptemplateconvertstpltotwig, console, command, CLI, exptemplateconvertstpltotwig.php, exp:template:convert:stpl-to-twig
type: generated
generated: exp:help:examples
---
# exp:exptemplateconvertstpltotwig

Konvertiert die Smarty-Templates (.stpl) eines Designs in Twig-Templates (.twig); --dry-run, --file, --force, --override-ini, --twig-suffix.

Skript `bin/php/exptemplateconvertstpltotwig.php`, Code in `kernel/private/classes/commands/exptemplateconvertstpltotwig.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:exptemplateconvertstpltotwig --help
./console exp:template:convert:stpl-to-twig --help
php bin/php/exptemplateconvertstpltotwig.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Convert Smarty templates (.stpl) to Twig templates (.twig)
Every *.stpl below --from becomes the *.twig at the same place below --to (templates/ and
override/templates/ alike), so that it prints what the .stpl printed.
The .stpl is converted to Exponential templates and then to Twig, in memory, in one run.
--override-ini writes the override rules that make the -s siteaccess use the .twig.
The same as exp:template:convert --from-engine=stpl --to-engine=twig.

./console exp:template:convert:stpl-to-twig --from=extension/<ext>/design/<design> --to=var/tmp/<design>
./console exp:template:convert:stpl-to-twig --from=extension/<ext>/design/<design> --dry-run
./console exp:template:convert:stpl-to-twig --file=design/<design>/templates/<name>.stpl
./console exp:template:convert:stpl-to-twig -s <siteaccess> --from=<design> --to=<design> --override-ini=<file>

Every written file ends with a marker comment ({# exp:template:convert stpl->twig md5:... from ... #});
a target that has no marker, or was changed after it was written, is skipped (SKIP) unless --force
is given. The result is checked before it is written: a .twig must compile with Twig 3 (when
installed). Prints WRITE or REPLACE for every file written, NOTE for every construct that could not
be converted exactly, ERROR for a template that could not be converted, then PASS or FAIL with the counts.
Exit code: 0 PASS; 1 FAIL (a template could not be read, converted, checked or written); 2 wrong options;
3 the conversion is not available (the engine's library or converter is missing).
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--from` | erforderlich | Design-Verzeichnis mit den .stpl-Templates (jede .stpl darunter wird konvertiert) |
| `--to` | erforderlich | Design-Verzeichnis für die .twig-Templates (Standard: das --from-Verzeichnis, neben den Quellen) |
| `--file` | erforderlich | nur diese eine .stpl konvertieren (unterhalb von --from, oder eine einzelne Datei: dann ist --to die Zieldatei) |
| `--override-ini` | erforderlich | override.ini.append.php, die für das --to-Design geschrieben wird (die Override-Regeln des Siteaccess von -s, MatchFile zeigt auf die konvertierte .twig) |
| `--twig-suffix` | erforderlich | die Endung der geschriebenen Twig-Templates: .twig (Standard) oder .html.twig |
| `--dry-run` |  | zeigt, was geschrieben würde; nichts wird geschrieben |
| `--force` |  | auch eine .twig ersetzen, die geändert oder von Hand geschrieben wurde (keine Markierung, oder sie passt nicht) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/exptemplateconvertstpltotwig.php`, `kernel/private/classes/commands/exptemplateconvertstpltotwig.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
