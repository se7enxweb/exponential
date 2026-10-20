---
title: Funktionen und Operatoren
audience: developer, designer, app-creator
category: development
order: 120
summary: Die zwei Arten benannter Dinge in der Template-Sprache verglichen - Template-Funktionen sind Tags, die Ausgabe schreiben und das Template steuern, Operatoren berechnen Werte - mit ihren Arrays zur Registrierung, den Methoden, die jede PHP-Klasse umsetzt, Block-Funktionen und Kindern, interpretierter Verarbeitung gegenüber kompilierter Transformation und wann Sie welches schreiben.
keywords: Template-Funktionen, Template-Operatoren, eZTemplateFunctionArray, eZTemplateOperatorArray, process, modify, hasChildren, functionList, operatorList, templateNodeTransformation, operatorTemplateHints, Compiler, Block-Funktion, template functions, template operators, compiler, block function
type: help
---
# Funktionen und Operatoren

Die Template-Sprache hat zwei Arten benannter Bausteine. Eine **Funktion** ist ein Tag: `{if}`, `{foreach}`, `{include}`, `{cache-block}`.
Ein **Operator** erzeugt einen Wert innerhalb eines Ausdrucks: `wash`, `fetch`, `concat`, `count`. Sie werden in getrennten Listen registriert,
durch verschiedene Methoden umgesetzt und unterschiedlich kompiliert. Wer weiß, was was ist, kann die meisten Fragen der Art „Warum
funktioniert das hier nicht?“ beantworten.

## Inhalt

