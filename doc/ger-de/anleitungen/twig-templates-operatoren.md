# Twig-Templates: Operatoren und Fetch-Funktionen

Diese Anleitung ist für Template-Entwickler, die Template-Operatoren und Fetch-Funktionen von Exponential aus einem
`.twig` aufrufen. Sie erklärt die zwei Formen, die ein Operator in Twig hat, wie ein Operator seine Eingabe bekommt, wann
ein Name das Präfix `exp_` braucht, `fetch()` und die Hilfsfunktionen, die der Konverter schreibt. Jeder Operator mit
seinen Formen steht in der [Operator-Referenz](twig-templates-operator-referenz.md). Die Beispiele wurden vom
Template-System in einer Entwicklungsinstallation gerendert, mit `list` = `['a', 'b', 'c']`, `user_name` = `'Ada'` und
`price` = `12.5`.

## Filter oder Funktion

Jeder Operator des Template-Systems (die des Kernels und die jeder aktiven Extension) ist sowohl ein Twig-Filter als auch
eine Twig-Funktion. Schreiben Sie ihn so, wie es das `.tpl` tut:

- **mit einer Eingabe** (`.tpl` `{$x|op( a, b )}`): ein Filter, `{{ x|op(a, b) }}`;
- **ohne Eingabe** (`.tpl` `{op( a, b )}`): eine Funktion, `{{ op(a, b) }}`.

```twig
{{ list|implode(', ') }}
{{ concat('a', 'b', user_name) }}
{{ cond(price|gt(10), 'expensive', 'cheap') }}
{{ first_set(missing, 'a default') }}
```

```
a, b, c
abAda
expensive
a default
```

Der Operator bekommt genau das, was ihm das Template-System in einem kompilierten `.tpl` übergeben würde, sodass sich die
beiden Formen so verhalten wie dort. Die Operatoren, die die Eingabe als ersten Parameter nehmen (`sum`, `concat`, `eq`,
`gt` und die anderen Logik- und Rechenoperatoren, deren Template-Hinweise `input-as-parameter` sagen), geben in beiden
Fällen dasselbe Ergebnis: `{{ price|mul(2) }}` und `{{ mul(price, 2) }}` drucken beide `25`.

## Das Präfix exp_

Twig hat eigene Filter und Funktionen (`length`, `merge`, `round`, `date`, `replace`, `trim`, `json_encode` ...) und
Wörter, die sein Parser reserviert (`and`, `or`, `not`, `true`, `false`, `null`). Ein Operator mit einem solchen Namen
heißt `exp_<name>`; der einfache Name behält Twigs Bedeutung:

| `.tpl` | `.twig` |
|---|---|
| `{and( $a, $b )}` | `{{ exp_and(a, b) }}` |
| `{$a\|merge( $b )}` | `{{ a\|exp_merge(b) }}` |
| `{$text\|trim}` | `{{ text\|exp_trim }}` |
| `{true()}` | `{{ exp_true() }}` (oder Twigs `true`) |

`exp_<name>` funktioniert auch für jeden anderen Operator (`{{ list|count }}`, `{{ list|exp_count }}` und
`{{ exp_count(list) }}` drucken alle `3`), was nützlich ist, wenn eine Extension später einen Twig-Filter gleichen
Namens hinzufügen könnte. Ein Operator, dessen eigener Name mit `exp_` beginnt (`exp_path`, `exp_environment`), wird mit
seinem Namen geschrieben. Ein `exp_`-Name, der gar kein Operator ist, reicht seine Eingabe durch und schreibt einen
Fehler in die Debug-Ausgabe, wie es ein unbekannter Operator eines `.tpl` tut; `exp:template:check --level=strict`
meldet ihn (`TWIG-STRICT-UNKNOWN-OPERATOR`).

## Operatoren, die direkt in PHP laufen

Die Logik-, Rechen- und Steueroperatoren (`eq`, `ne`, `lt`, `gt`, `le`, `ge`, `and`, `or`, `not`, `count`, `sum`,
`sub`, `mul`, `div`, `mod`, `inc`, `dec`, `cond`, `first_set`), `wash` (für xhtml), `implode`, `contains` und `concat`
stehen in jeder Bedingung und Schleife, deshalb laufen sie direkt in PHP (`expTwigNativeOperators`), mit genau den
Ergebnissen des Template-Systems: Die Paritätstests der Engines halten sie daran. Ein kompiliertes Template ruft sie als
statische Methoden auf, ohne Nachschlagen. Eine Extension, die einen eigenen Operator eines dieser Namen registriert,
behält ihren eigenen: Der direkte Weg wird nur genommen, solange die Klasse des Kernels den Namen hält. Jeder andere
Operator läuft durch den eigenen Code des Template-Systems (`eZTemplate::processOperator()`).

## fetch()

`fetch( module, function, parameters )` ist das Fetch des Template-Systems, mit den Parametern als Twig-Hash:

```twig
{% set children = fetch('content', 'list', {parent_node_id: node.parent_node_id, limit: 3}) %}
{{ children|length }} children
```

```
3 children
```

`fetch('content', 'node', {node_id: 2})`, `fetch('user', 'current_user')`, `fetch('content', 'list_count', {...})`:
jede Fetch-Funktion jedes Moduls, mit denselben Parametern wie in einem `.tpl`
([Fetch-Funktionen](../beispiele/fetch-funktionen/README.md)). `hash( 'a', 1 )` eines `.tpl` ist `{a: 1}`;
`array( 1, 2 )` ist `[1, 2]`.

## Einstellungen, INI-Werte und Übersetzungen

```twig
{{ ezini('SiteSettings', 'SiteName') }}
{{ 'Close'|i18n('design/standard/content') }}
{{ node.object.published|l10n('shortdate') }}
{{ price|l10n('currency') }}
```

```
Fit & Healthy
Close
07/23/2018
$12.50
```

Ein konstanter `i18n`-Text wird beim Kompilieren des Templates übersetzt (das kompilierte Template wird je Siteaccess
und Locale gehalten), er kostet also nichts je Anfrage.

## Die Hilfsfunktionen

Der Konverter schreibt ein paar eigene Funktionen, wo Twig kein Konstrukt für eines des `.tpl` hat. Sie funktionieren
auch in handgeschriebenen Templates:

| Hilfsfunktion | Für |
|---|---|
| `expvoid()` | einen leeren Parameter: `.tpl` `i18n( 'ctx',, hash(...) )` ist `'text'\|i18n('ctx', expvoid(), {...})` |
| `expvar(name, namespace)` | eine Variable eines anderen Namensraums (`$#name`, `$:name` eines `.tpl`) |
| `expattr(value, key)` | ein Attribut mit berechnetem Namen (`$a[$key]`, wo `$key` kein Literal ist) |
| `expsectionloop(loop, offset, max, reverse)` | die Elemente einer `{section loop=...}`; eine Zahl n läuft über 1..n |
| `expslice(list, offset, max, reverse)` | ein `{foreach}` mit `offset=`, `max=`, `reverse` |
| `expsequence(list, index)` | den `sequence=`-Wert einer Schleifenrunde |
| `exprange(from, to)` | ein `{for 1 to 5 as $i}` |
| `expcolumn(list, key)` | die Werte eines Attributs jedes Elements |
| `expi18nargs(text, arguments)` | die Argumente eines `i18n`, dessen Text beim Kompilieren des Templates übersetzt wurde |

## Siehe auch

- [Operator-Referenz](twig-templates-operator-referenz.md)
- [Template-Funktionen und Tags](twig-templates-funktionen.md)
- [Operatoren und Funktionen schreiben](twig-templates-plugins.md)
