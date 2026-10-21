---
title: Operatoren in Ausdrücken
audience: developer, designer, app-creator
category: development
order: 110
summary: Wie Operatoren geschrieben und ausgewertet werden - die Pipe-Form und die Aufruf-Form, was der Eingabewert in jeder ist, Verkettung, Operatoren als Parameter verschachteln, Attribute nach einem Operator, Operatoren in Parametern von Funktionen, wie Parameter benannten Parametern mit Standardwerten zugeordnet werden und was bei einem unbekannten Operator oder einem fehlenden Parameter geschieht.
keywords: Operatoren, Pipe, Aufruf-Form, Eingabewert, Verkettung, Verschachtelung, Parameter, benannte Parameter, namedParameterList, Standardwert, fehlender Parameter, unbekannter Operator, processOperator, modify, operators, pipe, call form, input value, chaining, nesting, named parameters, default, missing parameter, unknown operator
type: help
---
# Operatoren in Ausdrücken

Ein Operator ist eine benannte Umwandlung eines Werts. Im Parserbaum ist er ein Element vom Typ `TYPE_OPERATOR`, das den Namen des Operators
und die Ausdrücke seiner Parameter hält (`parseVariableTag()` in `lib/eztemplate/classes/eztemplateelementparser.php`); zur Laufzeit ordnet
`eZTemplate::processOperator()` in `lib/eztemplate/classes/eztemplate.php` die Parameter zu und ruft `modify()` der Operatorklasse auf. Diese
Seite zeigt, was das für das Schreiben von Ausdrücken bedeutet.

## Inhalt

