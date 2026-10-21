---
title: Arithmetische Operatoren
audience: developer, designer, app-creator
category: development
order: 100
summary: Die 18 Operatoren von eZTemplateArithmeticOperator (sum, sub, mul, div, mod, inc, dec, min, max, abs, ceil, floor, round, int, float, count, roman, rand) mit ihren Parametern, ihrer Eingabe, ihrem Rückgabewert und auf alpha ausgegebenen Beispielen.
keywords: Arithmetik, Rechnen, Mathematik, Rechenoperatoren, Runden, Zählen, Zufallszahl, römische Zahlen, arithmetic, math, sum, sub, mul, div, mod, inc, dec, min, max, abs, ceil, floor, round, int, float, count, roman, rand, eZTemplateArithmeticOperator
type: help
---
# Arithmetische Operatoren

Die Template-Sprache kennt keine Infix-Arithmetik: `{1 + 2}` ist kein Ausdruck. Jede Berechnung ist ein
Operatoraufruf. Alle 18 arithmetischen Operatoren registriert `eZTemplateArithmeticOperator`
(`lib/eztemplate/classes/eztemplatearithmeticoperator.php`) über `lib/eztemplate/classes/eztemplateautoload.php`.

Die meisten nehmen ihre Operanden entweder aus der Eingabe über die Pipe (`10|sum( 5 )`) oder aus den Parametern
(`sum( 10, 5 )`). Wo sich ein Operator anders verhält, sagt es sein Abschnitt. Die Beispiele wurden auf alpha vom
Template-Interpreter und anschließend noch einmal vom Template-Compiler ausgegeben; die kompilierte Form von `sum`,
`sub`, `mul` und `div` ruft dasselbe `eZTemplateArithmeticOperator::calculate()` auf. Der einzige gefundene
Unterschied, bei `min`, steht in dessen Abschnitt.

## Inhalt

