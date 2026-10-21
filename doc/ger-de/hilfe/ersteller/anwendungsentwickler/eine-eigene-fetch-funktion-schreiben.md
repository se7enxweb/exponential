---
title: Eine eigene Fetch-Funktion schreiben
audience: app-creator, developer
category: app-creation
order: 160
summary: Die Daten Ihrer Anwendung mit fetch() für Templates bereitstellen - function_definition.php, die Klasse der Funktionssammlung, die array( 'result' => ... ) zurückgibt, Parametertypen und Standardwerte, Rechteprüfungen und Testen.
keywords: Fetch-Funktion, Funktionssammlung, Parameter, Templates, Rechte, Anwendungsentwickler, fetch function, function_definition.php, FunctionList, call_method, parameter_type, function collection, result, templates, permission, app creator
type: help
---
# Eine eigene Fetch-Funktion schreiben

Diese Seite richtet sich an Anwendungsentwickler, die möchten, dass Templates ihre Daten mit `fetch( 'expdemo', 'items',
hash( ... ) )` lesen. Eine Fetch-Funktion wird in der `function_definition.php` des Moduls deklariert und von einer
statischen Methode implementiert, die `array( 'result' => ... )` zurückgibt.

## Inhalt

1. [Die Deklaration](#die-deklaration)
2. [Die Implementierung](#die-implementierung)
3. [Verwendung](#verwendung)
4. [Regeln](#regeln)

## Die Deklaration

`extension/expdemo/modules/expdemo/function_definition.php`, im selben Verzeichnis wie `module.php`:

```php
<?php
$FunctionList = array();
// fetch( 'expdemo', 'items', hash( 'limit', 5, 'offset', 0 ) )
$FunctionList['items'] = array(
    'name' => 'items',
    'operation_types' => array( 'read' ),
    'call_method' => array( 'class' => 'expDemoFunctionCollection',
                            'method' => 'fetchItems' ),
    'parameter_type' => 'standard',
    'parameters' => array(
        array( 'name' => 'limit',  'type' => 'integer', 'required' => false, 'default' => 10 ),
        array( 'name' => 'offset', 'type' => 'integer', 'required' => false, 'default' => 0 ) ) );
```

Die Dateien des Kernels haben dieselbe Form (`kernel/exphelp/function_definition.php`,
`kernel/content/function_definition.php`).

## Die Implementierung

`extension/expdemo/classes/expdemofunctioncollection.php`:

```php
<?php
class expDemoFunctionCollection
{
    public static function fetchItems( $limit = 10, $offset = 0 )
    {
        $limit = max( 1, min( 100, (int)$limit ) );
        $offset = max( 0, (int)$offset );
        $access = eZUser::currentUser()->hasAccessTo( 'expdemo', 'read' );
        if ( $access['accessWord'] === 'no' )
            return array( 'result' => array() );
        return array( 'result' => expDemoItem::fetchList( $limit, $offset ) );
    }
}
```

Geben Sie bei Erfolg `array( 'result' => $value )` zurück. Generieren Sie danach die Autoloads neu und leeren Sie die
Caches.

## Verwendung

```text
{def $items = fetch( 'expdemo', 'items', hash( 'limit', 5 ) )}
{foreach $items as $item}<li>{$item.title|wash}</li>{/foreach}
{undef $items}
```

Zurückgegebene Objekte sollten `eZPersistentObject`s sein oder `attribute()` und `hasAttribute()` anbieten, damit
Templates `$item.title` lesen können ([Persistente Objekte und eigene Tabellen](persistente-objekte-und-eigene-tabellen.md)).

## Regeln

- Prüfen Sie die Rechte innerhalb der Funktion; Templates können von überall aufgerufen werden.
- Begrenzen Sie jedes Limit.
- Halten Sie die Methode schlank: Rufen Sie eine Dienstklasse auf, die auch Ihre Ansichten und Befehle verwenden.
- Benennen Sie Parameter wie die Inhalts-Fetches (`limit`, `offset`, `sort_by`), damit Template-Autoren richtig raten.
- Testen Sie die statische Methode wie jede Klasse mit PHPUnit ([PHPUnit-Tests ohne Datenbank](phpunit-tests-ohne-datenbank.md)).

## Verwandte Seiten

- [Fetch-Funktionen: Daten in Templates lesen](fetch-funktionen.md)
- [Muster für Fetch-Funktionen](../../../../eng-us/standards/patterns/fetch-function.md)
- [Template-Operatoren schreiben](template-operatoren-schreiben.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
