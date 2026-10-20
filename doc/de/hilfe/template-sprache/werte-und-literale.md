---
title: Werte und Literale
audience: developer, designer, app-creator
category: development
order: 70
summary: Die Werte, die ein Template hinschreiben kann - Zeichenketten in einfachen oder doppelten Anführungszeichen (keine Interpolation von Variablen, die Escapes, die der Parser kennt), ganze Zahlen und Gleitkommazahlen, true() und false(), mit array() und hash() gebaute Arrays und Hashes, bloße Bezeichner und was für null steht - jedes auf alpha mit get_type gezeigt.
keywords: Werte, Literale, Zeichenketten, Anführungszeichen, Escape-Sequenzen, Zahlen, ganze Zahl, Gleitkommazahl, Boolescher Wert, true, false, array, hash, Bezeichner, null, get_type, Wahrheitswert, values, literals, strings, quotes, escape sequences, numbers, integer, float, boolean, identifier, truthiness
type: help
---
# Werte und Literale

Der Ausdrucksparser (`lib/eztemplate/classes/eztemplateelementparser.php`) kennt sechs Arten literaler Werte. Diese Seite führt sie mit den
genauen Regeln des Parsers auf und zeigt jede auf alpha. `get_type` (aus `eZTemplateTypeOperator`) gibt den PHP-Typ eines Werts aus und wird
hier deshalb genutzt, um zu zeigen, was aus einem Literal wird.

## Inhalt

