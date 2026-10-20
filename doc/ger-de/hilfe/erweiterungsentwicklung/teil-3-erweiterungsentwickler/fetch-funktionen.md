---
title: Fetch-Funktionen
audience: developer, app-creator, designer
category: extension-development
order: 140
summary: Templates Ihre Daten abrufen lassen - function_definition.php, die Collection-Klasse und ihr Ergebnis-Array, ein geprüftes Beispiel, Fetch-Aliase und erweiterte Attributfilter für Inhaltsabfragen.
keywords: Fetch-Funktion, Collection-Klasse, Ergebnis-Array, Fetch-Alias, erweiterter Attributfilter, fetch function, function_definition.php, call_method, parameters, function collection, result array, fetch, fetch_alias, fetchalias.ini, extended attribute filter, extendedattributefilter.ini
type: help
---
# Fetch-Funktionen

Teil 3, Kapitel 14. `fetch( 'content', 'list', ... )` ist eine **Fetch-Funktion** des Moduls `content`. Ihr Modul kann
eigene anbieten: `fetch( 'hello', 'newest', hash( 'class_identifier', 'article' ) )`. Fetch-Funktionen sind der
richtige Weg, Templates Daten zu geben, die PHP brauchen, ohne Logik in Templates zu legen. Die Engine sind
`eZFunctionHandler` und `eZModuleFunctionInfo` (`lib/ezutils/classes/`).

## Wo sie liegt

Eine Fetch-Funktion gehört zu einem Modul, daher braucht Ihre Erweiterung einen Modulordner, der mit
`ExtensionRepositories[]` registriert ist
([Module und Views](../../../../eng-us/help/extension-development/part-3-extension-developer/modules-and-views.md)).
Das Modul darf überhaupt keine Views haben; dann existiert es nur für seine Fetch-Funktionen.

```text
extension/mysite_hello/
    modules/hello/function_definition.php
    classes/mysitehellofunctioncollection.php
```

## function_definition.php

```php
<?php
$FunctionList = array();

$FunctionList['newest'] = array(
    'name' => 'newest',
    'call_method' => array( 'class' => 'mysiteHelloFunctionCollection',
                            'method' => 'fetchNewest' ),
    'parameters' => array(
        array( 'name' => 'class_identifier', 'type' => 'string', 'required' => true ),
        array( 'name' => 'parent_node_id', 'type' => 'integer', 'required' => false, 'default' => 2 ),
        array( 'name' => 'limit', 'type' => 'integer', 'required' => false, 'default' => 5 )
    )
);
```

Die Parameter werden der Methode **in der aufgeführten Reihenfolge** übergeben, über den Namen aus dem `hash()` des
Templates zugeordnet; fehlende optionale Parameter erhalten ihren `default`. Die Datei
`modules/tags/function_definition.php` der Erweiterung eztags ist ein größeres echtes Beispiel.

## Die Collection-Klasse

```php
<?php
/**
 * Fetch functions of the module "hello":
 *   fetch( 'hello', 'newest', hash( 'class_identifier', 'article', 'limit', 3 ) )
 */
class mysiteHelloFunctionCollection
{
    public static function fetchNewest( $classIdentifier, $parentNodeID = 2, $limit = 5 )
    {
        $limit = max( 1, min( (int)$limit, 50 ) );
        $nodes = eZContentObjectTreeNode::subTreeByNodeID(
            array( 'ClassFilterType' => 'include',
                   'ClassFilterArray' => array( (string)$classIdentifier ),
                   'SortBy' => array( 'published', false ),
                   'Limit' => $limit ),
            (int)$parentNodeID
        );
        return array( 'result' => is_array( $nodes ) ? $nodes : array() );
    }
}
```

