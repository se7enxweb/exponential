---
title: WebDAV und SOAP
audience: integrator, developer, admin
category: integration
order: 120
summary: Die beiden älteren Protokolle, die Exponential noch spricht - WebDAV, um Inhalte als Laufwerk einzubinden, und ein SOAP-1.1-Server, dem Erweiterungen Funktionen hinzufügen - wie Sie jedes einschalten, wie sie authentifizieren, ihre bekannten Lücken und wann Sie sie in einer neuen Integration verwenden.
keywords: WebDAV, SOAP, Laufwerk, ältere Protokolle, Authentifizierung, webdav.php, webdav.ini, EnableWebDAV, StartNode, FolderClasses, soap.php, soap.ini, EnableSOAP, SOAPExtensions, registerFunction, eZSOAPServer, eZSOAPClient, legacy protocols
type: help
---
# WebDAV und SOAP

Diese Seite richtet sich an Integratoren, die auf eine Anforderung treffen wie "Redakteure speichern Dateien von ihrem
Desktop direkt in die Website" oder "unsere Middleware spricht SOAP". Exponential liefert einen WebDAV-Server
(`webdav.php`) und einen SOAP-1.1-Server (`soap.php`) mit. Beide sind **standardmäßig aus**, beide haben bekannte
Lücken, und beide lohnen sich nur, wenn die Gegenseite nichts Neueres verwenden kann. Ihren Stand gegenüber den
Standards beschreibt [Standards für APIs und Protokolle](../../standards/apis-und-protokolle.md#webdav-rfc-4918).

## Inhalt

1. [WebDAV](#webdav)
2. [SOAP](#soap)
3. [Den SOAP-Dienst eines anderen Systems aufrufen](#den-soap-dienst-eines-anderen-systems-aufrufen)
4. [Wann Sie was verwenden](#wann-sie-was-verwenden)

## WebDAV

Der Einstiegspunkt ist `webdav.php` im Wurzelverzeichnis der Installation. Er führt den WebDAV-Server der Zeta
Components (`zetacomponents/webdav ~1.2`) mit dem Inhalts-Backend
`kernel/private/classes/webdav/ezwebdavcontentbackend.php` und Basic-Authentifizierung gegen Exponential-Benutzer aus
(`ezwebdavcontentbackendauth.php`).

Schalten Sie ihn in `settings/override/webdav.ini.append.php` ein:

```ini
<?php /* #?ini charset="utf-8"?
[GeneralSettings]
EnableWebDAV=true
# the node WebDAV starts at (2 = the content root)
StartNode=2
# classes shown as folders
FolderClasses[]
FolderClasses[]=folder
*/ ?>
```

| Punkt | Einzelheit |
|---|---|
| Authentifizierung | Basic-Authentifizierung mit einem Exponential-Login; nur über HTTPS ausliefern |
| Rechte | Die Richtlinien des Benutzers gelten; geben Sie WebDAV-Benutzern eine Rolle, die auf die Teilbäume begrenzt ist, die sie ändern dürfen |
| Was funktioniert | Die Methoden der WebDAV-Klasse 1 (COPY, DELETE, GET, HEAD, MKCOL, MOVE, OPTIONS, PROPFIND, PROPPATCH, PUT), mit aufgezeichneten Regressionsfällen in `tests/tests/kernel/private/webdav/regression/` |
| Bekannte Lücken | Sperren (Klasse 2) sind unvollständig: Die Methoden des Lock-Authorizers sind leer; ob LOCK und UNLOCK mit echten Clients funktionieren, ist nicht geprüft. Die Regressionsaufzeichnungen laufen nicht in der CI |

> **Hinweis**
> Ihr Webserver muss WebDAV-Anfragen an `webdav.php` leiten (eine Rewrite-Regel oder ein eigener virtueller Host).
> Testen Sie mit dem Client, den Ihr Kunde wirklich verwenden wird, bevor Sie es zusagen; Desktop-Clients gehen
> unterschiedlich mit Sperren um.

### Berechtigungen (seit 6.0.15)

Was ein WebDAV-Benutzer sehen und ändern darf, wird wie im Web in zwei Schritten entschieden:

| Anfrage | Wer darf | Antwort bei Ablehnung |
|---|---|---|
| Die Wurzel `/` (die Liste der Siteaccesses) | jeder, ohne Anmeldung | - |
| Alles unterhalb von `/<siteaccess>/`, auch Lesen | ein angemeldeter Benutzer mit `user/login` für diesen Siteaccess (die SiteAccess-Begrenzung der Richtlinie) | `401` ohne oder mit falschen Zugangsdaten, `403` ohne `user/login` dort |
| Einen Ordner oder eine Datei lesen | `content/read` auf dem Knoten | `404`: ein Knoten, den der Benutzer nicht lesen darf, wird weder gelistet noch gezeigt |
| PUT, MKCOL | `content/create` (eine neue Datei, ein neuer Ordner) oder `content/edit` (eine ersetzte Datei) unterhalb des Ordners | `403`; nichts wird gespeichert |
| DELETE | `content/remove` | `403`; nichts wird entfernt |
| COPY, MOVE | die Rechte desselben Kopierens oder Verschiebens in der Administration (`content/create` am Ziel) | `403` (in einer `207`-Multistatus-Antwort, wie WebDAV-Clients es erwarten); nichts wird kopiert oder verschoben. COPY oder MOVE zwischen zwei Siteaccesses wird immer abgelehnt |

Bis 6.0.15 wurde jedes Lesen vor der Prüfung der Richtlinien erlaubt, und ein abgelehntes PUT, MKCOL, DELETE, COPY oder
MOVE wurde mit `201` oder `204` beantwortet: der Client zeigte einen Erfolg, und die Änderung ging verloren. Prüfen Sie,
dass WebDAV-Benutzer `user/login` für den Siteaccess haben, den sie verwenden (`/<siteaccess>/` im WebDAV-Pfad), wie sie
es auch für das Web brauchen.

"Identität bestätigen" wird bei WebDAV nie gefragt: es gehört zu Aktionen in der Weboberfläche. Ein zweiter
Anmeldeschritt wird nur gefragt, wo die Site WebDAV einbezieht (`Guard[]=webdav`), siehe
[zweiter Schritt auf anderen Anmeldewegen](../../funktionen/6.0/zweiter-schritt-auf-anderen-anmeldewegen.md); ein Konto
ohne seine Zeile in `ezuser_setting` kann sich nicht anmelden.

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
| Redakteure ziehen Dateien von ihrem Desktop in einen Ordner der Website | WebDAV, nachdem Sie den Client getestet haben; oder die Upload-Funktionen der Administration |
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
