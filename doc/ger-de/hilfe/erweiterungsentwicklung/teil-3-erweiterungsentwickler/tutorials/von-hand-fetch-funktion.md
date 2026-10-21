---
title: "Von Hand: eine Fetch-Funktion"
audience: developer, app-creator, designer
category: extension-development
order: 120
summary: Templates fetch( 'hello', 'newest', ... ) geben - einen Modulordner registrieren, function_definition.php und die Sammlungsklasse schreiben und die Funktion aus einem Template aufrufen, geprüft.
keywords: Tutorial, von Hand, Fetch-Funktion, Funktionssammlung, Modulordner, Ergebnis-Array, tutorial, by hand, fetch function, function_definition.php, call_method, function collection, ExtensionRepositories, result array, fetch
type: help
---
# Von Hand: eine Fetch-Funktion

Tutorial 11. Sie geben Templates `fetch( 'hello', 'newest', hash( 'class_identifier', 'article' ) )`, die neuesten
Knoten einer Klasse. Eine Fetch-Funktion gehört zu einem Modul, daher registriert dieses Tutorial auch den Modulordner,
den das nächste Tutorial mit Views füllt. Die Dateien unten haben `php -l` bestanden, und der Template-Aufruf am Ende
wurde von der Template-Engine gerendert, nachdem der Modulordner dem Modulpfad hinzugefügt worden war.

## 1. Den Modulordner registrieren

`extension/mysite_hello/settings/module.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[ModuleSettings]
ExtensionRepositories[]=mysite_hello

*/ ?>
```

## 2. Die Definition

`extension/mysite_hello/modules/hello/function_definition.php`:

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

## 3. Die Sammlungsklasse

`extension/mysite_hello/classes/mysitehellofunctioncollection.php`:

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

Das Limit wird begrenzt und die IDs werden umgewandelt, weil ein Template Werte aus der Adresse übergeben kann. Das
Ergebnis wird in `array( 'result' => ... )` verpackt, das der Kernel wieder auspackt.

## 4. Prüfen und einschalten

```bash
php -l extension/mysite_hello/modules/hello/function_definition.php
php -l extension/mysite_hello/classes/mysitehellofunctioncollection.php
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all
```

## 5. Verwenden

```text
{def $newest = fetch( 'hello', 'newest', hash( 'class_identifier', 'article', 'limit', 2 ) )}
{foreach $newest as $node}[{$node.name|wash}]{/foreach}
{fetch( 'hello', 'newest', hash( 'class_identifier', 'nothing_like_this' ) )|count}
{undef $newest}
```

Auf der Installation, die für dieses Buch verwendet wurde (mit ihrer eigenen Artikelklasse), gab die erste Zeile die
Namen der beiden neuesten Artikel aus und die zweite `0`.

## 6. Wenn es nicht funktioniert

| Meldung | Abhilfe |
|---|---|
| `Missing function definition file for module: hello` | `ExtensionRepositories[]` fehlt oder die Erweiterung ist inaktiv; Caches |
| `Function 'newest' in module 'hello' did not return a result value` | die Methode muss `array( 'result' => ... )` zurückgeben |
| `Class 'mysiteHelloFunctionCollection' not found` | Autoloads neu erzeugen |

## 7. Committen

```bash
git -C extension/mysite_hello add modules classes settings
git -C extension/mysite_hello commit -m "Added: The fetch function hello/newest"
```

## Verwandte Seiten

- Zurück: [Von Hand: ein Template-Operator](von-hand-template-operator.md)
- Weiter: [Von Hand: ein Modul mit einer View](von-hand-modul-mit-view.md)
- [Fetch-Funktionen](../fetch-funktionen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
