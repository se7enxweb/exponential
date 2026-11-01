---
title: exp:exptemplatecheck
audience: developer, admin
category: examples
summary: "Prüft Templates jeder Engine (.tpl, .stpl, .twig, .html.twig) auf gültige Syntax: Dateien, Verzeichnisse, Designs, Erweiterungen, Siteaccesses; Stufen syntax/compile/strict; Ausgabe als Text, json, jsonl, junit, checkstyle, github und sarif."
keywords: Konsole, Befehl, exp:exptemplatecheck, exptemplatecheck, console, command, CLI, exptemplatecheck.php, exp:template:check, exp:template:lint, exp:template:convert:check
type: generated
generated: exp:help:examples
---
# exp:exptemplatecheck

Prüft Templates jeder Engine (.tpl, .stpl, .twig, .html.twig) auf gültige Syntax: Dateien, Verzeichnisse, Designs, Erweiterungen, Siteaccesses; Stufen syntax/compile/strict; Ausgabe als Text, json, jsonl, junit, checkstyle, github und sarif.

Skript `bin/php/exptemplatecheck.php`, Code in `kernel/private/classes/commands/exptemplatecheck.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:exptemplatecheck --help
./console exp:template:check --help
./console exp:template:lint --help
./console exp:template:convert:check --help
php bin/php/exptemplatecheck.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Check templates of every template engine for valid syntax
Exponential templates (.tpl), Smarty templates (.stpl) and Twig templates (.twig, .html.twig), each by
its own parser and compiler with the bridge loaded. Targets: files and directories (TARGET ...), '-' for
stdin, --design, --design-dir, --extension, --extensions, --siteaccess, --all-siteaccesses; nothing:
the current siteaccess's design chain. Nothing is written: compiled output goes to a private directory.

./console exp:template:check design/standard/templates/node/view/full.tpl
./console exp:template:check --design=<design> --level=strict
./console exp:template:check --siteaccess=<siteaccess> --format=json --output=var/tmp/check.json
./console exp:template:check --changed-since=HEAD --format=github
./console exp:template:check --rules

Levels: syntax (the parser), compile (the default: + compiled and the compiled PHP linted), strict
(+ the security policy, includes resolved, unknown operators). Every finding has a rule id
(--rules, --explain=<id>). Aliases: exp:template:lint, exp:template:convert:check.
Exit code: 0 all passed; 1 a finding at or above --fail-on; 2 usage error; 3 an engine needed is
unavailable (unless --allow-missing-engine); 4 internal error.
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--design` | erforderlich | ein Design nach Namen, im Kernel und in den (aktiven) Erweiterungen; wiederholbar |
| `--design-dir` | erforderlich | ein Design-Verzeichnis nach Pfad; wiederholbar |
| `--extension` | erforderlich | jedes Design der Erweiterung; wiederholbar |
| `--extensions` |  | jedes Design jeder aktiven Erweiterung |
| `--all-extensions` |  | mit --design/--extensions: auch die inaktiven Erweiterungen auf der Platte |
| `--siteaccess` | erforderlich | die Design-Kette und die Override-Regeln des Siteaccess; wiederholbar |
| `--all-siteaccesses` |  | jeder Siteaccess aus AvailableSiteAccessList |
| `--engine` | erforderlich | all (die Voreinstellung) oder eine Liste: tpl,stpl,twig |
| `--suffix-map` | erforderlich | <endung>:<engine>, eine zusätzliche Dateiendung (.tpl.html:tpl); wiederholbar |
| `--level` | erforderlich | syntax, compile (die Voreinstellung) oder strict |
| `--security` |  | gegen die Sicherheitsrichtlinie der Engine prüfen (bei strict eingeschaltet) |
| `--includes` |  | jedes Design: include muss aufgelöst werden (bei strict eingeschaltet) |
| `--overrides` |  | jede Override-Regel muss auf ein vorhandenes Template zeigen (mit --siteaccess eingeschaltet) |
| `--parity` | optional | Templates melden, die in zwei Engines vorhanden sind; =render ist experimentell |
| `--deprecations` |  | veraltete Konstrukte melden (Twig-Deprecations beim Kompilieren) |
| `--encoding` |  | UTF-8-Gültigkeit, Byte Order Mark, gemischte Zeilenenden |
| `--max-depth` | erforderlich | Include-Tiefe für --includes (Voreinstellung 32) |
| `--format` | erforderlich | text (die Voreinstellung), json, jsonl, junit, checkstyle, github oder sarif |
| `--output` | erforderlich | das Maschinenformat in diese Datei schreiben; die Konsole behält den Text |
| `--color` | erforderlich | auto, always oder never |
| `--no-progress` |  | kein Fortschrittszähler |
| `--progress` |  | ein Fortschrittszähler in einem Terminal |
| `--summary-by` | erforderlich | die Zusammenfassung nach Engine, Design, Erweiterung, Siteaccess oder Verzeichnis gruppieren |
| `--fail-on` | erforderlich | der Schweregrad, der scheitert: error (die Voreinstellung), warn oder note |
| `--baseline` | erforderlich | nur Befunde melden, die nicht in dieser Baseline-Datei stehen |
| `--generate-baseline` | erforderlich | die heutigen Befunde in diese Baseline-Datei schreiben |
| `--ignore` | erforderlich | ein Glob von Dateien, die ausgelassen werden; wiederholbar (auch site.ini [TemplateCheckSettings] IgnoreList[]) |
| `--rules` |  | jede Regel-ID auflisten und beenden |
| `--explain` | erforderlich | eine Regel-ID erklären und beenden |
| `--schema` | erforderlich | die Schemaversion der Maschinenausgabe (1) |
| `--stdin-engine` | erforderlich | die Engine des Templates, das von der Standardeingabe (-) gelesen wird: tpl, stpl oder twig |
| `--stdin-name` | erforderlich | der Name des von der Standardeingabe gelesenen Templates, für die Meldungen |
| `--allow-missing-engine` |  | eine nicht verfügbare Engine macht den Exit-Code nicht zu 3 |
| `--keep-compiled` |  | das private Verzeichnis der kompilierten Ausgabe zur Ansicht behalten |
| `--jobs` | erforderlich | in so vielen Worker-Prozessen prüfen |
| `--changed-since` | erforderlich | nur die Templates, die sich seit dieser Git-Referenz geändert haben (und nicht verfolgte) |
| `--files-from` | erforderlich | die in dieser Datei genannten Dateien prüfen, eine je Zeile (- für die Standardeingabe) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[TARGET*]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/exptemplatecheck.php`, `kernel/private/classes/commands/exptemplatecheck.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
