---
title: Mathematische Operatoren
audience: designer, developer
category: templates
order: 210
summary: Rechnen in Templates mit sum, sub, mul, div, mod, inc, dec, min, max, abs, ceil, floor, round, int, float, count, roman und rand, mit Beispielen für Blättern, Prozentwerte und Raster.
keywords: mathematische Operatoren, Arithmetik, sum, sub, mul, div, mod, inc, dec, min, max, abs, ceil, floor, round, int, float, count, roman, rand, Berechnung, math operators, arithmetic, calculation
type: help
---
# Mathematische Operatoren

Diese Seite ist eine Referenz für Berechnungen in Templates. Alle stammen aus `eZTemplateArithmeticOperator`
(`lib/eztemplate/classes/eztemplatearithmeticoperator.php`).

## Inhalt

1. [Die Operatoren](#die-operatoren)
2. [Beispiele](#beispiele)
3. [Hinweise](#hinweise)

## Die Operatoren

| Operator | Beispiel | Ergebnis |
|---|---|---|
| `sum` | `sum( 2, 3, 4 )` oder `2\|sum( 3 )` | 9, 5 |
| `sub` | `sub( 10, 3 )` | 7 |
| `mul` | `mul( 4, 2.5 )` | 10 |
| `div` | `div( 10, 4 )` | 2.5 |
| `mod` | `mod( 10, 4 )` | 2 |
| `inc`, `dec` | `$i\|inc` | `$i` plus oder minus 1 |
| `min`, `max` | `max( $a, $b )` | der kleinere / größere |
| `abs`, `ceil`, `floor`, `round` | `$x\|round` | |
| `int`, `float` | `$text\|int` | eine Zahl aus einer Zeichenkette |
| `count` | `$list\|count` | Zahl der Einträge (die Anzahl eines Arrays, die Länge einer Zeichenkette) |
| `roman` | `4\|roman` | `IV` |
| `rand` | `rand( 1, 6 )` | eine Zufallszahl (nicht in gecachter Ausgabe verwenden) |

## Beispiele

Zahl der Seiten:

```tpl
{def $pages = ceil( div( $total, $limit ) )}
```

Versatz der aktuellen Seite:

```tpl
{def $offset = first_set( $view_parameters.offset, 0 )|int}
{def $page = sum( div( $offset, $limit )|floor, 1 )}
```

Eine Prozentklasse für eine Anzeige (admin4 hat `.a4-pct-0` bis `.a4-pct-100` in Schritten von 4):

```tpl
{def $pct = mul( div( $done, $total ), 100 )|round}
<span class="a4-meter"><span class="a4-pct-{mul( div( $pct, 4 )|round, 4 )}"></span></span>
```

Spalten für ein Raster:

```tpl
{if mod( $index, 3 )|eq( 0 )}<div class="row">{/if}
```

(CSS Grid macht solches Zählen meist überflüssig.)

## Hinweise

- Division durch null: Prüfen Sie zuerst den Divisor.
- Rechnen im Template ist für die Darstellung in Ordnung; geschäftliche Berechnungen (Preise, Steuern) gehören in das PHP
  des Shops.
- Innerhalb eines Templates im View-Cache oder eines Cache-Blocks gibt `rand` denselben Wert zurück, bis der Cache neu
  aufgebaut wird.

## Verwandte Seiten

- [Logik- und Vergleichsoperatoren](logik-und-vergleichsoperatoren.md)
- [Seitenumbruch](../../../../eng-us/help/designers/templates/pagination.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
