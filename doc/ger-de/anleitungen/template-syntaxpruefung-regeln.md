# exp:template:check: Regel-Referenz

Jeder Befund von [exp:template:check](template-syntaxpruefung.md) trägt eine Regel-ID. Diese Seite listet jede Regel:
ihren Schweregrad, die Stufe oder Option, die sie einschaltet, was sie bedeutet, und ein Beispiel eines Templates, das
sie auslöst, mit der Korrektur. `./console exp:template:check --rules` gibt die Liste aus, `--explain=<id>` eine Regel.
Die Meldungen des Befehls sind englisch.

Regel-IDs sind dauerhaft: Eine Regel kann stillgelegt werden (dann meldet sie nie mehr etwas), ihre ID wird aber nie
wiederverwendet ([Kompatibilitätsversprechen](template-syntaxpruefung.md#kompatibilitätsversprechen)).

Die Ausgaben unten stammen aus Läufen auf einer Entwicklungsinstallation. Die maschinelle Ausgabe beschreibt
[das JSON-Schema](../../eng-us/reference/template-check-schema.json) (Englisch); siehe
[Die maschinelle Ausgabe](#die-maschinelle-ausgabe) am Ende dieser Seite.

| Regel | Schweregrad | Engine | Eingeschaltet durch |
|---|---|---|---|
| [TPL-SYNTAX-001](#tpl-syntax-001) | ERROR | tpl | jede Stufe |
| [TPL-SYNTAX-002](#tpl-syntax-002) | WARN | tpl | jede Stufe |
| [TPL-COMPILE-001](#tpl-compile-001) | ERROR | tpl | `compile`, `strict` |
| [TPL-STRICT-INCLUDE-MISSING](#tpl-strict-include-missing) | ERROR | tpl | `strict`, `--includes` |
| [TPL-STRICT-UNKNOWN-OPERATOR](#tpl-strict-unknown-operator) | WARN | tpl | `strict` |
| [STPL-SYNTAX-001](#stpl-syntax-001) | ERROR | stpl | jede Stufe |
| [STPL-COMPILE-001](#stpl-compile-001) | ERROR | stpl | `compile`, `strict` |
| [STPL-SECURITY-001](#stpl-security-001) | ERROR | stpl | jede Stufe, mit `smarty.ini Security=enabled` |
| [STPL-STRICT-INCLUDE-MISSING](#stpl-strict-include-missing) | ERROR | stpl | `strict`, `--includes` |
| [TWIG-SYNTAX-001](#twig-syntax-001) | ERROR | twig | jede Stufe |
| [TWIG-COMPILE-001](#twig-compile-001) | ERROR | twig | `compile`, `strict` |
| [TWIG-SECURITY-001](#twig-security-001) | ERROR | twig | `strict`, `--security` |
| [TWIG-STRICT-UNKNOWN-OPERATOR](#twig-strict-unknown-operator) | WARN | twig | `strict` |
| [TWIG-STRICT-INCLUDE-MISSING](#twig-strict-include-missing) | ERROR | twig | `strict`, `--includes` |
| [TWIG-DEPRECATION-001](#twig-deprecation-001) | WARN | twig | `--deprecations` |
| [ENCODING-UTF8-001](#encoding-utf8-001) | ERROR | alle | `--encoding` |
| [ENCODING-BOM-002](#encoding-bom-002) | WARN | alle | `--encoding` |
| [ENCODING-CRLF-003](#encoding-crlf-003) | NOTE | alle | `--encoding` |
| [OVERRIDE-MISSING-FILE](#override-missing-file) | ERROR | Einstellungen | `--overrides`, `--siteaccess`, `--all-siteaccesses` |
| [OVERRIDE-ENGINE-MISMATCH](#override-engine-mismatch) | WARN | Einstellungen | `--overrides`, `--siteaccess`, `--all-siteaccesses` |
| [PARITY-PAIR](#parity-pair) | NOTE | alle | `--parity` |
| [ENGINE-UNAVAILABLE](#engine-unavailable) | ERROR | stpl, twig | die Bibliothek einer Engine fehlt |
| [CHECK-READ](#check-read) | ERROR | alle | eine Datei lässt sich nicht lesen |
| [CHECK-INTERNAL-ERROR](#check-internal-error) | ERROR | alle | die Prüfung ist an einer Datei gescheitert |

Schweregrade: `ERROR` lässt einen Lauf standardmäßig scheitern; `WARN` und `NOTE` nur mit `--fail-on=warn` oder
`--fail-on=note`.

## TPL-SYNTAX-001

**ERROR**, Exponential-Templates, jede Stufe. Der Template-Parser kann die `.tpl` nicht lesen: ein End-Tag, das nicht
zum offenen passt, ein End-Tag ohne Start-Tag, überzählige Zeichen in einem Ausdruck, ein falsches Anführungszeichen.
Es ist der Parser des Template-Systems (wie ihn `eztemplatecheck` nutzt), also ist die Meldung die, die auch die
Debug-Ausgabe der Website zeigen würde.

Schlecht:

```text
{foreach $items as $item}
    {$item|wash}
{/if}
```

```text
ERROR  tpl   var/tmp/check-docs/design/templates/broken.tpl:3
       Unterminated tag "foreach" does not match tag "if" [TPL-SYNTAX-001]
       > 3 | {/if}
```

Gut: Schließen Sie, was Sie öffnen, `{/foreach}`. Ein anderer Fall, ein schließendes Tag zu viel:

```text
ERROR  tpl   var/tmp/check-docs/rules2/w8.tpl:1:23
       End tag "foreach" without a start tag, ignoring tag [TPL-SYNTAX-001]
       > 1 | {foreach $l as $x}{/foreach}{/foreach}
```

Ein `{if}`, das ganz am Ende einer Datei offen bleibt, schließt der Parser ohne Meldung; die Website verhält sich
genauso, also gibt es keinen Befund.

## TPL-SYNTAX-002

**WARN**, Exponential-Templates, jede Stufe. Der Template-Parser hat beim Lesen der `.tpl` eine Warnung (keinen Fehler)
in sein Protokoll geschrieben: etwas, das er erraten oder ausgelassen hat. Das Template wird gerendert. Die meisten
Warnungen des Template-Systems kommen, wenn ein Template läuft (eine unbekannte Funktion, ein fehlender Parameter eines
Operators), nicht beim Parsen, daher ist diese Regel selten; wenn sie auslöst, ist die Meldung die des Parsers.

## TPL-COMPILE-001

**ERROR**, Exponential-Templates, `compile` und `strict`. Die `.tpl` lässt sich parsen, aber der Template-Compiler kann
sie nicht kompilieren, oder das geschriebene PHP ist ungültig (die kompilierte Datei prüft PHPs eigener Parser, sie läuft
nicht). Die kompilierte Datei wird in ein privates Verzeichnis unter `var/cache/template-check/` geschrieben und am Ende
entfernt; `--keep-compiled` behält sie zur Ansicht. Ist das Kompilieren von Templates aus
(`site.ini [TemplateSettings] TemplateCompile=disabled`), entfällt dieser Schritt.

Korrektur: Ändern Sie das Konstrukt des Templates, das die Meldung nennt; ist das Template richtig und der Compiler
falsch, ist das ein Fehler, den Sie mit der behaltenen kompilierten Datei melden.

## TPL-STRICT-INCLUDE-MISSING

**ERROR**, Exponential-Templates, `strict` oder `--includes`. Ein `{include uri='design:...'}` mit konstanter URI nennt
ein Template, das kein Design der Kette des aktuellen Siteaccess hat. Mit `-s <siteaccess>` lösen Sie in der Kette des
Siteaccess auf, zu dem das Template gehört.

Schlecht:

```text
{include uri='design:parts/no_such_part.tpl'}
```

```text
ERROR  tpl   var/tmp/check-docs/rules/include.tpl:1
       the included template design:parts/no_such_part.tpl is not found in the design chain [TPL-STRICT-INCLUDE-MISSING]
       > 1 | {include uri='design:parts/no_such_part.tpl'}
```

Gut: der Name eines Templates, das es in einem der Designs gibt (`design:parts/title.tpl`), oder legen Sie das fehlende
Template an.

## TPL-STRICT-UNKNOWN-OPERATOR

**WARN**, Exponential-Templates, `strict`. Ein Operator der `.tpl` ist in der Installation nicht registriert: Wenn das
Template läuft, reicht das Template-System die Eingabe des Operators durch und schreibt eine Warnung in die
Debug-Ausgabe. Meist ein Tippfehler oder ein Operator einer Erweiterung, die nicht aktiv ist. Der Hinweis nennt den
nächstliegenden registrierten Operator.

Schlecht:

```text
ok
{$name|wahs}
```

```text
WARN   tpl   var/tmp/check-docs/rules/operator.tpl:2
       the operator wahs is not registered: it passes its input through [TPL-STRICT-UNKNOWN-OPERATOR]
       > 2 | {$name|wahs}
       hint: did you mean "wash"?
```

Gut: `{$name|wash}`, oder aktivieren Sie die Erweiterung, die den Operator hat.

## STPL-SYNTAX-001

**ERROR**, Smarty-Templates, jede Stufe. Smarty 5 kann die `.stpl` mit geladener Brücke (die Template-Operatoren als
Modifikatoren, die `exp*`-Tags und Blöcke) nicht kompilieren: ein offener Block, ein unbekanntes Tag, ein falscher
Ausdruck. Smarty hat keinen eigenen Parse-Schritt, daher deckt diese Regel auch die Stufe `syntax` ab.

Schlecht:

```text
{if $node}
<h1>{$node.name|wash}</h1>
```

```text
ERROR  stpl  var/tmp/check-docs/design/templates/broken.stpl:3
       unclosed {if} tag [STPL-SYNTAX-001]
```

Gut: `{/if}` nach der Überschrift.

## STPL-COMPILE-001

**ERROR**, Smarty-Templates, `compile` und `strict`. Smarty hat die `.stpl` kompiliert, aber das geschriebene PHP ist
ungültig (im Speicher von PHPs eigenem Parser geprüft). In Smartys Kompilierverzeichnis wird nichts geschrieben.

## STPL-SECURITY-001

**ERROR**, Smarty-Templates, jede Stufe, wenn `smarty.ini [SmartySettings] Security=enabled` (der Standard). Die
Sicherheitsrichtlinie von Smarty, wie die Website sie anwendet, verweigert ein Konstrukt der `.stpl`: ein Tag, einen
Modifikator oder eine PHP-Funktion, die die Richtlinie nicht erlaubt. Weil Smarty seine Richtlinie beim Kompilieren
prüft, kommt die Verweigerung mit dem Kompilierschritt.

Schlecht:

```text
{eval var='x'}
```

Smarty verweigert `{eval}`, `{fetch}` und die anderen Tags aus `smarty.ini SecurityDisabledTagList[]`, gleich ob es die
Verweigerung mit "not allowed by security setting" oder mit "disabled by security setting" formuliert:

```text
ERROR  stpl  var/tmp/check-docs/rules/security.stpl:1
       tag 'eval' disabled by security setting [STPL-SECURITY-001]
       > 1 | {eval var='x'}
```

Gut: die überbrückte Form (`{expinclude}` für ein anderes Template, `fetch()` als Template-Operator für Inhalte), oder
erlauben Sie das Konstrukt in den Einstellungen, wenn Sie ihm vertrauen.

## STPL-STRICT-INCLUDE-MISSING

**ERROR**, Smarty-Templates, `strict` oder `--includes`. Ein `{expinclude uri='design:...'}` mit konstanter URI nennt
ein Template, das kein Design der Kette des aktuellen Siteaccess hat.

```text
ERROR  stpl  var/tmp/check-docs/rules/include.stpl:1
       the included template design:parts/no_such_part.tpl is not found in the design chain [STPL-STRICT-INCLUDE-MISSING]
       > 1 | {expinclude uri='design:parts/no_such_part.tpl'}
```

## TWIG-SYNTAX-001

**ERROR**, Twig-Templates, jede Stufe. Twig 3 kann die `.twig` mit der Brücke, ihren Tags und Node-Visitors nicht
zerlegen oder parsen: ein offenes `{{` oder `{%`, ein offener Block, ein unbekanntes Tag, ein unbekannter Filter oder eine
unbekannte Funktion. Bei einem unbekannten Filter oder einer unbekannten Funktion nennt der Hinweis den nächstliegenden
Template-Operator.

Schlecht:

```text
{% if node %}
<h1>{{ node.name|wash }</h1>
{% endif %}
```

```text
ERROR  twig  var/tmp/check-docs/design/templates/broken.twig:2
       Unexpected "}". [TWIG-SYNTAX-001]
       > 2 | <h1>{{ node.name|wash }</h1>
```

Gut: `{{ node.name|wash }}`.

## TWIG-COMPILE-001

**ERROR**, Twig-Templates, `compile` und `strict`. Twig hat die `.twig` geparst, kann sie aber nicht kompilieren, oder
das kompilierte PHP ist ungültig (im Speicher von PHPs eigenem Parser geprüft). In Twigs Cache-Verzeichnis wird nichts
geschrieben.

## TWIG-SECURITY-001

**ERROR**, Twig-Templates, `strict` oder `--security`. Die Twig-Sandbox mit der Richtlinie aus `twig.ini`, wie die
Website sie anwendet (standardmäßig `Security=enabled`), würde ein Tag, einen Filter oder eine Funktion der `.twig`
verweigern. Jeder Template-Operator, jede Template-Funktion, jede Block-Funktion und jedes `exp*`-Tag ist erlaubt; Twigs
eigene Tags, Filter und Funktionen nur so, wie `SecurityAllowedTagList[]`, `SecurityAllowedFilterList[]` und
`SecurityAllowedFunctionList[]` sie aufführen.

Schlecht:

```text
{% do 1 %}
```

```text
ERROR  twig  var/tmp/check-docs/design/templates/parts/sandbox.twig:1
       Tag "do" is not allowed. (the sandbox would refuse the template) [TWIG-SECURITY-001]
       > 1 | {% do 1 %}
```

Gut: `{% set %}` für einen Wert, ein Template-Operator für eine Berechnung, oder ergänzen Sie das Tag in
`SecurityAllowedTagList[]` eines Siteaccess, dessen Templates Sie vertrauen.

## TWIG-STRICT-UNKNOWN-OPERATOR

**WARN**, Twig-Templates, `strict`. Ein Filter oder eine Funktion namens `exp_<name>` nennt keinen Template-Operator. Der
Konverter schreibt `exp_<name>` für einen Operator, dessen Namen Twig selbst hat (`exp_count`, `exp_merge`), und für
einen Operator, den die `.tpl` nutzte, den die Installation aber nicht hat; ein solcher Name reicht seine Eingabe
durch, wie es ein unbekannter Operator einer `.tpl` tut, und schreibt beim Lauf einen Fehler in die Debug-Ausgabe.
Meist ein Tippfehler oder eine Erweiterung, die nicht aktiv ist.

Schlecht:

```text
{{ node.name|exp_washh }}
```

```text
WARN   twig  var/tmp/check-docs/design/templates/menu.twig:2
       exp_washh names no template operator: it passes its input through [TWIG-STRICT-UNKNOWN-OPERATOR]
       > 2 | {{ node.name|exp_washh }}
       hint: did you mean "wash"?
```

Gut: `{{ node.name|wash }}`, oder aktivieren Sie die Erweiterung, die den Operator hat.

## TWIG-STRICT-INCLUDE-MISSING

**ERROR**, Twig-Templates, `strict` oder `--includes`. Ein `{% expinclude uri='design:...' %}` mit konstanter URI nennt
ein Template, das kein Design der Kette des aktuellen Siteaccess hat.

```text
ERROR  twig  var/tmp/check-docs/design/templates/parts/missing.twig:1
       the included template design:parts/no_such_part.tpl is not found in the design chain [TWIG-STRICT-INCLUDE-MISSING]
       > 1 | {% expinclude uri='design:parts/no_such_part.tpl' %}
```

## TWIG-DEPRECATION-001

**WARN**, Twig-Templates, `--deprecations`. Twig hat beim Parsen oder Kompilieren der `.twig` einen Hinweis auf ein
veraltetes Konstrukt ausgegeben: etwas, das noch funktioniert, sich aber in einer späteren Version von Twig ändert. Die
Meldung ist die von Twig und nennt den Ersatz.

## ENCODING-UTF8-001

**ERROR**, jede Engine, `--encoding`. Die Datei ist kein gültiges UTF-8, etwa in ISO-8859-1 gespeichert:

```text
ERROR  tpl   var/tmp/check-docs/rules/latin1.tpl
       the file is not valid UTF-8 [ENCODING-UTF8-001]
```

Gut: Speichern Sie die Datei als UTF-8.

## ENCODING-BOM-002

**WARN**, jede Engine, `--encoding`. Die Datei beginnt mit einer Byte-Order-Mark. Die Engines geben sie aus, sie landet
also vor dem Doctype der Seite oder mitten in einem Include.

```text
WARN   tpl   var/tmp/check-docs/pair/bom.tpl:1:1
       the file starts with a byte order mark, which is printed [ENCODING-BOM-002]
```

Gut: Speichern Sie die Datei als UTF-8 ohne Byte-Order-Mark.

## ENCODING-CRLF-003

**NOTE**, jede Engine, `--encoding`. Die Datei mischt Zeilenenden CRLF und LF. Die Engines geben die Zeilenenden so aus,
wie sie sind, eine Seite kann also beide enthalten; meist wurde die Datei auf zwei Systemen bearbeitet.

```text
NOTE   tpl   var/tmp/check-docs/pair/bom.tpl
       the file mixes CRLF and LF line endings [ENCODING-CRLF-003]
```

## OVERRIDE-MISSING-FILE

**ERROR**, Einstellungen, mit `--overrides`, `--siteaccess` oder `--all-siteaccesses`. Eine Override-Regel eines
Siteaccess (`override.ini [<regel>] MatchFile=`) zeigt auf eine Datei, die kein Design der Kette des Siteaccess unter
`override/<Subdir>/` hat. Der Befund steht an der Datei `settings/siteaccess/<siteaccess>/override.ini.append.php` des
Siteaccess, und seine Meldung hat die Form `[<regel>] MatchFile=<datei>: no design of the siteaccess <siteaccess> has it`.

Gut: Korrigieren Sie `MatchFile`, oder legen Sie das Template in einem Design der Kette an.

## OVERRIDE-ENGINE-MISMATCH

**WARN**, Einstellungen, mit `--overrides`, `--siteaccess` oder `--all-siteaccesses`. Eine Override-Regel zeigt auf eine
vorhandene Datei, deren Endung aber keiner Template-Engine gehört (`full/article.html` statt `full/article.twig`), sodass
das Template-System sie als `.tpl` lesen würde.

## PARITY-PAIR

**NOTE**, jede Engine, `--parity`. Dasselbe Template gibt es nebeneinander in zwei oder mehr Engines, etwa `title.tpl`
und `title.twig` in einem Verzeichnis. Nützlich nach einer Konvertierung, um zu sehen, welche Templates ein Gegenstück
haben; welche Datei die Website nutzt, entscheiden die Override-Regeln.

```text
NOTE   tpl   var/tmp/check-docs/pair/title.tpl
       the same template in 2 engines: var/tmp/check-docs/pair/title.tpl, var/tmp/check-docs/pair/title.twig [PARITY-PAIR]
```

`--parity=render` (experimentell) meldet dasselbe und sagt, dass der Vergleich der Ausgabe noch nicht stattfindet.

## ENGINE-UNAVAILABLE

**ERROR**, Smarty- und Twig-Templates. Die Engine der Datei ist nicht verfügbar: Ihre Bibliothek (Smarty 5 oder Twig 3)
ist nicht installiert, oder die für sie in `site.ini [TemplateBridgeSettings] CheckerList[]` eingetragene Prüfung gibt
es nicht. Der Befund kommt einmal je Engine, an der ersten ihrer Dateien; die Dateien werden übersprungen (`SKIP`), die
Konsole gibt `UNAVAILABLE <engine>` aus, und der Exit-Code ist 3, außer mit `--allow-missing-engine`. Dieser Befund zählt
nie für `--fail-on`. `./console exp:template:engines` zeigt, welche Engines verfügbar sind.

## CHECK-READ

**ERROR**, jede Engine. Die Datei lässt sich nicht lesen (Rechte, ein defekter Link).

## CHECK-INTERNAL-ERROR

**ERROR**, jede Engine. Die Prüfung der Engine ist an der Datei mit einer Exception gescheitert; die Meldung nennt sie.
Die anderen Dateien werden weiter geprüft, und der Exit-Code ist 1, nicht 4 (4 steht für ein Scheitern des Befehls
selbst). Melden Sie es mit der Datei.

## Die maschinelle Ausgabe

`--format=json` schreibt ein Dokument des Schemas `exp-template-check/1`, beschrieben als JSON Schema in
[template-check-schema.json](../../eng-us/reference/template-check-schema.json):

| Feld | Inhalt |
|---|---|
| `schema` | immer `exp-template-check/1` |
| `summary` | `files`, `ok` (Dateien ohne Befund), `skipped`, `errors`, `warnings`, `notes` (Anzahl der Befunde), `engines` (Dateien je Engine), `unavailable` (Engines), `seconds`, `level`, `exit` (der Exit-Code), `result` (`PASS` oder `FAIL`) |
| `findings` | ein Objekt je Befund: `rule`, `severity`, `engine`, `file`, `line` und `column` (0, wenn unbekannt), `message`, `hint` (oder null), `source` (die Quellzeile, oder null), `belongs` (wozu die Datei gehört: Designs, Erweiterungen, Siteaccesses) |
| `files` | ein Objekt je geprüfter Datei: `file`, `engine`, `status` (`OK`, `ERROR`, `WARN`, `NOTE`, `SKIP`), `ms`, `belongs` |

`--format=jsonl` schreibt dieselben Befunde einen je Zeile mit `"type": "finding"` und die Zusammenfassung als letzte
Zeile mit `"type": "summary"`; jede Zeile trägt auch `schema`. In späteren Versionen können Felder dazukommen, sie werden
nie entfernt oder in ihrem Typ geändert.
