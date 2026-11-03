# .twig-Templates schreiben und ausführen

Diese Anleitung ist für Template-Entwickler, die Twig-3-Templates für ein Exponential-Design schreiben. Sie erklärt, wie
ein `.twig` gefunden und gerendert wird, was es sieht, wie es das Template-System aufruft und was es nicht kann. Die
Funktionsseite [Twig-Templates](../funktionen/6.0/twig-templates.md) hat den Überblick und die Einstellungen; die ersten
Schritte stehen in [Ein erstes .twig, Schritt für Schritt](twig-templates-tutorial.md).

## Wie ein .twig gerendert wird

Das Template-System findet Templates über die Design-Ressource: Override-Regeln, der Design-Fallback und `design:`-URIs
funktionieren für jede Dateiendung. Endet die gefundene Datei auf `.twig` oder `.html.twig`
(`settings/site.ini [TemplateBridgeSettings] EngineSuffixList[]`), parst das Template-System sie nicht. Es parst an ihrer
Stelle einen einzeiligen Stub:

```
{exp_twig_render( 'extension/<ext>/design/<design>/templates/node/view/full.twig' )}
```

Der Stub wird kompiliert und gecacht wie jedes `.tpl`. Wenn er läuft, übergibt `exp_twig_render` die Datei an
`expTwigRenderer::render()`, das sie mit Twig rendert und den Text zurückgibt. So bleibt alles um das Template herum
Sache des Template-Systems: der View-Cache, die `{cache-block}`s des aufrufenden Templates, die Debug-Leiste (der Reiter
Rendering nennt sowohl die `.tpl`-URI als auch die `.twig`-Datei, die darauf geantwortet hat), `{include}` aus einem
`.tpl`, Override-Regeln.

Je Prozess und je Design-Kette wird eine Twig-Umgebung gehalten. Twig kompiliert ein `.twig` einmal zu einer PHP-Klasse;
die Klasse liegt in `<cache directory>/twig/compile-secure` (oder `compile` bei abgeschalteter Sandbox) und wird mit den
Template-Caches geleert (`./console exp:ezcache --clear-tag=template`). Der Klassenname trägt die Änderungszeit der
Datei, den Siteaccess und die Locale, sodass eine geänderte Datei auch in einem dauerhaften Worker neu kompiliert wird
und Texte, die beim Kompilieren des Templates übersetzt wurden (siehe unten), nie in eine andere Sprache durchsickern.

## Einrichtung