1. [Pipe-Form und Aufruf-Form](#pipe-form-und-aufruf-form)
2. [Verkettung](#verkettung)
3. [Verschachtelung](#verschachtelung)
4. [Attribute nach einem Operator](#attribute-nach-einem-operator)
5. [Operatoren in Parametern von Funktionen](#operatoren-in-parametern-von-funktionen)
6. [Wie Parameter zugeordnet werden](#wie-parameter-zugeordnet-werden)
7. [Unbekannte Operatoren und fehlende Parameter](#unbekannte-operatoren-und-fehlende-parameter)
8. [Verwandte Seiten](#verwandte-seiten)

## Pipe-Form und Aufruf-Form

Derselbe Operator lässt sich auf zwei Arten schreiben:

- **Pipe-Form**, `{$value|name( params )}`: Der Wert vor der Pipe ist die **Eingabe** des Operators.
- **Aufruf-Form**, `{name( params )}`: Es gibt keine Eingabe; die Eingabe ist null.

Operatoren unterscheiden sich darin, was sie mit der Eingabe tun. `upcase` arbeitet nur auf seiner Eingabe, die Aufruf-Form ergibt also
nichts. `sum` addiert die Eingabe zu seinen Parametern, wenn es eine gibt. `concat` stellt die Eingabe vor seine Parameter:

```tpl
[{'abc'|upcase}] [{upcase( 'abc' )}]
[{2|sum( 3 )}] [{sum( 2, 3 )}]
[{'a'|concat( 'b', 'c' )}] [{concat( 'a', 'b', 'c' )}]
```

Ausgabe auf alpha:

```text
[ABC] []
[5] [5]
[abc] [abc]
```

Eine Eingabe, die ein Operator nicht nutzen kann, ist kein Fehler. `sum` behandelt eine nicht numerische Eingabe als nichts, was zu addieren
wäre:

```tpl
[{'ignored'|sum( 1, 2 )}] [{5|sum( 1, 2 )}]
```

Ausgabe auf alpha:

```text
[3] [8]
```

Welche Form ein Operator akzeptiert, gehört zu seiner Dokumentation. Die erzeugte Seite jedes Operators sagt, ob er auf seiner Eingabe
arbeitet ([jeder Template-Operator](../../../eng-us/examples/template-operators/README.md)).

## Verkettung

Jedes `|` gibt das Ergebnis des vorigen Operators an den nächsten weiter, von links nach rechts:

```tpl
[{'  Hello Template World  '|trim|downcase|shorten( 12 )|upfirst}]
[{array( 3, 1, 2 )|count|mul( 10 )|sum( 5 )}]
```

Ausgabe auf alpha:

```text
[Hello tem...]
[35]
```

## Verschachtelung

Parameter sind vollständige Ausdrücke: Variablen, Literale, andere Operatoren in beiden Formen, mit ihren eigenen Attributen. Bedingungen
werden so gebaut, weil es keine Operatoren in Infixschreibweise gibt:

```tpl
{def $a = 4 $b = 2}
[{concat( 'sum=', sum( $a, $b ), ', product=', mul( $a, $b ) )}]
[{if and( $a|gt( 1 ), $b|lt( 5 ), or( $a|eq( 4 ), $b|eq( 0 ) ) )}all true{/if}]
[{cond( $a|gt( $b ), 'a wins', 'b wins' )}]
```

Ausgabe auf alpha:

```text
[sum=6, product=8]
[all true]
[a wins]
```

## Attribute nach einem Operator

Ein Punkt oder eckige Klammern können auf einen Operatoraufruf folgen. Das Attribut wird aus dem Ergebnis des Operators gelesen:

```tpl
[{fetch( 'content', 'node', hash( 'node_id', 89 ) ).name}]
[{fetch( 'content', 'node', hash( 'node_id', 89 ) ).children|count}]
[{array( 'x', 'y', 'z' )|reverse.0}]
```

Ausgabe auf alpha:

```text
[Fit & Healthy]
[16]
[z]
```

## Operatoren in Parametern von Funktionen

Parameter von Funktionen sind ebenfalls Ausdrücke; ein Operator kann also das Array eines `foreach` oder den Wert eines `def` liefern:

```tpl
{foreach array( 'b', 'a', 'c' )|reverse as $letter}{$letter|upcase}{/foreach}
{def $label = concat( 'Item ', 3|inc )}[{$label}]
```

Ausgabe auf alpha:

```text
CAB[Item 4]
```

Umgekehrt gilt das nicht: Eine Funktion ist kein Wert und lässt sich nicht in einem Ausdruck aufrufen. `if( ... )` in einem Ausdruck wird als
Operator namens `if` gesucht, den es nicht gibt:

```tpl
[{concat( 'a', if( true() ) )}]
```

Ausgabe auf alpha:

```text
[a]
```

Von der Template-Engine protokolliert:

```text
WARNING : Operator 'if' is not registered
```

## Wie Parameter zugeordnet werden

Parameter sind positionsabhängig. Eine Operatorklasse kann in `namedParameterList()` Namen für sie angeben (pro Operator, wenn
`namedParameterPerOperator()` true zurückgibt). `processOperator()` geht diese Liste der Reihe nach durch:

1. Gibt das Template an dieser Position einen Wert an, wird er ausgewertet und unter dem Namen des Parameters gespeichert.
2. Wenn nicht, und der Parameter ist `required`, wird die Warnung `Parameter '<name>' (<position>) missing` geschrieben.
3. Ein fehlender Parameter bekommt seinen `default` aus der Liste. Hat die Liste auch keinen Standardwert, bekommt er null, und die Engine
   warnt einmal pro Operator und Parameter, dass es keinen Standardwert gibt.

`modify()` des Operators bekommt dann den Eingabewert (als Referenz; was es dort hinterlässt, ist das Ergebnis), die rohen Ausdrücke der
Parameter und die benannten Werte. Operatoren, die beliebig viele Parameter nehmen, wie `sum` oder `concat`, geben keine an und werten die
rohen Ausdrücke selbst aus. Wie Sie eine solche Klasse schreiben, steht in
[Template-Operatoren schreiben](../../../eng-us/help/creators/app-creators/template-operators.md).

## Unbekannte Operatoren und fehlende Parameter

Ein nicht registrierter Operatorname schreibt `Operator '<name>' is not registered` und lässt die Eingabe unverändert. Die Seite wird weiter
gerendert, was Tippfehler verbirgt:

```tpl
[{'kept as it is'|nosuchoperator( 1 )}]
```

Ausgabe auf alpha:

```text
[kept as it is]
```

Von der Template-Engine protokolliert:

```text
WARNING : Operator 'nosuchoperator' is not registered
```

Ein fehlender Pflichtparameter wird gemeldet, und dann läuft der Operator mit dem, was er hat. `datetime` braucht die Formatklasse als ersten
Parameter; ohne sie wird der Zeitstempel unformatiert ausgegeben, und zwei Meldungen werden protokolliert:

```tpl
[{1674667522|datetime}]
```

Ausgabe auf alpha:

```text
[1674667522]
```

Von der Template-Engine protokolliert:

```text
ERROR datetime: DateTime class '' is not defined
WARNING eZTemplateOperatorElement: Parameter 'class' (0) missing
```

## Verwandte Seiten

- Zurück: [Zugriff auf Attribute](zugriff-auf-attribute.md)
- Weiter: [Funktionen und Operatoren](funktionen-und-operatoren.md)
- [Überblick über Operatoren](../../../eng-us/help/designers/templates/operators-overview.md)
- [Referenz der Operatoren](../../../eng-us/help/designers/templates/operator-reference.md)
- [Jeder Template-Operator](../../../eng-us/examples/template-operators/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
