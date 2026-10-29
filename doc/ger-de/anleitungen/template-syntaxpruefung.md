# Template-Syntax prüfen: exp:template:check

Diese Anleitung richtet sich an Template-Entwickler, Site-Bauer und alle, die die Continuous Integration eines Projekts
betreuen. Ein Konsolenbefehl, `exp:template:check`, prüft Templates aller drei Template-Engines von Exponential auf
gültige Syntax: Exponential-Templates (`.tpl`), Smarty-Templates (`.stpl`) und Twig-Templates (`.twig` und
`.html.twig`). Er prüft einzelne Dateien, Verzeichnisse, ganze Designs (im Kernel und in Erweiterungen), die Designs von
Erweiterungen und die vollständige Design-Kette eines Siteaccess mit seinen Override-Regeln. Jede Engine prüft ihre
eigenen Dateien mit ihrem eigenen Parser und Compiler, mit allem, was die Website für sie lädt (die Brücke zu den
Template-Operatoren, die `exp*`-Tags, die Sicherheitsrichtlinie): Eine Datei, die hier besteht, kompiliert auch auf der
Website.

Der Befehl schreibt nichts: kein Template, keine Einstellung, nichts in die Caches der Website. Seine Ausgabe ist für
Menschen gedacht (der Text, Standard) und für Werkzeuge (JSON, JUnit, Checkstyle, GitHub-Annotationen, SARIF). Seine
Namen, Optionen, Exit-Codes, Regel-IDs und die maschinelle Ausgabe sind ein stabiler Vertrag: siehe
[Kompatibilitätsversprechen](#kompatibilitätsversprechen).

Jeder Befehl läuft im Wurzelverzeichnis der Installation. Als `root` angemeldet ergänzen Sie `--allow-root-user`; die
Beispiele lassen es weg. Die gezeigten Ausgaben stammen aus Läufen auf einer Entwicklungsinstallation, ohne den Hinweis
für den Root-Benutzer; die Zeiten unterscheiden sich von Rechner zu Rechner. Die Ausgaben des Befehls sind englisch.

Verwandte Seiten:

- [Regel-Referenz](template-syntaxpruefung-regeln.md): jede Regel-ID mit Erklärung und Beispielen
- [Schema der maschinellen Ausgabe](../../eng-us/reference/template-check-schema.json) (Englisch): das JSON Schema von `exp-template-check/1`
- [Templates zwischen Template-Engines konvertieren](template-konvertierung.md): erst konvertieren, dann prüfen
- Die erzeugten Befehlsseiten: [exp:template:check](../beispiele/befehle/exptemplatecheck.md),
  [exp:template:engines](../beispiele/befehle/exptemplateengines.md)

## 1. Schnellstart

Ein Template, ein Verzeichnis oder ein Design nach Namen prüfen:

```bash
./console exp:template:check design/standard/templates/node/view/full.tpl
./console exp:template:check extension/<erweiterung>/design/<design>/templates
./console exp:template:check --design=<design>
```

Ohne Ziel prüft der Befehl die Design-Kette des aktuellen Siteaccess (den `-s` nennt, sonst des Standard-Siteaccess),
wie es das ältere `eztemplatecheck` tut.

Um die Ausgabe zu sehen, legen Sie ein kleines Design mit einem guten und einem fehlerhaften Template je Engine an:

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

Dann prüfen Sie das Verzeichnis:

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

Der Exit-Code ist 1: ein Befund ab dem Schweregrad von `--fail-on` (standardmäßig error). Bestehen alle Dateien, ist er
0 und die letzte Zeile lautet `PASS`.

## 2. Was geprüft wird: Engines und Stufen

Die Engine einer Datei ergibt sich aus ihrer Endung:

| Endung | Engine | Name in der Ausgabe |
|---|---|---|
| `.tpl` | `tpl` | Exponential templates |
| `.stpl` | `stpl` | Smarty templates (Smarty 5) |
| `.twig`, `.html.twig` | `twig` | Twig templates (Twig 3) |

Die Endungen stammen aus `site.ini [TemplateBridgeSettings] EngineSuffixList[]`, derselben Liste, mit der das
Template-System eine Datei ihrer Engine übergibt. `--suffix-map=<endung>:<engine>` ergänzt eine Endung für einen Lauf,
etwa `--suffix-map=.tpl.html:tpl` (wiederholbar). `--engine=tpl,stpl,twig` (eine Liste mit Kommas) beschränkt den Lauf
auf einige Engines; `--engine=all` ist der Standard. Die Engine-Werte `tpl`, `stpl` und `twig` haben auch die Namen
`exp` und `exponential` (für `tpl`) und `smarty` (für `stpl`).

`--level` wählt, wie weit jede Datei geführt wird:

| Stufe | Exponential-Templates (`.tpl`) | Smarty-Templates (`.stpl`) | Twig-Templates (`.twig`) |
|---|---|---|---|
| `syntax` | der Template-Parser baut den Baum ohne Fehler oder Warnung (`eZTemplate::validateTemplateFile()`, der Parser, den `eztemplatecheck` nutzt) | Smarty 5 kompiliert die Datei mit geladener Brücke (Smarty hat keinen eigenen Parse-Schritt) | Twig zerlegt und parst die Datei mit der Brücke, ihren `exp*`-Tags und Node-Visitors |
| `compile` (Standard) | + vom Template-Compiler in ein privates Verzeichnis kompiliert, und das kompilierte PHP geprüft | + das kompilierte PHP geprüft | + im Speicher kompiliert, und das kompilierte PHP geprüft |
| `strict` | + jedes `{include uri='design:...'}` wird aufgelöst | + jedes `{expinclude uri='design:...'}` wird aufgelöst | + die Sandbox-Richtlinie angewendet, jedes `exp_<name>` ohne Operator gemeldet, jedes `{% expinclude uri='design:...' %}` aufgelöst |

Das kompilierte PHP prüfen heißt: PHP selbst parst es (es läuft nicht). So fällt ein Template, das zu PHP kompiliert,
das der Server nicht laden kann, auf, bevor ein Besucher es findet.

Zwei Dinge zum eigenen Parser des Template-Systems: Ein `{if}` oder `{foreach}`, das am Ende einer `.tpl` offen bleibt,
schließt der Parser ohne Meldung, also meldet auch die Prüfung es nicht (die Website verhält sich genauso). Und eine
`.tpl`-Warnung (`TPL-SYNTAX-002`) ist etwas, das der Parser erraten oder ausgelassen hat; das Template wird gerendert.

`--level=strict` läuft auf der Stufe compile mit diesen Prüfungen dazu:

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

Ein Include wird in der Design-Kette des aktuellen Siteaccess aufgelöst: Mit `-s <siteaccess>` lösen Sie in der Kette
des Siteaccess auf, zu dem die Templates gehören. Nur eine konstante `design:`-URI wird aufgelöst; ein Include, dessen
URI erst zur Laufzeit entsteht, lässt sich nicht vorher prüfen.

### Wenn eine Engine nicht verfügbar ist

Smarty 5 und Twig 3 sind Bibliotheken: Ist eine nicht installiert (siehe `exp:template:engines`), bestehen ihre Dateien
nicht, sie werden übersprungen. Die Engine wird einmal als `UNAVAILABLE <engine>` gemeldet, jede ihrer Dateien zählt als
`SKIP`, und der Exit-Code ist 3, außer mit `--allow-missing-engine` (dann bleiben die Dateien übersprungen, und der Lauf
wird nur nach den anderen Dateien beurteilt).

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

`exp:template:engines --format=json` gibt dasselbe als JSON-Dokument aus (`"schema": "exp-template-engines/1"`); sein
Exit-Code ist 3, wenn die Bibliothek einer Engine fehlt.

## 3. Ziele

Beliebig viele, beliebig gemischt:

| Ziel | Was geprüft wird |
|---|---|
| `pfad/zur/datei.tpl`, `.stpl`, `.twig`, `.html.twig` | eine Template-Datei; ihre Engine ergibt sich aus der Endung. Eine Datei ohne bekannte Endung ist ein Bedienfehler (Exit 2). |
| `pfad/zum/verzeichnis` | jede Template-Datei unter dem Verzeichnis, rekursiv, alle Engines; Sicherungskopien von Editoren (`name~`, `#name#`) und versteckte Dateien bleiben außen vor |
| `--design=<name>` | das Design `<name>`, wo es liegt: `design/<name>/` und das `design/<name>/` jeder aktiven Erweiterung; wiederholbar |
| `--design-dir=<verzeichnis>` | ein Design-Verzeichnis per Pfad (ein Design in einem Paket, in einer inaktiven Erweiterung, eine konvertierte Kopie in `var/tmp`); wiederholbar |
| `--extension=<name>` | jedes Design der Erweiterung (`extension/<name>/design/*`); wiederholbar |
| `--extensions` | jedes Design jeder aktiven Erweiterung |
| `--all-extensions` | mit `--design` oder `--extensions`: auch die inaktiven Erweiterungen auf der Platte |
| `--siteaccess=<siteaccess>` | die Design-Kette des Siteaccess in der Reihenfolge, in der das Template-System sie auflöst: `SiteDesign`, `AdditionalSiteDesignList[]`, `StandardDesign`, jeweils in den Erweiterungen aus `design.ini [ExtensionSettings] DesignExtensions[]` und im Kernel; dazu seine Override-Regeln (siehe `--overrides`); wiederholbar |
| `--all-siteaccesses` | jeder Siteaccess aus `site.ini [SiteAccessSettings] AvailableSiteAccessList[]` |
| `-` | ein Template von stdin; `--stdin-engine=tpl\|stpl\|twig` (Standard `tpl`) und `--stdin-name=` für die Meldungen |
| `--files-from=<datei>` | die in der Datei aufgeführten Dateien, eine pro Zeile (`-` liest die Liste von stdin) |
| `--changed-since=<git-referenz>` | die seit der Git-Referenz geänderten Templates und die nicht versionierten; mit anderen Zielen nur die geänderten unter ihnen |
| nichts | die Design-Kette des aktuellen Siteaccess |

Eine Datei, die mehrere Ziele erreichen, wird einmal geprüft. Mit `-v` nennt jeder Befund, wozu die Datei gehört
(`in: design media (extension ...)`, `siteaccess site: ...`), und `--summary-by` gruppiert die Zusammenfassung nach
`engine`, `design`, `extension`, `siteaccess` oder `directory`. Ein ganzer Siteaccess:

```text
$ ./console exp:template:check --siteaccess=twig --engine=twig --summary-by=design
SUMMARY 517 files: 517 OK, 0 failed, 0 SKIP; findings: 0 ERROR, 0 WARN, 0 NOTE  (twig 517)  27.7 s
  design     twig_media                                 517 files, 0 with errors
PASS
```

Ein Template von stdin, für die Anbindung an einen Editor:

```text
$ printf '{{ a|wash }\n' | ./console exp:template:check - --stdin-engine=twig --stdin-name=snippet.twig
ERROR  twig  snippet.twig:1
       Unexpected "}". [TWIG-SYNTAX-001]
       > 1 | {{ a|wash }
SUMMARY 1 files: 0 OK, 1 failed, 0 SKIP; findings: 1 ERROR, 0 WARN, 0 NOTE  (twig 1)  0.1 s
FAIL
```

Ein Template von stdin heißt in den Befunden so, wie `--stdin-name` es nennt (ohne die Option `stdin`), und der Name wird
auch festgehalten als das, wozu es gehört (mit `-v` und im Feld `belongs` der maschinellen Formate).

Ein unbekanntes Design, eine unbekannte Erweiterung oder ein unbekannter Siteaccess, ein Ziel, das es nicht gibt, und ein
falscher Optionswert sind Bedienfehler:

```text
$ ./console exp:template:check --level=deep
FAIL --level must be one of syntax, compile, strict: deep
```

Der Exit-Code ist dann 2.

## 4. Weitere Prüfungen

Diese werden beim Namen eingeschaltet; einige sind bei `--level=strict` oder mit `--siteaccess` an.

| Option | Was sie ergänzt | Standardmäßig an |
|---|---|---|
| `--security` | die Sicherheitsrichtlinie jeder Engine, wie die Website sie anwendet: die Twig-Sandbox aus `twig.ini` (`TWIG-SECURITY-001`); bei Smarty ist, was seine Richtlinie verweigert, schon ein Kompilierfehler (`STPL-SECURITY-001`) | bei `strict` |
| `--includes` | jedes konstante `design:`-Include wird in der Design-Kette des aktuellen Siteaccess aufgelöst | bei `strict` |
| `--overrides` | jede Override-Regel der geprüften Siteaccesses zeigt auf eine vorhandene Datei ihrer Design-Kette (`OVERRIDE-MISSING-FILE`) und auf eine Datei mit der Endung einer Template-Engine (`OVERRIDE-ENGINE-MISMATCH`); ohne `--siteaccess` der aktuelle Siteaccess | mit `--siteaccess` oder `--all-siteaccesses` |
| `--parity` | ein Template, das es nebeneinander in zwei Engines gibt (`x.tpl` und `x.twig`, `x.stpl`), wird als Hinweis gemeldet (`PARITY-PAIR`) | nein |
| `--deprecations` | Twigs Hinweise auf veraltete Konstrukte beim Kompilieren einer `.twig` (`TWIG-DEPRECATION-001`) | nein |
| `--encoding` | gültiges UTF-8 (`ENCODING-UTF8-001`), eine Byte-Order-Mark (`ENCODING-BOM-002`), gemischte Zeilenenden CRLF und LF (`ENCODING-CRLF-003`) | nein |
| `--max-depth=<n>` | für die Tiefe der Includes angenommen; in dieser Version werden Includes eine Ebene tief aufgelöst (die eigenen Includes jeder geprüften Datei), daher ändert die Option noch nichts | — |

`--parity=render` ist experimentell: Es meldet dieselben Paare und sagt, dass der Vergleich der Ausgabe noch nicht
stattfindet.

Zwei weitere Testdateien zeigen sie: derselbe Titel in zwei Engines, und eine `.tpl`, gespeichert mit Byte-Order-Mark
und gemischten Zeilenenden.

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

Die Zusammenfassung zählt Dateien (`OK`: ohne Befund; `failed`: mit einem; `SKIP`: einer nicht verfügbaren Engine) und danach die Befunde nach Schwere.

## 5. Die Ausgabe

### Text (Standard)

Ein Block je Befund, dann die Zusammenfassung und `PASS` oder `FAIL`:

- der Schweregrad (`ERROR`, `WARN`, `NOTE`), die Engine, die Datei relativ zum Wurzelverzeichnis mit Zeile und Spalte,
  wo die Engine sie meldet;
- die Meldung und die Regel-ID in eckigen Klammern;
- die Quellzeile mit `>` und ihrer Nummer, wenn es eine gibt;
- ein Hinweis, wo einer bekannt ist (`hint: did you mean "wash"?`);
- mit `-v`: wozu die Datei gehört (`in: ...`) und eine Zeile `OK` mit Stufe und Zeit für jede bestandene Datei.

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

`-q` ist die übliche Option der Konsole für leisen Betrieb: Die Prüfung gibt dann nur die Befunde der Stufe ERROR aus,
ohne Zusammenfassung und Ergebniszeile; der Exit-Code sagt, ob sie bestanden ist:

```text
$ ./console exp:template:check -q var/tmp/check-docs/rules/broken.twig var/tmp/check-docs/rules/security.stpl
ERROR  twig  var/tmp/check-docs/rules/broken.twig:1
       Unexpected "}". [TWIG-SYNTAX-001]
       > 1 | {{ a|wash }
ERROR  stpl  var/tmp/check-docs/rules/security.stpl:1
       tag 'eval' disabled by security setting [STPL-SECURITY-001]
       > 1 | {eval var='x'}
```

Um das ganze Ergebnis in einer Datei zu behalten, nutzen Sie `--format` mit `--output`.

`--color=auto|always|never`, `--progress` und `--no-progress` werden angenommen; in dieser Version hat die Textausgabe
keine Farben und keinen Fortschrittszähler, daher ändern sie noch nichts. Sie sind Teil des Vertrags und behalten ihre
Bedeutung.

### Maschinelle Formate

`--format=` schreibt statt des Textes ein maschinelles Format nach stdout:

| Format | Für |
|---|---|
| `json` | ein Dokument: `schema`, `summary`, `findings`, `files` |
| `jsonl` | ein Befund je Zeile (`"type": "finding"`), dann eine Zeile mit der Zusammenfassung (`"type": "summary"`); zum Streamen |
| `junit` | Testberichte der CI: eine Testsuite je Engine, ein Testfall je Datei, ein Fehlschlag je Fehler |
| `checkstyle` | Editoren und Werkzeuge für Code-Reviews |
| `github` | Annotationen in GitHub-Actions-Workflows |
| `sarif` | SARIF 2.1.0 für Code-Scanning, mit jeder Regel in der Regelliste des Werkzeugs |

`--output=<datei>` schreibt das maschinelle Format (JSON, wenn das Format `text` ist) in die Datei und lässt den Text
auf der Konsole: Ein CI-Job zeigt ein lesbares Protokoll und legt den Bericht ab.

Das JSON-Dokument (Schema [exp-template-check/1](../../eng-us/reference/template-check-schema.json)):

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

Eine Zeile oder Spalte, die die Engine nicht meldet, ist 0. Die anderen Formate für dieselbe Datei:

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

In JUnit stehen Warnungen und Hinweise als `system-out` ihres Testfalls im Bericht, ohne ihn scheitern zu lassen.
Skripte lesen ein maschinelles Format, nie den Text: Der Text kann in späteren Versionen hilfreicher werden, die
maschinellen Formate behalten ihre Felder (siehe [Kompatibilitätsversprechen](#kompatibilitätsversprechen)).

## 6. Regeln, Schweregrade und der Exit-Code

Jeder Befund trägt eine Regel-ID wie `TPL-SYNTAX-001`, `STPL-SECURITY-001` oder `TWIG-STRICT-UNKNOWN-OPERATOR`.
`--rules` listet alle mit ihrem Schweregrad; `--explain=<id>` erklärt eine:

```text
$ ./console exp:template:check --explain=TWIG-STRICT-UNKNOWN-OPERATOR
TWIG-STRICT-UNKNOWN-OPERATOR (WARN): exp_<name> names no template operator: it passes its input through, as an unknown operator of a .tpl does.
Examples: doc/eng-us/reference/template-check-rules.md#twig-strict-unknown-operator
```

Die [Regel-Referenz](template-syntaxpruefung-regeln.md) erklärt jede Regel mit einem schlechten und einem guten Beispiel.

`--fail-on=error|warn|note` (Standard `error`) legt fest, ab welchem Schweregrad ein Befund den Lauf scheitern lässt.
Eine Warnung allein besteht standardmäßig und scheitert mit `--fail-on=warn`:

```text
$ ./console exp:template:check var/tmp/check-docs/design/templates/menu.twig --level=strict --fail-on=warn
WARN   twig  var/tmp/check-docs/design/templates/menu.twig:2
       exp_washh names no template operator: it passes its input through [TWIG-STRICT-UNKNOWN-OPERATOR]
       > 2 | {{ node.name|exp_washh }}
       hint: did you mean "wash"?
SUMMARY 1 files: 0 OK, 1 failed, 0 SKIP; findings: 0 ERROR, 1 WARN, 0 NOTE  (twig 1)  0.7 s
FAIL
```

Die Exit-Codes:

| Code | Bedeutung |
|---|---|
| 0 | jede geprüfte Datei hat die verlangte Stufe bestanden (`--fail-on` berücksichtigt) |
| 1 | mindestens ein Befund ab `--fail-on` |
| 2 | Bedienfehler: ein unbekannter Optionswert, ein unbekanntes Design, eine unbekannte Erweiterung oder ein unbekannter Siteaccess, ein Ziel, das es nicht gibt |
| 3 | eine Engine, die die Ziele brauchen, ist nicht verfügbar (außer mit `--allow-missing-engine`) |
| 4 | interner Fehler: die Prüfung selbst ist gescheitert (mit `-vv` wird der Stack-Trace ausgegeben) |

Scheitert die Prüfung an einer einzelnen Datei (eine Exception einer Engine), ist das nicht Exit 4, sondern der Befund
`CHECK-INTERNAL-ERROR` an dieser Datei, und die anderen Dateien werden weiter geprüft.

## 7. Strenge Prüfung in einem alten Design einführen: die Baseline

Ein großes altes Design kann bei `--level=strict` viele Befunde haben. Halten Sie die heutigen Befunde einmal fest und
melden Sie von da an nur neue:

```bash
./console exp:template:check --design=<design> --level=strict --generate-baseline=template-check-baseline.json
./console exp:template:check --design=<design> --level=strict --baseline=template-check-baseline.json
```

Mit dem Test-Design:

```text
$ ./console exp:template:check var/tmp/check-docs/design --generate-baseline=var/tmp/check-docs/baseline.json
...
FAIL
$ ./console exp:template:check var/tmp/check-docs/design --baseline=var/tmp/check-docs/baseline.json
SUMMARY 7 files: 7 OK, 0 failed, 0 SKIP; findings: 0 ERROR, 0 WARN, 0 NOTE  (stpl 1, tpl 2, twig 4)  0.6 s
PASS
```

`--generate-baseline` schreibt die Datei und meldet (und beurteilt) den Lauf trotzdem. Ein Eintrag der Baseline ist die
Regel-ID, die Datei und die Meldung, nicht die Zeile: Wer eine Datei an anderer Stelle bearbeitet, macht ihre bekannten
Befunde nicht neu; ein behobener Befund verschwindet für immer, und eine neu erzeugte Baseline wird kürzer. Legen Sie die
Baseline-Datei mit dem Design ins Repository. Eine Datei bei `--baseline`, die keine Baseline ist, ist ein Bedienfehler.

## 8. Dateien und Zeilen auslassen

- `--ignore=<glob>` (wiederholbar) lässt Dateien aus, deren Pfad oder Dateiname zum Muster passt (`*/generated/*`,
  `legacy_*.tpl`).
- `site.ini [TemplateCheckSettings] IgnoreList[]` tut dasselbe für jeden Lauf; setzen Sie es in einem Override oder in
  den Einstellungen einer Erweiterung:

  ```ini
  [TemplateCheckSettings]
  IgnoreList[]
  IgnoreList[]=*/templates/vendor_partials/*
  ```

- Ein Kommentar in der Zeile vor einem Befund lässt ihn aus, in der Kommentar-Syntax jeder Engine; mit einer Regel-ID nur
  diese Regel, ohne jede:

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

## 9. Tempo und Sicherheit

- **Nur lesend.** Kompilierte Ausgabe landet in einem privaten Verzeichnis, `var/cache/template-check/<prozess>-<zufall>/`,
  das am Ende des Laufs entfernt wird; `--keep-compiled` behält es zur Ansicht. Die Template-Caches der Website,
  kompilierte Templates und Einstellungen werden nie geschrieben; Twig- und Smarty-Templates werden im Speicher
  kompiliert.
- **Keine Datenbank** nötig für die Stufen `syntax`, `compile` und `strict`; die Einstellungen eines Siteaccess
  (`--siteaccess`) werden nur aus Dateien gelesen. Kein Webserver nötig.
- **`--jobs=<n>`** prüft in `n` Arbeitsprozessen, wenn es mehr als 20 Dateien sind und PHP die Erweiterung `pcntl` hat;
  jeder Arbeitsprozess läuft mit niedrigerer Priorität (nice 10), was auf einem geteilten Server zählt. Sonst werden die
  Dateien in einem Prozess geprüft. Das Ergebnis ist in beiden Fällen dasselbe.
- **`--changed-since=<git-referenz>`** und **`--files-from=<datei>`** prüfen nur einige Dateien: in einem
  Pre-Commit-Hook, in einem CI-Job für einen Merge-Request.

Auf einer Entwicklungsinstallation prüft die Stufe compile ein ganzes Design aus 517 Twig-Templates in etwa 10 Sekunden,
dasselbe Design als Smarty-Templates in etwa 5.

## 10. CI, Hooks und Editoren

### Ein Git-Pre-Commit-Hook

[`doc/eng-us/examples/hooks/template-check-pre-commit.sh`](../../eng-us/examples/hooks/template-check-pre-commit.sh)
prüft die Templates, die ein Commit ändert, und lehnt den Commit ab, wenn eines nicht kompiliert:

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

Jeder Befund wird zu einer Annotation an seiner Datei und Zeile im Pull-Request.

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

Mit `--output` behält das Job-Protokoll den lesbaren Text, und der JUnit-Bericht geht in den Testbericht von GitLab.

### Code-Scanning und Editoren

`--format=sarif --output=template-check.sarif` erzeugt eine Datei für Werkzeuge zum Code-Scanning (bei GitHub laden Sie
sie mit der Action `github/codeql-action/upload-sarif` hoch). `--format=checkstyle` lesen die Checkstyle-Anbindungen der
meisten Editoren und Review-Werkzeuge. Eine Aufgabe für Visual Studio Code, die die offene Datei prüft:

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

## 11. Engines aus Erweiterungen

Die Prüfung jeder Engine ist eine Klasse, die `expTemplateEngineChecker` umsetzt (`available()` und
`check( $file, $text, $level, $context )`, das die Befunde zurückgibt). Die des Kernels sind `expTplTemplateChecker`,
`expSmartyTemplateChecker` und `expTwigTemplateChecker`. Eine Erweiterung, die eine Template-Engine ergänzt, trägt ihre
Endung in `site.ini [TemplateBridgeSettings] EngineSuffixList[]` ein und ihre Prüfung in `CheckerList[]`; dieselbe
Einstellung ersetzt eine Prüfung des Kernels:

```ini
[TemplateBridgeSettings]
EngineSuffixList[.mytpl]=myengine
CheckerList[myengine]=myEngineTemplateChecker
```

## 12. Das ältere eztemplatecheck

`bin/php/eztemplatecheck.php` (`./console exp:eztemplatecheck`) ist die Syntaxprüfung für Templates, die Exponential seit
langem hat. Sie arbeitet weiter genau wie bisher, mit ihrem Namen, ihren Argumenten, ihrer Ausgabe und ihren Exit-Codes:
Sie prüft `.tpl`-Dateien mit dem Template-Parser, als Argumente angegeben, oder ohne Argumente die Design-Kette des
aktuellen Siteaccess. In dieser Version läuft sie mit ihrem eigenen Code und ruft `exp:template:check` nicht auf. Für
neue Skripte tut `exp:template:check --engine=tpl` dasselbe und mehr (die Stufe compile, Regel-IDs, maschinelle Formate,
die Override-Regeln eines Siteaccess).

## 13. Fehlersuche

| Was Sie sehen | Warum, und was zu tun ist |
|---|---|
| `UNAVAILABLE stpl` oder `UNAVAILABLE twig`, Exit 3 | die Bibliothek der Engine ist nicht installiert: `exp:template:engines` sagt, welche; installieren Sie sie (siehe die Anleitungen zu Smarty und Twig) oder geben Sie `--allow-missing-engine` an |
| `FAIL not a template of a known engine: x.html` | die Endung der Datei steht nicht in der Liste: geben Sie für einen Lauf `--suffix-map=.html:tpl` an oder ergänzen Sie sie in `EngineSuffixList[]` |
| `TWIG-SYNTAX-001 Unknown "x" filter` mit einem Hinweis | ein Name eines Filters oder einer Funktion, der weder ein Template-Operator noch ein Name von Twig ist; der Hinweis nennt den nächstliegenden Operator |
| `TWIG-STRICT-INCLUDE-MISSING` für ein vorhandenes Template | Includes werden in der Kette des aktuellen Siteaccess aufgelöst: Lassen Sie den Befehl mit `-s <siteaccess>` des Designs laufen, zu dem das Template gehört |
| `TWIG-SECURITY-001` für ein Template, das auf einer anderen Installation rendert | jene Installation hat eine andere Sandbox-Richtlinie (`twig.ini SecurityAllowed*List[]`); die Prüfung wendet die Einstellungen dieser Installation an |
| `TPL-COMPILE-001 the compiled template is not valid PHP` | das Template lässt sich parsen, aber der Compiler schreibt PHP, das sich nicht laden lässt; sehen Sie sich die kompilierte Datei mit `--keep-compiled` an |
| Gar nichts mit `-q` | siehe [die Textausgabe](#text-standard): Lesen Sie den Exit-Code, oder nutzen Sie `--format` und `--output` |
| Ein Lauf ist langsam | `--jobs=<n>` auf einem Rechner mit freien Kernen, `--engine` beschränkt die Engines, `--changed-since` die Dateien |
| `--changed-since=HEAD` prüft weit mehr Dateien, als Sie geändert haben | es nimmt auch nicht versionierte Dateien: Template-Kopien in einem Arbeitsverzeichnis, das Git nicht ignoriert, zählen als geändert; tragen Sie das Verzeichnis in `.gitignore` ein oder lassen Sie es mit `--ignore` aus |

## Kompatibilitätsversprechen

Die Befehle `exp:template:*` sind eine öffentliche Schnittstelle. Endentwickler können sich lange auf sie verlassen, ohne
ihre Skripte, CI-Dateien oder Gewohnheiten zu ändern: Das gilt für die ganze Lebenszeit einer Hauptversion und wird über
Hauptversionen hinweg gehalten, solange es geht. Der Vertrag beginnt mit dem ersten Release von `exp:template:*`.

- **Namen sind dauerhaft.** Ein Befehlsname oder Alias wird nach dem Release nie entfernt oder umbenannt; ein besserer
  Name kommt als Alias dazu. Das gilt für `exp:template:check` mit `exp:template:lint` und `exp:template:convert:check`,
  `exp:template:engines`, `exp:template:convert` mit seinen sechs Richtungen und `exp:template:convert:diff`, und für das
  ältere `eztemplatecheck`, das längst veröffentlicht ist und bleibt.
- **Optionen kommen nur dazu.** Eine Option wird nie entfernt, umbenannt oder in ihrer Bedeutung geändert; ihr Standard
  ändert sich nie so, dass ein Lauf, der vorher bestand, jetzt scheitert. Strengere Prüfung wird eingeschaltet, über
  eine neue Option oder einen neuen Wert von `--level`. Kurze Optionen werden nie wiederverwendet.
- **Exit-Codes sind dauerhaft**: 0 bis 4 wie in der Tabelle oben. Ein neuer Exit-Code käme nur über 4 dazu.
- **Die maschinelle Ausgabe ist versioniert.** Die Felder von `exp-template-check/1` werden nie entfernt oder in ihrem
  Typ geändert; neue Felder können dazukommen. Eine inkompatible Änderung käme als `exp-template-check/2` hinter
  `--schema=2`, mit `/1` weiter als Standard und weiter erzeugt. `--schema=1` ist heute der einzige Wert. Die Textausgabe
  kann hilfreicher werden; Skripte lesen ein maschinelles Format.
- **Regel-IDs sind dauerhaft.** Eine Regel kann stillgelegt werden (dann meldet sie nie mehr etwas), ihre ID wird aber nie
  für etwas anderes verwendet.
- **Engine-Werte sind dauerhaft**: `tpl`, `stpl`, `twig` und die Namen `exp`, `exponential` und `smarty`. Eine künftige
  Engine bekommt einen neuen Wert; die Zuordnung der Dateiendungen wächst nur.
- **Eine Abkündigung, falls sie je nötig ist,** braucht mindestens eine ganze Hauptversion mit einer Warnzeile
  (`DEPRECATED ...`, nie auf stdout eines maschinellen Formats), dokumentiert im Changelog und auf der Seite des Befehls,
  bevor sich ein Verhalten ändert; und nur für Optionen, die bei ihrer Einführung als experimentell gekennzeichnet waren.
  `--parity=render` ist heute experimentell.
- **Ein Test hält das fest.** `tests/tests/kernel/private/classes/commands/expTemplateCommandContractTest.php` hält einen
  Schnappschuss der Befehlsnamen, Aliase, Optionsnamen, Exit-Codes, Regel-IDs, Engine-Werte und der Felder des
  JSON-Schemas. Jede Änderung daran lässt den Test scheitern, außer einer Ergänzung, die im selben Commit in den
  Schnappschuss aufgenommen wird.
