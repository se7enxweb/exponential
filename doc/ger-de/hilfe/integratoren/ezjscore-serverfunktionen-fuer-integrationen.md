---
title: ezjscore-Serverfunktionen für Integrationen
audience: integrator, developer
category: integration
order: 90
summary: Wann ein kleiner eigener JSON-Endpunkt die richtige Integrationsschnittstelle ist, wie Sie ihn als ezjscore-Serverfunktion schreiben, wie Sie ihn mit der Richtlinie ezjscore/call und dem Formular-Token schützen und wann Sie stattdessen expservices oder eine REST-Route verwenden.
keywords: ezjscore, Serverfunktion, ezjscServerFunctions, ezjscore.ini, ezjscServer, /ezjscore/call, JSON-Endpunkt, Richtlinie ezjscore/call, FunctionList, Formular-Token, Ajax, Integrationsendpunkt, server function, JSON endpoint, form token, integration endpoint
type: help
---
# ezjscore-Serverfunktionen für Integrationen

Diese Seite ist für Integrationsentwickler, die einen kleinen eigenen Endpunkt brauchen: einen Lagerbestand für die
Produktseite, einen Status, den das externe System abfragt, ein Nachschlagen, das ein eingebettetes Widget aufruft.
Eine **ezjscore-Serverfunktion** ist eine öffentliche statische Methode Ihrer Klasse, erreichbar unter
`/ezjscore/call/<server>::<function>[::<arg> ...]` und als JSON beantwortet. Sie ist der leichteste Weg, einen
HTTP-Endpunkt in eine Erweiterung zu bringen.

## Inhalt

