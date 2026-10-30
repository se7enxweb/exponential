# Fehlersuche in Twig-Templates

Diese Anleitung ist für Template-Entwickler, die herausfinden müssen, warum ein `.twig` nichts druckt, das Falsche
druckt oder langsam ist. Sie geht die Werkzeuge in der Reihenfolge durch, in der Sie sie benutzen würden: die Prüfung vor
der Seite, die Meldung in der Seite, die Debug-Leiste, die Kommentare mit Template-Pfaden, das Twig-Profil und das Log.
Jede Meldung und jede Ausgabe unten wurde in einer Entwicklungsinstallation gedruckt; Dateipfade unterhalb des
Installationsverzeichnisses sind zu `<root>` gekürzt.

## 1. Prüfen, bevor Sie eine Seite ansehen

Die meisten Fehler in einem `.twig` sind Kompilierfehler, und `exp:template:check` findet sie ohne Webserver, Datenbank
oder Besucher:

```bash
./console exp:template:check extension/<ext>/design/<design>/templates --level=strict
```

Bei drei kleinen Dateien mit typischen Fehlern:

```
ERROR  twig  var/tmp/twigdocs/check/article.twig:3
       Tag "do" is not allowed. (the sandbox would refuse the template) [TWIG-SECURITY-001]
       > 3 | {% do 1 %}
WARN   twig  var/tmp/twigdocs/check/article.twig:2
       exp_upper names no template operator: it passes its input through [TWIG-STRICT-UNKNOWN-OPERATOR]
       > 2 | {{ node.name|exp_upper }}
ERROR  twig  var/tmp/twigdocs/check/article.twig:4
       the included template design:parts/no_such_part.tpl is not found in the design chain [TWIG-STRICT-INCLUDE-MISSING]
       > 4 | {% expinclude uri='design:parts/no_such_part.tpl' %}
ERROR  twig  var/tmp/twigdocs/check/typo.twig:1
       Unknown "wahs" filter. [TWIG-SYNTAX-001]
       > 1 | {{ node.name|wahs }}
       hint: did you mean "wash"?
ERROR  twig  var/tmp/twigdocs/check/unclosed.twig:3
       Unexpected end of template. [TWIG-SYNTAX-001]
SUMMARY 3 files: 0 OK, 3 failed, 0 SKIP; findings: 4 ERROR, 1 WARN, 0 NOTE  (twig 3)  0.1 s
FAIL
```

Der Exit-Code war 1. Was jede Regel bedeutet:

| Regel | Stufe | Was sie findet | Was zu tun ist |
|---|---|---|---|
| `TWIG-SYNTAX-001` | jede Stufe | Twig kann die Datei nicht parsen oder kompilieren: ein unbekannter Filter, eine unbekannte Funktion oder ein unbekanntes Tag, ein nicht geschlossenes `{% if %}`, ein verirrtes `}` | Meldung und Zeile lesen; bei einem unbekannten Namen schlägt der Hinweis den nächstliegenden vor |
| `TWIG-COMPILE-001` | `compile`, `strict` | das kompilierte PHP ist ungültig | selten; mit dem Template melden |
| `TWIG-SECURITY-001` | `strict`, `--security` | ein Tag, Filter oder eine Funktion, die die Sandbox ablehnt | ein erlaubtes Konstrukt nehmen oder es in der `twig.ini` des Siteaccess erlauben |
| `TWIG-STRICT-UNKNOWN-OPERATOR` | `strict` | ein `exp_<name>`, das kein Operator ist; es kompiliert, reicht seine Eingabe aber durch | den Namen korrigieren (`upcase`, nicht `exp_upper`) oder die Extension aktivieren, die den Operator hat |
| `TWIG-STRICT-INCLUDE-MISSING` | `strict`, `--includes` | ein `expinclude`, dessen `design:`-URI in der Design-Kette nicht aufgelöst wird | die URI korrigieren oder das Template anlegen |
| `TWIG-DEPRECATION-001` | `--deprecations` | ein Konstrukt, das Twig als veraltet markiert | umschreiben, bevor die nächste Twig-Version es entfernt |

Warum der unbekannte Operator nur eine Warnung ist: `exp_<name>` ist ein gültiger Filtername in Twig, das Template
kompiliert also, und zur Laufzeit macht ein unbekannter Operator, was er in einem `.tpl` macht: Er reicht seine Eingabe
durch. `--level=strict` ist der einzige Weg, ihn zu sehen, bevor es eine Seite tut.
`./console exp:template:check --explain=<regel>` erklärt eine Regel; die
[Regelreferenz](template-syntaxpruefung-regeln.md) listet alle mit guten und schlechten Beispielen.

Für einen Editor oder die CI druckt `--format=jsonl` einen Befund je Zeile:

