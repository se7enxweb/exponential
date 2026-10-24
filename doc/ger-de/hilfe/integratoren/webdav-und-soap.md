---
title: WebDAV und SOAP
audience: integrator, developer, admin
category: integration
order: 120
summary: Der SOAP-1.1-Server, dem Erweiterungen Funktionen hinzufügen - wie Sie ihn einschalten, wie er authentifiziert und wann Sie ihn in einer neuen Integration verwenden - und was den WebDAV-Server ersetzt, der nicht mehr zu Exponential gehört.
keywords: SOAP, ältere Protokolle, Authentifizierung, soap.php, soap.ini, EnableSOAP, SOAPExtensions, registerFunction, eZSOAPServer, eZSOAPClient, legacy protocols, WebDAV, webdav.php, entfernt
type: help
---
# WebDAV und SOAP

Diese Seite richtet sich an Integratoren, die auf eine Anforderung treffen wie "unsere Middleware spricht SOAP" oder
"Redakteure speichern Dateien von ihrem Desktop direkt in die Website". Exponential liefert einen SOAP-1.1-Server
(`soap.php`) mit. Er ist **standardmäßig aus**, hat bekannte Lücken und lohnt sich nur, wenn die Gegenseite nichts
Neueres verwenden kann. Seinen Stand gegenüber den Standards beschreibt
[Standards für APIs und Protokolle](../../standards/apis-und-protokolle.md#soap-11).

## Inhalt

1. [WebDAV](#webdav)
2. [SOAP](#soap)
3. [Den SOAP-Dienst eines anderen Systems aufrufen](#den-soap-dienst-eines-anderen-systems-aufrufen)
4. [Wann Sie was verwenden](#wann-sie-was-verwenden)

## WebDAV

Exponential enthält keinen WebDAV-Server mehr. `webdav.php`, `kernel/private/classes/webdav/`, `settings/webdav.ini`
und das Paket `zetacomponents/webdav` wurden am 24. Oktober 2026 entfernt: Der Server war standardmäßig aus, sein
Sperren wurde nie fertig, und sein Autorisierer ließ jedes Lesen durch. Für Redakteure, die Dateien in die Website
bringen, gibt es die Upload-Funktionen der Administration (Mehrfach-Upload, den Upload-Dialog des Rich-Text-Editors)
oder einen zeitgesteuerten Import. Eine Website, die WebDAV weiter braucht, behält die entfernten Dateien in einer
eigenen Extension und verlangt `zetacomponents/webdav` selbst
([Changelog](../../../changelogs/6.0/6.0.15.md#2026-10-24-the-webdav-server-is-removed)).

## SOAP

Der Einstiegspunkt ist `soap.php`. Die Bibliothek ist `lib/ezsoap/classes/` (`eZSOAPServer`, `eZSOAPClient`, Envelope,
Codec, Fault), nur SOAP 1.1, ohne WSDL. Schalten Sie sie ein und ergänzen Sie in
`settings/override/soap.ini.append.php` die Erweiterungen, die Funktionen registrieren:

```ini
<?php /* #?ini charset="utf-8"?
[GeneralSettings]
EnableSOAP=true
# enabled: the default siteaccess; disabled: matched from the request like any page
UseDefaultAccess=enabled

[ExtensionSettings]
SOAPExtensions[]=myintegration
*/ ?>
```

Für jede aufgeführte Erweiterung bindet `soap.php` `extension/<extension>/soap/initialize.php` ein, wo die Erweiterung
ihre Funktionen beim Serverobjekt `$server` (einem `eZSOAPServer`) registriert. Die Erweiterung `syndication` ist das
echte Beispiel:

```php
<?php
// extension/myintegration/soap/initialize.php
$server->registerFunction( 'myIntegrationStock', array( 'remoteID' => 'string' ) );

function myIntegrationStock( $remoteID )
{
    $object = eZContentObject::fetchByRemoteID( $remoteID );
    if ( !$object instanceof eZContentObject || !$object->canRead() )
        return 0;
    $dataMap = $object->dataMap();
    return isset( $dataMap['stock'] ) ? (int)$dataMap['stock']->toString() : 0;
}
```

Trägt die Anfrage HTTP-Basic-Zugangsdaten, meldet `soap.php` diesen Benutzer vor dem Aufruf an
(`eZUser::loginUser()`), sodass die Funktion mit den Richtlinien des Benutzers läuft. Ohne Zugangsdaten läuft sie
anonym.

## Den SOAP-Dienst eines anderen Systems aufrufen

`eZSOAPClient` sendet SOAP-1.1-Anfragen und prüft TLS-Zertifikate standardmäßig (getestet in
`tests/tests/lib/ezsoap/eZSOAPClientTlsOptionsTest.php`; siehe [TLS des SOAP-Clients](../../funktionen/6.0/soap-client-tls.md)).
Für einen modernen SOAP-Dienst mit WSDL, einen SOAP-1.2-Endpunkt oder WS-Security ist in Ihrer Erweiterung meist der
eigene `SoapClient` von PHP (die PHP-Erweiterung `soap`) das bessere Werkzeug.

## Wann Sie was verwenden

| Anforderung | Verwenden Sie |
|---|---|
| Redakteure bringen Dateien von ihrem Desktop in die Website | Die Upload-Funktionen der Administration (Mehrfach-Upload); WebDAV gehört nicht mehr zu Exponential |
| Ein Middleware-Produkt kann nur SOAP aufrufen | Den SOAP-Server mit Funktionen Ihrer Erweiterung |
| Zwei Exponential-Installationen teilen Inhalte | `syndication`, das darunter SOAP verwendet ([Seite](syndizierung-zwischen-installationen.md)) |
| Alles Neue | Die REST-API, `expservices` oder eine eigene Serverfunktion |

## Verwandte Seiten

- [Standards für APIs und Protokolle](../../standards/apis-und-protokolle.md)
- [TLS des SOAP-Clients](../../funktionen/6.0/soap-client-tls.md)
- [Syndizierung zwischen Installationen](syndizierung-zwischen-installationen.md)
- [Integrationsschnittstellen auf einen Blick](integrationsschnittstellen-auf-einen-blick.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