1. [Wie Operanden gelesen werden](#wie-operanden-gelesen-werden)
2. [sum und sub](#sum-und-sub)
3. [mul, div und mod](#mul-div-und-mod)
4. [inc und dec](#inc-und-dec)
5. [min und max](#min-und-max)
6. [abs, ceil, floor, round, int und float](#abs-ceil-floor-round-int-und-float)
7. [count](#count)
8. [roman und rand](#roman-und-rand)
9. [Ein durchgerechnetes Beispiel: die Anzahl der Seiten](#ein-durchgerechnetes-beispiel-die-anzahl-der-seiten)
10. [Verwandte Seiten](#verwandte-seiten)

## Wie Operanden gelesen werden

`sum`, `sub`, `mul` und `div` machen mit `eZTemplateArithmeticOperator::numericOperand()` aus jedem Operanden eine
Zahl: Ganzzahlen und Gleitkommazahlen bleiben, wie sie sind, eine numerische Zeichenkette wird zu ihrer Zahl, eine
Zeichenkette, die mit einer Zahl beginnt, wird zu dieser Zahl (`'7 days'` ist 7), jede andere Zeichenkette und `null`
werden zu 0, ein boolescher Wert wird zu 0 oder 1, ein Array wird zu 0, wenn es leer ist, sonst zu 1, und ein Objekt
wird zu 0. Nur diese vier Operatoren verwenden diese Regel; die anderen nutzen die Umwandlung von PHP selbst.

## sum und sub

### sum

Addiert alle Operanden: die Eingabe über die Pipe (wenn vorhanden) und jeden Parameter.

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| beliebig viele Werte | Zahl oder numerische Zeichenkette | nein | keiner |

Eingabe: optional; wenn vorhanden, ist sie der erste Operand. Gibt eine Ganzzahl oder eine Gleitkommazahl zurück.
Erzeugte Referenz: [sum](../../../eng-us/examples/template-operators/sum.md).

### sub

Zieht jeden folgenden Operanden vom ersten ab. Der erste Operand ist die Eingabe über die Pipe oder, wenn nichts
übergeben wird, der erste Parameter.

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| beliebig viele Werte | Zahl oder numerische Zeichenkette | nein | keiner |

Eingabe: optional. Gibt eine Ganzzahl oder eine Gleitkommazahl zurück.
Erzeugte Referenz: [sub](../../../eng-us/examples/template-operators/sub.md).

```tpl
sum( 2, 3, 4 ) = {sum( 2, 3, 4 )}
10|sum( 5 ) = {10|sum( 5 )}
sub( 10, 3, 2 ) = {sub( 10, 3, 2 )}
'7 days'|sum( 1 ) = {'7 days'|sum( 1 )}
sum( 'abc', 2 ) = {sum( 'abc', 2 )}
```

Ausgabe auf alpha:

```text
sum( 2, 3, 4 ) = 9
10|sum( 5 ) = 15
sub( 10, 3, 2 ) = 5
'7 days'|sum( 1 ) = 8
sum( 'abc', 2 ) = 2
```

## mul, div und mod

### mul

Multipliziert die Eingabe (wenn vorhanden) mit jedem Parameter. Ganz ohne Parameter protokolliert er die Warnung
"Requires at least 1 parameter value" und lässt den Wert unverändert.

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| Werte | Zahl | ja, mindestens einer | keiner |

Eingabe: optional. Gibt eine Ganzzahl oder eine Gleitkommazahl zurück.
Erzeugte Referenz: [mul](../../../eng-us/examples/template-operators/mul.md).

### div

Teilt den ersten Operanden der Reihe nach durch jeden folgenden. Eine Division durch null ergibt 0 statt eines
Fehlers. Braucht wie `mul` mindestens einen Parameter.

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| Werte | Zahl | ja, mindestens einer | keiner |

Eingabe: optional; wenn vorhanden, ist sie der Dividend. Gibt eine Ganzzahl oder eine Gleitkommazahl zurück.
Erzeugte Referenz: [div](../../../eng-us/examples/template-operators/div.md).

### mod

Der Rest einer ganzzahligen Division (PHP `%`). Mit einem Parameter ist die Eingabe der Dividend und der Parameter
der Divisor; mit zwei Parametern sind diese Dividend und Divisor, und die Eingabe wird ignoriert. Ohne Parameter
protokolliert er "Missing dividend and divisor". Es gibt keine Prüfung auf null: Prüfen Sie den Divisor vorher.

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| Dividend oder Divisor | Ganzzahl | ja | keiner |
| Divisor | Ganzzahl | nein | keiner |

Gibt eine Ganzzahl zurück.
Erzeugte Referenz: [mod](../../../eng-us/examples/template-operators/mod.md).

```tpl
mul( 4, 2.5 ) = {mul( 4, 2.5 )}
div( 10, 4 ) = {div( 10, 4 )}
div( 10, 0 ) = {div( 10, 0 )}
100|div( 2, 5 ) = {100|div( 2, 5 )}
mod( 10, 4 ) = {mod( 10, 4 )}
10|mod( 3 ) = {10|mod( 3 )}
```

Ausgabe auf alpha:

```text
mul( 4, 2.5 ) = 10
div( 10, 4 ) = 2.5
div( 10, 0 ) = 0
100|div( 2, 5 ) = 10
mod( 10, 4 ) = 2
10|mod( 3 ) = 1
```

## inc und dec

### inc

Gibt den Wert plus eins zurück: die Eingabe über die Pipe oder, wenn nichts übergeben wird, den benannten Parameter
`value`. Die Variable selbst wird nicht geändert; verwenden Sie `{set $i = $i|inc}`, um das Ergebnis zu speichern.

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| `value` | beliebig | nein | `false` |

Erzeugte Referenz: [inc](../../../eng-us/examples/template-operators/inc.md).

### dec

Dasselbe, minus eins.

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| `value` | beliebig | nein | `false` |

Erzeugte Referenz: [dec](../../../eng-us/examples/template-operators/dec.md).

```tpl
{def $i = 5}
$i|inc = {$i|inc}, $i|dec = {$i|dec}, $i is still {$i}
inc( 9 ) = {inc( 9 )}
dec( 1 ) = {dec( 1 )}
```

Ausgabe auf alpha:

```text
$i|inc = 6, $i|dec = 4, $i is still 5
inc( 9 ) = 10
dec( 1 ) = 0
```

## min und max

### max

Gibt den größten Wert zurück. Die Eingabe über die Pipe nimmt, wenn vorhanden, am Vergleich teil; sonst beginnt der
erste Parameter. Braucht mindestens einen Parameter.

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| Werte | beliebig | ja, mindestens einer | keiner |

Erzeugte Referenz: [max](../../../eng-us/examples/template-operators/max.md).

### min

Gibt den kleinsten Wert zurück. Braucht mindestens einen Parameter. Verwenden Sie ihn in der Aufrufform,
`min( $a, $b )`: Mit einer Eingabe über die Pipe sind sich Interpreter und kompiliertes Template nicht einig. Der
Interpreter (`modify()`) vergleicht nur die Parameter, daher ist `7|min( 9, 8 )` dort 8; das kompilierte Template
bezieht die Eingabe ein und liefert 7.

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| Werte | beliebig | ja, mindestens einer | keiner |

Erzeugte Referenz: [min](../../../eng-us/examples/template-operators/min.md).

```tpl
max( 3, 9, 4 ) = {max( 3, 9, 4 )}
min( 3, 9, 4 ) = {min( 3, 9, 4 )}
7|max( 3, 5 ) = {7|max( 3, 5 )}
7|min( 9, 8 ) = {7|min( 9, 8 )}
```

Ausgabe auf alpha:

```text
max( 3, 9, 4 ) = 9
min( 3, 9, 4 ) = 3
7|max( 3, 5 ) = 7
7|min( 9, 8 ) = 8
```

Dasselbe Template, auf alpha kompiliert (das Kompilieren von Templates ist in `site.ini` eingeschaltet,
`[TemplateSettings] TemplateCompile`), gab in der letzten Zeile `7|min( 9, 8 ) = 7` aus; die anderen Zeilen waren
gleich:

Ausgabe auf alpha mit dem Template-Compiler (in einen Arbeitsordner kompiliert):

```text
max( 3, 9, 4 ) = 9
min( 3, 9, 4 ) = 3
7|max( 3, 5 ) = 7
7|min( 9, 8 ) = 7
```

## abs, ceil, floor, round, int und float

Jeder nimmt einen Wert: den ersten Parameter, wenn es einen gibt, sonst die Eingabe über die Pipe.

| Operator | Tut | Gibt zurück |
|---|---|---|
| `abs` | PHP `abs()` | Zahl |
| `ceil` | PHP `ceil()`, rundet auf | Gleitkommazahl |
| `floor` | PHP `floor()`, rundet ab | Gleitkommazahl |
| `round` | PHP `round()` ohne Genauigkeit: auf eine ganze Zahl, Hälften von null weg | Gleitkommazahl |
| `int` | PHP-Umwandlung `(int)`: `'42abc'` ist 42, `9.99` ist 9 | Ganzzahl |
| `float` | PHP-Umwandlung `(float)` | Gleitkommazahl |

Keiner von ihnen hat benannte Parameter. `round` hat keinen Parameter für die Genauigkeit; für zwei Nachkommastellen
verwenden Sie `mul( $x, 100 )|round|div( 100 )` oder die Locale-Operatoren (`l10n( 'number' )`).
Erzeugte Referenzen: [abs](../../../eng-us/examples/template-operators/abs.md), [ceil](../../../eng-us/examples/template-operators/ceil.md),
[floor](../../../eng-us/examples/template-operators/floor.md), [round](../../../eng-us/examples/template-operators/round.md),
[int](../../../eng-us/examples/template-operators/int.md), [float](../../../eng-us/examples/template-operators/float.md).

```tpl
-4.6|abs = {-4.6|abs}
4.2|ceil = {4.2|ceil}
4.8|floor = {4.8|floor}
4.5|round = {4.5|round}
round( 2.4 ) = {round( 2.4 )}
'42abc'|int = {'42abc'|int}
'3.75'|float = {'3.75'|float}
int( 9.99 ) = {int( 9.99 )}
```

Ausgabe auf alpha:

```text
-4.6|abs = 4.6
4.2|ceil = 5
4.8|floor = 4
4.5|round = 5
round( 2.4 ) = 2
'42abc'|int = 42
'3.75'|float = 3.75
int( 9.99 ) = 9
```

## count

### count

Zählt den ersten Parameter oder, wenn es keinen Parameter gibt, die Eingabe über die Pipe:

- ein Array: die Anzahl seiner Elemente;
- ein Objekt mit einer Methode `attributes()` (Knoten, Inhaltsobjekte, Attribute): die Anzahl der Attributnamen, die
  es Templates anbietet;
- eine Zeichenkette: ihre Länge in Bytes (`strlen`), ein Multibyte-Zeichen zählt also mehr als einmal;
- alles andere: 0.

Ein zweiter Parameter protokolliert eine Warnung über "extra parameters".

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| Wert | beliebig | nein | die Eingabe |

Erzeugte Referenz: [count](../../../eng-us/examples/template-operators/count.md).

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 89 ) )}
array( 1, 2, 3 )|count = {array( 1, 2, 3 )|count}
'Hello'|count = {'Hello'|count}
count( hash( 'a', 1, 'b', 2 ) ) = {count( hash( 'a', 1, 'b', 2 ) )}
$node.object.data_map|count = {$node.object.data_map|count}
$node|count (attributes of the node object) = {$node|count}
42|count = {42|count}
```

Ausgabe auf alpha:

```text
array( 1, 2, 3 )|count = 3
'Hello'|count = 5
count( hash( 'a', 1, 'b', 2 ) ) = 2
$node.object.data_map|count = 7
$node|count (attributes of the node object) = 50
42|count = 0
```

## roman und rand

### roman

Schreibt eine positive Ganzzahl als römische Zahl, erzeugt von `buildRoman()`. Der Wert ist der benannte Parameter
`value` oder die Eingabe über die Pipe. Null und negative Zahlen ergeben eine leere Zeichenkette.

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| `value` | beliebig | nein | `false` (die Eingabe verwenden) |

Erzeugte Referenz: [roman](../../../eng-us/examples/template-operators/roman.md).

### rand

Mit zwei Parametern eine zufällige Ganzzahl zwischen beiden, einschließlich (PHP `mt_rand( min, max )`); mit jeder
anderen Anzahl von Parametern `mt_rand()` ohne Grenzen. In einem Cache-Block oder einem Template mit Ansichtscache
bleibt der Wert gleich, bis der Cache neu aufgebaut wird.

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| Minimum | Ganzzahl | nein | keiner |
| Maximum | Ganzzahl | nein | keiner |

Erzeugte Referenz: [rand](../../../eng-us/examples/template-operators/rand.md).

```tpl
{def $r = rand( 1, 6 )}
4|roman = {4|roman}
roman( 1999 ) = {roman( 1999 )}
2026|roman = {2026|roman}
rand( 1, 6 ) is between 1 and 6: {and( $r|ge( 1 ), $r|le( 6 ) )|choose( 'no', 'yes' )}
```

Ausgabe auf alpha:

```text
4|roman = IV
roman( 1999 ) = MCMXCIX
2026|roman = MMXXVI
rand( 1, 6 ) is between 1 and 6: yes
```

## Ein durchgerechnetes Beispiel: die Anzahl der Seiten

Die Kategorie Recipes auf alpha (Knoten 144), vier Einträge pro Seite:

```tpl
{def $total = fetch( 'content', 'list_count', hash( 'parent_node_id', 144 ) )
     $limit = 4
     $pages = ceil( div( $total, $limit ) )}
{$total} items under Recipes, {$limit} per page: {$pages} pages
```

Ausgabe auf alpha:

```text
18 items under Recipes, 4 per page: 5 pages
```

## Verwandte Seiten

- [Mathematische Operatoren](../../../eng-us/help/designers/templates/math-operators.md): der Überblick für Designer, mit Rezepten für Seitenumbruch und Raster
- [Logische Operatoren](logische-operatoren.md), um die Ergebnisse zu vergleichen
- [Seitenumbruch](../../../eng-us/help/designers/templates/pagination.md)
- [Alle Template-Operatoren (erzeugt)](../../../eng-us/examples/template-operators/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
