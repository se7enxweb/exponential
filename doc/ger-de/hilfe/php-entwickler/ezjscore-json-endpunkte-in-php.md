---
title: ezjscore-JSON-Endpunkte in PHP
audience: php-developer, developer
category: php-development
order: 310
summary: ezjscore-Serverfunktionen in PHP schreiben - der Block [ezjscServer_<name>], die statische Methode, die das Argument-Array erhält, die Antwort-Hülle, Berechtigungen mit Functions[] und RequirePolicy, Schreibvorgänge per POST mit dem Formular-Token, mehrere Aufrufe in einer Anfrage und wann Sie stattdessen einen expservices-Dienst schreiben.
keywords: Serverfunktion, JSON-Endpunkt, Berechtigungen, Formular-Token, ezjscore, server function, ezjscServer, ezjscServerRouter, ezjscore/call, JSON, ContentType=json, Functions, FunctionList, RequirePolicy, X-CSRF-Token, POST, expservices, expServiceBase, AJAX, endpoint
type: help
---
# ezjscore-JSON-Endpunkte in PHP

Diese Seite richtet sich an PHP-Entwickler, die ihrem eigenen JavaScript Daten oder Aktionen über HTTP bereitstellen:
Autovervollständigung, Zähler, kleine Speichervorgänge. `ezjscore` (`extension/ezjscore`) ruft eine registrierte
statische PHP-Methode auf und antwortet in JSON, XML oder Text. Die JavaScript-Seite und ein erstes Beispiel finden Sie
unter [JSON-Endpunkte mit ezjscore](../ersteller/anwendungsentwickler/json-endpunkte-mit-ezjscore.md).

## Inhalt