Es muss nichts aktiviert werden. Twig 3 muss installiert sein (`./console exp:template:engines` zeigt es an); siehe
[Twig installieren](../funktionen/6.0/twig-templates.md#twig-installieren). Legen Sie `.twig`-Dateien dorthin, wo
`.tpl`-Dateien hingehören:

```
extension/<ext>/design/<design>/templates/pagelayout.twig
extension/<ext>/design/<design>/override/templates/full/article.twig
```

und richten Sie Override-Regeln auf sie wie auf jedes Template:

```ini
[full_article]
Source=node/view/full.tpl
MatchFile=full/article.twig
Subdir=templates
Match[class_identifier]=article
```

`Source` bleibt die `.tpl`-URI, nach der der Kernel fragt; nur `MatchFile` nennt das `.twig`. Ein `.twig` kann auch über
seine eigene URI gefunden werden (`design:parts/menu.twig`). Wenn der Konverter ein Design schreibt, schreibt er diese
Override-Regeln für jedes Template, das er umwandelt (siehe [Der Konverter](twig-templates-konverter.md)).

## Variablen

Ein `.twig` sieht die Variablen des Namensraums, in dem es läuft, wie ein `.tpl`: `node`, `view_parameters`,
`module_result`, `site`, `current_user`, die Parameter des `{include}`, das es eingebunden hat. Die Werte sind die des
Template-Systems selbst: Ein Knoten ist ein `eZContentObjectTreeNode` hinter einem Proxy, sodass `node.name`,
`node.data_map.title.content` und `node.object.published` Attribute genau so lesen wie `$node.name`.

```twig
{{ node.name|wash }} - {{ node.url_alias|ezurl }}
```

```
Why Track Your Workouts? - "/fitness/why-track-your-workouts"
```

(`ezurl` fügt die Anführungszeichen hinzu, wie in einem `.tpl`; `ezurl('no')` lässt sie weg.)

Was ein `.twig` mit `{% set %}` setzt, wird an seinem Ende in den Namensraum zurückgeschrieben, wie es `{def}` und
`{set}` in einem `.tpl` tun: Ein Pagelayout, ein Modul oder ein Mail-Template, das `$title` nach dem Include liest, sieht
den Wert. Eine Variable, die auf `null` gesetzt wird, wird entfernt, wie es `{undef}` tut. Twigs eigene Variablen
(`_context`, `loop`, `_self`) werden nicht zurückgeschrieben.

## Operatoren

Jeder Template-Operator der Installation und ihrer Extensions ist ein Twig-Filter und eine Twig-Funktion. Ein Filter ist
der Operator mit einer Eingabe, eine Funktion der Operator ohne Eingabe, genau so, wie das `.tpl` ihn schreibt:

| `.tpl` | `.twig` |
|---|---|
| `{$user_name\|wash}` | `{{ user_name\|wash }}` |
| `{$list\|implode( ', ' )}` | `{{ list\|implode(', ') }}` |
| `{cond( $price\|gt( 10 ), 'expensive', 'cheap' )}` | `{{ cond(price\|gt(10), 'expensive', 'cheap') }}` |
| `{concat( 'a', 'b', $user_name )}` | `{{ concat('a', 'b', user_name) }}` |
| `{first_set( $missing, 'a default' )}` | `{{ first_set(missing, 'a default') }}` |

Wo Twig einen Filter oder eine Funktion gleichen Namens hat (`count`, `merge`, `date`, `round` ...), heißt der Operator
`exp_<name>`: `{{ list|exp_count }}`. Twigs eigener behält seinen Namen und seine Bedeutung. Die Regeln, die
Eingabemodi und `fetch()` stehen in [Operatoren und Fetch-Funktionen](twig-templates-operatoren.md); jeder Operator mit
seinen Formen steht in der [Operator-Referenz](twig-templates-operator-referenz.md).

Die Logik- und Rechenoperatoren (`eq`, `gt`, `and`, `sum`, `mod` ...), `cond`, `first_set`, `wash`, `implode`,
`contains` und `concat` laufen direkt in PHP mit genau den Ergebnissen des Template-Systems; jeder andere Operator läuft
durch den eigenen Code des Template-Systems und verhält sich deshalb ohne Ausnahme wie in einem `.tpl`.

## fetch()

```twig
{% set children = fetch('content', 'list', {parent_node_id: node.parent_node_id, limit: 3}) %}
{{ children|length }} children: {% for child in children %}{{ child.name|wash }}; {% endfor %}
```

```
3 children: 7 Keystone Habits to Maximize Your Mornings; The BiPolar Learning Curve; Split Options Weight Training Program;
```

Die Parameter sind ein Twig-Hash (`{key: value}`), das `hash( 'key', value )` des `.tpl`.

## Schleifen

Twigs `{% for %}` läuft über Arrays und über jede Liste, die das Template-System zurückgibt. `loop.index`, `loop.first`
und `loop.last` ersetzen die Zähler im Stil von `$item@index` und `{delimiter}` des `.tpl`:

```twig
{% for item in list %}{{ loop.index }}:{{ item }} {% endfor %}
```

```
1:a 2:b 3:c
```

Eine umgewandelte `{section}` läuft über `expsectionloop(...)`, ein umgewandeltes `{while}` und `{do}` über das Tag
`expwhile` (höchstens `[TwigSettings] WhileLoopLimit` Runden). Twig hat kein `break` und kein `continue`; der Konverter
schreibt sie als Flags, handgeschriebene Templates nehmen `{% for x in list|filter(...) %}` oder `{% if %}`.

## Template-Funktionen und Includes

Die `*_view_gui`-Funktionen und jede andere Template-Funktion ohne Rumpf sind Twig-Funktionen, die einen Hash nehmen:

```twig
{{ attribute_view_gui({attribute: node.data_map.body}) }}
{{ node_view_gui({content_node: child, view: 'line'}) }}
```

Eine Funktion mit Rumpf ist ein Tag: `{% explblock name='content' %}...{% endexplblock %}`. Includes gehen mit
`{% expinclude uri='design:parts/menu.tpl' %}` über die Design-Ressource, die ein `.tpl`, `.stpl` oder `.twig` samt
seinen Override-Regeln findet. Twigs `{% include %}` funktioniert für `.twig`-Dateien über ihren Pfad. Einzelheiten:
[Template-Funktionen und Tags](twig-templates-funktionen.md).

## Übersetzungen

`{{ 'Close'|i18n('design/standard/content') }}` übersetzt wie in einem `.tpl`. Bei konstantem Text und Kontext wird die
Übersetzung beim Kompilieren des Templates erledigt, nicht bei jeder Anfrage: Die kompilierte Klasse wird je Siteaccess
und Locale gehalten. Mit Argumenten (`'%count items'|i18n('ctx', '', {'%count': n})`) ist der übersetzte Text konstant,
und nur die Argumente werden beim Ausführen eingesetzt.

## Caching

`{% expcacheblock keys=[node.node_id] %}...{% endexpcacheblock %}` ist der `{cache-block}` des Template-Systems: dieselben
Schlüssel, `expiry`, `subtree_expiry`, `ignore_content_expiry`, dieselben Cache-Dateien, geleert durch dieselben
Ereignisse. Siehe [Caching](twig-templates-caching.md).

## Fehler und Fehlersuche

Ein `.twig`, das nicht kompiliert oder beim Rendern scheitert, druckt an seiner Stelle nichts und schreibt den Fehler mit
Datei und Zeile in die Debug-Ausgabe und nach `var/log/error.log`. Mit eingeschalteter Debug-Ausgabe bleibt dort, wo die
Ausgabe gestanden hätte, ein HTML-Kommentar mit der Meldung:

```
<!-- twig: rendering <root>/extension/<ext>/design/<design>/templates/parts/menu.twig failed: Unknown &quot;fooo&quot; filter in &quot;extension/<ext>/design/<design>/templates/parts/menu.twig&quot; at line 1. (menu.twig:1) -->
```

`./console exp:template:check --design-dir=<dir>` findet solche Fehler, bevor eine Seite es tut. Mit `[TwigSettings]
Profile=enabled` und eingeschalteter Debug-Ausgabe schreibt jede Seite einen Hinweis "twig profile": die Zeit in
Operatoren, Funktionen, Includes und Cache-Blöcken. [Problembehebung](twig-templates-problembehebung.md) hat die
Meldungen und ihre Abhilfen.

## Velocity

Die dauerhaften Worker von Velocity halten die Twig-Umgebung und die kompilierten Klassen im Speicher. Ein geändertes
`.twig` bekommt einen neuen Klassennamen (die Änderungszeit ist Teil davon), sodass ein Worker bei der nächsten Anfrage
ohne Neustart die neue Fassung rendert. Die Operatoren und Funktionen werden einmal je Worker registriert, wenn er
startet; PHP-FPM, das die Umgebung bei jeder Anfrage aufbaut, löst nur die Namen auf, die ein Template benutzt.

## Grenzen

- Keine Methodenaufrufe und keine Eigenschaften von Objekten in der Sandbox (der Voreinstellung): Lesen Sie Werte über
  ihre Attribute.
- Kein `break` und kein `continue` in Twig; Makros und Template-Vererbung haben keine `.tpl`-Form (der
  Rückwärtskonverter behält sie als Anmerkung).
- `{% set %}` in einem eingebundenen `.twig` schreibt in den gemeinsamen Namensraum zurück, wie es ein
  Smarty-Untertemplate tut; ein `{def}` eines eingebundenen `.tpl` ist lokal zu seiner Datei.
- Eine undefinierte Variable druckt nichts (`StrictVariables=disabled`), wie in einem `.tpl`.

## Siehe auch

- [Twig-Templates](../funktionen/6.0/twig-templates.md) (Funktionsseite)
- [Twig-Templates nativ schreiben](twig-templates-nativ-schreiben.md), [Rezepte](twig-templates-rezepte.md),
  [Fehlersuche](twig-templates-fehlersuche.md), [Ihr Editor](twig-templates-im-editor.md),
  [Versionen und Kompatibilität](twig-templates-versionen-und-kompatibilitaet.md)
- [Die drei Template-Engines im Vergleich](template-engines.md) und ihre
  [Geschwindigkeit](template-engines-geschwindigkeit.md)
- [Von .tpl zu .twig umziehen](../hilfe/template-sprache/von-tpl-zu-twig-umziehen.md)
