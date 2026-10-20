---
title: Sections und foreach
audience: developer, designer, app-creator
category: templates
order: 140
summary: Die Funktion section vollständig - Schleifen über Arrays, Zahlen und Zeichenketten, name und der Namensraum, den sie anlegt, var und der Iterator mit last-value, sequence, offset, max, reverse, show mit section-else, die Filter section-exclude und section-include und delimiter mit modulo - jede neben ihrer Entsprechung mit foreach, for oder if, alles auf alpha gerendert, plus der eine gefundene Unterschied im kompilierten Modus.
keywords: section, foreach, veraltet, Schleife, show, section-else, section-exclude, section-include, var, last-value, sequence, offset, max, reverse, delimiter, modulo, Namensraum, umstellen, Migration, legacy, loop, namespace, convert, migration
type: help
---
# Sections und foreach

`section` war die Schleife und die Bedingung der Template-Sprache, bevor `foreach`, `for` und `if` kamen (siehe
[Geschichte](geschichte-der-template-sprache.md#die-verbesserte-syntax-2005-version-36)). Sie ist weiterhin registriert, ältere Designs und Erweiterungen nutzen sie
überall, und sie kann einige Dinge, die die neueren Funktionen anders lösen. Diese Seite dokumentiert jeden Parameter von
`eZTemplateSectionFunction` (`lib/eztemplate/classes/eztemplatesectionfunction.php`) und zeigt neben jedem die moderne Entsprechung. Eine kurze
Anleitung zum Umstellen bietet [Section (veraltet)](../../../en/us/help/designers/templates/section-legacy.md).

## Inhalt

1. [Parameter](#parameter)
2. [Schleife, Namensraum und die Schleifenvariablen](#schleife-namensraum-und-die-schleifenvariablen)
3. [Zahlen und Zeichenketten](#zahlen-und-zeichenketten)
4. [offset, max, reverse](#offset-max-reverse)
5. [show und section-else](#show-und-section-else)
6. [Filter: section-exclude und section-include](#filter-section-exclude-und-section-include)
7. [var und last-value](#var-und-last-value)
8. [delimiter und modulo](#delimiter-und-modulo)
9. [Ein Unterschied in kompilierten Templates](#ein-unterschied-in-kompilierten-templates)
10. [Verwandte Seiten](#verwandte-seiten)

## Parameter

| Parameter | Bedeutung | Moderne Entsprechung |
|---|---|---|
| `loop` | ein Array, eine Zahl oder eine Zeichenkette zum Durchlaufen | `foreach` (Arrays), `for` (Zahlen) |
| `name` | der Namensraum für die Schleifenvariablen | nicht nötig: `foreach ... as $item` |
| `var` | eine einzelne Iteratorvariable statt des Namensraums | `foreach ... as $key => $item` |
| `last-value` | mit `var`: `$it.last` hält den vorigen Iterator | das vorige Element in einer Variablen behalten |
| `sequence` | ein durchgewechseltes Array, `$Name:sequence` | `sequence <array> as $s` |
| `offset`, `max` | überspringen und begrenzen; ein Array zählt als seine Länge | `offset`, `max` von `foreach` |
| `reverse` | rückwärts durchlaufen | `reverse` |
| `show` | den Rumpf nur verarbeiten, wenn wahr; ohne `loop` ist es ein `if` | `if` |
| Kind `section-else` | was gezeigt wird, wenn `show` falsch ist | `else` |
| Kind `section-exclude match=...` | Elemente verwerfen, für die match wahr ist | `{if ...}{skip}{/if}` |
| Kind `section-include match=...` | Elemente wieder aufnehmen | ein `if` um den Rumpf |
| Kind `delimiter [modulo=n] [match=...]` | Text zwischen den Durchläufen | `delimiter` der Schleife |

## Schleife, Namensraum und die Schleifenvariablen

Mit `name=Loop` setzt jeder Durchlauf `item`, `key`, `index` (ab 0), `number` (ab 1) und, mit einer `sequence`, `sequence` im Namensraum
`Loop`, gelesen als `$Loop:item` oder, innerhalb der Section, `$:item` (siehe [Namensräume](namensraeume.md)). Ohne Namen landen sie im
aktuellen Namensraum als `$item`, `$key` und so weiter und werden nach der Schleife entfernt.

```tpl
{section name=Loop loop=array( 'a', 'b', 'c' ) sequence=array( 'odd', 'even' )}[{$Loop:item}/{$Loop:key}/{$Loop:index}/{$Loop:number}/{$Loop:sequence}]{delimiter},{/delimiter}{/section}
```

Ausgabe auf alpha:

```text
[a/0/0/1/odd],[b/1/1/2/even],[c/2/2/3/odd]
```

Dasselbe mit `foreach`:

```tpl
{foreach array( 'a', 'b', 'c' ) as $key => $item sequence array( 'odd', 'even' ) as $seq}[{$item}/{$key}/{$key}/{$key|inc}/{$seq}]{delimiter},{/delimiter}{/foreach}
```

Ausgabe auf alpha:

```text
[a/0/0/1/odd],[b/1/1/2/even],[c/2/2/3/odd]
```

## Zahlen und Zeichenketten

`loop` nimmt auch eine Zahl, wobei `item` von 1 bis n läuft (oder von -1 bis -n bei einer negativen Zahl), und eine Zeichenkette, deren Zeichen
durchlaufen werden. `foreach` kann beides nicht: Nutzen Sie `for`, oder teilen Sie die Zeichenkette mit `str_split`:

```tpl
[{section loop=3}{$item}{/section}] [{section loop=-3}{$item}{/section}] [{section loop='abc' reverse=true()}{$item}{/section}]
[{for 1 to 3 as $i}{$i}{/for}] [{foreach 'abc'|str_split|reverse as $c}{$c}{/foreach}]
```

Ausgabe auf alpha:

```text
[123] [-1-2-3] [cba]
[123] [cba]
```

## offset, max, reverse

`offset` verwirft die ersten n Elemente, und `max` hört nach n verarbeiteten Elementen auf (ein an eines von beiden übergebenes Array zählt als
seine Länge; ein negativer Offset wird mit einer Warnung auf 0 zurückgesetzt). Mit `reverse` werden die Schlüssel umgekehrt, bevor der Offset
angewendet wird:

```tpl
[{section name=S loop=array( 1, 2, 3, 4, 5, 6 ) offset=1 max=3}{$S:item}{/section}]
[{foreach array( 1, 2, 3, 4, 5, 6 ) as $v offset 1 max 3}{$v}{/foreach}]
[{section name=R loop=array( 1, 2, 3 ) reverse=true()}{$R:item}{/section}] [{foreach array( 1, 2, 3 ) as $v reverse}{$v}{/foreach}]
```

Ausgabe auf alpha:

```text
[234]
[234]
[321] [321]
```

## show und section-else

`show` entscheidet, ob der Rumpf überhaupt läuft. Es ist falsch bei `false()`, 0, einer leeren Zeichenkette und einem leeren Array. Die Kinder
nach `{section-else}` laufen dann stattdessen. Ohne `loop` ist `section show=...` einfach eine Bedingung:

```tpl
{def $results = array()}
[{section show=$results|count}results{section-else}nothing found{/section}]
[{if $results|count}results{else}nothing found{/if}]
```

Ausgabe auf alpha:

```text
[nothing found]
[nothing found]
```

```tpl
{def $user = hash( 'name', 'Ada' )}
[{section show=$user.name}Hello {$user.name}{/section}]
```

Ausgabe auf alpha:

```text
[Hello Ada]
```

`section-else` gehört zu `show`: Ein leeres `loop` ohne `show` gibt überhaupt nichts aus, auch nicht den else-Teil:

```tpl
[{section loop=array()}x{section-else}else part{/section}]
[{section loop=array() show=array()}x{section-else}else part{/section}]
```

Ausgabe auf alpha:

```text
[]
[else part]
```

## Filter: section-exclude und section-include

Filter-Tags werden für jedes Element im Namensraum der Section ausgewertet, der Reihe nach. Ein `exclude`, dessen `match` wahr ist, entfernt das
Element, ein `include`, dessen `match` wahr ist, nimmt es wieder auf; der letzte passende Filter entscheidet. Ein ausgeschlossenes Element zählt
nicht für `max` und bekommt keinen Delimiter. Die Form mit `foreach` nutzt `{skip}`:

```tpl
[{section name=F loop=array( 1, 2, 3, 4, 5, 6 )}{section-exclude match=$:item|mod( 2 )|eq( 0 )}{$F:item}{/section}]
[{section name=G loop=array( 1, 2, 3, 4, 5, 6 )}{section-exclude match=true()}{section-include match=$:item|gt( 4 )}{$G:item}{/section}]
[{foreach array( 1, 2, 3, 4, 5, 6 ) as $v}{if $v|mod( 2 )|eq( 0 )}{skip}{/if}{$v}{/foreach}]
```

Ausgabe auf alpha:

```text
[135]
[56]
[135]
```

## var und last-value

`var=it` gibt statt eines Namensraums ein einzelnes Iterator-Objekt (`eZTemplateSectionIterator`). Ausgegeben ist es das aktuelle Element;
seine Attribute sind `item`, `key`, `index`, `number`, `sequence` und die eigenen Attribute des Elements. Mit `last-value=true()` hält das
Attribut `last` den Iterator des vorigen Durchlaufs:

```tpl
[{section var=it loop=array( 'a', 'b', 'c' ) last-value=true()}{$it}{if $it.last} (after {$it.last}){/if}; {/section}]
[{section var=it loop=array( 'a', 'b' )}{$it.item}/{$it.key}/{$it.index}/{$it.number} {/section}]
```

Ausgabe auf alpha:

```text
[a; b (after a); c (after b); ]
[a/0/0/1 b/1/1/2 ]
```

## delimiter und modulo

Wie bei den neueren Schleifen wird `delimiter` zwischen verarbeiteten Elementen ausgegeben, und `modulo=n` beschränkt ihn auf jedes n-te. Ein
Delimiter von `section` akzeptiert außerdem `match=<condition>`:

```tpl
[{section name=D loop=array( 1, 2, 3, 4 )}{delimiter modulo=2}|{/delimiter}{$D:item}{/section}]
[{foreach array( 1, 2, 3, 4 ) as $v}{$v}{delimiter modulo=2}|{/delimiter}{/foreach}]
```

Ausgabe auf alpha:

```text
[12|34]
[12|34]
```

## Ein Unterschied in kompilierten Templates

Jedes Beispiel auf dieser Seite wurde auch durch den Template-Compiler gerendert. Eines ergibt ein anderes Ergebnis: eine umgekehrte Schleife
über eine **Zeichenkette**. Interpretiert gibt `{section loop='abc' reverse=true()}` den Text `cba` aus; kompiliert gibt es nur `c` aus,
während die Form mit `foreach` auf beide Arten `cba` ergibt:

```tpl
[{section loop=3}{$item}{/section}] [{section loop=-3}{$item}{/section}] [{section loop='abc' reverse=true()}{$item}{/section}]
[{for 1 to 3 as $i}{$i}{/for}] [{foreach 'abc'|str_split|reverse as $c}{$c}{/foreach}]
```

Ausgabe auf alpha:

```text
[123] [-1-2-3] [cba]
[123] [cba]
```

Ausgabe auf alpha mit dem Template-Compiler (in einen Arbeitsordner kompiliert):

```text
[123] [-1-2-3] [c]
[123] [cba]
```

Live-Websites kompilieren ihre Templates (`site.ini` [TemplateSettings] standardmäßig `TemplateCompile=enabled`); stellen Sie eine solche
Schleife also auf `foreach` mit `str_split|reverse` um.

## Verwandte Seiten

- Zurück: [Referenz der Kontrollstrukturen](referenz-der-kontrollstrukturen.md)
- [Section (veraltet)](../../../en/us/help/designers/templates/section-legacy.md)
- [section](../../../en/us/examples/template-functions/section.md), aus dem Code erzeugt
- [Foreach-Schleifen](../../../en/us/help/designers/templates/foreach-loops.md)
- [Freie Dokumentation](../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
