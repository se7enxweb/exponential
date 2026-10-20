---
title: Referenz der Kontrollstrukturen
audience: developer, designer, app-creator
category: development
order: 130
summary: Jeder Parameter von if, elseif und else, switch und case (match, in, key und der Standardfall), foreach (key, sequence, offset, max, reverse), for, while und do und die Schleifenhelfer delimiter (mit modulo), break, continue und skip - mit ihrem genauen Verhalten aus dem Code, jeder auf alpha gezeigt.
keywords: Kontrollstrukturen, if, elseif, else, switch, case, match, in, key, foreach, offset, max, reverse, sequence, for, while, do, delimiter, modulo, break, continue, skip, Schleifen, control structures, loops
type: help
---
# Referenz der Kontrollstrukturen

Diese Seite führt jeden Parameter der Verzweigungs- und Schleifenfunktionen auf, wie sie in `lib/eztemplate/classes/` umgesetzt sind
(`eztemplateiffunction.php`, `eztemplateswitchfunction.php`, `eztemplateforeachfunction.php`, `eztemplateforfunction.php`,
`eztemplatewhilefunction.php`, `eztemplatedofunction.php` und der gemeinsame Schleifenhelfer `eztemplateloop.php`). Die besondere Grammatik der
Schleifen wird in `eztemplatemultipassparser.php` gelesen. Sanftere Einführungen bieten die Seiten für Designer, auf die am Ende verwiesen
wird.

## Inhalt

