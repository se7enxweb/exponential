---
title: Logische Operatoren
audience: developer, designer, app-creator
category: development
order: 120
summary: Die 13 Operatoren von eZTemplateLogicOperator (eq, ne, lt, gt, le, ge, and, or, not, null, true, false, choose), einschließlich der Zählregel von lt/gt/le/ge, die Zeichenketten nach ihrer Länge vergleicht, mit auf alpha ausgegebenen Beispielen.
keywords: Logik, Vergleich, Bedingung, boolescher Wert, Wahrheitswert, logic, comparison, eq, ne, lt, gt, le, ge, and, or, not, null, true, false, choose, boolean, condition, eZTemplateLogicOperator
type: help
---
# Logische Operatoren

Bedingungen in `{if}`, `{elseif}` und `{while}` sind Operatoraufrufe, denn die Template-Sprache kennt kein `==`, `<`,
`&&` oder `!`. Die 13 Operatoren auf dieser Seite registriert `eZTemplateLogicOperator`
(`lib/eztemplate/classes/eztemplatelogicoperator.php`) über `lib/eztemplate/classes/eztemplateautoload.php`.

Zwei ihrer Regeln überraschen alle, die PHP oder Smarty kennen, und beide werden unten belegt:

- `lt`, `gt`, `le` und `ge` vergleichen **Anzahlen**, keine Werte: Eine nicht numerische Zeichenkette zählt als ihre
  Länge, daher ist `'pear'|lt( 'apple' )` wahr (4 ist kleiner als 5).
- `and` und `or` geben einen ihrer Operanden zurück, nicht immer einen booleschen Wert.

## Inhalt

