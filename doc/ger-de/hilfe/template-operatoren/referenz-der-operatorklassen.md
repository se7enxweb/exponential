---
title: Die Schnittstelle einer Operatorklasse
audience: developer, app-creator
category: development
order: 530
summary: Jede Methode und jedes Argument, das die Template-Engine an einem Template-Operator-Objekt verwendet - operatorList, namedParameterPerOperator, namedParameterList, modify mit seinen acht Argumenten, operatorTemplateHints und Element-Transformationen, operatorCompiledStaticData - sowie die Schlüssel des Registrierungseintrags, jeweils mit einer Beispielklasse, die auf alpha interpretiert und kompiliert gerendert wurde.
keywords: Operatorklasse, Schnittstelle, Template-Operator, Registrierungseintrag, Element-Transformation, Template-Compiler, operator class, operatorList, namedParameterPerOperator, namedParameterList, modify, operatorTemplateHints, element-transformation, input-as-parameter, transform-parameters, elementValue, placement, eztemplateautoload.php, eZTemplateOperatorArray
type: help
---
# Die Schnittstelle einer Operatorklasse

Ein Template-Operator ist ein beliebiges PHP-Objekt mit den unten beschriebenen Methoden; es gibt keine Basisklasse und keine
Schnittstelle, die implementiert werden müsste. Alles auf dieser Seite stammt aus `lib/eztemplate/classes/eztemplate.php`
(`processOperator()`, `operatorParameterList()`, `loadAndRegisterOperators()`) und `lib/eztemplate/classes/eztemplatecompiler.php`.
Eine geführte Einführung in den ersten eigenen Operator finden Sie unter
[Einen Operator Schritt für Schritt anlegen](einen-operator-schritt-fuer-schritt-anlegen.md).

## Inhalt

