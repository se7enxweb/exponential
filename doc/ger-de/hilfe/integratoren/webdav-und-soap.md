---
title: WebDAV und SOAP
audience: integrator, developer, admin
category: integration
order: 120
summary: Die beiden älteren Protokolle, die Exponential noch spricht - WebDAV, um Inhalte als Laufwerk einzubinden, und ein SOAP-1.1-Server, dem Erweiterungen Funktionen hinzufügen - wie Sie jedes einschalten, wie sie authentifizieren, ihre bekannten Lücken und wann Sie sie in einer neuen Integration verwenden.
keywords: WebDAV, SOAP, Laufwerk, ältere Protokolle, Authentifizierung, index_webdav.php, webdav.php, webdav.ini, EnableWebDAV, StartNode, FolderClasses, index_soap.php, soap.php, soap.ini, EnableSOAP, SOAPExtensions, registerFunction, eZSOAPServer, eZSOAPClient, legacy protocols
type: help
---
# WebDAV und SOAP

Diese Seite richtet sich an Integratoren, die auf eine Anforderung treffen wie "Redakteure speichern Dateien von ihrem
Desktop direkt in die Website" oder "unsere Middleware spricht SOAP". Exponential liefert einen WebDAV-Server
(`index_webdav.php`) und einen SOAP-1.1-Server (`index_soap.php`; vor 6.0.15 hießen sie `webdav.php` und `soap.php`) mit. Beide sind **standardmäßig aus**, beide haben bekannte
Lücken, und beide lohnen sich nur, wenn die Gegenseite nichts Neueres verwenden kann. Ihren Stand gegenüber den
Standards beschreibt [Standards für APIs und Protokolle](../../standards/apis-und-protokolle.md#webdav-rfc-4918).

> **Warnung**
> **`.htaccess` bremst jede Anfrage.** Sind `.htaccess`-Dateien erlaubt (`AllowOverride` alles außer `None`), geschweige
> denn genutzt, sucht Apache sie bei jeder Anfrage in jedem Verzeichnis des Pfads und liest und wertet sie aus, und
> die Ladezeiten der Seiten steigen drastisch ([gemessen](../../anleitungen/setup-dateien-von-der-konsole.md#gemessen-htaccess-gegen-regeln-im-virtuellen-host)). Die einzige wirkliche Abhilfe ist, die
> Funktion für den virtuellen Host abzuschalten: `AllowOverride None` im virtuellen HTTPS-Host, und die Regeln in
> der Konfiguration des virtuellen Hosts selbst als mod_rewrite-Regeln. Das ist in Geschwindigkeitstests im
> Produktivbetrieb weit schneller und die Zeit für die Optimierung einer Produktivkonfiguration unbedingt wert.
> Stellen Sie sofort um: Übernehmen Sie die Regeln des [Beispiels für einen virtuellen HTTPS-Host](../../beispiele/webserver/apache-virtueller-host.md) in
> Ihren virtuellen Host (mit dem statischen Cache auch [`staticcache_offload.conf`](../../../eng-us/examples/staticcache_offload.conf)), setzen Sie
> `AllowOverride None`, führen Sie `apachectl configtest` aus, laden Sie Apache neu und testen Sie die Website.
> Dieselben Regeln für andere Server: [nginx](../../beispiele/webserver/nginx.md), [LiteSpeed](../../beispiele/webserver/litespeed.md), [Caddy](../../beispiele/webserver/caddy.md), [IIS](../../beispiele/webserver/iis.md),
> [Velocity](../../beispiele/webserver/velocity.md) (eingebaut); wohin Regeln für virtuelle Hosts in einem Control Panel gehören:
> [Plesk](../hosting/control-panels/plesk.md), [cPanel](../hosting/control-panels/cpanel.md), [DirectAdmin](../hosting/control-panels/directadmin.md), [ISPConfig](../hosting/control-panels/ispconfig.md).
> **Unter LiteSpeed oder OpenLiteSpeed** kann zusätzlich der Seiten-Cache des Servers anonyme Besucher und Crawler ohne PHP bedienen: [Seiten-Caching mit LiteSpeed und OpenLiteSpeed](../../anleitungen/seiten-cache-mit-litespeed.md).

## Inhalt

1. [WebDAV](#webdav)
2. [SOAP](#soap)
3. [Den SOAP-Dienst eines anderen Systems aufrufen](#den-soap-dienst-eines-anderen-systems-aufrufen)
4. [Wann Sie was verwenden](#wann-sie-was-verwenden)

## WebDAV

> **Wichtig: Das Einstiegsskript wurde in 6.0.15 umbenannt**
> Der WebDAV-Server ist `index_webdav.php` (bis 6.0.14 `webdav.php`). `webdav.php` leitet nur noch dorthin weiter,
> schreibt einmal am Tag eine Warnung und wird in einer späteren Hauptversion entfernt. Ändern Sie Ihren virtuellen
> Host (HTTP **und** HTTPS) und die Rewrite-Regeln, und testen Sie sie: [Das WebDAV-Einstiegsskript heißt jetzt index_webdav.php](../../bc/6.0/webdav-einstiegsskript-index-webdav.md).

Der Einstiegspunkt ist `index_webdav.php` im Wurzelverzeichnis der Installation. Er führt den WebDAV-Server der Zeta
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
> Ihr Webserver muss WebDAV-Anfragen an `index_webdav.php` leiten: auf dem Haupthost eine Regel wie
> `RewriteRule ^index_webdav\.php - [L]` (in `.htaccess_root` auskommentiert mitgeliefert) oder ein eigener virtueller
> Host wie `webdav.example.com`, der jede Anfrage dorthin schickt. Die Regeln für Apache, Plesk, nginx und IIS und ein
> Test mit `curl -X PROPFIND` (erwartet: `207 Multi-Status`) stehen auf
> [der Seite zur Änderung](../../bc/6.0/webdav-einstiegsskript-index-webdav.md). Velocity liefert WebDAV nicht aus;
> verwenden Sie dafür Apache oder nginx mit PHP-FPM. Testen Sie mit dem Client, den Ihr Kunde wirklich verwenden wird,
> bevor Sie es zusagen; Desktop-Clients gehen unterschiedlich mit Sperren um.

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

Der Einstiegspunkt ist `index_soap.php` im Wurzelverzeichnis der Installation; bis 6.0.15 war es `soap.php`, das
jetzt nur noch dorthin weiterleitet und einmal am Tag einen Hinweis nach `var/log/warning.log` schreibt (siehe
[Das SOAP-Einstiegsskript heißt jetzt index_soap.php](../../bc/6.0/soap-einstiegsskript-index-soap.md)). Die Bibliothek ist `lib/ezsoap/classes/` (`eZSOAPServer`, `eZSOAPClient`, Envelope,
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

Für jede aufgeführte Erweiterung bindet `index_soap.php` `extension/<extension>/soap/initialize.php` ein, wo die Erweiterung
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

Trägt die Anfrage HTTP-Basic-Zugangsdaten, meldet `index_soap.php` diesen Benutzer vor dem Aufruf an
(`eZUser::loginUser()`), sodass die Funktion mit den Richtlinien des Benutzers läuft. Ohne Zugangsdaten läuft sie
anonym. Ein zweiter Anmeldeschritt gilt für SOAP nie: Ein Konto, das einen braucht, meldet sich mit einem App-Passwort
an ([der zweite Schritt auf anderen Anmeldewegen](../../funktionen/6.0/zweiter-schritt-auf-anderen-anmeldewegen.md)).

### Durch den Webserver lassen

Die mitgelieferten Rewrite-Regeln schicken jede unbekannte Adresse an `index.php`; mit SOAP an muss der Webserver
`index_soap.php` also durchlassen: Nehmen Sie in der `.htaccess` (aus `.htaccess_root`) das Kommentarzeichen vor
`RewriteRule ^index_soap\.php - [L]` weg, ergänzen Sie `RewriteRule ^/index_soap\.php - [L]` im HTTPS-Virtual-Host
oder eine `location = /index_soap.php` in nginx. Beschränken Sie den Zugriff auf die Adressen der Systeme, die ihn
aufrufen. Dann prüfen Sie es:

```bash
curl -sS -i 'https://www.example.com/index_soap.php?wsdl'
```

Die Antwort ist `200` mit „Error: this web page does only understand POST methods“ (der Server hat keine WSDL); ein
`404` oder eine Seite der Website heißt, dass die Regel fehlt. Die Zeilen für Apache, Plesk, nginx und die anderen
Server, einen `POST`-Test mit einem SOAP-Umschlag und die Prüfung im Protokoll auf Clients, die noch `soap.php`
aufrufen, stehen auf [Das SOAP-Einstiegsskript heißt jetzt index_soap.php](../../bc/6.0/soap-einstiegsskript-index-soap.md).

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
- [Das SOAP-Einstiegsskript heißt jetzt index_soap.php](../../bc/6.0/soap-einstiegsskript-index-soap.md)
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