1. [eq und ne](#eq-und-ne)
2. [lt, gt, le und ge](#lt-gt-le-und-ge)
3. [and und or](#and-und-or)
4. [not, null, true und false](#not-null-true-und-false)
5. [choose](#choose)
6. [Verwandte Seiten](#verwandte-seiten)

In den Beispielen macht `choose( 'false', 'true' )` aus einem booleschen Wert ein Wort, denn ein boolesches `true` wird
als `1` ausgegeben und `false` gibt nichts aus.

## eq und ne

### eq

Mit einem Parameter: ob die Eingabe über die Pipe ihm gleich ist. Mit zwei oder mehr Parametern: ob alle Parameter
gleich sind (die Eingabe wird nicht verwendet). Der Vergleich ist das lose `==` von PHP, daher ist `'5'|eq( 5 )` wahr.
Ohne Parameter protokolliert er "Requires one parameter for input checking or two or more for parameter checking"
und gibt false zurück.

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| Werte | beliebig | ja, einer (mit Eingabe) oder zwei und mehr | keiner |

Gibt einen booleschen Wert zurück. Erzeugte Referenz: [eq](../../../eng-us/examples/template-operators/eq.md).

### ne

Das Gegenteil: mit einem Parameter, ob sich die Eingabe von ihm unterscheidet; mit mehreren, ob sich irgendwelche zwei
Nachbarn unterscheiden. Dieselbe Warnung ohne Parameter.

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| Werte | beliebig | ja | keiner |

Gibt einen booleschen Wert zurück. Erzeugte Referenz: [ne](../../../eng-us/examples/template-operators/ne.md).

```tpl
5|eq( 5 ) = {5|eq( 5 )|choose( 'false', 'true' )}
'5'|eq( 5 ) = {'5'|eq( 5 )|choose( 'false', 'true' )}
eq( 3, 3, 3 ) = {eq( 3, 3, 3 )|choose( 'false', 'true' )}
eq( 3, 3, 4 ) = {eq( 3, 3, 4 )|choose( 'false', 'true' )}
'abc'|ne( 'abd' ) = {'abc'|ne( 'abd' )|choose( 'false', 'true' )}
ne( 1, 1, 2 ) = {ne( 1, 1, 2 )|choose( 'false', 'true' )}
```

Ausgabe auf alpha:

```text
5|eq( 5 ) = true
'5'|eq( 5 ) = true
eq( 3, 3, 3 ) = true
eq( 3, 3, 4 ) = false
'abc'|ne( 'abd' ) = true
ne( 1, 1, 2 ) = true
```

## lt, gt, le und ge

### lt, gt, le, ge

`lt` (kleiner als), `gt` (größer als), `le` (kleiner oder gleich) und `ge` (größer oder gleich) vergleichen zwei
Werte: die Eingabe über die Pipe mit dem benannten Parameter `threshold` oder, ohne Eingabe, den ersten Parameter mit
dem zweiten.

Jede Seite wird zuerst von `eZTemplateLogicOperator::comparisonCount()` in eine Anzahl umgewandelt:

| Wert | Zählt als |
|---|---|
| `null` | 0 |
| boolescher Wert | 0 oder 1 |
| Array | die Anzahl seiner Elemente |
| Objekt mit `attributes()` | die Anzahl seiner Attribute |
| Zahl oder numerische Zeichenkette | ihr Wert |
| jede andere Zeichenkette | ihre Länge (`strlen`) |

Jeder andere Typ protokolliert "Unsupported input type" und lässt den Wert unverändert. Wegen der Regel für
Zeichenketten verwenden Sie zum Ordnen von Wörtern `strcmp` oder `strnatcmp` (siehe
[PHP-Zeichenkettenfunktionen: suchen und vergleichen](php-zeichenkettenfunktionen-suchen-und-vergleichen.md)). Kompilierte
Templates rufen zur Laufzeit `compareCounts()` auf und wenden dieselbe Regel an.

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| `threshold` | beliebig | ja | `false` |

Gibt einen booleschen Wert zurück. Erzeugte Referenzen: [lt](../../../eng-us/examples/template-operators/lt.md),
[gt](../../../eng-us/examples/template-operators/gt.md), [le](../../../eng-us/examples/template-operators/le.md),
[ge](../../../eng-us/examples/template-operators/ge.md).

```tpl
3|lt( 5 ) = {3|lt( 5 )|choose( 'false', 'true' )}
gt( 10, 2 ) = {gt( 10, 2 )|choose( 'false', 'true' )}
5|le( 5 ) = {5|le( 5 )|choose( 'false', 'true' )}
5|ge( 6 ) = {5|ge( 6 )|choose( 'false', 'true' )}
array( 1, 2, 3 )|gt( 2 ) = {array( 1, 2, 3 )|gt( 2 )|choose( 'false', 'true' )}
'banana'|gt( 'apple' ) = {'banana'|gt( 'apple' )|choose( 'false', 'true' )}
'pear'|lt( 'apple' ) = {'pear'|lt( 'apple' )|choose( 'false', 'true' )}
'10'|gt( '9' ) = {'10'|gt( '9' )|choose( 'false', 'true' )}
```

Ausgabe auf alpha:

```text
3|lt( 5 ) = true
gt( 10, 2 ) = true
5|le( 5 ) = true
5|ge( 6 ) = false
array( 1, 2, 3 )|gt( 2 ) = true
'banana'|gt( 'apple' ) = true
'pear'|lt( 'apple' ) = true
'10'|gt( '9' ) = true
```

## and und or

Beide werten nur ihre **Parameter** aus; die Eingabe über die Pipe wird nicht verwendet. Jeder Operand wird so
beurteilt: `null` ist falsch, ein boolescher Wert ist er selbst, ein Array ist wahr, wenn es Elemente hat, eine Zahl
ist wahr, wenn sie nicht 0 ist, ein Objekt ist wahr, wenn es die Methoden `attributes()` und `attribute()` hat, und
eine Zeichenkette ist wahr, wenn sie etwas anderes als Leerraum enthält (`and` behandelt auch `''` als falsch).

### and

Gibt beim ersten falschen Operanden `false` zurück; sind alle wahr, gibt er den **letzten Operanden** selbst zurück.

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| Operanden | beliebig | nein | keiner |

Erzeugte Referenz: [and](../../../eng-us/examples/template-operators/and.md).

### or

Gibt den **ersten wahren Operanden** selbst zurück oder `false`, wenn keiner wahr ist. Damit eignet sich `or` zur Wahl
eines Ersatzwerts, auch wenn [first_set](steueroperatoren.md#first_set) dafür klarer ist.

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| Operanden | beliebig | nein | keiner |

Erzeugte Referenz: [or](../../../eng-us/examples/template-operators/or.md).

```tpl
and( 1, 'yes', array( 1 ) ) = {and( 1, 'yes', array( 1 ) )|implode( ',' )}
and( 1, '  ', 3 ) = {and( 1, '  ', 3 )|choose( 'false', 'true' )}
or( false(), '', 'first', 'second' ) = {or( false(), '', 'first', 'second' )}
or( 0, array() ) = {or( 0, array() )|choose( 'false', 'true' )}
```

Ausgabe auf alpha:

```text
and( 1, 'yes', array( 1 ) ) = 1
and( 1, '  ', 3 ) = false
or( false(), '', 'first', 'second' ) = first
or( 0, array() ) = false
```

Die Regel für Leerraum ist die des Interpreters. Kompiliert auf alpha behandelte `and( 1, '  ', 3 )` die leere
Zeichenkette als wahr und gab 3 zurück, den letzten Operanden, woraufhin das folgende `choose` "Index 3 out of range"
protokollierte und nichts ausgab. Entfernen Sie Leerraum, bevor Sie Zeichenketten prüfen (`$text|trim`), dann sind
sich beide einig.

Ausgabe auf alpha mit dem Template-Compiler (in einen Arbeitsordner kompiliert):

```text
and( 1, 'yes', array( 1 ) ) = 1
and( 1, '  ', 3 ) = 
or( false(), '', 'first', 'second' ) = first
or( 0, array() ) = false
```

## not, null, true und false

### not

Verneint die Eingabe über die Pipe oder, wenn es keine Eingabe gibt, den ersten Parameter. "Leer" bedeutet: `null`,
ein leeres Array, ein Objekt ohne Attribute, eine Zahl oder numerische Zeichenkette gleich 0 (also auch `'0'`) oder
eine leere Zeichenkette. Gibt einen booleschen Wert zurück.

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| Wert | beliebig | nein | die Eingabe |

Erzeugte Referenz: [not](../../../eng-us/examples/template-operators/not.md).

### null

Ob die Eingabe über die Pipe genau `null` ist (`===`). Ein Fetch, der nichts findet, gibt `null` zurück. Keine
Parameter. Erzeugte Referenz: [null](../../../eng-us/examples/template-operators/null.md).

### true und false

Geben die booleschen Werte `true` und `false` zurück. So schreiben Sie ein boolesches Literal: `true()`, `false()`.
Keine Parameter. Erzeugte Referenzen: [true](../../../eng-us/examples/template-operators/true.md),
[false](../../../eng-us/examples/template-operators/false.md).

```tpl
{def $empty = array()
     $node = fetch( 'content', 'node', hash( 'node_id', 89 ) )
     $missing = fetch( 'content', 'node', hash( 'node_id', 999999999 ) )}
$empty|not = {$empty|not|choose( 'false', 'true' )}
not( 0 ) = {not( 0 )|choose( 'false', 'true' )}
'0'|not = {'0'|not|choose( 'false', 'true' )}
''|not = {''|not|choose( 'false', 'true' )}
$node|not = {$node|not|choose( 'false', 'true' )}
$missing|null (no node has that id) = {$missing|null|choose( 'false', 'true' )}
true() = {true()|choose( 'false', 'true' )}, false() = {false()|choose( 'false', 'true' )}
```

Ausgabe auf alpha:

```text
$empty|not = true
not( 0 ) = true
'0'|not = true
''|not = true
$node|not = false
$missing|null (no node has that id) = true
true() = true, false() = false
```

## choose

### choose

Wählt anhand der Eingabe über die Pipe einen seiner Parameter: Eine Zahl ist der Index (0 wählt den ersten Parameter),
ein boolescher Wert ist 0 oder 1 (`false` wählt also den ersten und `true` den zweiten), und `null` ist 0. Nur der
gewählte Parameter wird ausgewertet. Eine Eingabe vom Typ Array oder Objekt protokolliert "Only supports numeric and
boolean values"; ein Index außerhalb der Parameter protokolliert "Index ... out of range" und gibt `false` zurück.

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| Auswahlmöglichkeiten | beliebig | ja | keiner |

Erzeugte Referenz: [choose](../../../eng-us/examples/template-operators/choose.md).

```tpl
{def $n = 2
     $node = fetch( 'content', 'node', hash( 'node_id', 144 ) )}
$n|choose( 'zero', 'one', 'two' ) = {$n|choose( 'zero', 'one', 'two' )}
true()|choose( 'no', 'yes' ) = {true()|choose( 'no', 'yes' )}
The Recipes node {$node.children_count|gt( 0 )|choose( 'has no children', concat( 'has ', $node.children_count, ' children' ) )}.
```

Ausgabe auf alpha:

```text
$n|choose( 'zero', 'one', 'two' ) = two
true()|choose( 'no', 'yes' ) = yes
The Recipes node has 18 children.
```

Ein Index außerhalb der Liste ist ein Fehler:

```tpl
3|choose( 'a', 'b' ) = {3|choose( 'a', 'b' )|choose( 'false', 'true' )}
```

Ausgabe auf alpha:

```text
3|choose( 'a', 'b' ) = false
```

Von der Template-Engine protokolliert:

```text
ERROR choose: Index 3 out of range 0 => 1
```

Für eine Wahl zwischen zwei Möglichkeiten bei einer beliebigen Bedingung liest sich [cond](steueroperatoren.md#cond)
natürlicher.

## Verwandte Seiten

- [Logik- und Vergleichsoperatoren für Designer](../../../eng-us/help/designers/templates/logic-and-comparison-operators.md)
- [If und Bedingungen](../../../eng-us/help/designers/templates/if-and-conditions.md)
- [Steueroperatoren: cond und first_set](steueroperatoren.md)
- [Typ-Operatoren](typ-operatoren.md): `is_set`, `is_array` und die anderen Prüfungen
- [Alle Template-Operatoren (erzeugt)](../../../eng-us/examples/template-operators/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