1. [Wie ein Aufruf Ihre Methode erreicht](#wie-ein-aufruf-ihre-methode-erreicht)
2. [Die Klasse registrieren](#die-klasse-registrieren)
3. [Die Methode](#die-methode)
4. [Berechtigungen](#berechtigungen)
5. [Schreibvorgänge](#schreibvorgänge)
6. [Mehrere Aufrufe in einer Anfrage](#mehrere-aufrufe-in-einer-anfrage)
7. [expservices: die strukturierte Form](#expservices-die-strukturierte-form)
8. [Testen](#testen)

## Wie ein Aufruf Ihre Methode erreicht

```text
<root>/ezjscore/call/<name>::<function>[::<arg>[::<arg>...]]?ContentType=json
```

Der Router `ezjscServerRouter` (`extension/ezjscore/classes/ezjscserverrouter.php`) lehnt jeden Namen ohne Block
`[ezjscServer_<name>]` ab, ruft `<class>::<function>( array $args )` mit den durch `::` getrennten Argumenten als
Strings auf und verpackt den Rückgabewert:

```json
{"error_text": "", "content": <what your method returned>}
```

Das Format folgt dem Header `Accept` oder `?ContentType=json|xml|text`. Der Kernel liefert einen Testaufruf mit:
`<root>/ezjscore/call/ezjsc::time`.

## Die Klasse registrieren

`extension/acmereport/settings/ezjscore.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?
[ezjscServer_acmereport]
Class=acmeReportServerFunctions
Functions[]=acmereport
RequirePolicy=enabled
*/ ?>
```

| Schlüssel | Bedeutung |
|---|---|
| `Class` | die Klasse; ohne sie wird der Blockname verwendet |
| `File` | optional; ohne sie findet die Autoload-Map die Klasse |
| `TemplateFunction` | mit einem Template statt einer PHP-Methode antworten |
| `Functions[]` | die Einträge in `[ezjscServer] FunctionList[]`, die der Benutzer braucht (Richtlinie `ezjscore/call`, Einschränkung `FunctionList`) |
| `PermissionPrFunction` | einen `FunctionList`-Eintrag pro Funktion prüfen (`<name>_<function>`) |
| `RequirePolicy` | nicht gesetzt: abgelehnte Aufrufe antworten mit einem Fehlertext; `enabled`: eine Anfrage, deren Aufrufe alle abgelehnt werden, antwortet mit 403; `disabled`: nicht geprüft |

Die Kommentare von `[ezjscServer]` in `extension/ezjscore/settings/ezjscore.ini` beschreiben jeden Schlüssel. Fügen Sie
Ihren Eintrag zu `[ezjscServer] FunctionList[]` hinzu, wenn Sie `Functions[]` verwenden.

## Die Methode

```php
<?php
class acmeReportServerFunctions
{
    /**
     * acmereport::count::<status>
     *
     * @param array $args the ::-separated arguments, as strings
     * @return array
     */
    public static function count( $args )
    {
        $access = eZUser::currentUser()->hasAccessTo( 'acmereport', 'read' );
        if ( $access['accessWord'] === 'no' )
            throw new Exception( 'Access denied' );

        $status = isset( $args[0] ) ? (string)$args[0] : '';
        if ( !in_array( $status, array( 'open', 'closed' ), true ) )
            throw new Exception( 'Unknown status' );

        return array( 'count' => (int)eZPersistentObject::count( acmeReportItem::definition(),
                                                                 array( 'status' => $status ) ) );
    }
}
```

- Jedes Argument ist ein String aus der URL: Casten Sie es und prüfen Sie es gegen eine Allow-Liste.
- Geben Sie Daten zurück (Arrays, Zahlen, Strings), kein HTML. Wenn Sie Markup zurückgeben müssen, maskieren Sie es.
- Eine Exception wird zum `error_text` der Antwort; halten Sie ihre Meldung frei von Interna.

## Berechtigungen

`ezjscore/call` prüft von sich aus keine Richtlinie für Ihre Funktion. Verwenden Sie beide Ebenen:

1. `Functions[]` mit `RequirePolicy=enabled` im Block, sodass ein Benutzer ohne die Richtlinie `ezjscore/call` für Ihren
   Eintrag abgelehnt wird, bevor Ihr Code läuft.
2. Eine Prüfung innerhalb der Methode auf die Modulrichtlinie, zu der die Daten gehören (`hasAccessTo()`, `canRead()`
   bei Knoten).

## Schreibvorgänge

Eine Methode, die Daten ändert, muss alles außer POST ablehnen und sich auf das Formular-Token verlassen:

```php
if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
    throw new Exception( 'POST required' );
$http = eZHTTPTool::instance();
$title = trim( (string)$http->postVariable( 'title', '' ) );
```

Für angemeldete Benutzer prüft `ezformtoken` das Token bei jedem POST, bevor der Router läuft: Das JavaScript sendet es
als Feld `ezxform_token` oder als Header `X-CSRF-Token` ([CSRF-Schutz und ezformtoken](csrf-und-ezformtoken.md)).

## Mehrere Aufrufe in einer Anfrage

Ein POST von `ezjscServer_function_arguments` mit mehreren durch `@SEPARATOR$` verbundenen Aufrufen führt sie in einer
Anfrage aus. Jeder Aufruf wird wie ein einzelner geprüft.

## expservices: die strukturierte Form

Für Dienste, die Skripte, Apps und andere Frontends aufrufen (nicht nur Ihre eigenen Seiten), baut die Erweiterung
`expservices` auf demselben Router auf, mit einer Klasse pro Domäne, die `expServiceBase` erweitert, einer deklarierten
Liste `public static $services`, Auffindbarkeit (`expservices::catalog`), einer festen Hülle (`{"ok": true, "data": ...}`),
Paging, Fehlercodes, API-Tokens und einem Audit-Ereignis für jeden Schreibvorgang
([Backend-Dienste über ezjscore](../../bc/6.0/backend-dienste-ueber-ezjscore.md)):

```php
class expFooServices extends expServiceBase
{
    public static $services = array(
        'bar' => array( 'summary' => '...', 'access' => array( 'content', 'read' ),
                        'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => '...' ) );

    public static function bar( $args )
    {
        self::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ) );
        return self::ok( array( 'name' => $node->attribute( 'name' ) ) );
    }
}
```

Deklarieren Sie `$services` als Wert der Eigenschaft im Klassenrumpf, niemals durch eine Zuweisung nach der Klasse: Ein
persistenter Velocity-Worker setzt statische Eigenschaften zwischen Anfragen auf ihre deklarierten Werte zurück, und eine
Zuweisung außerhalb der Klasse läuft nicht erneut ([Fallen durch statischen Zustand](fallen-durch-statischen-zustand.md)).

## Testen

```bash
curl -s 'https://www.example.com/ezjscore/call/acmereport::count::open?ContentType=json'
php vendor/bin/phpunit tests/tests/extension/expservices/
```

Testen Sie die Methode selbst in einem Test ohne Datenbank, indem Sie sie mit einem Argument-Array aufrufen.

## Verwandte Seiten

- [Referenz der ezjscore-Serverfunktionen](../erweiterungsentwicklung/teil-8-referenz/ezjscore-serverfunktionen-referenz.md)
- [Ajax mit ezjscore-Serverfunktionen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/ajax-mit-ezjscore-serverfunktionen.md)
- [REST und entfernte Dienste aus PHP](rest-und-entfernte-dienste-aus-php.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