1. [if, elseif, else](#if-elseif-else)
2. [Was als wahr gilt](#was-als-wahr-gilt)
3. [switch und case](#switch-und-case)
4. [foreach](#foreach)
5. [break, continue, skip](#break-continue-skip)
6. [delimiter und modulo](#delimiter-und-modulo)
7. [for](#for)
8. [while](#while)
9. [do](#do)
10. [Verwandte Seiten](#verwandte-seiten)

## if, elseif, else

```text
{if <condition>} ... [{elseif <condition>} ...] [{else} ...] {/if}
```

Die Bedingung ist ein einzelner Ausdruck; verbinden Sie Prüfungen mit `and()`, `or()` und `not()`. `elseif` und `else` sind Kind-Tags von
`if` (seine `function_attributes`).

```tpl
{foreach array( 0, 1, 7 ) as $n}
{if $n|eq( 0 )}none{elseif $n|eq( 1 )}one{else}{$n} items{/if}
{/foreach}
```

Ausgabe auf alpha:

```text
noneone7 items
```

## Was als wahr gilt

Bedingungen nutzen die Wahrheitswerte von PHP: `0`, `'0'`, `''`, ein leeres Array, `false()` und null sind falsch. Die hier geprüften Werte
sind `0`, `1`, `''`, `'x'`, `'0'`, `array()`, `array( 0 )`, `false()`, `true()`:

```tpl
{foreach array( 0, 1, '', 'x', '0', array(), array( 0 ), false(), true() ) as $v}[{if $v}T{else}F{/if}]{/foreach}
```

Ausgabe auf alpha:

```text
[F][T][F][T][F][F][T][F][T]
```

## switch und case

```text
{switch match=<value>}
  {case match=<value>} ... {/case}
  {case in=<array> [key=<array of attribute names>]} ... {/case}
  {case} ... {/case}        (the default)
{/switch}
```

| Parameter | Von | Bedeutung |
|---|---|---|
| `match` | `switch` | der zu vergleichende Wert; Pflicht |
| `match` | `case` | dieser Fall wird genommen, wenn er dem Wert des switch gleicht (`==`) |
| `in` | `case` | dieser Fall wird genommen, wenn der Wert des switch im Array steht (`in_array`) |
| `key` | `case` mit `in` | den Wert des switch stattdessen mit diesem Attribut jedes Array-Elements vergleichen |
| keiner | `case` | der Standardfall |

Der erste passende Fall gewinnt. Ein zweiter Fall mit demselben Wert für `match` wird mit einer Warnung übersprungen.

```tpl
{foreach array( 1, 3, 'other' ) as $v}
{switch match=$v}{case match=1}one{/case}{case in=array( 2, 3 )}two or three{/case}{case}default for {$v}{/case}{/switch}
{/foreach}
```

Ausgabe auf alpha:

```text
onetwo or threedefault for other
```

Mit `key` übergibt der Code den Wert an `eZTemplate::variableAttribute()`, das eine **Liste** von Attributnamen durchläuft. Eine einfache
Zeichenkette wird dort nicht in eine Liste umgewandelt, also findet nur die Form als Array das Element; so verhält sich alpha heute:

```tpl
{def $people = array( hash( 'id', 7, 'name', 'Ada' ), hash( 'id', 9, 'name', 'Lin' ) )}
key as a string: {switch match=9}{case in=$people key='id'}found{/case}{case}not found{/case}{/switch}
key as an array: {switch match=9}{case in=$people key=array( 'id' )}found{/case}{case}not found{/case}{/switch}
```

Ausgabe auf alpha:

```text
key as a string: not foundkey as an array: found
```

Wenn nichts passt und es keinen Standardfall gibt, gibt der switch nichts aus und warnt:

```tpl
[{switch match=5}{case match=1}one{/case}{/switch}]
```

Ausgabe auf alpha:

```text
[]
```

Von der Template-Engine protokolliert:

```text
WARNING switch: No case match and no default case
```

## foreach

```text
{foreach <array> as [$key =>] $item
         [sequence <array> as $seq] [offset <n>] [max <n>] [reverse]}
  ...
{/foreach}
```

| Parameter | Bedeutung |
|---|---|
| `<array> as $item` | das Array und die Variable für jeden Wert; Pflicht |
| `$key =>` | eine Variable für jeden Schlüssel |
| `sequence <array> as $seq` | wechselt durch das Array, ein Wert pro Durchlauf |
| `offset <n>` | bei Position n beginnen; außerhalb des Bereichs wird mit einer Warnung zurückgesetzt |
| `max <n>` | höchstens n Durchläufe; ein negativer Wert wird mit einer Warnung ignoriert |
| `reverse` | vom Ende her durchlaufen; ein bloßes Wort, ohne Wert |

`offset` und `max` zählen Positionen, keine Schlüssel; mit `reverse` zählt der Offset vom Ende. Die Variablen für Schlüssel, Element und
Sequenz sind gewöhnliche Variablen der Datei, nicht eines Namensraums.

```tpl
{foreach array( 'a', 'b', 'c', 'd', 'e' ) as $i => $v sequence array( 'x', 'y' ) as $s offset 1 max 3}[{$i}:{$v}:{$s}]{delimiter}|{/delimiter}{/foreach}
{foreach array( 1, 2, 3, 4 ) as $v reverse}{$v}{/foreach}
{foreach hash( 'one', 1, 'two', 2 ) as $key => $value}{$key}={$value} {/foreach}
```

Ausgabe auf alpha:

```text
[1:b:x]|[2:c:y]|[3:d:x]4321one=1 two=2
```

Ein leeres Array führt den Rumpf null Mal aus, ohne Warnung:

```tpl
[{foreach array() as $v}{$v}{/foreach}] empty array: no output
```

Ausgabe auf alpha:

```text
[] empty array: no output
```

Verlassen Sie sich nach der Schleife nicht auf die Schleifenvariablen. Ein interpretiertes Template entfernt eine Variable, die die Schleife
angelegt hat, ein kompiliertes behält sie mit ihrem letzten Wert; eine Variable, die vor der Schleife existierte, wird in beiden Modi
überschrieben:

```tpl
{foreach array( 1, 2 ) as $v}{/foreach}
[{if is_set( $v )}v still set{else}v removed after the loop{/if}]
{def $item = 'before'}
{foreach array( 1, 2 ) as $item}{/foreach}
[{$item}]
```

Ausgabe auf alpha:

```text
[v removed after the loop][2]
```

Ausgabe auf alpha mit dem Template-Compiler (in einen Arbeitsordner kompiliert):

```text
[v still set][2]
```

Mit eingeschalteter Template-Kompilierung, wie auf einer Live-Website, bekommen Besucher das kompilierte Ergebnis.

`foreach` läuft nicht über die Zeichen einer Zeichenkette oder über einen Zahlenbereich; nutzen Sie `for` für Bereiche, und siehe
[Sections und foreach](sections-und-foreach.md) dazu, was `section` mit Zeichenketten tut.

## break, continue, skip

Alle vier Schleifen (`foreach`, `for`, `while`, `do`) akzeptieren diese Kind-Tags, auch innerhalb eines `if` in der Schleife
(`eZTemplateLoop::processChildren()`):

| Tag | Beendet | Nächster Durchlauf | Delimiter nach diesem Durchlauf | Sequenz rückt weiter |
|---|---|---|---|---|
| `{break}` | die Schleife | nein | nein | nein |
| `{continue}` | diesen Durchlauf | ja | **ja** | nein |
| `{skip}` | diesen Durchlauf | ja | nein | nein |

In dieser Schleife wird bei 2 fortgesetzt (sein Delimiter wird trotzdem ausgegeben, daher der doppelte Bindestrich), 4 wird übersprungen
(kein Delimiter), und bei 6 wird abgebrochen:

```tpl
{foreach array( 1, 2, 3, 4, 5, 6 ) as $v}{if eq( $v, 2 )}{continue}{/if}{if eq( $v, 4 )}{skip}{/if}{if eq( $v, 6 )}{break}{/if}{$v}{delimiter}-{/delimiter}{/foreach}
```

Ausgabe auf alpha:

```text
1--3-5-
```

## delimiter und modulo

`{delimiter}...{/delimiter}` wird zwischen den Durchläufen ausgegeben. Es muss ein direktes Kind der Schleife sein. Mit `modulo=<n>` wird es
nur ausgegeben, wenn der Index des Durchlaufs durch n teilbar ist:

```tpl
{foreach array( 1, 2, 3, 4, 5, 6 ) as $v}{$v}{delimiter modulo=2} / {/delimiter}{/foreach}
```

Ausgabe auf alpha:

```text
12 / 34 / 56
```

## for

```text
{for <first> to <last> as $i [sequence <array> as $seq]} ... {/for}
```

Zählt vom ersten bis einschließlich zum letzten Wert, aufwärts oder abwärts. Die Schleifenhelfer gelten; in der dritten Schleife wird der
Delimiter nach 3 ausgegeben, bevor das `{break}` des nächsten Durchlaufs läuft:

```tpl
{for 1 to 5 as $i sequence array( 'o', 'e' ) as $s}{$i}{$s} {/for}
{for 5 to 1 as $i}{$i}{/for}
{for 0 to 10 as $i}{if $i|gt( 3 )}{break}{/if}{$i}{delimiter},{/delimiter}{/for}
```

Ausgabe auf alpha:

```text
1o 2e 3o 4e 5o 543210,1,2,3,
```

## while

```text
{while <condition> [sequence <array> as $seq]} ... {/while}
```

Die Bedingung wird vor jedem Durchlauf ausgewertet. Ändern Sie die geprüfte Variable im Rumpf mit `{set}`, sonst endet die Schleife nicht:

```tpl
{def $n = 0}
{while $n|lt( 3 ) sequence array( 'a', 'b' ) as $s}[{$n}{$s}]{set $n = $n|inc}{/while}
```

Ausgabe auf alpha:

```text
[0a][1b][2a]
```

## do

```text
{do} ... {/do while <condition> [sequence <array> as $seq]}
```

Die Bedingung steht im End-Tag und wird nach jedem Durchlauf geprüft; der Rumpf läuft also mindestens einmal, auch wenn die Bedingung von
Anfang an falsch ist:

```tpl
{def $n = 5}
{do}[{$n}]{set $n = $n|inc}{/do while $n|lt( 3 )}
```

Ausgabe auf alpha:

```text
[5]
```

## Verwandte Seiten

- Zurück: [Funktionen und Operatoren](funktionen-und-operatoren.md)
- Weiter: [Sections und foreach](sections-und-foreach.md)
- [If und Bedingungen](../../../en/us/help/designers/templates/if-and-conditions.md), [Switch und case](../../../en/us/help/designers/templates/switch-case.md)
- [Foreach-Schleifen](../../../en/us/help/designers/templates/foreach-loops.md), [For-Schleifen](../../../en/us/help/designers/templates/for-loops.md), [While- und do-Schleifen](../../../en/us/help/designers/templates/while-and-do-loops.md)
- [foreach](../../../en/us/examples/template-functions/foreach.md), [switch](../../../en/us/examples/template-functions/switch.md), aus dem Code erzeugt
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
