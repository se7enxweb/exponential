---
title: JSON-Endpunkte mit ezjscore
audience: app-creator, developer
category: app-creation
order: 340
summary: Dem JavaScript Ihrer Seiten ein JSON-Backend mit ezjscore-Serverfunktionen geben - der Block [ezjscServer_<name>], die statische Methode, die Aufruf-URL und die Antworthülle, Rechteprüfungen, Formular-Tokens und wann stattdessen expservices oder REST passen.
keywords: Serverfunktionen, JSON-Endpunkt, AJAX, JavaScript, Rechteprüfung, Anwendungsentwickler, ezjscore, server functions, ezjscServer, ezjscore/call, JSON, ezjscServerRouter, RequirePolicy, FunctionList, app creator
type: help
---
# JSON-Endpunkte mit ezjscore

Diese Seite richtet sich an Anwendungsentwickler, deren Seiten Daten aus PHP ohne Neuladen brauchen: Autovervollständigung,
Live-Zähler, kleine Speichervorgänge. `ezjscore` ruft registrierte PHP-Funktionen über HTTP auf und antwortet in JSON
([ezjscore](../../../../eng-us/features/6.0/extensions/ezjscore.md), [Backend-Dienste von ezjscore](../../../bc/6.0/backend-dienste-ueber-ezjscore.md)).

## Inhalt

1. [Wie ein Aufruf funktioniert](#wie-ein-aufruf-funktioniert)
2. [Eine Serverfunktion registrieren](#eine-serverfunktion-registrieren)
3. [Die Klasse](#die-klasse)
4. [Aufruf aus JavaScript](#aufruf-aus-javascript)
5. [Sicherheit](#sicherheit)
6. [ezjscore, expservices oder REST?](#ezjscore-expservices-oder-rest)

## Wie ein Aufruf funktioniert

```text
<root>/ezjscore/call/<name>::<function>[::<arg>...]?ContentType=json
```

Der Router (`ezjscServerRouter`) weist jeden Namen ohne Block in `ezjscore.ini` ab, ruft `<class>::<function>( array
$args )` auf und verpackt den Rückgabewert: `{"error_text": "", "content": <your value>}`. Eine Testfunktion wird
mitgeliefert: `<root>/ezjscore/call/ezjsc::time`.

## Eine Serverfunktion registrieren

```ini
# extension/expdemo/settings/ezjscore.ini.append.php
<?php /* #?ini charset="utf-8"?
[ezjscServer_expdemo]
Class=expDemoServerFunctions
*/ ?>
```

Optionale Schlüssel (siehe die Kommentare zu `[ezjscServer]` in `extension/ezjscore/settings/ezjscore.ini`): `File`,
`TemplateFunction`, `Functions[]` (Richtlinienfunktionen aus `[ezjscServer] FunctionList[]`, die der Benutzer braucht).

## Die Klasse

```php
<?php
class expDemoServerFunctions
{
    public static function count( array $args )
    {
        $user = eZUser::currentUser();
        $access = $user->hasAccessTo( 'expdemo', 'read' );
        if ( $access['accessWord'] === 'no' )
            throw new Exception( 'Access denied' );
        $userID = isset( $args[0] ) ? (int)$args[0] : 0;
        return array( 'count' => eZPersistentObject::count( expDemoItem::definition(), array( 'user_id' => $userID ) ) );
    }
}
```

`ezjscore/call` prüft von sich aus keine Richtlinie: **Jede Funktion prüft selbst.**

## Aufruf aus JavaScript

```javascript
// extension/expdemo/design/standard/javascript/expdemo.js
fetch( root + 'ezjscore/call/expdemo::count::' + encodeURIComponent( userId ) + '?ContentType=json',
       { credentials: 'same-origin', headers: { 'Accept': 'application/json' } } )
    .then( function ( r ) { return r.json(); } )
    .then( function ( data ) { if ( !data.error_text ) show( data.content.count ); } );
```

`root` stammt aus einem JSON-Datenblock im Pagelayout, nicht aus einem Inline-Skript
([Content Security Policy für Anwendungen](content-security-policy.md)). Schreibende Aufrufe sind POSTs und tragen das
Formular-Token.

## Sicherheit

- Prüfen Sie die Rechte in jeder Funktion; prüfen Sie jedes Argument und wandeln Sie seinen Typ um.
- Geben Sie Daten zurück, kein HTML; wenn Sie doch HTML zurückgeben müssen, maskieren Sie es.
- Schreiben nur per POST, mit dem Formular-Token.
- Geben Sie nicht mehr preis, als die Seite braucht.

## ezjscore, expservices oder REST?

| Wofür | Verwenden |
|---|---|
| das JavaScript Ihrer eigenen Seiten | ezjscore-Serverfunktionen |
| Funktionen der Administration aus Skripten, Apps oder anderen Frontends | `expservices`, mit Discovery, einer einheitlichen Antworthülle und API-Tokens ([Entfernte Dienste mit expservices](entfernte-dienste-expservices.md)) |
| Inhalte für andere Programme, OAuth für Dritte | REST ([Die REST-API](rest-api.md)) |

## Verwandte Seiten

- [Entfernte Dienste mit expservices](entfernte-dienste-expservices.md)
- [Stylesheets und Skripte](../website-ersteller/stylesheets-und-skripte.md)
- [Erweiterungspunkte: serverseitige Ajax-Funktion](../../../bc/6.0/rad-erweiterungspunkte.md#serverseitige-ajax-funktion)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
