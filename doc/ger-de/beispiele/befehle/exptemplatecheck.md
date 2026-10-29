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
| `--design` | erforderlich | a design by name, in the kernel and in the (active) extensions; repeatable |
| `--design-dir` | erforderlich | a design directory by path; repeatable |
| `--extension` | erforderlich | every design of the extension; repeatable |
| `--extensions` |  | every design of every active extension |
| `--all-extensions` |  | with --design/--extensions: the inactive extensions on disk too |
| `--siteaccess` | erforderlich | the design chain and override rules of the siteaccess; repeatable |
| `--all-siteaccesses` |  | every siteaccess of AvailableSiteAccessList |
| `--engine` | erforderlich | all (the default) or a list: tpl,stpl,twig |
| `--suffix-map` | erforderlich | <ending>:<engine>, an extra file ending (.tpl.html:tpl); repeatable |
| `--level` | erforderlich | syntax, compile (the default) or strict |
| `--security` |  | check against the engine's security policy (on at strict) |
| `--includes` |  | every design: include must resolve (on at strict) |
| `--overrides` |  | every override rule must point to an existing template (on with --siteaccess) |
| `--parity` | optional | report templates present in two engines; =render is experimental |
| `--deprecations` |  | report deprecated constructs (Twig deprecations while compiling) |
| `--encoding` |  | UTF-8 validity, byte order mark, mixed line endings |
| `--max-depth` | erforderlich | include depth for --includes (default 32) |
| `--format` | erforderlich | text (the default), json, jsonl, junit, checkstyle, github or sarif |
| `--output` | erforderlich | write the machine format to this file; the console keeps the text |
| `--color` | erforderlich | auto, always or never |
| `--no-progress` |  | no progress counter |
| `--progress` |  | a progress counter on a terminal |
| `--summary-by` | erforderlich | group the summary by engine, design, extension, siteaccess or directory |
| `--fail-on` | erforderlich | the severity that fails: error (the default), warn or note |
| `--baseline` | erforderlich | report only findings that are not in this baseline file |
| `--generate-baseline` | erforderlich | write today's findings to this baseline file |
| `--ignore` | erforderlich | a glob of files to leave out; repeatable (site.ini [TemplateCheckSettings] IgnoreList[] too) |
| `--rules` |  | list every rule id and exit |
| `--explain` | erforderlich | explain one rule id and exit |
| `--schema` | erforderlich | the machine output schema version (1) |
| `--stdin-engine` | erforderlich | the engine of the template read from stdin (-): tpl, stpl or twig |
| `--stdin-name` | erforderlich | the name of the template read from stdin, for the messages |
| `--allow-missing-engine` |  | an unavailable engine does not make the exit code 3 |
| `--keep-compiled` |  | keep the private directory of compiled output for inspection |
| `--jobs` | erforderlich | check in this many worker processes |
| `--changed-since` | erforderlich | only the templates changed since this git reference (and untracked ones) |
| `--files-from` | erforderlich | check the files listed in this file, one per line (- for stdin) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[TARGET*]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/exptemplatecheck.php`, `kernel/private/classes/commands/exptemplatecheck.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
