---
title: Typ-Operatoren
audience: developer, designer, app-creator
category: development
order: 140
summary: Die 13 Operatoren von eZTemplateTypeOperator (is_array, is_boolean, is_integer, is_float, is_numeric, is_string, is_object, is_null, is_class, is_set, is_unset, get_type, get_class) mit ihren Regeln für die Eingabe und Beispielen, die auf alpha ausgegeben wurden.
keywords: Typ, Typprüfung, Datentyp, Klasse, is_array, is_boolean, is_integer, is_float, is_numeric, is_string, is_object, is_null, is_class, is_set, is_unset, get_type, get_class, eZTemplateTypeOperator, type operators
type: help
---
# Typ-Operatoren

Diese Operatoren prüfen, welche Art von Wert eine Variable enthält, und geben das an. Registriert werden sie von
`eZTemplateTypeOperator` (`lib/eztemplate/classes/eztemplatetypeoperator.php`) über
`lib/eztemplate/classes/eztemplateautoload.php`. Keiner hat benannte Parameter.

## Inhalt

1. [Die PHP-Typprüfungen](#die-php-typprüfungen)
2. [is_class](#is_class)
3. [is_set und is_unset](#is_set-und-is_unset)
4. [get_type und get_class](#get_type-und-get_class)
5. [Verwandte Seiten](#verwandte-seiten)

## Die PHP-Typprüfungen

Acht Operatoren entsprechen eins zu eins einer PHP-Funktion (`$PHPNameMap` in der Klasse):

| Operator | PHP-Funktion | Generierte Referenz |
|---|---|---|
| `is_array` | `is_array()` | [is_array](../../../eng-us/examples/template-operators/is_array.md) |
| `is_boolean` | `is_bool()` | [is_boolean](../../../eng-us/examples/template-operators/is_boolean.md) |
| `is_integer` | `is_integer()` | [is_integer](../../../eng-us/examples/template-operators/is_integer.md) |
| `is_float` | `is_float()` | [is_float](../../../eng-us/examples/template-operators/is_float.md) |
| `is_numeric` | `is_numeric()` | [is_numeric](../../../eng-us/examples/template-operators/is_numeric.md) |
| `is_string` | `is_string()` | [is_string](../../../eng-us/examples/template-operators/is_string.md) |
| `is_object` | `is_object()` | [is_object](../../../eng-us/examples/template-operators/is_object.md) |
| `is_null` | `is_null()` | [is_null](../../../eng-us/examples/template-operators/is_null.md) |

### is_array, is_boolean, is_integer, is_float, is_numeric, is_string, is_object, is_null

Ohne Parameter prüfen sie die Eingabe über die Pipe. Im Interpreter prüfen sie mit Parametern jeden Parameter und
geben nur dann true zurück, wenn alle bestehen (`checkType()`); die Eingabe wird dann ignoriert. Sie geben einen
booleschen Wert zurück.

Die kompilierte Form (`isTransform()`) prüft nur den ersten Wert. Kompiliert war auf alpha `is_integer( 1, 2, 'three' )`
true, wo der Interpreter false sagt. Prüfen Sie einen Wert je Aufruf: `and( $a|is_integer, $b|is_integer )`.


| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| zu prüfende Werte | beliebig | nein | die Eingabe |

Eine Zeichenkette, die Ziffern enthält, ist eine Zeichenkette: `'42'|is_integer` ist false, `'42'|is_numeric` ist
true. Aus Inhalten gelesene Attributwerte sind aus diesem Grund oft Zeichenketten.

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 89 ) )}
array( 1 )|is_array = {array( 1 )|is_array|choose( 'false', 'true' )}
true()|is_boolean = {true()|is_boolean|choose( 'false', 'true' )}
42|is_integer = {42|is_integer|choose( 'false', 'true' )}
'42'|is_integer = {'42'|is_integer|choose( 'false', 'true' )}
'42'|is_numeric = {'42'|is_numeric|choose( 'false', 'true' )}
1.5|is_float = {1.5|is_float|choose( 'false', 'true' )}
'text'|is_string = {'text'|is_string|choose( 'false', 'true' )}
$node|is_object = {$node|is_object|choose( 'false', 'true' )}
$node.parent_node_id|is_null = {$node.parent_node_id|is_null|choose( 'false', 'true' )}
is_integer( 1, 2, 'three' ) = {is_integer( 1, 2, 'three' )|choose( 'false', 'true' )}
```

Ausgabe auf alpha:

```text
array( 1 )|is_array = true
true()|is_boolean = true
42|is_integer = true
'42'|is_integer = false
'42'|is_numeric = true
1.5|is_float = true
'text'|is_string = true
$node|is_object = true
$node.parent_node_id|is_null = false
is_integer( 1, 2, 'three' ) = false
```

Dasselbe Template, auf alpha kompiliert; die letzte Zeile unterscheidet sich:

Ausgabe auf alpha mit dem Template-Compiler (in einen Arbeitsordner kompiliert):

```text
array( 1 )|is_array = true
true()|is_boolean = true
42|is_integer = true
'42'|is_integer = false
'42'|is_numeric = true
1.5|is_float = true
'text'|is_string = true
$node|is_object = true
$node.parent_node_id|is_null = false
is_integer( 1, 2, 'three' ) = true
```

## is_class

### is_class

Ob ein Wert ein Objekt einer bestimmten Klasse ist. Der Klassenname wird ohne Beachtung der Groß- und Kleinschreibung
verglichen. Mit einem Parameter (dem Klassennamen) prüft er die Eingabe über die Pipe; mit zwei prüft er den zweiten
Parameter. Ein Wert, der kein Objekt ist, gehört zu keiner Klasse, daher lautet die Antwort false (der Code prüft
`is_object()` vor `get_class()`, das unter PHP 8 einen `TypeError` auslösen würde).

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| Klassenname | Zeichenkette | ja | keiner |
| Wert | beliebig | nein | die Eingabe |

Gibt einen booleschen Wert zurück. Generierte Referenz: [is_class](../../../eng-us/examples/template-operators/is_class.md).

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 89 ) )}
$node|is_class( 'eZContentObjectTreeNode' ) = {$node|is_class( 'eZContentObjectTreeNode' )|choose( 'false', 'true' )}
is_class( 'ezcontentobject', $node.object ) = {is_class( 'ezcontentobject', $node.object )|choose( 'false', 'true' )}
'text'|is_class( 'eZContentObject' ) = {'text'|is_class( 'eZContentObject' )|choose( 'false', 'true' )}
```

Ausgabe auf alpha:

```text
$node|is_class( 'eZContentObjectTreeNode' ) = true
is_class( 'ezcontentobject', $node.object ) = true
'text'|is_class( 'eZContentObject' ) = false
```

Verwenden Sie die Form mit der Pipe. Der kompilierte Code behandelt den ersten Wert als Objekt und den zweiten als
Klassennamen, daher war kompiliert auf alpha die Aufrufform `is_class( 'ezcontentobject', $node.object )` false.

Ausgabe auf alpha mit dem Template-Compiler (in einen Arbeitsordner kompiliert):

```text
$node|is_class( 'eZContentObjectTreeNode' ) = true
is_class( 'ezcontentobject', $node.object ) = false
'text'|is_class( 'eZContentObject' ) = false
```

## is_set und is_unset

### is_set

Ob der Parameter gesetzt ist: nicht `null` und vorhanden. Der Parameter wird mit der Existenzprüfung von
`elementValue()` gelesen, daher ergibt eine nicht definierte Variable oder ein fehlender Hash-Schlüssel false, ohne
den Fehler „Unknown template variable“. Die Eingabe über die Pipe wird nicht verwendet; ohne Parameter meldet er einen
fehlenden Parameter. Ein zweiter Parameter protokolliert eine Warnung „extra parameters“.

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| Wert | beliebig | ja | keiner |

Gibt einen booleschen Wert zurück. Generierte Referenz: [is_set](../../../eng-us/examples/template-operators/is_set.md).

### is_unset

Das Gegenteil von `is_set`, mit denselben Regeln.

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| Wert | beliebig | ja | keiner |

Gibt einen booleschen Wert zurück. Generierte Referenz: [is_unset](../../../eng-us/examples/template-operators/is_unset.md).

```tpl
{def $settings = hash( 'title', 'Hello' )}
is_set( $settings.title ) = {is_set( $settings.title )|choose( 'false', 'true' )}
is_set( $settings.subtitle ) = {is_set( $settings.subtitle )|choose( 'false', 'true' )}
is_set( $not_defined ) = {is_set( $not_defined )|choose( 'false', 'true' )}
is_unset( $not_defined ) = {is_unset( $not_defined )|choose( 'false', 'true' )}
```

Ausgabe auf alpha:

```text
is_set( $settings.title ) = true
is_set( $settings.subtitle ) = false
is_set( $not_defined ) = false
is_unset( $not_defined ) = true
```

## get_type und get_class

### get_type

Beschreibt den ersten Parameter oder die Eingabe über die Pipe: `null`, `boolean[true]` oder `boolean[false]`,
`object[<class>]`, `array[<count>]`, `string[<length in bytes>]` und sonst PHPs `gettype()` (`integer`, `double`).
Nützlich bei der Fehlersuche in einem Template.

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| Wert | beliebig | nein | die Eingabe |

Gibt eine Zeichenkette zurück. Generierte Referenz: [get_type](../../../eng-us/examples/template-operators/get_type.md).

### get_class

Der Klassenname eines Objekts, **in Kleinbuchstaben**; eine leere Zeichenkette für einen Wert, der kein Objekt ist.

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| Wert | beliebig | nein | die Eingabe |

Gibt eine Zeichenkette zurück. Generierte Referenz: [get_class](../../../eng-us/examples/template-operators/get_class.md).

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 89 ) )
     $missing = fetch( 'content', 'node', hash( 'node_id', 999999999 ) )}
{foreach array( 42, 1.5, 'Fit', true(), array( 1, 2 ), $node, $node.data_map.title, $missing ) as $v}{$v|get_type} {/foreach}

$node|get_class = {$node|get_class}
get_class( $node.object ) = {get_class( $node.object )}
'text'|get_class = [{'text'|get_class}]
```

Ausgabe auf alpha:

```text
integer double string[3] boolean[true] array[2] object[eZContentObjectTreeNode] object[eZContentObjectAttribute] null 
$node|get_class = ezcontentobjecttreenode
get_class( $node.object ) = ezcontentobject
'text'|get_class = []
```

## Verwandte Seiten

- [Logische Operatoren](logische-operatoren.md): `null` und `not`
- [Steueroperatoren](steueroperatoren.md): `first_set` für Ersatzwerte
- [Attribute- und Dump-Operatoren](attribute-und-dump-operatoren.md): in Objekte hineinsehen
- [Fehlersuche in Templates](../../../eng-us/help/designers/templates/template-debugging.md)
- [Alle Template-Operatoren (generiert)](../../../eng-us/examples/template-operators/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
