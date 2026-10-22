---
title: Eine Fetch-Funktion schreiben
audience: php-developer, developer
category: php-development
order: 260
summary: Eine Anleitung, um Templates über fetch( 'module', 'function', hash( ... ) ) Daten zu geben - der Eintrag in function_definition.php, die statische Methode und ihr Ergebnis-Array, Parameter, Fehler, Berechtigungen und Grenzen, und wie eZModuleFunctionInfo Ihren Code aufruft.
keywords: Fetch-Funktion, Parameter, Ergebnis, Fehler, Berechtigungen, Grenzen, Template-Fetch, fetch function, function_definition.php, FunctionList, call_method, parameter_type, parameters, result, error, eZModuleFunctionInfo, function collection, template fetch, permissions, limits
type: help
---
# Eine Fetch-Funktion schreiben

Diese Anleitung richtet sich an PHP-Entwickler, die möchten, dass Templates ihre Daten mit
`fetch( 'acmereport', 'items', hash( 'limit', 5 ) )` lesen. Eine Fetch-Funktion ist eine Deklaration in der
`function_definition.php` des Moduls plus eine statische PHP-Methode. Die Template-Seite ist unter
[Fetch-Funktionen: Daten in Templates lesen](../ersteller/anwendungsentwickler/fetch-funktionen.md) beschrieben.

## Inhalt

1. [Schritt 1: die Deklaration](#schritt-1-die-deklaration)
2. [Schritt 2: die Methode](#schritt-2-die-methode)
3. [Schritt 3: verwenden](#schritt-3-verwenden)
4. [Wie der Kernel Sie aufruft](#wie-der-kernel-sie-aufruft)
5. [Das Ergebnis-Array](#das-ergebnis-array)
6. [Regeln](#regeln)

## Schritt 1: die Deklaration

`extension/acmereport/modules/acmereport/function_definition.php`, neben `module.php`:

```php
<?php
$FunctionList = array();
$FunctionList['items'] = array(
    'name' => 'items',
    'operation_types' => array( 'read' ),
    'call_method' => array( 'class' => 'acmeReportFunctionCollection',
                            'method' => 'fetchItems' ),
    'parameter_type' => 'standard',
    'parameters' => array(
        array( 'name' => 'status', 'type' => 'string',  'required' => false, 'default' => false ),
        array( 'name' => 'limit',  'type' => 'integer', 'required' => false, 'default' => 10 ),
        array( 'name' => 'offset', 'type' => 'integer', 'required' => false, 'default' => 0 ) ) );
```

Das Modul muss registriert sein (`[ModuleSettings] ExtensionRepositories[]` und `ModuleList[]`); die eigenen
Deklarationen des Kernels sehen genauso aus (`kernel/content/function_definition.php`).

## Schritt 2: die Methode

`extension/acmereport/classes/acmereportfunctioncollection.php`:

```php
<?php
class acmeReportFunctionCollection
{
    /**
     * @param string|false $status
     * @param int $limit
     * @param int $offset
     * @return array array( 'result' => acmeReportItem[] )
     */
    public static function fetchItems( $status = false, $limit = 10, $offset = 0 )
    {
        $access = eZUser::currentUser()->hasAccessTo( 'acmereport', 'read' );
        if ( $access['accessWord'] === 'no' )
            return array( 'result' => array() );

        $conds = null;
        if ( $status !== false && in_array( $status, array( 'open', 'closed' ), true ) )
            $conds = array( 'status' => $status );

        $items = eZPersistentObject::fetchObjectList( acmeReportItem::definition(), null, $conds,
                                                      array( 'created' => 'desc' ),
                                                      array( 'offset' => max( 0, (int)$offset ),
                                                             'length' => max( 1, min( 100, (int)$limit ) ) ) );
        return array( 'result' => is_array( $items ) ? $items : array() );
    }
}
```

Danach `php bin/php/ezpgenerateautoloads.php -e` und die Caches leeren.

## Schritt 3: verwenden

```text
{def $items = fetch( 'acmereport', 'items', hash( 'status', 'open', 'limit', 5 ) )}
{foreach $items as $item}
    <li>{$item.status|wash} {$item.amount|wash}</li>
{/foreach}
{undef $items}
```

## Wie der Kernel Sie aufruft

`eZModuleFunctionInfo` (`lib/ezutils/classes/ezmodulefunctioninfo.php`) liest die Deklaration, ordnet die benannten
Parameter des `hash()` den Parametern der Methode **in der Reihenfolge von `parameters`** zu, füllt Standardwerte auf
und ruft `call_method` auf. Halten Sie die Parameterliste der Methode in derselben Reihenfolge wie die Deklaration.

## Das Ergebnis-Array

| Sie geben zurück | Das Template erhält |
|---|---|
| `array( 'result' => $value )` | `$value` |
| `array( 'error' => array( 'error_type' => 'kernel', 'error_code' => eZError::KERNEL_NOT_FOUND ) )` | `null` |
| alles ohne `result` (auch `array( 'result' => null )`) | `null` und einen Fehler im Log: die Funktion „did not return a result value“ |

Geben Sie für „nichts gefunden“ ein leeres Array oder `false` in `result` zurück, damit Templates es ohne Fehler im Log
prüfen können.

## Regeln

- **Prüfen Sie Berechtigungen innerhalb der Funktion.** Templates werden von vielen Stellen eingebunden; die Funktion
  ist der einzige Ort, der jeden Aufruf sieht.
- **Begrenzen Sie jedes Limit** und casten Sie jeden Parameter, wie bei jeder Eingabe.
- **Halten Sie sie schlank.** Rufen Sie den Dienst auf, den Ihre Views und Befehle verwenden.
- **Benennen Sie Parameter wie bei den Inhalts-Fetches** (`limit`, `offset`, `sort_by`), damit Template-Autoren richtig raten.
- **Cache-freundlich.** Fetches in `{cache-block}` laufen nur, wenn der Block abläuft; verlassen Sie sich nicht auf Nebenwirkungen.
- **Testen Sie die statische Methode** wie jede andere ([PHPUnit ohne Datenbank](phpunit-ohne-datenbank.md)).

## Verwandte Seiten

- [Eine eigene Fetch-Funktion schreiben (Anwendungsentwickler)](../ersteller/anwendungsentwickler/eine-eigene-fetch-funktion-schreiben.md)
- [Referenz der Fetch-Funktionen](../erweiterungsentwicklung/teil-8-referenz/referenz-der-fetch-funktionen.md)
- [Das Muster Fetch-Funktion](../../standards/muster/fetch-funktion.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