1. [Zeichenketten](#zeichenketten)
2. [Zahlen](#zahlen)
3. [Boolesche Werte](#boolesche-werte)
4. [Arrays und Hashes](#arrays-und-hashes)
5. [Bezeichner](#bezeichner)
6. [Null](#null)
7. [Wahrheitswerte](#wahrheitswerte)
8. [Verwandte Seiten](#verwandte-seiten)

## Zeichenketten

Eine Zeichenkette wird in einfache oder doppelte Anführungszeichen geschrieben. Beide sind gleich: Keine interpoliert Variablen, also gibt
`"Hello $name"` das Dollarzeichen und den Namen wörtlich aus. Verbinden Sie Zeichenketten mit `concat`.

`unescapeCharacters()` kennt diese Escapes, in beiden Arten von Anführungszeichen:

| Escape | Wird zu |
|---|---|
| `\n` | einem Zeilenumbruch |
| `\r` | einem Wagenrücklauf |
| `\t` | einem Tabulator |
| `\'` und `\"` | dem Anführungszeichen |
| `\\` | einem Backslash |
| allem anderen, etwa `\q` | bleibt wie geschrieben, Backslash eingeschlossen |

Innerhalb eines Tags muss eine `}` in einer Zeichenkette als `\}` geschrieben werden, sonst endet das Tag dort (siehe
[Lexikalische Struktur](lexikalische-struktur.md#eine-klammer-in-einem-tag)).

```tpl
{def $name = 'World'}
[{'Hello $name'}] [{"Hello $name"}] [{concat( 'Hello ', $name )}]
[{'It\'s'}] [{"say \"hi\""}] [{'tab:\t:'}] [{'back\\slash'}] [{'unknown \q stays'}]
[{'line one\nline two'}]
```

Ausgabe auf alpha:

```text
[Hello $name] [Hello $name] [Hello World]
[It's] [say "hi"] [tab:	:] [back\slash] [unknown \q stays]
[line one
line two]
```

## Zahlen

Eine Zahl ist eine Folge von Ziffern mit einem wahlweise vorangestellten `-` und höchstens einem `.`, gefolgt von Ziffern. Ohne Punkt ist sie
eine ganze Zahl, mit einem eine Gleitkommazahl (`numericEndPos()`). Auf einen Punkt muss eine Ziffer folgen: `1.` ist die Zahl `1`, gefolgt
von einem Parserfehler (am Ende dieses Abschnitts gezeigt). Gleitkommazahlen werden von PHP ausgegeben, also gibt `1.50` den Wert `1.5` aus.

Zeichenketten aus Inhalten oder Anfragen bleiben Zeichenketten, bis ein Operator sie umwandelt; `int` und `float` tun es ausdrücklich. `div`
gibt eine ganze Zahl zurück, wenn die Division ohne Rest aufgeht, und sonst eine Gleitkommazahl.

```tpl
[{42}] [{-7}] [{3.14}] [{0.5}] [{1.50}]
[{42|get_type}] [{3.14|get_type}] [{'42'|get_type}] [{'42'|int|get_type}]
[{10|div( 4 )}] [{10|div( 5 )|get_type}]
```

Ausgabe auf alpha:

```text
[42] [-7] [3.14] [0.5] [1.5]
[integer] [double] [string[2]] [integer]
[2.5] [integer]
```

Ein Punkt ohne Ziffer danach:

```tpl
[{1.|get_type}]
```

Ausgabe auf alpha:

```text
[1]
```

Von der Template-Engine protokolliert:

```text
ERROR : parser error @ tl-values/07-number-dot.tpl:1[2]
Extra characters found in expression, they will be ignored.
.|get_type' (1)
```

## Boolesche Werte

Es gibt kein Schlüsselwort `true` oder `false`: `true()` und `false()` sind Operatoren von `eZTemplateLogicOperator`, die die booleschen Werte
von PHP zurückgeben. Ausgegeben wird true zu `1` und false zur leeren Zeichenkette, wie in PHP.

```tpl
[{true()}] [{false()}] [{true()|get_type}] [{false()|get_type}]
[{if 0}0 is true{else}0 is false{/if}] [{if '0'}'0' is true{else}'0' is false{/if}] [{if ''}empty is true{else}empty is false{/if}] [{if array()}empty array is true{else}empty array is false{/if}]
```

Ausgabe auf alpha:

```text
[1] [] [boolean[true]] [boolean[false]]
[0 is false] ['0' is false] [empty is false] [empty array is false]
```

## Arrays und Hashes

`array( ... )` baut eine Liste mit den Schlüsseln 0, 1, 2 und `hash( key, value, ... )` eine Zuordnung aus Paaren. Beide stammen aus
`eZTemplateArrayOperator`, sind also Operatoren wie alle anderen und lassen sich verschachteln, durch die Pipe leiten und als Parameter
übergeben. Elemente werden mit einem Punkt oder mit eckigen Klammern gelesen: `$list.1`, `$map.red`, `$deep['count']`.

```tpl
{def $list = array( 'a', 'b', 'c' )
     $map  = hash( 'red', '#f00', 'green', '#0f0' )
     $deep = hash( 'sizes', array( 'S', 'M', 'L' ), 'count', 3 )}
[{$list|count}] [{$list.1}] [{$list|implode( '-' )}]
[{$map.red}] {foreach $map as $k => $v}[{$k}={$v}]{/foreach}
[{$deep.sizes.2}] [{$deep['count']}]
[{array()|count}] [{$list|get_type}] [{$map|get_type}]
```

Ausgabe auf alpha:

```text
[3] [b] [a-b-c]
[#f00] [red=#f00][green=#0f0][L] [3]
[0] [array[3]] [array[2]]
```

`get_type` fügt bei Arrays die Anzahl der Elemente und bei Zeichenketten die Länge hinzu, was es bei der Fehlersuche nützlich macht.

## Bezeichner

Ein bloßes Wort, auf das kein `(` folgt, ist ein Bezeichner, und sein Wert ist das Wort selbst. Manche Operatoren und Funktionen nutzen
Bezeichner, wo andere Sprachen Schlüsselwörter in Anführungszeichen nutzen würden, zum Beispiel `attribute( show, 2 )` oder
`sequence array( odd, even )`:

```tpl
[{1|choose( zero, one, two )}] [{array( red, green )|implode( '+' )}]
```

Ausgabe auf alpha:

```text
[one] [red+green]
```

In Bezeichnern und Variablennamen erlaubt sind Buchstaben, Ziffern, `_` und `-` (`identifierEndPosition()`). Ein Bindestrich gehört zum
Namen, also ist `$my-var` eine einzige Variable, keine Subtraktion.

## Null

Es gibt kein Literal für null. `null` existiert, ist aber ein Test aus `eZTemplateLogicOperator`, der wahr ist, wenn seine Eingabe null ist,
kein Wert. Null-Werte kommen aus PHP, zum Beispiel von einem Fetch, der nichts findet; prüfen Sie sie mit `is_null` oder `is_set`, und ersetzen
Sie eine fehlende Variable mit `first_set`:

```tpl
{def $empty = fetch( 'content', 'node', hash( 'node_id', 999999999 ) )}
[{$empty|get_type}] [{if $empty|is_null}is null{/if}] [{if is_set( $empty )}is set{else}not set{/if}] [{first_set( $missing, 'default' )}]
```

Ausgabe auf alpha:

```text
[null] [is null] [not set] [default]
```

## Wahrheitswerte

Bedingungen nutzen die Wahrheitswerte von PHP. `0`, `'0'`, die leere Zeichenkette, ein leeres Array, `false()` und null sind falsch; alles
andere, auch ein Array, das eine `0` enthält, ist wahr. Die in dieser Schleife geprüften Werte sind `0`, `1`, `''`, `'x'`, `'0'`, `array()`,
`array( 0 )`, `false()` und `true()`:

```tpl
{foreach array( 0, 1, '', 'x', '0', array(), array( 0 ), false(), true() ) as $v}[{if $v}T{else}F{/if}]{/foreach}
```

Ausgabe auf alpha:

```text
[F][T][F][T][F][F][T][F][T]
```

## Verwandte Seiten

- Zurück: [Lexikalische Struktur](lexikalische-struktur.md)
- Weiter: [Variablen im Detail](variablen-im-detail.md)
- [Grundlagen der TPL-Syntax](../../../en/us/help/designers/templates/tpl-syntax-basics.md)
- [Array-Operatoren](../../../en/us/help/designers/templates/array-operators.md)
- [Die Typ-Operatoren](../../../en/us/examples/template-operators/get_type.md)
- [Freie Dokumentation](../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