1. [Auf einen Blick](#auf-einen-blick)
2. [Dieselbe Aufgabe auf beide Arten](#dieselbe-aufgabe-auf-beide-arten)
3. [Von der Ausgabe einer Funktion zu einem Operator](#von-der-ausgabe-einer-funktion-zu-einem-operator)
4. [Registrierung](#registrierung)
5. [Die PHP-Seite](#die-php-seite)
6. [Interpretiert und kompiliert](#interpretiert-und-kompiliert)
7. [Was Sie schreiben sollten](#was-sie-schreiben-sollten)
8. [Verwandte Seiten](#verwandte-seiten)

## Auf einen Blick

| | Funktion | Operator |
|---|---|---|
| Geschrieben als | ein Tag: `{name params}`, oft mit `{/name}` | in einem Ausdruck: `name( ... )` oder `\|name( ... )` |
| Parameter | Paare `name=value` oder eine eigene Grammatik (`foreach ... as ...`) | positionsabhängig, in Klammern |
| Erzeugt | Ausgabetext, Variablen, Ablaufsteuerung | einen Wert |
| Kann Kinder haben | ja, Block-Funktionen (`if`, `foreach`, `cache-block`) | nein |
| In einem Ausdruck nutzbar | nein | ja, verschachtelt und verkettet |
| Registriert in | `$eZTemplateFunctionArray` | `$eZTemplateOperatorArray` |
| Wichtigste PHP-Methode | `process()` | `modify()` |
| Haken für den Compiler | `templateNodeTransformation()` | `operatorTemplateHints()` und eine Methode zur Transformation |
| Auf dem Siteaccess `site` von alpha | 48 registriert | 395 registriert |

## Dieselbe Aufgabe auf beide Arten

Viele Aufgaben lassen sich mit beiden erledigen, und das Ergebnis ist derselbe Text:

```tpl
{def $tags = array( 'news', 'sport', 'weather' )}
function: {foreach $tags as $t}{$t}{delimiter}, {/delimiter}{/foreach}
operator: {$tags|implode( ', ' )}
function: {if $tags|count|gt( 2 )}many{else}few{/if}
operator: {cond( $tags|count|gt( 2 ), 'many', 'few' )}
```

Ausgabe auf alpha:

```text
function: news, sport, weatheroperator: news, sport, weather
function: manyoperator: many
```

Die Form mit Funktion liest sich leichter, wenn die Zweige oder der Schleifenrumpf Markup enthalten; die Form mit Operator passt in einen
Parameter oder einen Attributwert.

## Von der Ausgabe einer Funktion zu einem Operator

Die Ausgabe von Funktionen ist Text, und `set-block` macht aus Text wieder einen Wert, mit dem Operatoren arbeiten können:

```tpl
{set-block variable=list}{foreach array( 'a', 'b', 'c' ) as $x}<{$x}>{/foreach}{/set-block}
[{$list}] [{$list|count_chars}] [{$list|upcase}]
```

Ausgabe auf alpha:

```text
[<a><b><c>] [9] [<A><B><C>]
```

Eine Funktion lässt sich nicht dort nutzen, wo ein Wert erwartet wird. Innerhalb eines Ausdrucks kennt der Parser nur Operatoren, also wird
`if( ... )` als Operator gesucht:

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

## Registrierung

Beide Arten werden in einer Datei `eztemplateautoload.php` aufgeführt, die `eZTemplate::autoload()` aus jedem Pfad von `site.ini`
[TemplateSettings] `AutoloadPathList` und aus jeder Erweiterung in `ExtensionAutoloadPath` einbindet
(`<extension>/autoloads/eztemplateautoload.php`). Die eigene Datei der Template-Bibliothek, `lib/eztemplate/classes/eztemplateautoload.php`,
zeigt beide Formen:

```php
$eZTemplateOperatorArray[] = array( 'class' => 'eZTemplateTextOperator',
                                    'operator_names' => array( 'concat', 'indent' ) );

$eZTemplateFunctionArray[] = array( 'class' => 'eZTemplateIfFunction',
                                    'function_names' => array( 'if' ),
                                    'function_attributes' => array( 'elseif',
                                                                    'else' ) );
```

`function_attributes` nennt die Kind-Tags, die zur Funktion gehören (`elseif` und `else` von `if`; `delimiter`, `break`, `continue` und `skip`
der Schleifen; `case` von `switch`). Einträge werden zunächst nur vorgemerkt; die Klasse wird geladen, wenn einer ihrer Namen zum ersten Mal
genutzt wird (`loadAndRegisterOperators()`, `loadAndRegisterFunctions()`). Ein zweimal registrierter Name behält den zuletzt gelesenen Eintrag;
so kann eine Erweiterung einen Operator des Kernels ersetzen; die Seite
[Operatoren des Kernels überschreiben](../../../eng-us/help/template-operators/overriding-kernel-operators.md) behandelt das.

Die Ansichtsfunktionen des Kernels (`node_view_gui`, `attribute_view_gui` und die anderen Funktionen `*_gui`) sind alle eine Klasse,
`eZObjectForwarder` in `kernel/common/ezobjectforwarder.php`, konfiguriert durch die Regeln in `kernel/common/eztemplateautoload.php`.

## Die PHP-Seite

Eine **Operatorklasse** beantwortet:

- `operatorList()`: die Namen, die sie behandelt;
- `namedParameterPerOperator()` und `namedParameterList()`: die Parameter, mit `type`, `required` und `default`;
- `modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters, $placement )`:
  liest die Eingabe aus `$operatorValue` und schreibt das Ergebnis dorthin zurück.

Eine **Funktionsklasse** beantwortet:

- `functionList()`: die Namen, die sie behandelt;
- `hasChildren()`: ob sie ein Block ist (true, false oder ein Array pro Name);
- `process( $tpl, &$textElements, $functionName, $functionChildren, $functionParameters, $functionPlacement, $rootNamespace, $currentNamespace )`:
  hängt Ausgabe an `$textElements` an, setzt Variablen und verarbeitet ihre Kinder mit `$tpl->processNode()` so oft sie will; so wiederholen
  Schleifen ihren Rumpf.

## Interpretiert und kompiliert

Wenn `site.ini` [TemplateSettings] `TemplateCompile` eingeschaltet ist (der Standard), wird jedes Template einmal in eine PHP-Datei umgewandelt
(`lib/eztemplate/classes/eztemplatecompiler.php`). Beide Arten können daran teilnehmen:

- Eine Funktion mit `'tree-transformation' => true` in `functionTemplateHints()` ersetzt ihren Knoten in `templateNodeTransformation()` durch
  erzeugtes PHP. `foreach`, `if`, `def` und `set` tun das. Eine Funktion ohne diesen Hinweis wird aus der kompilierten Datei über `process()`
  aufgerufen.
- Ein Operator mit `'element-transformation' => true` in `operatorTemplateHints()` erzeugt PHP für seinen Aufruf, über die in
  `element-transformation-func` genannte Methode (zum Beispiel `phpMapTransformation` für `upcase`). Andere werden zur Laufzeit über
  `modify()` aufgerufen.

So oder so muss die Ausgabe dieselbe sein wie beim interpretierten Template. Die Beispiele dieser Seiten wurden auf alpha auf beide Arten
gerendert und verglichen; die tatsächlich gefundenen Unterschiede sind auf [Sections und foreach](sections-und-foreach.md#ein-unterschied-in-kompilierten-templates)
und in der Referenz zu [foreach](referenz-der-kontrollstrukturen.md#foreach) vermerkt.

## Was Sie schreiben sollten

- Schreiben Sie einen **Operator**, um einen Wert zu berechnen, zu formatieren oder nachzuschlagen: Er lässt sich mit jedem anderen Operator
  kombinieren und in jedem Parameter nutzen. Die meisten Bedürfnisse von Erweiterungen sind Operatoren.
- Schreiben Sie eine **Funktion** nur, um die Ausgabe zu steuern: einen Block zu wiederholen oder zu überspringen, ihn aufzufangen oder zu
  cachen oder etwas mit einem eigenen Template zu rendern. Funktionen sind schwerer gut zu schreiben, weil sie Kinder, Namensräume und
  Kompilierung behandeln.
- Schreiben Sie eine **Fetch-Funktion** (in der `function_definition.php` eines Moduls), um Templates Daten aus der Datenbank zu geben:
  `fetch( 'yourmodule', 'yourfunction', hash( ... ) )`. Siehe
  [Eine eigene Fetch-Funktion schreiben](../../../eng-us/help/creators/app-creators/writing-a-fetch-function.md).

## Verwandte Seiten

- Zurück: [Operatoren in Ausdrücken](operatoren-in-ausdruecken.md)
- Weiter: [Referenz der Kontrollstrukturen](referenz-der-kontrollstrukturen.md)
- [Referenz der Funktionen](../../../eng-us/help/designers/templates/function-reference.md) und [Referenz der Operatoren](../../../eng-us/help/designers/templates/operator-reference.md)
- [Template-Operatoren schreiben](../../../eng-us/help/creators/app-creators/template-operators.md)
- [Ein eigener Template-Operator](../../../eng-us/examples/cookbook/custom-template-operator.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