```
{"schema":"exp-template-check/1","type":"finding","rule":"TWIG-SYNTAX-001","severity":"ERROR","engine":"twig","file":"var/tmp/twigdocs/check/typo.twig","line":1,"column":0,"message":"Unknown \"wahs\" filter.","hint":"did you mean \"wash\"?","source":"{{ node.name|wahs }}","belongs":[]}
{"schema":"exp-template-check/1","type":"summary","files":1,"ok":0,"failed":1,"skipped":0,"errors":1,"warnings":0,"notes":0,"engines":{"twig":1},"unavailable":[],"seconds":0.046,"level":"compile","exit":1,"result":"FAIL"}
```

([Template-Syntax prüfen](template-syntaxpruefung.md) hat jede Option, die anderen Formate und einen Pre-Commit-Hook.)

## 2. Die Meldung in der Seite

Ein `.twig`, das nicht kompiliert oder beim Ausführen scheitert, druckt an seiner Stelle nichts, sodass ein Besucher nie
einen Stacktrace sieht. Mit eingeschalteter Debug-Ausgabe hinterlässt es einen HTML-Kommentar mit dem Grund; sehen Sie
in den Seitenquelltext und suchen Sie nach `twig:`:

```
<!-- twig: rendering <root>/var/tmp/doc-examples/example.twig failed: Unknown &quot;wahs&quot; filter in &quot;var/tmp/doc-examples/example.twig&quot; at line 1. (example.twig:1) -->
```

So lesen Sie ihn:

- `rendering <root>/.../example.twig failed:` die Datei, die scheiterte, mit vollem Pfad: die Datei, die die
  Design-Ressource oder eine Override-Regel gewählt hat, und das ist nicht immer die, die Sie gerade bearbeiten.
- `Unknown "wahs" filter in "var/tmp/doc-examples/example.twig" at line 1.` Twigs eigene Meldung, mit dem Namen des
  Templates (sein Pfad unterhalb des Installationsverzeichnisses) und der Zeile im `.twig`.
- `(example.twig:1)` wo der Fehler geworfen wurde. Bei einem Twig-Fehler ist das wieder das Template; kam der Fehler aus
  PHP-Code, den das Template aufrief (ein Operator, eine Fetch-Funktion), steht dort jene PHP-Datei mit Zeile. Das sagt
  Ihnen: Das Template war in Ordnung, der Operator ist gescheitert.

Derselbe Text, ohne HTML-Maskierung, geht in die Debug-Ausgabe (Label `twig`) und nach `var/log/error.log`.

### Die Meldungen, denen Sie begegnen