Die Methode **gibt ein Array mit dem Schlüssel `result` zurück**; die Engine reicht `result` an das Template weiter
(`eZModuleFunctionInfo::execute()`: Fehlt der Schlüssel `result`, wird "did not return a result value" gemeldet).
Geben Sie `array( 'result' => false )` für "nichts gefunden" zurück, die Konvention, die die Collection von eztags
verwendet. Bei geladenem Kernel mit Beispielinhalten aufgerufen, gab die Methode oben die drei neuesten Knoten der
übergebenen Klasse zurück.

`subTreeByNodeID()` wendet die Leserechte des aktuellen Benutzers an, wie jede Inhaltsabfrage; damit tut das auch Ihre
Funktion.

## Verwendung

```text
{def $newest = fetch( 'hello', 'newest', hash( 'class_identifier', 'article', 'limit', 3 ) )}
{foreach $newest as $node}
    {node_view_gui content_node=$node view='line'}
{/foreach}
{undef $newest}
```

## Fetch-Aliase

`fetchalias.ini` gibt einem Fetch mit festen Parametern einen kurzen Namen, der mit `fetch_alias()` verwendet wird:

```ini
[newest_articles]
Module=hello
FunctionName=newest
Constant[class_identifier]=article
Constant[limit]=3
```

```text
{def $newest = fetch_alias( 'newest_articles', hash() )}
```

Die Datei `settings/fetchalias.ini` des Kernels dokumentiert das Format und bringt Beispiele mit (`[object]`,
`[news_list]`).

## Erweiterte Attributfilter

Manchmal sind die Inhaltsabfragen (`list`, `tree`) die richtigen, brauchen aber eine zusätzliche SQL-Bedingung, zum
Beispiel einen Join mit Ihrer eigenen Tabelle. Ein **erweiterter Attributfilter** fügt ihnen Tabellen, Joins und
Spalten hinzu:

```ini
# extension/mysite_hello/settings/extendedattributefilter.ini.append.php
[MysiteRatedFilter]
ExtensionName=mysite_hello
FileName=classes/mysiteratedfilter.php
ClassName=mysiteRatedFilter
MethodName=createSqlParts
```

```php
<?php
class mysiteRatedFilter
{
    public function createSqlParts( $params )
    {
        $minimum = isset( $params['minimum'] ) ? (int)$params['minimum'] : 0;
        return array(
            'tables' => ', mysite_rating r',
            'joins'  => " r.contentobject_id = ezcontentobject.id AND r.score >= $minimum AND ",
            'columns' => ''
        );
    }
}
```

```text
{fetch( 'content', 'list', hash( 'parent_node_id', 2,
                                  'extended_attribute_filter', hash( 'id', 'MysiteRatedFilter',
                                                                     'params', hash( 'minimum', 4 ) ) ) )}
```

Der Kernel ruft `createSqlParts( $params )` auf und fügt `tables`, `joins` und `columns` in seine Abfrage ein
(`createExtendedAttributeFilterSQLStrings()` in `kernel/classes/ezcontentobjecttreenode.php`). Alles, was Sie in das
SQL schreiben, muss sicher sein: Wandeln Sie Zahlen um, maskieren Sie Strings mit `eZDB::instance()->escapeString()`.

## RAD

**Template extension** (`/setup/templateoperator`) schreibt Fetch-Funktionen mit ihrer Definition und
Collection-Klasse; siehe
[Mit RAD: ein Template-Operator in fünf Minuten](../../../../eng-us/help/extension-development/part-3-extension-developer/tutorials/rad-template-operator.md).

## Verwandte Seiten

- Zurück: [Template-Operatoren](../../../../eng-us/help/extension-development/part-3-extension-developer/template-operators.md)
- Weiter: [Template-Funktionen](../../../../eng-us/help/extension-development/part-3-extension-developer/template-functions.md)
- [Von Hand: eine Fetch-Funktion](../../../../eng-us/help/extension-development/part-3-extension-developer/tutorials/by-hand-fetch-function.md)
- [Inhalte in Templates abrufen](../teil-2-builder/fetch-fuer-builder.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