1. [Die Beispielklasse](#die-beispielklasse)
2. [Der Registrierungseintrag](#der-registrierungseintrag)
3. [operatorList](#operatorlist)
4. [namedParameterPerOperator und namedParameterList](#namedparameterperoperator-und-namedparameterlist)
5. [modify](#modify)
6. [operatorTemplateHints](#operatortemplatehints)
7. [Element-Transformationen](#element-transformationen)
8. [operatorCompiledStaticData](#operatorcompiledstaticdata)

## Die Beispielklasse

Die Beispiele verwenden `C11ExampleOperators`, eine Klasse, die für diese Seiten geschrieben wurde. Sie lief auf alpha nicht als
installierte Erweiterung: Das Skript, das die Beispiele gerendert hat, registrierte die Klasse im Speicher bei der Template-Engine
(mit demselben Eintrag, den die `eztemplateautoload.php` einer Erweiterung hinzufügen würde) und lud sie aus einem Arbeitsordner;
kompilierte Ergebnisse wurden in einen Arbeitsordner kompiliert. Ihre Operatoren:

| Operator | Was er zeigt |
|---|---|
| `exp_initials` | einen Operator, der mit seiner Eingabe über die Pipe arbeitet |
| `exp_reading_time( words_per_minute )` | einen optionalen Parameter mit Standardwert |
| `exp_clamp( min, max )` | erforderliche Parameter |
| `exp_parameters_seen( first, second )` | was `modify()` erhält |
| `exp_wrong_hint` | was ein falscher Hinweis in kompilierten Templates anrichtet |
| `exp_twice` | eine Element-Transformation (PHP direkt im kompilierten Template) |

## Der Registrierungseintrag

Ein Eintrag von `$eZTemplateOperatorArray` in einer `eztemplateautoload.php` ([Operatoren registrieren](operatoren-registrieren.md)):

| Schlüssel | Bedeutung |
|---|---|
| `class` | die zu erzeugende Klasse (`new $class()` oder `new $class( $class_parameter )`) |
| `script` | Datei, die per `include_once` geladen wird, wenn die Klasse noch nicht existiert; optional, wenn die Klasse in den Autoload-Arrays steht |
| `class_parameter` | optional, wird an den Konstruktor übergeben |
| `function` | statt `class`: eine PHP-Funktion, die das Operator-Objekt zurückgibt (im Kernel `eZPHPOperatorInit`) |
| `operator_names` | die Namen, die dieser Eintrag registriert |
| `operator_names_function` | statt `operator_names`: eine PHP-Funktion, die sie zurückgibt (im Kernel liest `eZPHPOperatorNameInit` die `template.ini`) |

Die Engine speichert den Eintrag unter jedem Namen und erzeugt das Objekt, sobald ein Template zum ersten Mal einen davon verwendet.

## operatorList

```php
function operatorList()
{
    return array( 'exp_initials', 'exp_reading_time', 'exp_clamp', 'exp_parameters_seen', 'exp_wrong_hint', 'exp_twice' );
}
```

Erforderlich. Wenn das Objekt erzeugt wird, registriert die Engine es unter jedem Namen, den diese Methode zurückgibt; dabei kann
es anderen Klassen Namen wieder abnehmen ([A loaded class takes its names back](registrierungsreihenfolge-und-ueberschreibungen.md#eine-geladene-klasse-holt-sich-ihre-namen-zurück)).
Geben Sie genau die Namen Ihres Registrierungseintrags zurück.

## namedParameterPerOperator und namedParameterList

```php
function namedParameterPerOperator()
{
    return true;
}

function namedParameterList()
{
    return array(
        'exp_initials' => array(),
        'exp_reading_time' => array(
            'words_per_minute' => array( 'type' => 'integer', 'required' => false, 'default' => 200 ) ),
        'exp_clamp' => array(
            'min' => array( 'type' => 'numeric', 'required' => true, 'default' => false ),
            'max' => array( 'type' => 'numeric', 'required' => true, 'default' => false ) ),
        // ...
    );
}
```

- Gibt `namedParameterPerOperator()` den Wert `true` zurück, ist `namedParameterList()` nach Operatornamen geschlüsselt. Fehlt die
  Methode (oder gibt sie `false` zurück), gilt die Liste für jeden Operator der Klasse.
- Das Template übergibt Parameter **nach Position**; die Namen beschriften sie nur in `$namedParameters`.
- `type` dient der Dokumentation: Die Engine prüft oder konvertiert den Wert nicht. Prüfen Sie Werte in `modify()`.
- `required`: Lässt das Template den Parameter weg, schreibt die Engine die Warnung
  `Parameter '<name>' (<position>) missing` und ruft `modify()` trotzdem auf, mit dem `default`.
- `default`: der Wert, der verwendet wird, wenn der Parameter fehlt. Fehlt er in der Definition, übergibt die Engine `null` und
  warnt `Operator '<operator>' has no default value for parameter '<name>'` (in interpretierten Templates einmal pro Anfrage,
  in kompilierten beim Kompilieren).

Optionale und erforderliche Parameter auf alpha:

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 89 ) )
     $text = 'one two three four five six seven eight nine ten eleven twelve'}
{$node.name|wash} -> {$node.name|exp_initials}
{$text|exp_reading_time} minute(s) at 200 words a minute, {$text|exp_reading_time( 5 )} at 5
{exp_initials( 'Bold Agency' )}
```

Ausgabe auf alpha:

```text
Fit &amp; Healthy -> FH
1 minute(s) at 200 words a minute, 3 at 5
```

```tpl
{150|exp_clamp( 0, 100 )} {-5|exp_clamp( 0, 100 )} {42|exp_clamp( 0, 100 )}
{42|exp_clamp( 0 )}
```

Ausgabe auf alpha:

```text
100 0 42
42
```

Von der Template-Engine protokolliert:

```text
WARNING eZTemplateOperatorElement: Parameter 'max' (1) missing
WARNING exp_clamp: min and max must be numbers
```

Die zweite Zeile übergibt nur `min`: Die Engine warnt wegen `max`, übergibt dessen Standardwert `false`, und die eigene Prüfung des
Operators lehnt den Aufruf ab und lässt die Eingabe unverändert (`42`).

## modify

```php
function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace,
                 &$operatorValue, $namedParameters, $placement = false )
```

| Argument | Was es ist |
|---|---|
| `$tpl` | das `eZTemplate`-Objekt: für `warning()`, `error()`, `elementValue()`, `variable()` |
| `$operatorName` | der im Template verwendete Name, für Klassen mit mehreren Operatoren |
| `$operatorParameters` | die **nicht ausgewerteten** Parameterelemente, eines pro im Template geschriebenem Parameter |
| `$rootNamespace`, `$currentNamespace` | die Namensräume, in denen Elemente und Variablen ausgewertet werden |
| `&$operatorValue` | hinein: die Eingabe über die Pipe, in der Aufrufform `null`; heraus: das Ergebnis. Zuweisen, nicht zurückgeben |
| `$namedParameters` | die ausgewerteten Parameter nach Namen, mit eingesetzten Standardwerten |
| `$placement` | die Stelle des Aufrufs im Template, für Warnungen |

Was `exp_parameters_seen` über seine Argumente meldet, einmal als Funktion aufgerufen und einmal über die Pipe:

```tpl
{def $b = 'bee'}
{exp_parameters_seen( 'a', $b )}
{'piped'|exp_parameters_seen}
```

Ausgabe auf alpha:

```text
value=NULL named={"first":"a","second":"bee"} raw count=2 first raw element type=1 second evaluated again='bee' placement=[[2,0,18],[2,30,48],"var\/tmp\/examples\/ld-op\/03-what-modify-receives.tpl"]
value='piped' named={"first":"first-default","second":"second-default"} raw count=0 first raw element type=none second evaluated again=NULL placement=[[3,0,51],[3,27,78],"var\/tmp\/examples\/ld-op\/03-what-modify-receives.tpl"]
```

Ausgabe auf alpha mit dem Template-Compiler (in einen Arbeitsordner kompiliert):

```text
value=NULL named={"first":"a","second":"bee"} raw count=2 first raw element type=1 second evaluated again='bee' placement=false
value='piped' named={"first":"first-default","second":"second-default"} raw count=0 first raw element type=none second evaluated again=NULL placement=false
```

Was die Ausgabe zeigt:

- in der Aufrufform ist `$operatorValue` `NULL`; über die Pipe ist es die Eingabe;
- `$namedParameters` enthält die ausgewerteten Werte mit eingesetzten Standardwerten;
- `$operatorParameters` enthält Element-Arrays (Typ `1` ist `eZTemplate::TYPE_STRING`), und
  `$tpl->elementValue( $operatorParameters[1], $rootNamespace, $currentNamespace, $placement )` wertet eines erneut aus;
  verwenden Sie das nur für Operatoren, die einen Parameter verzögert oder mehrfach auswerten müssen;
- `$placement` ist in einem interpretierten Template `[[Startzeile, Spalte, Position], [Endzeile, Spalte, Position], Template-Datei]`
  und in einem kompilierten `false`. Übergeben Sie es trotzdem an `$tpl->warning( $operatorName, $text, $placement )`:
  Warnungen kompilierter Templates haben dann zwar keine Zeilennummer, werden aber trotzdem geschrieben.

Regeln für `modify()`: Weisen Sie das Ergebnis `$operatorValue` zu; geben Sie nie etwas mit echo aus; geben Sie Text unmaskiert
zurück und lassen Sie das Template ihn mit `|wash` maskieren; schreiben Sie bei ungültiger Eingabe eine Warnung und lassen Sie
`$operatorValue` unverändert oder setzen Sie einen neutralen Wert; ändern Sie aus einem Operator heraus weder Inhalte noch
Einstellungen (die Ausgabe wird gecacht, und Operatoren laufen, während Seiten kompiliert und gerendert werden).

## operatorTemplateHints

Optional. Teilt dem Template-Compiler mit, was ein Operator tut, geschlüsselt nach Operatornamen:

```php
function operatorTemplateHints()
{
    return array(
        // correct: takes the piped value, returns a value, no parameters
        'exp_initials' => array( 'input' => true, 'output' => true, 'parameters' => false ),
        // wrong on purpose: the operator uses its piped value but says it takes none
        'exp_wrong_hint' => array( 'input' => false, 'output' => true, 'parameters' => false ),
        // ...
    );
}
```

| Schlüssel | Bedeutung für den Compiler |
|---|---|
| `input` | der Operator verwendet seinen Wert aus der Pipe. Mit `input` `false` und `output` `true` **verwirft der Compiler den Wert aus der Pipe** |
| `output` | der Operator erzeugt einen Wert. `false` kennzeichnet das Ergebnis als null |
| `parameters` | der Operator nimmt Parameter. `false`: Der Compiler verwirft alle im Template geschriebenen |
| `output-type` | der Typ des Ergebnisses, für spätere Optimierungen |
| `element-transformation` | der Operator kann sein eigenes PHP in das kompilierte Template schreiben |
| `element-transformation-func` | die Methode, die das tut (Standard `templateElementTransformation`) |
| `transform-parameters` | die Parameter zuerst kompilieren und an diese Methode übergeben |
| `input-as-parameter` | den Wert aus der Pipe als ersten Parameter an die Methode übergeben (`true`: wenn es einen gibt; `'always'`) |
| `static` | das Ergebnis ist konstant: Der Compiler ruft `operatorCompiledStaticData()` einmal auf |

Der Interpreter ignoriert Hinweise; der Compiler vertraut ihnen. Ein falscher Hinweis ist daher ein Fehler, der sich nur in
kompilierten Templates zeigt, was bei einer Standardinstallation bedeutet: bei jedem Besucher.

```tpl
{'fit & healthy'|exp_initials} {'fit & healthy'|exp_wrong_hint}
```

Ausgabe auf alpha:

```text
FH FIT & HEALTHY
```

Ausgabe auf alpha mit dem Template-Compiler (in einen Arbeitsordner kompiliert):

```text
FH
```

Ein Operator ohne `operatorTemplateHints()` wird in einen Laufzeitaufruf von `$tpl->processOperator()` kompiliert, der `modify()`
genauso aufruft wie der Interpreter. Das ist immer korrekt, nur langsamer; lassen Sie Hinweise weg, bis Sie die Geschwindigkeit
brauchen.

## Element-Transformationen

Ein Operator, dessen Arbeit einfaches PHP ist, kann dieses PHP in das kompilierte Template schreiben, sodass zur Laufzeit kein
Operator-Objekt aufgerufen wird. `exp_twice` multipliziert seine Eingabe mit zwei:

```php
// hints
'exp_twice' => array( 'input' => true, 'output' => true, 'parameters' => false,
                      'element-transformation' => true, 'transform-parameters' => true,
                      'input-as-parameter' => 'always',
                      'element-transformation-func' => 'twiceTransformation' ),

// the method the compiler calls
function twiceTransformation( $operatorName, &$node, $tpl, &$resourceData,
                              $element, $lastElement, $elementList, $elementTree, &$parameters )
{
    $values = array( $parameters[0] );
    return array( eZTemplateNodeTool::createCodePieceElement( "%output% = %1% * 2; // exp_twice, compiled\n", $values ) );
}
```

`%1%` wird durch den Code des ersten Werts in `$values` ersetzt (hier die Eingabe über die Pipe), `%output%` durch die Variable,
die das Ergebnis erhält. Geben Sie `false` zurück (kein Array), wenn sich der Aufruf nicht transformieren lässt, zum Beispiel wenn
ein Parameter nicht konstant ist (`eZTemplateExecuteOperator::fetchTransform()` macht das so); der Compiler behält dann einen
Laufzeitaufruf bei. `modify()` muss den Operator für interpretierte Templates trotzdem implementieren:

```tpl
{def $n = 21}
{$n|exp_twice} {4|exp_twice|exp_twice}
```

Ausgabe auf alpha:

```text
42 16
```

Ausgabe auf alpha mit dem Template-Compiler (in einen Arbeitsordner kompiliert):

```text
42 16
```

Ein Auszug aus dem PHP, das der Compiler auf alpha für die zweite Zeile (`{4|exp_twice|exp_twice}`) erzeugt hat: Die Konstante
wurde in die erste Multiplikation eingefaltet, und es wird kein Operator-Objekt aufgerufen:

```php
$var1 = 4 * 2; // exp_twice, compiled
if (! isset( $var1 ) ) $var1 = NULL;
while ( is_object( $var1 ) and method_exists( $var1, 'templateValue' ) )
    $var1 = $var1->templateValue();
$var = $var1 * 2; // exp_twice, compiled
```

Interpretierte und kompilierte Ergebnisse müssen immer gleich sein; testen Sie beide. Die Operatoren der Bibliothek sind die beste
Referenz: `eZTemplateStringOperator` (`phpMap` und `customMap` machen PHP-Funktionen zu Code) und
`eZTemplateArithmeticOperator`.

## operatorCompiledStaticData

Mit `'static' => true` in den Hinweisen ruft der Compiler `operatorCompiledStaticData( $operatorName )` einmal auf und schreibt den
zurückgegebenen Wert als Konstante in das kompilierte Template (die Template-Bibliothek nutzt das für `true()` und `false()` in
`eZTemplateLogicOperator`). Verwenden Sie es nur für Werte, die sich zwischen dem Kompilieren und dem nächsten Leeren des Caches nie
ändern, niemals für Benutzer-, Zeit- oder Inhaltsdaten.

## Verwandte Seiten

- [Einen Operator Schritt für Schritt anlegen](einen-operator-schritt-fuer-schritt-anlegen.md)
- [Operatoren registrieren](operatoren-registrieren.md)
- [Kernel-Operatoren überschreiben](kernel-operatoren-ueberschreiben.md)
- [Der Template-Compiler](../template-sprache/der-template-compiler.md)
- [Template-Operatoren schreiben](../ersteller/anwendungsentwickler/template-operatoren-schreiben.md) (Einführung für Anwendungsentwickler)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