1. [Welche Art von Endpunkt Sie wählen sollten](#welche-art-von-endpunkt-sie-wählen-sollten)
2. [Eine Serverfunktion in einer Integrationserweiterung](#eine-serverfunktion-in-einer-integrationserweiterung)
3. [Sie schützen](#sie-schützen)
4. [Sie von einem anderen System aufrufen](#sie-von-einem-anderen-system-aufrufen)
5. [Checkliste](#checkliste)

## Welche Art von Endpunkt Sie wählen sollten

| Art | Wählen Sie sie, wenn | Zugangsdaten |
|---|---|---|
| Einfache ezjscore-Serverfunktion | Browser-Widgets und einfache Abfragen auf derselben Website; Sie prüfen den Zugriff selbst | Sitzung; Richtlinie `ezjscore/call` |
| Eigene `expservices`-Domäne | Andere Systeme rufen sie auf; Sie möchten deklarierten Zugriff, Seitenweise Abfrage, Token-Unterstützung, Audit und einen Katalogeintrag | Sitzung oder `expt_...`-Token |
| REST-Controller in Ihrer Erweiterung | Der Endpunkt gehört zur Familie `/api/`, mit OAuth-Token und API-Schlüsseln | OAuth-Token, API-Schlüssel, Basic |

Für einen Endpunkt zwischen Maschinen ist die `expservices`-Domäne meist die bessere Wahl
([Entfernte Dienste mit expservices](entfernte-dienste-mit-expservices.md#ihre-eigene-dienstdomäne)).

## Eine Serverfunktion in einer Integrationserweiterung

`extension/myintegration/settings/ezjscore.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[ezjscServer_myintegration]
Class=myIntegrationServerFunctions
# the function list name that roles are limited by (policy ezjscore/call, limitation FunctionList)
Functions[]=myintegration

*/ ?>
```

`extension/myintegration/classes/myintegrationserverfunctions.php`:

```php
<?php
/**
 * GET <root>/ezjscore/call/myintegration::stock::<remote id>
 * Returns the stock level the ERP import stored on the product object.
 */
class myIntegrationServerFunctions extends ezjscServerFunctions
{
    public static function stock( $args )
    {
        $remoteID = isset( $args[0] ) ? (string)$args[0] : '';
        $object = eZContentObject::fetchByRemoteID( $remoteID );
        if ( !$object instanceof eZContentObject || !$object->canRead() )
        {
            // the same answer for "missing" and "not allowed", so nothing leaks
            return array( 'found' => false );
        }
        $dataMap = $object->dataMap();
        $stock = isset( $dataMap['stock'] ) ? (int)$dataMap['stock']->toString() : null;
        return array( 'found' => true, 'stock' => $stock );
    }
}
```

Erzeugen Sie die Autoloads neu (`php bin/php/ezpgenerateautoloads.php -e`) und leeren Sie den INI-Cache
(`php bin/php/ezcache.php --clear-tag=ini --allow-root-user`); laden Sie PHP-FPM neu und starten Sie Velocity neu,
damit beide Laufzeitumgebungen die Klasse kennen.

## Sie schützen

- **Die Richtlinie.** Jeder Aufruf braucht `ezjscore/call`; ihre Einschränkung **FunctionList** nennt die
  Funktionslisten, die eine Rolle aufrufen darf. Geben Sie der anonymen Rolle Ihre Liste nur, wenn die Antwort
  öffentlich ist.
- **Prüfen Sie, was die Funktion tut.** Eine Funktion, die Inhalte liest, prüft `canRead()`; eine Funktion, die Daten
  ändert, verlangt `POST` und den Formular-Token
  ([ezformtoken und CSRF](../erweiterungsentwicklung/teil-8-referenz/ezformtoken-und-csrf.md)).
- **Prüfen Sie Argumente.** Jeder Teil von `$args` stammt aus der Adresse; casten Sie IDs mit `(int)` und prüfen Sie
  Zeichenketten.
- **Geben Sie nur zurück, was gebraucht wird.** Geben Sie nie Passwörter, Token oder personenbezogene Daten zurück,
  mit denen der Aufrufer nichts anfangen kann.
- **Content Security Policy.** Ein Browser-Widget auf einer anderen Domain kann den Endpunkt nicht über Ursprungsgrenzen
  hinweg aufrufen, sofern Sie nicht dafür entwerfen; rufen Sie ihn besser von Ihrem eigenen Server auf.

## Sie von einem anderen System aufrufen

```bash
curl -s -H 'Accept: application/json' 'https://example.com/ezjscore/call/myintegration::stock::erp-product-10442'
# {"error_text":"","content":{"found":true,"stock":12}}
```

Mit dem Header `Accept: application/json` ist die Antwort der Umschlag; `error_text` ist gefüllt, wenn der Aufruf
scheiterte oder abgelehnt wurde. Im Browser gibt `Exp.io.call( 'myintegration::stock', [ 'erp-product-10442' ] )`
ein Promise zurück (Designs der Administration und `expui`), und `$.ez()` funktioniert dort, wo `ezjsc::jqueryio`
geladen ist.

## Checkliste

- [ ] Der Server-Block, die Klasse und die Liste `Functions[]` in Ihrer Erweiterung.
- [ ] Eine Rolle mit `ezjscore/call`, beschränkt auf Ihre Funktionsliste und nur denen gegeben, die sie brauchen.
- [ ] Lesezugriffe prüfen `canRead()`; Schreibzugriffe verlangen `POST` und den Formular-Token.
- [ ] Argumente geprüft und gecastet.
- [ ] Die Antwort in der Schnittstellenspezifikation dokumentiert.

## Verwandte Seiten

- [Referenz der ezjscore-Serverfunktionen](../erweiterungsentwicklung/teil-8-referenz/ezjscore-serverfunktionen-referenz.md)
- [Ajax mit ezjscore-Serverfunktionen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/ajax-mit-ezjscore-serverfunktionen.md)
- [JSON-Endpunkte mit ezjscore](../ersteller/anwendungsentwickler/json-endpunkte-mit-ezjscore.md)
- [ezjscore](../../funktionen/6.0/erweiterungen/ezjscore.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
