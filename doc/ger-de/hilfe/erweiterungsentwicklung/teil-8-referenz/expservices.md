---
title: "expservices: entfernte Dienste für Erweiterungen"
audience: developer, app-creator
category: extension-development
order: 370
summary: Was expservices Erweiterungsautoren bietet - Dienste über ezjscore mit deklariertem Zugriff, Seitenaufteilung und einem einheitlichen Antwortumschlag, ein Katalog, API-Tokens - und wie Sie einen eigenen Dienst schreiben.
keywords: Dienste, Webdienste, Katalog, Antwortumschlag, API-Token, Zugriff, Seitenaufteilung, Fernzugriff, expservices, expServiceBase, services, ezjscore, catalogue, envelope, API token, X-Exp-Token, access, paging, write, guard, ok, page, remote access
type: help
---
# expservices: entfernte Dienste für Erweiterungen

Teil 8, Referenz 33. `expservices` ist eine mit dem Kernel ausgelieferte Erweiterung, die die Funktionen der
Installation als Dienste über ezjscore anbietet: eine Klasse je Bereich (System, Caches, Einstellungen, Benutzer,
Inhalte ...), aufgerufen als `<root>/ezjscore/call/exp<domain>::<method>`, mit der Antwort in einem einheitlichen
JSON-Umschlag. Apps, Skripte und andere Server verwenden sie; eine Erweiterung kann einen eigenen Bereich hinzufügen.
Die vollständige Dokumentation ist [Backend-Dienste über ezjscore](../../../bc/6.0/backend-dienste-ueber-ezjscore.md).

## Was Sie gegenüber einer einfachen Serverfunktion gewinnen

| | Einfache ezjscore-Serverfunktion | expservices-Dienst |
|---|---|---|
| Deklaration | eine öffentliche statische Methode | die Methode plus ein Eintrag in `static::$services` (summary, access, write, args, returns) |
| Zugriff | von Ihrem Code geprüft | deklariert, von `guard()` geprüft |
| Schreibzugriffe | Ihr Code prüft POST und das Token | `'write' => true`: POST und Formular-Token erforderlich, im Audit-Log erfasst |
| Fehler | `error_text` | `{ "ok": false, "error": { "code": 403, "message": ... } }` mit Codes nach HTTP-Art |
| Listen | Ihr eigenes Format | seitenweise: `data` plus `meta` (`total`, `offset`, `limit`, `count`, `has_more`) |
| Auffindbarkeit | keine | `expservices::catalog` listet jeden Dienst mit seiner Deklaration |
| Clients außerhalb des Browsers | das Session-Cookie | zusätzlich persönliche API-Tokens (Header `X-Exp-Token`) |

## Einen Dienst schreiben

Nach dem Beispiel der Dokumentation, mit Ihrem Bereich:

```php
<?php
class expMyExtServices extends expServiceBase
{
    public static $services = array(
        'itemName' => array( 'summary' => 'The name of one item',
            'access' => array( 'myext', 'read' ), 'write' => false,
            'args' => array( 'item_id' => 'int' ), 'returns' => 'id, name' ),
    );

    public static function itemName( $args )
    {
        self::guard( __FUNCTION__ );
        $item = myExtItem::fetch( self::arg( $args, 0, 'int' ) );
        if ( !$item )
        {
            throw new expServiceException( 'No such item', 404 );
        }
        return self::ok( array( 'id' => (int)$item->attribute( 'id' ), 'name' => $item->attribute( 'name' ) ) );
    }
}
```

Registrieren Sie ihn in `settings/ezjscore.ini.append.php` Ihrer Erweiterung:

```ini
[ezjscServer_expmyext]
Class=expMyExtServices
```

und erzeugen Sie die Autoloads neu. Aufgerufen wird er als `<root>/ezjscore/call/expmyext::itemName::42`.

| Hilfsmethode (protected static) | Verwendung |
|---|---|
| `guard( $method )` | den deklarierten Zugriff prüfen, bei Schreibzugriffen auch POST und Token |
| `arg( $args, $i, $type, $default = null )` | ein typisiertes URL-Argument |
| `post( $name, $type, $default = null )` | ein typisiertes POST-Feld |
| `ok( $data, array $meta = array() )` | der Umschlag für den Erfolgsfall |
| `page( array $items, $total, $offset, $limit )`, `pageOf( ... )` | eine seitenweise Liste |

Codes von `expServiceException`: 400 (Argumente), 401 (Anmeldung nötig), 403 (verweigert), 404 (nicht gefunden),
409 (Konflikt), 422 (ungültig).

## Sicherheit

- Jeder Dienst deklariert seinen Zugriff; `'public'` nur für das, was anonyme Besucher wissen dürfen.
- Schreibzugriffe erfolgen per POST mit dem Formular-Token oder mit einem API-Token und werden im Audit-Log
  festgehalten.
- Die ezjscore-Richtlinie `ezjscore/call` gilt weiterhin ([Referenz der ezjscore-Serverfunktionen](ezjscore-serverfunktionen-referenz.md)).
- Geben Sie nie Passwörter, Tokens oder personenbezogene Daten zurück, die ein Aufrufer nicht braucht.

**Demomodus.** Im Demomodus (`EXP_DEMO_MODE=1`) verweigern die Passwortmethoden Administratoren:
`requireAcceptablePassword()` wirft HTTP 403 mit `expDemoMode::refusalText()`, und `expaccount.forgotRequest` erzeugt
für einen Administrator keinen Schlüssel. Ein eigener Dienst, der Passwörter ändert, sollte zuerst
`expDemoMode::protectsPassword()` fragen. [Demomodus für Entwickler](demomodus-fuer-entwickler.md).

## Verwandte Seiten

- Zurück: [Das Hilfesystem für Erweiterungen](hilfesystem-fuer-erweiterungen.md)
- Weiter: [Glossar für Erweiterungsentwickler](glossar-fuer-erweiterungsentwickler.md)
- [Backend-Dienste über ezjscore](../../../bc/6.0/backend-dienste-ueber-ezjscore.md)
- [API-Schlüssel](../../../bc/6.0/api-schluessel.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