| Meldung | Ursache | Abhilfe |
|---|---|---|
| `Unknown "x" filter.` / `Unknown "x" function.` | kein Operator, keine Template-Funktion und kein Twig-Filter dieses Namens in diesem Siteaccess | ein Tippfehler, oder die Extension, die ihn registriert, ist hier nicht aktiv |
| `Filter "x" is not allowed` / `Function "x" is not allowed` / `Tag "x" is not allowed` | die Sandbox lehnt eines von Twigs eigenen Konstrukten ab (`filter`, `map`, `spaceless`, `do`, `autoescape` ...) | ein erlaubtes Konstrukt nehmen oder es in `[TwigSettings] SecurityAllowed...List[]` eines vertrauenswürdigen Siteaccess erlauben |
| `Calling "name" method on a "expTwigObjectProxy" object is not allowed` | ein Methodenaufruf: `node.name()` | das Attribut lesen: `node.name` |
| `Unexpected end of template.` | ein Tag geöffnet und nie geschlossen (`{% if %}` ohne `{% endif %}`) | schließen; die Zeile ist die des Dateiendes, suchen Sie das öffnende Tag weiter oben |
| `Unknown operator upper` (nur in der Debug-Ausgabe, die Seite druckt die Eingabe) | `exp_upper`: das Präfix `exp_` vor einem Namen, der kein Operator ist | der echte Name des Operators (`upcase`); siehe Abschnitt 1 |
| `No template could be loaded for "parts/x.tpl" using resource "design"` (eine Warnung, das Include druckt nichts) | `expinclude` einer URI, die die Design-Kette nicht hat | die URI korrigieren; `--level=strict` meldet es als `TWIG-STRICT-INCLUDE-MISSING` |
| `template not found: <datei>` | die Datei, die der kompilierte Stub nennt, ist weg (umbenannt oder gelöscht) | die Template-Caches leeren: `./console exp:ezcache --clear-tag=template` |
| `the Twig library is not available (...)` / `Twig 3 not found ...` | Twig ist für dieses PHP nicht installiert | [Versionen und Kompatibilität](twig-templates-versionen-und-kompatibilitaet.md#wenn-twig-nicht-installiert-ist) |
| `a loop in <datei> stopped after 10000 rounds ([TwigSettings] WhileLoopLimit)` | ein umgewandeltes `{while}` oder `{do}`, das nicht endet | die Bedingung korrigieren oder das Limit anheben |

## 3. Welches Template geantwortet hat: Pfadkommentare und die Debug-Leiste

**Kommentare mit Template-Pfaden.** Mit `[TemplateSettings] ShowTemplatePathComments=enabled` (oder `Debug=enabled`) in
der `site.ini.append.php` eines Entwicklungs-Siteaccess wird jedes Template in Kommentare gehüllt, die seine Datei und die
angefragte URI nennen, und `.twig`-Dateien nehmen daran teil wie jede andere:

```
<!-- START: including template: <root>/var/tmp/doc-examples/example.twig (<root>/var/tmp/doc-examples/example.twig) -->
Why Track Your Workouts?07/23/2018 x "/fitness/why-track-your-workouts"

<!-- START: including template: var/site/cache/twig/functions/attribute_view_gui-d2eb444e35c0a71f0a85df8194acb5b6.tpl (file:var/site/cache/twig/functions/attribute_view_gui-d2eb444e35c0a71f0a85df8194acb5b6.tpl) -->

<!-- START: including template: design/standard/templates/content/datatype/view/ezstring.tpl (design:content/datatype/view/ezstring.tpl) -->

Why Track Your Workouts?
<!-- STOP: including template: design/standard/templates/content/datatype/view/ezstring.tpl (design:content/datatype/view/ezstring.tpl) -->

<!-- STOP: including template: var/site/cache/twig/functions/attribute_view_gui-d2eb444e35c0a71f0a85df8194acb5b6.tpl (file:var/site/cache/twig/functions/attribute_view_gui-d2eb444e35c0a71f0a85df8194acb5b6.tpl) -->

<!-- STOP: including template: <root>/var/tmp/doc-examples/example.twig (<root>/var/tmp/doc-examples/example.twig) -->
```

Zwei Dinge fallen auf. Eine Template-Funktion wie `attribute_view_gui` läuft über ein kleines erzeugtes `.tpl` in
`<Cache-Verzeichnis>/twig/functions/`, das die eigene Funktion des Template-Systems aufruft; das Template des Datentyps
(`ezstring.tpl` hier) wird also über die Override-Regeln gewählt, genau wie aus einem `.tpl`. Und die Kommentare
zerstören nichts, das in ein Start-Tag druckt: Der Kernel lässt sie bei Templates weg, die in
`TemplatesWithoutDebugComments` stehen. Schalten Sie diese Kommentare nie für einen Produktions-Siteaccess ein: Sie
machen jede Seite größer und zeigen Ihre Dateistruktur.

**Die Debug-Leiste.** Mit eingeschalteter Debug-Ausgabe zeigt die Exp-Debug-Leiste:

- **Templates**: den Baum der Templates, die die Seite gerendert hat, mit `expinclude` eingebundene `.twig`-Dateien
  darunter, jedes mit der angefragten URI und der Datei, die geantwortet hat.
- **Rendering**: den Controller, was der Content-View-Cache tat und seinen Schlüssel, wie viele Templates für diese
  Anfrage kompiliert wurden, und die Cache-Blöcke: Ein `{% expcacheblock %}` erscheint als sein Template mit Nummer
  (`pagelayout.twig id=4`), mit Treffer oder Fehlschlag, seinen Schlüsseln und seiner Cache-Datei.

Eine Seite, die sich nach einer Änderung an einem `.twig` „nicht ändert“, kommt fast immer aus einem dieser Caches: Der
Reiter Rendering sagt, aus welchem. Siehe [Caching](../../eng-us/guides/twig-templates-caching.md) (Englisch).

## 4. Das Twig-Profil

Mit `[TwigSettings] Profile=enabled` und eingeschalteter Debug-Ausgabe schreibt jede Seite einen Hinweis mit dem Label
`twig profile`, wohin die Zeit des Twig-Anteils der Seite ging. Für das dreizeilige Template aus Abschnitt 3, auf der
Kommandozeile einer Entwicklungsinstallation gerendert (dort ohne OPcache, das Laden kompilierter Klassen kostet also
mehr als unter einem Webserver), der zweite Lauf:

```
environment built (once per process)           1 calls      8.9 ms
render() of a .twig from the template system     1 calls     65.5 ms
variables in (wrap)                            1 calls      0.5 ms
load (compiled template)                       1 calls     30.3 ms
operators (filters)                            2 calls     12.8 ms
template functions (*_view_gui ...)            1 calls     13.7 ms
variables out (write-back)                     1 calls      0.0 ms
  |ezurl                                       1 calls     11.8 ms
  |l10n                                        1 calls      0.9 ms
  {% expcacheblock %} hit twig:var/tmp/doc-examples/example.twig:0     1 calls      0.0 ms
```

So lesen Sie es:

| Zeile | Bedeutung | Wenn sie hoch ist |
|---|---|---|
| environment built | Aufbau der Twig-Umgebung; einmal je Prozess (einmal je Velocity-Worker) | unter PHP-FPM bei jeder Anfrage bezahlt; normal |
| render() | alle vom Template-System gerenderten `.twig`, inklusive | die Summe; mit der Skriptzeit der Seite vergleichen |
| variables in / out | die Variablen des Namensraums an Twig übergeben und zurückschreiben, was das Template gesetzt hat | viele große Arrays im Namensraum |
| load (compiled template) | Laden der kompilierten Klassen | die erste Anfrage nach einer Änderung kompiliert; ohne OPcache liest jede Anfrage die Dateien |
| operators (filters), template functions | Zeit in den Operatoren und Funktionen des Template-Systems | die eingerückten Zeilen ansehen |
| Zeilen `\|name` | jeder Operator nach Namen, der langsamste zuerst | ein Operator in einer Schleife; `ezurl` und `i18n` mit Argumenten sind die üblichen |
| Zeilen `{% expcacheblock %}` | jeder Cache-Block mit `hit` oder `miss` | ein Block, der bei jeder Anfrage verfehlt: Seine Schlüssel ändern sich zu oft |

Der erste Lauf desselben Templates hatte `miss` für den Cache-Block, `load` 35,8 ms (das Template wurde kompiliert) und
die Template-Funktion bei 51,1 ms (ihr Datentyp-Template wurde ebenfalls kompiliert): die Kosten eines kalten Caches,
einmal bezahlt.

Lassen Sie `Profile` in der Produktion ausgeschaltet; es kostet einen Zeitmesser je Aufruf.

## 5. Twigs eigener Debug-Modus

`[TwigSettings] Debug=enabled` schaltet Twigs Debug-Modus und seine Funktion `dump()` ein. `dump` steht nicht in der
Liste der erlaubten Funktionen der Sandbox; nehmen Sie sie für einen Entwicklungs-Siteaccess auf:

```ini
# settings/siteaccess/<Entwicklungs-Siteaccess>/twig.ini.append.php
[TwigSettings]
Debug=enabled
SecurityAllowedFunctionList[]=dump
```

Mit beidem druckte `{{ dump(user_name) }}` die Ausgabe `string(3) "Ada"`.

Der eigene Operator `attribute` des Template-Systems mit `show` ist bei Inhaltsobjekten oft nützlicher, weil er die
Attribute zeigt, die das Objekt anbietet, mit Typ und Wert, statt PHP-Interna: `{{ node|attribute('show', 1) }}` druckte
eine Tabelle, die mit `node_id` (integer, 98), `parent_node_id` (integer, 92), `main_node_id`, `contentobject_id` beginnt
und durch jedes Attribut des Knotens geht.

## 6. Eine Änderung, die sich nicht zeigt

Gehen Sie diese Liste der Reihe nach durch:

1. **Ein Cache hat geantwortet.** Der Reiter Rendering der Debug-Leiste oder der Antwort-Header `X-Exp-Cache` sagt,
   welcher. Leeren Sie den View-Cache (`./console exp:ezcache --clear-id=content`) oder den Cache der Template-Blöcke
   (`--clear-id=template-block`).
2. **Eine andere Datei antwortet.** Die Pfadkommentare oder der Reiter Templates nennen die Datei; eine Override-Regel
   eines Siteaccess oder ein Design weiter oben in der Liste kann gewinnen.
3. **AutoReload ist aus.** Mit `[TwigSettings] AutoReload=disabled` wird ein geändertes `.twig` nicht neu kompiliert;
   leeren Sie die kompilierten Templates: `./console exp:ezcache --clear-id=twig`.
4. **Das Template ist gescheitert.** Suchen Sie im Seitenquelltext nach `<!-- twig:` oder prüfen Sie die Datei mit
   `exp:template:check`.

Velocitys Worker kompilieren ein geändertes `.twig` bei der nächsten Anfrage ohne Neustart: Der Name der kompilierten
Klasse enthält die Änderungszeit der Datei.

## Verwandte Seiten

- [Template-Syntax prüfen](template-syntaxpruefung.md) und die [Regeln](template-syntaxpruefung-regeln.md)
- [Twig-Templates im Editor](twig-templates-im-editor.md)
- [Geschwindigkeit der drei Template-Engines](template-engines-geschwindigkeit.md)
- [Debugging Twig templates](../../eng-us/guides/twig-templates-debugging.md) (Englisch)
- [Twig templates: troubleshooting](../../eng-us/guides/twig-templates-troubleshooting.md) (Englisch)
