---
title: exp:exptemplatecheck
audience: developer, admin
category: examples
summary: "Check templates of every engine (.tpl, .stpl, .twig, .html.twig) for valid syntax: files, directories, designs, extensions, siteaccesses; levels syntax/compile/strict; text, json, jsonl, junit, checkstyle, github and sarif output."
keywords: exp:exptemplatecheck, exptemplatecheck, console, command, CLI, exptemplatecheck.php, exp:template:check, exp:template:lint, exp:template:convert:check
type: generated
generated: exp:help:examples
---
# exp:exptemplatecheck

Check templates of every engine (.tpl, .stpl, .twig, .html.twig) for valid syntax: files, directories, designs, extensions, siteaccesses; levels syntax/compile/strict; text, json, jsonl, junit, checkstyle, github and sarif output.

Script `bin/php/exptemplatecheck.php`, code in `kernel/private/classes/commands/exptemplatecheck.php`. Run it from the installation root, as the user the web server runs as:

```bash
./console exp:exptemplatecheck --help
./console exp:template:check --help
./console exp:template:lint --help
./console exp:template:convert:check --help
php bin/php/exptemplatecheck.php --help
```

## Help text

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

## Options

| Option | Value | Help |
|---|---|---|
| `--design` | required | a design by name, in the kernel and in the (active) extensions; repeatable |
| `--design-dir` | required | a design directory by path; repeatable |
| `--extension` | required | every design of the extension; repeatable |
| `--extensions` |  | every design of every active extension |
| `--all-extensions` |  | with --design/--extensions: the inactive extensions on disk too |
| `--siteaccess` | required | the design chain and override rules of the siteaccess; repeatable |
| `--all-siteaccesses` |  | every siteaccess of AvailableSiteAccessList |
| `--engine` | required | all (the default) or a list: tpl,stpl,twig |
| `--suffix-map` | required | <ending>:<engine>, an extra file ending (.tpl.html:tpl); repeatable |
| `--level` | required | syntax, compile (the default) or strict |
| `--security` |  | check against the engine's security policy (on at strict) |
| `--includes` |  | every design: include must resolve (on at strict) |
| `--overrides` |  | every override rule must point to an existing template (on with --siteaccess) |
| `--parity` | optional | report templates present in two engines; =render is experimental |
| `--deprecations` |  | report deprecated constructs (Twig deprecations while compiling) |
| `--encoding` |  | UTF-8 validity, byte order mark, mixed line endings |
| `--max-depth` | required | include depth for --includes (default 32) |
| `--format` | required | text (the default), json, jsonl, junit, checkstyle, github or sarif |
| `--output` | required | write the machine format to this file; the console keeps the text |
| `--color` | required | auto, always or never |
| `--no-progress` |  | no progress counter |
| `--progress` |  | a progress counter on a terminal |
| `--summary-by` | required | group the summary by engine, design, extension, siteaccess or directory |
| `--fail-on` | required | the severity that fails: error (the default), warn or note |
| `--baseline` | required | report only findings that are not in this baseline file |
| `--generate-baseline` | required | write today's findings to this baseline file |
| `--ignore` | required | a glob of files to leave out; repeatable (site.ini [TemplateCheckSettings] IgnoreList[] too) |
| `--rules` |  | list every rule id and exit |
| `--explain` | required | explain one rule id and exit |
| `--schema` | required | the machine output schema version (1) |
| `--stdin-engine` | required | the engine of the template read from stdin (-): tpl, stpl or twig |
| `--stdin-name` | required | the name of the template read from stdin, for the messages |
| `--allow-missing-engine` |  | an unavailable engine does not make the exit code 3 |
| `--keep-compiled` |  | keep the private directory of compiled output for inspection |
| `--jobs` | required | check in this many worker processes |
| `--changed-since` | required | only the templates changed since this git reference (and untracked ones) |
| `--files-from` | required | check the files listed in this file, one per line (- for stdin) |

Read from the option definitions in the code (`startup()`, `getOptions()`, `options()` or `ezcConsoleOption`). Every script also takes the standard options of the script framework (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Arguments: `[TARGET*]`.

## Related

- [All console commands](README.md)

---

Generated by `./console exp:help:examples` from `bin/php/exptemplatecheck.php`, `kernel/private/classes/commands/exptemplatecheck.php`. Do not edit this page: change the source and generate again ([how](../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../help/free-documentation.md).
