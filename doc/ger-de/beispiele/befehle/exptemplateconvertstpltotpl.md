---
title: exp:exptemplateconvertstpltotpl
audience: developer, admin
category: examples
summary: Konvertiert die Smarty-Templates (.stpl) eines Designs in Exponential-Templates (.tpl); --dry-run, --file, --force, --override-ini.
keywords: Konsole, Befehl, exp:exptemplateconvertstpltotpl, exptemplateconvertstpltotpl, console, command, CLI, exptemplateconvertstpltotpl.php, exp:template:convert:stpl-to-tpl
type: generated
generated: exp:help:examples
---
# exp:exptemplateconvertstpltotpl

Konvertiert die Smarty-Templates (.stpl) eines Designs in Exponential-Templates (.tpl); --dry-run, --file, --force, --override-ini.

Skript `bin/php/exptemplateconvertstpltotpl.php`, Code in `kernel/private/classes/commands/exptemplateconvertstpltotpl.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:exptemplateconvertstpltotpl --help
./console exp:template:convert:stpl-to-tpl --help
php bin/php/exptemplateconvertstpltotpl.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Convert Smarty templates (.stpl) to Exponential templates (.tpl)
Every *.stpl below --from becomes the *.tpl at the same place below --to, converted by
expSmartyReverseConverter: what the tpl -> stpl conversion writes, and common hand-written Smarty.
What has no exact .tpl form is kept as a {* SMARTY ... *} comment in the .tpl and reported as a NOTE.
--override-ini writes the -s siteaccess's override rules with MatchFile pointing at the converted .tpl.
The same as exp:template:convert --from-engine=stpl --to-engine=tpl.

./console exp:template:convert:stpl-to-tpl --from=extension/<ext>/design/<design> --to=var/tmp/<design>
./console exp:template:convert:stpl-to-tpl --from=extension/<ext>/design/<design> --dry-run
./console exp:template:convert:stpl-to-tpl --file=design/<design>/templates/<name>.stpl
./console exp:template:convert:stpl-to-tpl -s <siteaccess> --from=<design> --to=<design> --override-ini=<file>

Every written file ends with a marker comment ({* exp:template:convert stpl->tpl md5:... from ... *});
a target that has no marker, or was changed after it was written, is skipped (SKIP) unless --force
is given. The result is checked before it is written: a .tpl must parse. Prints WRITE or REPLACE for
every file written, NOTE for every construct that could not be converted exactly, ERROR for a
template that could not be converted, then PASS or FAIL with the counts.
Exit code: 0 PASS; 1 FAIL (a template could not be read, converted, checked or written); 2 wrong options.
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--from` | erforderlich | Design-Verzeichnis mit den .stpl-Templates (jede .stpl darunter wird konvertiert) |
| `--to` | erforderlich | Design-Verzeichnis für die .tpl-Templates (Standard: das --from-Verzeichnis, neben den Quellen) |
| `--file` | erforderlich | nur diese eine .stpl konvertieren (unterhalb von --from, oder eine einzelne Datei: dann ist --to die Zieldatei) |
| `--override-ini` | erforderlich | die override.ini.append.php, die für das --to-Design geschrieben wird (die Override-Regeln des Siteaccess von -s, MatchFile zeigt auf die konvertierte .tpl) |
| `--dry-run` |  | zeigt, was geschrieben würde; nichts wird geschrieben |
| `--force` |  | ersetzt auch eine .tpl, die von Hand geändert oder geschrieben wurde (keine Markierung, oder sie passt nicht mehr) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/exptemplateconvertstpltotpl.php`, `kernel/private/classes/commands/exptemplateconvertstpltotpl.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
