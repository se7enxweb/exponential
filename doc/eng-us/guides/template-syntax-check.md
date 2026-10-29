# Checking template syntax: exp:template:check

This guide is for template developers, site builders and whoever runs a project's continuous integration. One console
command, `exp:template:check`, checks templates of all three template engines of Exponential for valid syntax:
Exponential templates (`.tpl`), Smarty templates (`.stpl`) and Twig templates (`.twig` and `.html.twig`). It checks
single files, directories, whole designs (in the kernel and in extensions), the designs of extensions and the full
design chain of a siteaccess with its override rules. Each engine checks its own files with its own parser and
compiler, with everything the site loads for it (the bridge to the template operators, the `exp*` tags, the security
policy), so a file that passes here also compiles on the site.

The command writes nothing: no template, no setting, nothing in the site's caches. Its output is meant for people
(the default text) and for tools (JSON, JUnit, Checkstyle, GitHub annotations, SARIF). Its names, options, exit codes,
rule ids and machine output are a stable contract: see [Compatibility promise](#compatibility-promise).

Every command runs from the installation root. When you are logged in as `root`, add `--allow-root-user`; the examples
leave it out. The outputs shown were produced by running the commands on a development installation, with the root
user's banner left out; timings differ from machine to machine.

Related pages:

- [Rule reference](../reference/template-check-rules.md): every rule id with an explanation and examples
- [Machine output schema](../reference/template-check-schema.json): the JSON Schema of `exp-template-check/1`
- [Converting templates between template engines](template-conversion.md): convert first, then check
- The generated command pages: [exp:template:check](../examples/commands/exptemplatecheck.md),
  [exp:template:engines](../examples/commands/exptemplateengines.md)

## 1. Quick start

Check one template, a directory, or a design by name:

```bash
./console exp:template:check design/standard/templates/node/view/full.tpl
./console exp:template:check extension/<extension>/design/<design>/templates
./console exp:template:check --design=<design>
```

Without any target, the command checks the design chain of the current siteaccess (the one `-s` names, or the default
siteaccess), as the older `eztemplatecheck` does.

To see what the output looks like, make a small design with one good and one broken template per engine:

```bash
mkdir -p var/tmp/check-docs/design/templates/parts
printf '{if $node}\n    <h1>{$node.name|wash}</h1>\n{/if}\n' > var/tmp/check-docs/design/templates/page.tpl
printf '{foreach $items as $item}\n    {$item|wash}\n{/if}\n' > var/tmp/check-docs/design/templates/broken.tpl
printf '{if $node}\n<h1>{$node.name|wash}</h1>\n' > var/tmp/check-docs/design/templates/broken.stpl
printf '<h1>{{ node.name|wash }}</h1>\n{{ node.name|exp_washh }}\n' > var/tmp/check-docs/design/templates/menu.twig
printf '{%% if node %%}\n<h1>{{ node.name|wash }</h1>\n{%% endif %%}\n' > var/tmp/check-docs/design/templates/broken.twig
printf "{%% expinclude uri='design:parts/no_such_part.tpl' %%}\n" > var/tmp/check-docs/design/templates/parts/missing.twig
printf '{%% do 1 %%}\n' > var/tmp/check-docs/design/templates/parts/sandbox.twig
```

Then check the directory:

```text
$ ./console exp:template:check var/tmp/check-docs/design
ERROR  stpl  var/tmp/check-docs/design/templates/broken.stpl:3
       unclosed {if} tag [STPL-SYNTAX-001]
ERROR  tpl   var/tmp/check-docs/design/templates/broken.tpl:3
       Unterminated tag "foreach" does not match tag "if" [TPL-SYNTAX-001]
       > 3 | {/if}
ERROR  twig  var/tmp/check-docs/design/templates/broken.twig:2
       Unexpected "}". [TWIG-SYNTAX-001]
       > 2 | <h1>{{ node.name|wash }</h1>
SUMMARY 7 files: 4 OK, 3 failed, 0 SKIP; findings: 3 ERROR, 0 WARN, 0 NOTE  (stpl 1, tpl 2, twig 4)  1.1 s
FAIL
```

The exit code is 1: a finding at or above the `--fail-on` severity (error by default). With every file passing it is
0 and the last line is `PASS`.

## 2. What is checked: engines and levels

The engine of a file follows from its ending:

| Ending | Engine | Name in the output |
|---|---|---|
| `.tpl` | `tpl` | Exponential templates |
| `.stpl` | `stpl` | Smarty templates (Smarty 5) |
| `.twig`, `.html.twig` | `twig` | Twig templates (Twig 3) |

The endings come from `site.ini [TemplateBridgeSettings] EngineSuffixList[]`, the same registry the template system
uses to hand a file to its engine. `--suffix-map=<ending>:<engine>` adds an ending for one run, for example
`--suffix-map=.tpl.html:tpl` (repeatable). `--engine=tpl,stpl,twig` (a comma list) limits the run to some engines;
`--engine=all` is the default. The engine values `tpl`, `stpl` and `twig` also have the names `exp` and `exponential`
(for `tpl`) and `smarty` (for `stpl`).

`--level` chooses how far each file is taken:

| Level | Exponential templates (`.tpl`) | Smarty templates (`.stpl`) | Twig templates (`.twig`) |
|---|---|---|---|
| `syntax` | the template parser builds the tree without an error or warning (`eZTemplate::validateTemplateFile()`, the parser `eztemplatecheck` uses) | Smarty 5 compiles the file with the bridge loaded (Smarty has no separate parse step) | Twig tokenizes and parses the file with the bridge, its `exp*` tags and node visitors |
| `compile` (the default) | + compiled by the template compiler into a private directory, and the compiled PHP linted | + the compiled PHP linted | + compiled in memory, and the compiled PHP linted |
| `strict` | + every `{include uri='design:...'}` resolves | + every `{expinclude uri='design:...'}` resolves | + the sandbox policy applied, every `exp_<name>` that names no operator reported, every `{% expinclude uri='design:...' %}` resolves |

Linting means the compiled PHP is parsed (not run) by PHP itself, so a template that compiles into PHP the server
cannot load is found before a visitor finds it.

Two things to know about the template system's own parser: an `{if}` or `{foreach}` left open at the very end of a
`.tpl` is closed by the parser without a message, so the checker does not report it either (the site behaves the same).
And a `.tpl` warning (`TPL-SYNTAX-002`) is something the parser guessed or left out; the template still renders.

`--level=strict` runs at compile level plus these checks:

```text
$ ./console exp:template:check var/tmp/check-docs/design --level=strict
ERROR  stpl  var/tmp/check-docs/design/templates/broken.stpl:3
       unclosed {if} tag [STPL-SYNTAX-001]
ERROR  tpl   var/tmp/check-docs/design/templates/broken.tpl:3
       Unterminated tag "foreach" does not match tag "if" [TPL-SYNTAX-001]
       > 3 | {/if}
ERROR  twig  var/tmp/check-docs/design/templates/broken.twig:2
       Unexpected "}". [TWIG-SYNTAX-001]
       > 2 | <h1>{{ node.name|wash }</h1>
WARN   twig  var/tmp/check-docs/design/templates/menu.twig:2
       exp_washh names no template operator: it passes its input through [TWIG-STRICT-UNKNOWN-OPERATOR]
       > 2 | {{ node.name|exp_washh }}
       hint: did you mean "wash"?
ERROR  twig  var/tmp/check-docs/design/templates/parts/missing.twig:1
       the included template design:parts/no_such_part.tpl is not found in the design chain [TWIG-STRICT-INCLUDE-MISSING]
       > 1 | {% expinclude uri='design:parts/no_such_part.tpl' %}
ERROR  twig  var/tmp/check-docs/design/templates/parts/sandbox.twig:1
       Tag "do" is not allowed. (the sandbox would refuse the template) [TWIG-SECURITY-001]
       > 1 | {% do 1 %}
SUMMARY 7 files: 1 OK, 6 failed, 0 SKIP; findings: 5 ERROR, 1 WARN, 0 NOTE  (stpl 1, tpl 2, twig 4)  1.2 s
FAIL
```

An include is resolved in the design chain of the current siteaccess: run with `-s <siteaccess>` to resolve in the
chain of the siteaccess the templates belong to. Only a constant `design:` URI is resolved; an include whose URI is
built at run time cannot be checked before it runs.

### When an engine is not available

Smarty 5 and Twig 3 are libraries: when one is not installed (see `exp:template:engines`), its files are not passed,
they are skipped. The engine is reported once as `UNAVAILABLE <engine>`, each of its files counts as `SKIP`, and the
exit code is 3 unless `--allow-missing-engine` is given (then the files are still skipped, and the run is judged by the
other files only).

```text
$ ./console exp:template:engines
AVAILABLE    tpl (Exponential templates) Exponential 6.0.15stable
             endings .tpl; settings settings/site.ini [TemplateSettings]; compiled var/cache/template/compiled
AVAILABLE    stpl (Smarty templates) Smarty 5.8.5
             endings .stpl; settings settings/smarty.ini [SmartySettings]; security policy on; compiled var/cache/smarty/compile-secure
AVAILABLE    twig (Twig templates)  Twig 3.30.0
             endings .html.twig .twig; settings settings/twig.ini [TwigSettings]; security policy on; compiled var/cache/twig/compile-secure
PASS 3 engines available
```

`exp:template:engines --format=json` prints the same as a JSON document (`"schema": "exp-template-engines/1"`); its
exit code is 3 when an engine's library is missing.

## 3. Targets

Any number and any mix of these:

| Target | What is checked |
|---|---|
| `path/to/file.tpl`, `.stpl`, `.twig`, `.html.twig` | one template file; its engine follows from its ending. A file of no known ending is a usage error (exit 2). |
| `path/to/dir` | every template file below the directory, recursively, all engines; editor backups (`name~`, `#name#`) and hidden files are left out |
| `--design=<name>` | the design `<name>` wherever it lives: `design/<name>/` and the `design/<name>/` of every active extension; repeatable |
| `--design-dir=<dir>` | a design directory given by path (a design in a package, in an inactive extension, a converted copy in `var/tmp`); repeatable |
| `--extension=<name>` | every design of the extension (`extension/<name>/design/*`); repeatable |
| `--extensions` | every design of every active extension |
| `--all-extensions` | with `--design` or `--extensions`: the inactive extensions on disk too |
| `--siteaccess=<siteaccess>` | the design chain of the siteaccess, in the order the template system resolves it: `SiteDesign`, `AdditionalSiteDesignList[]`, `StandardDesign`, each in the extensions of `design.ini [ExtensionSettings] DesignExtensions[]` and in the kernel; and its override rules (see `--overrides`); repeatable |
| `--all-siteaccesses` | every siteaccess of `site.ini [SiteAccessSettings] AvailableSiteAccessList[]` |
| `-` | one template read from stdin; `--stdin-engine=tpl\|stpl\|twig` (default `tpl`) and `--stdin-name=` for the messages |
| `--files-from=<file>` | the files listed in the file, one per line (`-` reads the list from stdin) |
| `--changed-since=<git ref>` | the templates changed since the git reference, and the untracked ones; with other targets, only those of them that changed |
| nothing | the design chain of the current siteaccess |

A file reached by several targets is checked once. With `-v` each finding names where the file belongs (`in: design
media (extension ...)`, `siteaccess site: ...`), and `--summary-by` groups the summary by `engine`, `design`,
`extension`, `siteaccess` or `directory`. A whole siteaccess:

```text
$ ./console exp:template:check --siteaccess=twig --engine=twig --summary-by=design
SUMMARY 517 files: 517 OK, 0 failed, 0 SKIP; findings: 0 ERROR, 0 WARN, 0 NOTE  (twig 517)  27.7 s
  design     twig_media                                 517 files, 0 with errors
PASS
```

A template from stdin, for an editor integration:

```text
$ printf '{{ a|wash }\n' | ./console exp:template:check - --stdin-engine=twig --stdin-name=snippet.twig
ERROR  twig  snippet.twig:1
       Unexpected "}". [TWIG-SYNTAX-001]
       > 1 | {{ a|wash }
SUMMARY 1 files: 0 OK, 1 failed, 0 SKIP; findings: 1 ERROR, 0 WARN, 0 NOTE  (twig 1)  0.1 s
FAIL
```

A template read from stdin is named by `--stdin-name` in the findings (`stdin` without it), and the name is also
recorded as where it belongs (shown with `-v` and in the `belongs` field of the machine formats).

An unknown design, extension or siteaccess, a target that does not exist and a wrong option value are usage errors:

```text
$ ./console exp:template:check --level=deep
FAIL --level must be one of syntax, compile, strict: deep
```

The exit code is then 2.

## 4. Further checks

These are switched on by name; some are on at `--level=strict` or with `--siteaccess`.

| Option | What it adds | On by default |
|---|---|---|
| `--security` | each engine's security policy as the site applies it: the Twig sandbox of `twig.ini` (`TWIG-SECURITY-001`); for Smarty, what its policy refuses is already a compile error (`STPL-SECURITY-001`) | at `strict` |
| `--includes` | every constant `design:` include resolves in the current siteaccess's design chain | at `strict` |
| `--overrides` | every override rule of the siteaccesses checked points to an existing file in their design chain (`OVERRIDE-MISSING-FILE`), and to a file of a template engine's ending (`OVERRIDE-ENGINE-MISMATCH`); without `--siteaccess`, the current siteaccess | with `--siteaccess` or `--all-siteaccesses` |
| `--parity` | a template present in two engines side by side (`x.tpl` and `x.twig`, `x.stpl`) is reported as a note (`PARITY-PAIR`) | no |
| `--deprecations` | Twig's deprecations raised while compiling a `.twig` (`TWIG-DEPRECATION-001`) | no |
| `--encoding` | UTF-8 validity (`ENCODING-UTF8-001`), a byte order mark (`ENCODING-BOM-002`), mixed CRLF and LF line endings (`ENCODING-CRLF-003`) | no |
| `--max-depth=<n>` | accepted for the include depth; in this release includes are resolved one level (each checked file's own includes), so it changes nothing yet | — |

`--parity=render` is experimental: it reports the same pairs and says that the rendering comparison is not done yet.

Two more fixture files show them: the same title in two engines, and a `.tpl` saved with a byte order mark and mixed
line endings.

```bash
mkdir -p var/tmp/check-docs/pair
printf '<h1>{$node.name|wash}</h1>\n' > var/tmp/check-docs/pair/title.tpl
printf '<h1>{{ node.name|wash }}</h1>\n' > var/tmp/check-docs/pair/title.twig
printf '\xEF\xBB\xBF<p>{$text|wash}</p>\r\n<p>more</p>\n' > var/tmp/check-docs/pair/bom.tpl
```

```text
$ ./console exp:template:check var/tmp/check-docs/pair/title.tpl var/tmp/check-docs/pair/title.twig --parity
NOTE   tpl   var/tmp/check-docs/pair/title.tpl
       the same template in 2 engines: var/tmp/check-docs/pair/title.tpl, var/tmp/check-docs/pair/title.twig [PARITY-PAIR]
SUMMARY 2 files: 2 OK, 0 failed, 0 SKIP; findings: 0 ERROR, 0 WARN, 1 NOTE  (tpl 1, twig 1)  0.6 s
PASS

$ ./console exp:template:check var/tmp/check-docs/pair/bom.tpl --encoding
WARN   tpl   var/tmp/check-docs/pair/bom.tpl:1:1
       the file starts with a byte order mark, which is printed [ENCODING-BOM-002]
       > 1 | ﻿<p>{$text|wash}</p>
NOTE   tpl   var/tmp/check-docs/pair/bom.tpl
       the file mixes CRLF and LF line endings [ENCODING-CRLF-003]
SUMMARY 1 files: 0 OK, 1 failed, 0 SKIP; findings: 0 ERROR, 1 WARN, 1 NOTE  (tpl 1)  0.1 s
PASS
```

The summary counts files (`OK`: without any finding; `failed`: with one; `SKIP`: of an unavailable engine) and then the findings by severity.

## 5. The output

### Text (the default)

One block per finding, then the summary and `PASS` or `FAIL`:

- the severity (`ERROR`, `WARN`, `NOTE`), the engine, the file relative to the installation root with line and column
  where the engine reports them;
- the message and the rule id in brackets;
- the source line with `>` and its number, when there is one;
- a hint where one is known (`hint: did you mean "wash"?`);
- with `-v`: where the file belongs (`in: ...`) and an `OK` line with the level and the time for every file that passed.

```text
$ ./console exp:template:check var/tmp/check-docs/design -v
...
OK     twig  var/tmp/check-docs/design/templates/menu.twig (compile, 10 ms)
OK     tpl   var/tmp/check-docs/design/templates/page.tpl (compile, 174 ms)
OK     twig  var/tmp/check-docs/design/templates/parts/missing.twig (compile, 6 ms)
OK     twig  var/tmp/check-docs/design/templates/parts/sandbox.twig (compile, 1 ms)
SUMMARY 7 files: 4 OK, 3 failed, 0 SKIP; findings: 3 ERROR, 0 WARN, 0 NOTE  (stpl 1, tpl 2, twig 4)  0.6 s
FAIL
```

`-q` is the console's standard quiet option: the checker then prints only the findings at ERROR, without the
summary and the result line; the exit code says whether it passed:

```text
$ ./console exp:template:check -q var/tmp/check-docs/rules/broken.twig var/tmp/check-docs/rules/security.stpl
ERROR  twig  var/tmp/check-docs/rules/broken.twig:1
       Unexpected "}". [TWIG-SYNTAX-001]
       > 1 | {{ a|wash }
ERROR  stpl  var/tmp/check-docs/rules/security.stpl:1
       tag 'eval' disabled by security setting [STPL-SECURITY-001]
       > 1 | {eval var='x'}
```

To keep the whole result in a file, use `--format` with `--output`.

`--color=auto|always|never`, `--progress` and `--no-progress` are accepted; in this release the text output has no
colors and no progress counter, so they change nothing yet. They are part of the contract and will keep their meaning.

### Machine formats

`--format=` writes a machine format to stdout instead of the text:

| Format | For |
|---|---|
| `json` | one document: `schema`, `summary`, `findings`, `files` |
| `jsonl` | one finding per line (`"type": "finding"`), then one summary line (`"type": "summary"`); for streaming |
| `junit` | CI test reports: a test suite per engine, a test case per file, a failure per error |
| `checkstyle` | editors and code review tools |
| `github` | GitHub Actions workflow annotations |
| `sarif` | SARIF 2.1.0 for code scanning, with every rule in the tool's rule list |

`--output=<file>` writes the machine format (JSON when the format is `text`) to the file and keeps the text on the
console: a CI job shows a readable log and stores the report.

The JSON document (schema [exp-template-check/1](../reference/template-check-schema.json)):

```json
$ ./console exp:template:check var/tmp/check-docs/design/templates/broken.twig --format=json
{
    "schema": "exp-template-check/1",
    "summary": {
        "files": 1,
        "ok": 0,
        "failed": 1,
        "skipped": 0,
        "errors": 1,
        "warnings": 0,
        "notes": 0,
        "engines": {
            "twig": 1
        },
        "unavailable": [],
        "seconds": 0.044,
        "level": "compile",
        "exit": 1,
        "result": "FAIL"
    },
    "findings": [
        {
            "rule": "TWIG-SYNTAX-001",
            "severity": "ERROR",
            "engine": "twig",
            "file": "var/tmp/check-docs/design/templates/broken.twig",
            "line": 2,
            "column": 0,
            "message": "Unexpected \"}\".",
            "hint": null,
            "source": "<h1>{{ node.name|wash }</h1>",
            "belongs": []
        }
    ],
    "files": [
        {
            "file": "var/tmp/check-docs/design/templates/broken.twig",
            "engine": "twig",
            "status": "ERROR",
            "ms": 43,
            "belongs": []
        }
    ]
}
```

A line or column the engine does not report is 0. The other formats for the same file:

```text
$ ./console exp:template:check var/tmp/check-docs/design/templates/broken.twig --format=jsonl
{"schema":"exp-template-check/1","type":"finding","rule":"TWIG-SYNTAX-001","severity":"ERROR","engine":"twig","file":"var/tmp/check-docs/design/templates/broken.twig","line":2,"column":0,"message":"Unexpected \"}\".","hint":null,"source":"<h1>{{ node.name|wash }</h1>","belongs":[]}
{"schema":"exp-template-check/1","type":"summary","files":1,"ok":0,"failed":1,"skipped":0,"errors":1,"warnings":0,"notes":0,"engines":{"twig":1},"unavailable":[],"seconds":0.123,"level":"compile","exit":1,"result":"FAIL"}

$ ./console exp:template:check var/tmp/check-docs/design/templates/broken.twig --format=github
::error file=var/tmp/check-docs/design/templates/broken.twig,line=2,title=TWIG-SYNTAX-001::Unexpected "}".
```

```xml
<!-- --format=junit -->
<?xml version="1.0" encoding="UTF-8"?>
<testsuites name="exp:template:check">
 <testsuite name="twig" tests="1" failures="1">
  <testcase name="var/tmp/check-docs/design/templates/broken.twig" classname="template.twig">
   <failure type="TWIG-SYNTAX-001" message="Unexpected &quot;}&quot;.">ERROR TWIG-SYNTAX-001 var/tmp/check-docs/design/templates/broken.twig:2:0 Unexpected &quot;}&quot;.</failure>
  </testcase>
 </testsuite>
</testsuites>

<!-- --format=checkstyle -->
<?xml version="1.0" encoding="UTF-8"?>
<checkstyle version="4.3">
 <file name="var/tmp/check-docs/design/templates/broken.twig">
  <error line="2" severity="error" message="Unexpected &quot;}&quot;." source="exp.template.check.TWIG-SYNTAX-001"/>
 </file>
</checkstyle>
```

In JUnit, warnings and notes are written as `system-out` of their test case, so they show in the report without
failing it. Scripts must read a machine format, never the text: the text may become more helpful in later releases,
the machine formats keep their fields (see [Compatibility promise](#compatibility-promise)).

## 6. Rules, severities and the exit code

Every finding carries a rule id such as `TPL-SYNTAX-001`, `STPL-SECURITY-001` or `TWIG-STRICT-UNKNOWN-OPERATOR`.
`--rules` lists them all with their severity; `--explain=<id>` explains one:

```text
$ ./console exp:template:check --explain=TWIG-STRICT-UNKNOWN-OPERATOR
TWIG-STRICT-UNKNOWN-OPERATOR (WARN): exp_<name> names no template operator: it passes its input through, as an unknown operator of a .tpl does.
Examples: doc/eng-us/reference/template-check-rules.md#twig-strict-unknown-operator
```

The [rule reference](../reference/template-check-rules.md) explains every rule with a bad and a good example.

`--fail-on=error|warn|note` (default `error`) sets the severity from which a finding fails the run. A warning alone
passes by default, and fails with `--fail-on=warn`:

```text
$ ./console exp:template:check var/tmp/check-docs/design/templates/menu.twig --level=strict --fail-on=warn
WARN   twig  var/tmp/check-docs/design/templates/menu.twig:2
       exp_washh names no template operator: it passes its input through [TWIG-STRICT-UNKNOWN-OPERATOR]
       > 2 | {{ node.name|exp_washh }}
       hint: did you mean "wash"?
SUMMARY 1 files: 0 OK, 1 failed, 0 SKIP; findings: 0 ERROR, 1 WARN, 0 NOTE  (twig 1)  0.7 s
FAIL
```

The exit codes:

| Code | Meaning |
|---|---|
| 0 | every checked file passed at the requested level (`--fail-on` respected) |
| 1 | at least one finding at or above `--fail-on` |
| 2 | usage error: an unknown option value, design, extension or siteaccess, a target that does not exist |
| 3 | an engine the targets need is not available (unless `--allow-missing-engine`) |
| 4 | internal error: the checker itself failed (with `-vv` the trace is printed) |

A failure of the checker on one file (an exception from an engine) is not exit 4: it is the finding
`CHECK-INTERNAL-ERROR` on that file, and the other files are still checked.

## 7. Adopting strict checking in an old design: the baseline

A large old design may have many findings at `--level=strict`. Record today's findings once, and from then on report
only new ones:

```bash
./console exp:template:check --design=<design> --level=strict --generate-baseline=template-check-baseline.json
./console exp:template:check --design=<design> --level=strict --baseline=template-check-baseline.json
```

With the fixture design:

```text
$ ./console exp:template:check var/tmp/check-docs/design --generate-baseline=var/tmp/check-docs/baseline.json
...
FAIL
$ ./console exp:template:check var/tmp/check-docs/design --baseline=var/tmp/check-docs/baseline.json
SUMMARY 7 files: 7 OK, 0 failed, 0 SKIP; findings: 0 ERROR, 0 WARN, 0 NOTE  (stpl 1, tpl 2, twig 4)  0.6 s
PASS
```

`--generate-baseline` writes the file and still reports (and judges) the run. A baseline entry is the rule id, the file
and the message, not the line, so editing a file elsewhere does not make its known findings new; fixing a finding
removes it for good, and regenerating the baseline makes the file shorter. Commit the baseline file with the design. A
file given to `--baseline` that is not a baseline is a usage error.

## 8. Leaving files and lines out

- `--ignore=<glob>` (repeatable) leaves out files whose path or file name matches the glob (`*/generated/*`,
  `legacy_*.tpl`).
- `site.ini [TemplateCheckSettings] IgnoreList[]` does the same for every run; put it in an override or the settings
  of an extension:

  ```ini
  [TemplateCheckSettings]
  IgnoreList[]
  IgnoreList[]=*/templates/vendor_partials/*
  ```

- A comment on the line before a finding ignores it, in each engine's comment syntax; with a rule id only that rule, without one every rule:

  ```text
  {* exp:template:check ignore-next-line TPL-SYNTAX-002 *}
  {* exp:template:check ignore-next-line *}
  {# exp:template:check ignore-next-line TWIG-STRICT-UNKNOWN-OPERATOR #}
  ```

```text
$ ./console exp:template:check var/tmp/check-docs/design --ignore='broken.*'
SUMMARY 4 files: 4 OK, 0 failed, 0 SKIP; findings: 0 ERROR, 0 WARN, 0 NOTE  (twig 3, tpl 1)  0.2 s
PASS
```

## 9. Speed and safety

- **Read-only.** Compiled output goes to a private directory, `var/cache/template-check/<process>-<random>/`, which is
  removed at the end of the run; `--keep-compiled` keeps it for inspection. The site's template caches, compiled
  templates and settings are never written; Twig and Smarty templates are compiled in memory.
- **No database** is needed for the `syntax` and `compile` levels and for `strict`; reading a siteaccess's settings
  (`--siteaccess`) reads files only. No web server is needed.
- **`--jobs=<n>`** checks in `n` worker processes when there are more than 20 files and PHP has the `pcntl` extension;
  each worker runs at a lower priority (nice 10), which matters on a shared host. Otherwise the files are checked in
  one process. The result is the same either way.
- **`--changed-since=<git ref>`** and **`--files-from=<file>`** check only some files: in a pre-commit hook, in a CI job
  for a merge request.

On a development installation, a whole design of 517 Twig templates checks at compile level in about 10 seconds, the
same design as Smarty templates in about 5.

## 10. CI, hooks and editors

### A git pre-commit hook

[`doc/eng-us/examples/hooks/template-check-pre-commit.sh`](../examples/hooks/template-check-pre-commit.sh) checks the
templates a commit changes and refuses the commit when one does not compile:

```bash
cp doc/eng-us/examples/hooks/template-check-pre-commit.sh .git/hooks/pre-commit
chmod +x .git/hooks/pre-commit
```

### GitHub Actions

```yaml
name: templates
on: [push, pull_request]
jobs:
  template-check:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.5'
      - run: composer install --no-interaction --no-progress
      - run: ./console exp:template:check --design=<design> --level=strict --format=github
```

Each finding becomes an annotation on its file and line in the pull request.

### GitLab CI

```yaml
template-check:
  image: php:8.5-cli
  script:
    - composer install --no-interaction --no-progress
    - ./console exp:template:check --design=<design> --level=strict --format=junit --output=template-check.xml
  artifacts:
    when: always
    reports:
      junit: template-check.xml
```

With `--output` the job log keeps the readable text and the JUnit report goes to GitLab's test report.

### Code scanning and editors

`--format=sarif --output=template-check.sarif` produces a file for code scanning tools (for GitHub, upload it with the
`github/codeql-action/upload-sarif` action). `--format=checkstyle` is read by most editors' and review tools'
Checkstyle integrations. A Visual Studio Code task that checks the open file:

```json
{
    "version": "2.0.0",
    "tasks": [
        {
            "label": "exp:template:check current file",
            "type": "shell",
            "command": "./console exp:template:check ${relativeFile} --level=strict",
            "problemMatcher": {
                "owner": "exp-template-check",
                "fileLocation": ["relative", "${workspaceFolder}"],
                "severity": "error",
                "pattern": [
                    { "regexp": "^(?:ERROR|WARN|NOTE)\\s+\\S+\\s+([^:]+):(\\d+)(?::(\\d+))?$", "file": 1, "line": 2, "column": 3 },
                    { "regexp": "^\\s+(.*)$", "message": 1 }
                ]
            }
        }
    ]
}
```

## 11. Engines added by extensions

The checker of each engine is a class implementing `expTemplateEngineChecker` (`available()` and
`check( $file, $text, $level, $context )`, which returns the findings). The kernel's are `expTplTemplateChecker`,
`expSmartyTemplateChecker` and `expTwigTemplateChecker`. An extension that adds a template engine registers its
ending in `site.ini [TemplateBridgeSettings] EngineSuffixList[]` and its checker in `CheckerList[]`; the same setting
replaces a kernel checker:

```ini
[TemplateBridgeSettings]
EngineSuffixList[.mytpl]=myengine
CheckerList[myengine]=myEngineTemplateChecker
```

## 12. The older eztemplatecheck

`bin/php/eztemplatecheck.php` (`./console exp:eztemplatecheck`) is the template syntax checker Exponential has had for a
long time. It keeps working exactly as before, with its name, arguments, output and exit codes: it checks `.tpl` files
with the template parser, given as arguments, or the design chain of the current siteaccess without arguments. In this
release it runs its own code and does not call `exp:template:check`. For new scripts, `exp:template:check --engine=tpl`
does the same and more (the compile level, rule ids, machine formats, siteaccess override rules).

## 13. Troubleshooting

| What you see | Why, and what to do |
|---|---|
| `UNAVAILABLE stpl` or `UNAVAILABLE twig`, exit 3 | the engine's library is not installed: `exp:template:engines` says which; install it (see the Smarty and Twig guides) or pass `--allow-missing-engine` |
| `FAIL not a template of a known engine: x.html` | the file's ending is not in the registry: give `--suffix-map=.html:tpl` for one run, or add it to `EngineSuffixList[]` |
| `TWIG-SYNTAX-001 Unknown "x" filter` with a hint | a filter or function name that is neither a template operator nor a Twig name; the hint names the closest operator |
| `TWIG-STRICT-INCLUDE-MISSING` for a template that exists | includes are resolved in the current siteaccess's chain: run with `-s <siteaccess>` of the design the template belongs to |
| `TWIG-SECURITY-001` for a template that renders on another installation | that installation has a different sandbox policy (`twig.ini SecurityAllowed*List[]`); the check applies this installation's settings |
| `TPL-COMPILE-001 the compiled template is not valid PHP` | the template parses but the compiler writes PHP that cannot load; look at the compiled file with `--keep-compiled` |
| Nothing at all with `-q` | see [the text output](#text-the-default): read the exit code, or use `--format` and `--output` |
| A run is slow | `--jobs=<n>` on a machine with spare cores, `--engine` to limit the engines, `--changed-since` to limit the files |
| `--changed-since=HEAD` checks far more files than you changed | it takes untracked files too: template copies in a scratch directory that git does not ignore count as changed; add the directory to `.gitignore` or leave it out with `--ignore` |

## Compatibility promise

The `exp:template:*` commands are a public interface. End developers can rely on them for a long time without changing
their scripts, CI files or habits: this holds for the whole life of a major version line and is kept across major
versions as long as it can be. The contract starts with the first release of `exp:template:*`.

- **Names are permanent.** A command name or alias, once released, is never removed or renamed; a better name is added
  as an alias. This covers `exp:template:check` with `exp:template:lint` and `exp:template:convert:check`,
  `exp:template:engines`, `exp:template:convert` with its six directions and `exp:template:convert:diff`, and the older
  `eztemplatecheck`, which was released long ago and stays.
- **Options are additive only.** An option is never removed, renamed or changed in meaning; its default never changes
  so that a run that passed before fails now. Stricter checking is opt-in, through a new option or a new `--level`
  value. Short options are never reused.
- **Exit codes are permanent**: 0 to 4 as in the table above. A new exit code would only be added above 4.
- **The machine output is versioned.** The fields of `exp-template-check/1` are never removed or changed in type; new
  fields may be added. An incompatible change would ship as `exp-template-check/2` behind `--schema=2`, with `/1` still
  the default and still produced. `--schema=1` is the only value today. The text output may become more helpful;
  scripts read a machine format.
- **Rule ids are permanent.** A rule may be retired (then it never fires), but its id is never reused for something
  else.
- **Engine values are permanent**: `tpl`, `stpl`, `twig`, and the names `exp`, `exponential` and `smarty`. A future
  engine gets a new value; the mapping of file endings only grows.
- **Deprecation, if it is ever needed**, takes at least one full major version with a warning line (`DEPRECATED ...`,
  never on the stdout of a machine format), documented in the changelog and on the command's page, before any change
  of behavior; and only for options marked experimental when they were introduced. `--parity=render` is experimental
  today.
- **A test holds it.** `tests/tests/kernel/private/classes/commands/expTemplateCommandContractTest.php` keeps a snapshot
  of the command names, aliases, option names, exit codes, rule ids, engine values and the fields of the JSON schema.
  Any change to them fails the test unless it is an addition, which is added to the snapshot in the same commit.
