---
title: Standards für APIs und Protokolle
audience: developer, admin
category: standards
order: 120
summary: Welche Standards für APIs und Netzwerkprotokolle Exponential umsetzt (OAuth 2.0, OpenID Connect, REST über HTTP, JSON, WebDAV, SOAP, RSS, Atom, Sitemaps, SMTP, LDAP, Webhooks), mit Nachweisen, Status und Lücken.
keywords: API, Protokoll, Schnittstelle, OAuth, OAuth 2.0, RFC 6749, RFC 6750, OpenID Connect, OIDC, PKCE, REST, HTTP, RFC 9110, Statuscodes, JSON, RFC 8259, WebDAV, RFC 4918, SOAP, XML-RPC, RSS, RSS 2.0, Atom, RFC 4287, OPML, Sitemaps, sitemaps.org, SMTP, RFC 5321, STARTTLS, RFC 8058, Abmeldung mit einem Klick, LDAP, RFC 4511, Webhooks, HMAC, OpenAPI, MCP, Model Context Protocol, ezprestapi, API-Schlüssel, protocol, status codes, one-click unsubscribe, API keys
type: reference
---
# Standards für APIs und Protokolle

Diese Seite richtet sich an Entwickler, die andere Systeme an Exponential anbinden, und an Administratoren, die angeben
müssen, welche Protokolle eine Installation spricht und wie weit jedes davon reicht. Für jede API und jedes
Netzwerkprotokoll der Reihe 6.0.x sagt sie, was der Standard ist, was Exponential umsetzt, wo Code, Einstellungen und
Tests liegen und was fehlt.

> **Hinweis** Ein hier aufgeführtes Protokoll ist nicht unbedingt eingeschaltet. Mehrere (SOAP, LDAP, der Audit-Webhook)
> sind aus, bis ein Administrator sie konfiguriert; der Abschnitt jedes Protokolls nennt die Einstellung.

## Zusammenfassung

| Standard | Version | Status | Nachweis (kurz) |
|---|---|---|---|
| OAuth 2.0, Autorisierungsserver für die REST-API | RFC 6749 (2012) | **Teilweise erfüllt** | `kernel/private/modules/oauth/`, `kernel/private/rest/classes/oauth/`, Token-Endpunkt `POST /api/auth/v1/oauth/token`; `state` und PKCE (RFC 7636, S256) seit 6.0.15; kein `token_type` |
| OAuth 2.0 Bearer-Tokens | RFC 6750 (2012) | **Teilweise erfüllt** | `Authorization: Bearer` und `OAuth` akzeptiert (`ezpOauthUtility`); Token wird auch als Query-Parameter akzeptiert |
| OAuth 2.0 / OpenID Connect, Client für Social Login | RFC 6749, RFC 7636, OIDC Core 1.0 | **Teilweise erfüllt** | Extension `sevenx_authentication_2fa`: Authorization-Code-Flow mit `state` und PKCE S256; ID-Tokens werden nicht validiert |
| REST über HTTP | HTTP-Semantik, RFC 9110 | **Teilweise erfüllt** | `ezprestapi`-Routen `/api/ezp/v1`, `/api/ezp/v2`; Statusklassen in `kernel/private/rest/classes/` |
| JSON | RFC 8259 | **Erfüllt** | `ezpRestJsonView` (`application/json`, UTF-8), Aufrufschnittstelle von `ezjscore` |
| OpenAPI-Beschreibung | 3.x | **Nicht anwendbar** (Kernel) | kein OpenAPI-Dokument für die REST-API des Kernels; Velocity veröffentlicht eines für seine eigenen Handler |
| WebDAV | RFC 4918 | **Teilweise erfüllt** | `index_webdav.php`, `kernel/private/classes/webdav/` auf Basis von `zetacomponents/webdav`; Sperr-Eigentümerschaft nicht implementiert |
| SOAP | 1.1 (W3C Note) | **Teilweise erfüllt** | `lib/ezsoap/`, `soap.php`, `settings/soap.ini [GeneralSettings] EnableSOAP=false`; keine WSDL |
| XML-RPC | Spezifikation von 1999 | **Nicht anwendbar** | nicht implementiert |
| RSS-Export und -Import | RSS 1.0, RSS 2.0 (Export); 0.91 bis 2.0 (Import) | **Erfüllt** | `kernel/classes/ezrssexport.php`, `ezrssimport.php` |
| Atom | RFC 4287 | **Teilweise erfüllt** | nur Export (`rss_version` `ATOM`); kein Atom-Import |
| Sitemaps | sitemaps.org 0.9 | **Erfüllt** | Extension `bcgooglesitemaps` schreibt `http://www.sitemaps.org/schemas/sitemap/0.9` |
| SMTP-Mail-Transport | RFC 5321 | **Erfüllt** | `lib/ezutils/classes/ezsmtptransport.php` auf Basis von `zetacomponents/mail`; STARTTLS verfügbar |
| Abmeldung mit einem Klick | RFC 8058 | **Erfüllt** | `kernel/classes/mailpreferences/expmailgate.php` fügt `List-Unsubscribe` und `List-Unsubscribe-Post` hinzu |
| LDAP-Authentifizierung | RFC 4511 (LDAPv3) | **Teilweise erfüllt** | `kernel/classes/datatypes/ezuser/ezldapuser.php`; Standard `LDAPVersion=2`; kein StartTLS |
| Webhooks (ausgehend, signiert) | kein formaler Standard | **Erfüllt** gemäß seinem eigenen dokumentierten Vertrag | `kernel/classes/audit/sinks/expauditwebhooksink.php`, HMAC-SHA-256-Signatur |
| Model Context Protocol | Spezifikation von 2025 | **Nicht anwendbar** (Kernel) | das Kernel-Repository liefert keinen MCP-Server mit |

## OAuth 2.0 (RFC 6749) und Bearer-Tokens (RFC 6750)

### Was es ist

Das OAuth 2.0 Authorization Framework (IETF RFC 6749, Oktober 2012) erlaubt einer Anwendung, für einen Benutzer zu
handeln, ohne dessen Passwort zu kennen. RFC 6750 legt fest, wie ein Access-Token als Bearer-Token gesendet wird.
RFC 7636 (PKCE) und die OAuth 2.0 Security Best Current Practice (RFC 9700, 2025) härten die Flows.

### Wie Exponential es erfüllt

Exponential ist ein **Autorisierungsserver** für seine REST-API:

- Clients werden unter Setup > oAuth-Verwaltung (`/oauthadmin/list`, `kernel/private/modules/oauthadmin/`) registriert.
  Jeder erhält eine Client-Kennung, ein Geheimnis und eine Weiterleitungsadresse.
- Der Autorisierungs-Endpunkt ist die Ansicht `oauth/authorize` (`kernel/private/modules/oauth/authorize.php`). Der
  Benutzer meldet sich an und benötigt die Richtlinie `oauth/authorize`
  ([Hinweis zur Aktualisierung](../bc/6.0/oauth-autorisierungsrichtlinie.md)). Unbekannte Clients und eine `redirect_uri`, die
  nicht der Endpunkt des Clients ist, erhalten `400 Bad Request` ohne Weiterleitung (RFC 6749, Abschnitt 4.1.2.1),
  getestet in `tests/tests/kernel/private/oauth/ezpOauthAuthorizeRedirectTest.php`.
- Der Token-Endpunkt ist `POST /api/auth/v1/oauth/token`
  (`kernel/private/rest/classes/controllers/oauth_token.php`). Er akzeptiert nur die Grants `authorization_code` und
  `refresh_token` (`validateGrantType()`), prüft das Client-Geheimnis (`ezpRestClient::validateSecret()`) und
  antwortet mit `access_token`, `refresh_token` und `expires_in`.
- Tokens sind 40 Hexadezimalzeichen aus `random_bytes()` (`ezpRestToken::generateToken()`); ihre Lebensdauer ist
  `settings/rest.ini [OAuthSettings] TokenTTL=3600`, die eines Refresh-Tokens `RefreshTokenTTL=2592000` (30 Tage,
  `0` = kein Ablauf; `ezpOauthUtility::refreshTokenExpired()`).
- Ein Code oder Refresh-Token wird nur von dem Client eingelöst, für den er ausgestellt wurde
  (`ezpOauthUtility::clientMatches()`), und nur, solange sein Benutzer aktiviert und nicht durch fehlgeschlagene
  Anmeldungen gesperrt ist (`ezpOauthUtility::userMayUseToken()`); dieselbe Prüfung läuft für jede Ressourcenanfrage in
  `ezpOauthFilter`.
- Eine Ressourcenanfrage trägt das Token als `Authorization: Bearer <token>` (RFC 6750), als
  `Authorization: OAuth <token>` (ein älterer Entwurf) oder als Parameter `oauth_token` eines POST-Formulars
  (RFC 6750 Abschnitt 2.2, `AllowTokenInBody=enabled`); den Query-Parameter `oauth_token` nur bei
  `AllowTokenInQuery=enabled`, standardmäßig aus (RFC 6750 Abschnitt 2.3; `ezpOauthUtility::tokenSources()` in
  `kernel/private/rest/classes/oauth/utility.php`).
- `rest.ini [Authentication] RequireAuthentication=enabled` und
  `AuthenticationStyle=ezpRestOauthAuthenticationStyle` sind die Standards.
- Persönliche API-Schlüssel (`Authorization: Bearer expk_...`) sind eine zweite Art von Zugangsdaten im selben Header,
  kein OAuth-Flow; siehe [Persönliche API-Schlüssel](../anleitungen/api-schluessel.md) und
  `kernel/private/rest/classes/auth/apikey_auth_filter.php`.

Der Leitfaden für die Client-Seite ist [REST API v1 und v2](../anleitungen/rest-api-v1-und-v2.md), Abschnitt 3.

### Status

**Teilweise erfüllt.** Der Authorization-Code-Grant mit Refresh-Tokens funktioniert so, wie RFC 6749 es beschreibt;
mehrere vorgeschriebene oder empfohlene Teile fehlen.

### Bekannte Lücken

- Seit 6.0.15 wird `state` unverändert mit dem Code, dem Token und dem Fehler zurückgegeben, und PKCE (RFC 7636) wird
  unterstützt: `S256`, `plain` nur bei eingeschaltetem `AllowPlainPkce`, standardmäßig verlangt für öffentliche
  Clients (ohne gespeichertes Geheimnis) ([PKCE und state](../bc/6.0/oauth-pkce-und-state.md)). Das sind keine
  Lücken mehr.
- CORS für die REST-Schnittstelle gibt es seit 6.0.15, standardmäßig ausgeschaltet (`rest.ini [CorsSettings]`,
  [CORS für die REST-API](../bc/6.0/cors-fuer-die-rest-api.md)).
- `response_type=token` (der Implicit Grant, von RFC 9700 abgeraten) wird weiterhin akzeptiert und gibt die Tokens im
  Query-String zurück statt im URL-Fragment, das RFC 6749 Abschnitt 4.2.2 vorschreibt.
- Die Token-Antwort hat kein `token_type` (von RFC 6749 Abschnitt 5.1 vorgeschrieben); `Cache-Control: no-store` wird
  vom Controller nicht gesetzt. **Nicht geprüft**, ob eine darunterliegende Schicht es hinzufügt.
- Kein Grant `client_credentials` oder `password`, keine Token-Introspektion (RFC 7662), kein Widerrufs-Endpunkt
  (RFC 7009).
- Das Token in einem Query-Parameter, das RFC 6750 nur als letztes Mittel erlaubt, wird seit 6.0.15 standardmäßig
  abgelehnt (`AllowTokenInQuery=disabled`, [Härtung der OAuth-Tokens](../bc/6.0/oauth-token-haertung.md)). Das ist
  keine Lücke mehr, solange eine Website es nicht einschaltet.

## OpenID Connect und OAuth 2.0 als Client (Social Login)

### Was es ist

OpenID Connect Core 1.0 (OpenID Foundation, 2014) ist eine Identitätsschicht auf OAuth 2.0: Ein ID-Token (ein
signiertes JWT) und ein Userinfo-Endpunkt teilen dem Client mit, wer sich angemeldet hat.

### Wie Exponential es erfüllt

Der Kernel hat weder einen OpenID-Connect-Provider noch einen OpenID-Connect-Client. Die Extension
`sevenx_authentication_2fa`, die die Wurzel-`composer.json` verlangt (`~1.0.12`), fügt **Social Login** als
OAuth-2.0-Client hinzu:

- Basisklasse `eZOAuthUser` (`extension/sevenx_authentication_2fa/classes/ezoauthuser.php`): der
  Authorization-Code-Grant; ein zufälliges `state`, das in der Sitzung gehalten, an den Provider gebunden und innerhalb
  von 600 Sekunden einmal akzeptiert wird (`STATE_TTL`); PKCE mit `code_challenge_method=S256`, wo konfiguriert; einer
  Adresse, die der Provider als nicht verifiziert markiert (`email_verified` false), wird nicht vertraut.
- Provider-Handler in `login_handler/`: Google, Facebook, Meta, Instagram, Twitter und ID.me. Der Handler für ID.me
  (`ezidmeuser.php`) ist als "OAuth 2.0 / OpenID Connect" beschrieben, sendet eine `nonce` und liest die
  OIDC-Antwort von `/userinfo`.
- Benutzerdokumentation: [Zwei-Faktor-Anmeldung](../anleitungen/zwei-faktor-anmeldung.md), "Social Login".

### Status

**Teilweise erfüllt.** Die Client-Seite von OAuth 2.0 folgt der aktuellen Praxis (state, PKCE). OpenID Connect wird
nur für seinen Userinfo-Endpunkt verwendet.

### Bekannte Lücken

- ID-Tokens werden weder angefordert noch validiert (keine Prüfung der JWT-Signatur, kein JWKS); die Identität stammt
  aus dem Userinfo-Aufruf.
- Kein OpenID Connect Discovery (`/.well-known/openid-configuration`); die Endpunkte werden pro Provider konfiguriert.
- Exponential kann nicht als OpenID-Connect-Provider für andere Anwendungen auftreten.

## REST über HTTP

### Was es ist

REST ist ein Architekturstil, kein Standard. Prüfen lässt sich die Verwendung von HTTP selbst: HTTP Semantics (IETF
RFC 9110, Juni 2022) legt Methoden und Statuscodes fest.

### Wie Exponential es erfüllt

- Das REST-Framework liegt im Kernel (`kernel/private/rest/classes/`: Router, Dispatcher, Authentifizierungsstile,
  Views) auf Basis von `zetacomponents/mvc-tools`. Die Inhalts-API ist die Extension `ezprestapi` (verlangt als
  `~1.2.6`), Routen `/api/ezp/v1/...` und `/api/ezp/v2/...`; lesende Routen antworten unter beiden Versionen,
  schreibende (Erstellen, Aktualisieren, Löschen) nur unter v2. Jede Route prüft die Inhaltsrichtlinien des Benutzers
  (`expRestContentPermission`).
- Statuscodes: `ezpHttpResponseCodes` (`kernel/private/rest/classes/http_response_codes.php`) benennt 200, 400, 401,
  403, 404, 405 und 500; `kernel/private/rest/classes/status/response.php` kennt auch 201 und 204;
  `status/rate_limited.php` antwortet mit `429 Too Many Requests` und `Retry-After` für einen API-Schlüssel über seinem
  Limit (RFC 6585).
- `settings/rest.ini`: `[ApiProvider] ProviderClass[]`, `[Authentication]`, `[OAuthSettings]`, `[ApiKeySettings]`,
  `[CacheSettings]`.
- Tests: `tests/tests/kernel/private/rest/` (Request-Parser, Routen), `tests/tests/kernel/classes/apikey/`.

Eine zweite HTTP-API ist die Aufrufschnittstelle von `ezjscore`, `/ezjscore/call/<class>::<method>`, die die Extension
`expservices` mit benannten Diensten füllt ([Entfernte Dienste und Apps](../anleitungen/entfernte-dienste-und-apps.md)).

### Status

**Teilweise erfüllt.**

### Bekannte Lücken

- `GET /content/node/<id>/listAtom` antwortet unter v1 und v2 mit 500
  ([REST API v1 und v2](../anleitungen/rest-api-v1-und-v2.md), "Known issue: listAtom").
- Welchen Statuscode jede schreibende Route zurückgibt (zum Beispiel 201 beim Erstellen), ist anhand des Codes
  **nicht geprüft**; der Leitfaden zeigt nur den JSON-Inhalt.
- Bedingte Anfragen (`ETag`, `If-Match`) bei schreibenden Aufrufen wurden nicht gefunden. **Nicht geprüft.**

## JSON (RFC 8259)

### Was es ist

Das JavaScript Object Notation (JSON) Data Interchange Format, IETF RFC 8259 (Dezember 2017, auch ECMA-404).

### Wie Exponential es erfüllt

- REST-Antworten werden von `ezpRestJsonView` (`kernel/private/rest/classes/views/json.php`) mit dem Inhaltstyp
  `application/json` und dem Zeichensatz UTF-8 über `ezcMvcJsonViewHandler` geschrieben.
- Die Aufrufschnittstelle von `ezjscore` sendet `Content-Type: application/json; charset=utf-8`
  (`extension/ezjscore/classes/runnable/views/ezjscore/call.php`).
- JSON wird von PHPs `json_encode()` erzeugt, das JSON-Text in UTF-8 schreibt, wie RFC 8259 es verlangt.

### Status

**Erfüllt.**

### Bekannte Lücken

- Keine gefunden. RFC 8259 definiert keinen Parameter `charset` für `application/json`; ihn zu senden ist harmlos.

## OpenAPI und JSON:API

Die REST-API des Kernels hat keine OpenAPI-Beschreibung und folgt nicht JSON:API; keiner der beiden Standards trifft
auf sie zu. Velocity, der separat paketierte Webserver, veröffentlicht `/.well-known/openapi.json` für seine eigenen
Handler ([Velocity: Discovery, Föderation und Deployment](../funktionen/6.0/velocity-erkennung-foederation-und-deployment.md));
dieses Dokument beschreibt die Inhalts-API nicht. **Nicht anwendbar** auf den Kernel.

## WebDAV (RFC 4918)

### Was es ist

HTTP Extensions for Web Distributed Authoring and Versioning (WebDAV), IETF RFC 4918 (Juni 2007): Methoden wie
PROPFIND, PROPPATCH, MKCOL, COPY, MOVE, LOCK und UNLOCK. Klasse 1 ist der Grundumfang, Klasse 2 fügt Sperren hinzu.

### Wie Exponential es erfüllt

- Einstiegspunkt `index_webdav.php` im Installationsverzeichnis (bis 6.0.14 `webdav.php`, jetzt ein weiterleitender
  Shim, siehe [die Änderung](../bc/6.0/webdav-einstiegsskript-index-webdav.md)); er verwendet den WebDAV-Server der Zeta Components
  (`zetacomponents/webdav ~1.2` in `composer.json`) mit dem Inhalts-Backend
  `kernel/private/classes/webdav/ezwebdavcontentbackend.php` und dem Authentifizierungs-Backend
  `ezwebdavcontentbackendauth.php` (Basic-Authentifizierung gegen Exponential-Benutzer).
- `settings/webdav.ini` (`[GeneralSettings]`, `[FolderSettings]`).
- Das Sperr-Plugin ist registriert (`ezcWebdavLockPluginConfiguration` in `index_webdav.php`), und das Backend deklariert
  `ezcWebdavLockBackend`.
- Aufzeichnungen von Anfragen und Antworten für COPY, DELETE, GET, HEAD, MKCOL, MOVE, OPTIONS, PROPFIND, PROPPATCH und
  PUT liegen in `tests/tests/kernel/private/webdav/regression/`.
- Geschichte: [die WebDAV-Engine von 4.1](../../eng-us/features/4.1/webdav_readme.txt) und die Entwurfsnotizen in
  [doc/eng-us/specifications/trunk/webdav](../../eng-us/specifications/trunk/webdav/readme.txt).

### Status

**Teilweise erfüllt.** Die Methoden der Klasse 1 sind implementiert; das Sperren (Klasse 2) ist unvollständig.

### Bekannte Lücken

- Die Methoden `assignLock()`, `ownsLock()` und `releaseLock()` des Sperr-Autorisierers in
  `ezwebdavcontentbackendauth.php` sind leer (`@todo implement`); der eigene Kommentar des Backends sagt, dass
  Sperraufrufe in `setProperty` und `removeProperty` noch fehlen. Ob LOCK und UNLOCK mit echten Clients funktionieren,
  ist **nicht geprüft**.
- Die Regressionsaufzeichnungen haben keinen `*Test.php`-Runner, sodass keine PHPUnit-Suite in `phpunit.xml` sie
  ausführt und die CI WebDAV nicht testet.

## SOAP 1.1

### Was es ist

SOAP 1.1 ist eine W3C Note (Mai 2000) für XML-Nachrichten über HTTP; SOAP 1.2 ist eine W3C Recommendation (2003, zweite
Ausgabe 2007). WSDL beschreibt SOAP-Dienste.

### Wie Exponential es erfüllt

- Bibliothek `lib/ezsoap/classes/` (Client, Server, Envelope, Codec, Fault). `eZSOAPEnvelope` verwendet die
  SOAP-1.1-Namensräume `http://schemas.xmlsoap.org/soap/envelope/` und `.../soap/encoding/`.
- Server-Einstiegspunkt `soap.php`; standardmäßig aus: `settings/soap.ini [GeneralSettings] EnableSOAP=false`.
  Funktionen werden von Extensions hinzugefügt (`[ExtensionSettings] SOAPExtensions[]`). Die Extension `syndication`,
  die die Wurzel-`composer.json` verlangt, ist ein SOAP-Client und -Server zwischen Installationen.
- Der Client prüft TLS-Zertifikate standardmäßig: `tests/tests/lib/ezsoap/eZSOAPClientTlsOptionsTest.php`
  (`testTheCertificateIsVerifiedByDefault`); weitere Tests in `tests/tests/lib/ezsoap/` (Suite `lib`).

### Status

**Teilweise erfüllt.** Nur SOAP-1.1-Nachrichten.

### Bekannte Lücken

- Kein SOAP 1.2 und kein Erzeugen oder Lesen von WSDL (eine Suche nach "wsdl" in `lib/ezsoap` und `kernel/` findet
  nichts).
- `[GeneralSettings] Logging` ist als "not supported yet" markiert.

## XML-RPC

Nicht implementiert. Die einzige Erwähnung im Kernel ist der Anfrage-Klassifizierer, der Sondierungsanfragen auf
`xmlrpc.php` als unerwünschten Verkehr erkennt (`kernel/classes/requestclassifier/exprequestrulesetprobes.php`).
**Nicht anwendbar.**

## RSS, Atom und OPML

### Was es ist

RSS 2.0 (RSS Advisory Board, Ausgabe 2009) und RSS 1.0 (RDF Site Summary, 2000) sind Web-Feed-Formate. Atom ist das
IETF Atom Syndication Format, RFC 4287 (Dezember 2005). OPML 2.0 listet Feeds auf.

### Wie Exponential es erfüllt

- **Export** (`kernel/classes/ezrssexport.php`, `rssXmlContent()`): RSS 1.0 (`rss1`), RSS 2.0 (`rss2`) und Atom
  (`atom`) über die Feed-Bibliothek der Zeta Components (`zetacomponents/feed ~1.4.6`); Feeds für Apple Podcasts
  (`ITUNES`) und OPML schreibt Exponential selbst. Feeds werden unter `/rss/feed/<address>` ausgeliefert.
- **Import** (`kernel/classes/ezrssimport.php`, Cronjob-Teil `kernel/private/classes/cronjobs/rssimport.php`): RSS
  0.91, 0.92, 1.0 und 2.0, gelesen mit abgewiesenen externen Entitäten und abgeschaltetem Netzwerk (`LIBXML_NONET`).
- Leitfäden: [RSS-Feeds](../anleitungen/rss-feeds.md), [RSS: Feeds für Apple Podcasts, eine seitenweise Feed-Liste und
  sicherere Exporte](../features/6.0/rss-podcast-and-feed-list.md), [OPML-Exporte](../bc/6.0/opml.md).
- Tests: `tests/tests/kernel/classes/eZRSSExportTest.php`, `eZRSSExportOPMLItemTest.php`.

### Status

- RSS: **Erfüllt.**
- Atom: **Teilweise erfüllt** (nur Export).

### Bekannte Lücken

- Atom-Feeds können nicht importiert werden: Der Import unterscheidet nur anhand des Attributs `version` eines
  RSS-Wurzelelements.
- In der CI läuft kein Feed-Validator. Die Gültigkeit erzeugter Feeds ist über die obigen Tests hinaus **nicht
  verifiziert**.

## Sitemaps (sitemaps.org 0.9)

### Was es ist

Das Sitemaps-Protokoll 0.9 (sitemaps.org, von den großen Suchmaschinen unterstützt) listet die URLs einer Site in XML
auf.

### Wie Exponential es erfüllt

Die Extension `bcgooglesitemaps` (verlangt als `~1.1.6.6`, Version 1.1.6.6) erzeugt Sitemaps per Cronjob
(`classes/runnable/cronjobs/generate.php`, `generatemultilingual.php`) mit dem Namensraum
`http://www.sitemaps.org/schemas/sitemap/0.9` und den Elementen `loc`, `lastmod`, `changefreq` und `priority` und
liefert sie über ihr Modul `sitemap` aus.

### Status

**Erfüllt.**

### Bekannte Lücken

- Das Limit von 50.000 URLs / 50 MB pro Datei und Sitemap-Indexdateien wurden für diese Seite nicht geprüft.
  **Nicht geprüft.**

## SMTP (RFC 5321) und Mail

### Was es ist

Das Simple Mail Transfer Protocol, IETF RFC 5321 (Oktober 2008). STARTTLS für SMTP ist RFC 3207. RFC 8058 (Januar
2017) legt die Abmeldung mit einem Klick mit `List-Unsubscribe-Post` fest.

### Wie Exponential es erfüllt

- `settings/site.ini [MailSettings] Transport` wählt `sendmail` (Standard), `smtp` oder `file`
  (`TransportAlias[smtp]=eZSMTPTransport`). SMTP-Einstellungen: `TransportServer`, `TransportPort=25`,
  `TransportConnectionType` (leer, `ssl`, `tls` und ältere Werte), `TransportUser`, `TransportPassword`.
- `eZSMTPTransport` (`lib/ezutils/classes/ezsmtptransport.php`) sendet über `ezcMailSmtpTransport` aus
  `zetacomponents/mail ~1.10.1`. In dieser Bibliothek bedeutet `tls` STARTTLS: Sie verlangt, dass der Server `STARTTLS`
  ankündigt (`vendor/zetacomponents/mail/src/transports/smtp/smtp_transport.php`).
- RFC 8058: Die Mail-Einstellungen fügen pro Empfänger `List-Unsubscribe` und `List-Unsubscribe-Post` hinzu
  (`kernel/classes/mailpreferences/expmailgate.php`, `expmailtoken.php`); siehe
  [Konformität der Mail-Einstellungen](../spezifikationen/6.0/mail-einstellungen-compliance.md).
- Tests: `tests/tests/lib/ezutils/eZMailTest.php`, `eZMailTransportSendTest.php`, `eZMailEzcTest.php`,
  `tests/tests/kernel/classes/mailpreferences/MailPreferencesComplianceTest.php`.

### Status

**Erfüllt** (SMTP und RFC 8058).

### Bekannte Lücken

- Der Kommentar in `site.ini` bietet noch `sslv2` und `sslv3` als Verbindungstypen an, die veraltet und unsicher sind;
  ob aktuelles PHP sie überhaupt akzeptiert, ist **nicht geprüft**.
- Der Kommentar in `site.ini` zitiert RFC 2821, das durch RFC 5321 ersetzt wurde.
- DKIM-Signaturen erstellt Exponential nicht; das bleibt dem Mailserver überlassen.

## LDAP (RFC 4511)

### Was es ist

Das Lightweight Directory Access Protocol Version 3, IETF RFC 4511 (Juni 2006); Filterzeichenketten sind RFC 4515.
LDAPv2 wurde in RFC 3494 (2003) für historisch erklärt.

### Wie Exponential es erfüllt

- Login-Handler `eZLDAPUser` (`kernel/classes/datatypes/ezuser/ezldapuser.php`), eingeschaltet mit
  `settings/site.ini [UserSettings] LoginHandler[]=LDAP` (standardmäßig auskommentiert). Einstellungen in
  `settings/ldap.ini [LDAPSettings]`: `LDAPServer`, `LDAPPort=389`, `LDAPBaseDn`, `LDAPBindUser`,
  `LDAPLoginAttribute=uid`, `LDAPVersion`, `LDAPFollowReferrals`.
- Er verwendet die PHP-Erweiterung `ldap` und setzt `LDAP_OPT_PROTOCOL_VERSION` aus `LDAPVersion`.
- Der Login wird maskiert, bevor er in einen Filter gelangt (`eZLDAPUser::ldap_escape()`: `*`, `(`, `)`, `\` und NUL
  wie in RFC 4515; ein eigener Satz für DNs).
- Tests: `tests/tests/kernel/datatypes/ezuser/eZLDAPUserTest.php` (Datenbankgruppe; übersprungen, wenn die
  Erweiterung `ldap` nicht geladen ist).

### Status

**Teilweise erfüllt.**

### Bekannte Lücken

- Der Standard ist `LDAPVersion=2`; setzen Sie ihn für jeden aktuellen Verzeichnisserver auf 3.
- Kein StartTLS: `ldap_start_tls()` wird nicht aufgerufen. Die Verschlüsselung hängt von einer Serveradresse
  `ldaps://` ab, was zusammen mit der separaten Einstellung `LDAPPort` **nicht geprüft** ist.
- `LDAPDebugTrace=enabled` protokolliert Logins und Passwörter (Warnung in `ldap.ini`); lassen Sie es im Produktivbetrieb
  aus.
- Der Test zielt auf einen festen entfernten LDAP-Server, nicht auf einen CI-Dienst; ob er in der CI läuft, ist
  **nicht geprüft**.

## Webhooks

### Was es ist

Es gibt keinen formalen Webhook-Standard. Übliche Praxis ist ein HTTPS-POST mit JSON, signiert mit einem HMAC, sodass
der Empfänger Herkunft und Aktualität prüfen kann.

### Wie Exponential es erfüllt

Das Audit-Log kann seine Einträge an einen Webhook ausliefern (`kernel/classes/audit/sinks/expauditwebhooksink.php`,
`settings/audit.ini [AuditSink_webhook]`, aus, bis `URL` gesetzt ist):

- Stapel von Einträgen als JSON, `{"v":1,"installation":...,"events":[...]}`.
- Header `X-Exponential-Timestamp`, `X-Exponential-Batch` und `X-Exponential-Signature: sha256=<hex>`, der
  HMAC-SHA-256 von `timestamp + "." + body` mit `SigningSecret`; Empfänger lehnen Zeitstempel ab, die mehr als
  300 Sekunden abweichen.
- Auslieferung über einen Spool mit Wiederholungen (`Retries`, `RetryBackoff`); mindestens einmal, dedupliziert über
  die Ereignis-ID.
- Tests: `tests/tests/kernel/classes/audit/expAuditSinksTest.php`. Dokumentation: [Audit](../bc/6.0/audit.md).

### Status

**Erfüllt** gemäß seinem eigenen dokumentierten Vertrag.

### Bekannte Lücken

- Nur das Audit-Log sendet Webhooks; Inhaltsereignisse (Veröffentlichen, Entfernen) haben keinen allgemeinen
  ausgehenden Webhook.
- Kein Endpunkt für eingehende Webhooks.

## Model Context Protocol (MCP)

Das Kernel-Repository von Exponential 6 liefert keinen MCP-Server mit. Die Übersicht der Erweiterungspunkte (Setup >
RAD tools, `settings/rad.ini`) kann MCP-Tool-Registries zählen, die eine Extension deklariert, und Velocity
veröffentlicht `/.well-known/mcp.json` für seine eigenen Handler
([Velocity: Discovery, Föderation und Deployment](../funktionen/6.0/velocity-erkennung-foederation-und-deployment.md));
keines davon wurde für diese Seite gegen die MCP-Spezifikation geprüft. **Nicht anwendbar** auf den Kernel.

## Verwandte Seiten

- [REST API v1 und v2](../anleitungen/rest-api-v1-und-v2.md), [Persönliche API-Schlüssel](../anleitungen/api-schluessel.md),
  [Entfernte Dienste und Apps](../anleitungen/entfernte-dienste-und-apps.md)
- [Richtlinie OAuth authorize (Hinweis zur Aktualisierung)](../bc/6.0/oauth-autorisierungsrichtlinie.md)
- [RSS-Feeds](../anleitungen/rss-feeds.md), [OPML-Exporte](../bc/6.0/opml.md)
- [Zwei-Faktor-Anmeldung](../anleitungen/zwei-faktor-anmeldung.md)
- [Audit](../bc/6.0/audit.md), [Konformität der Mail-Einstellungen](../spezifikationen/6.0/mail-einstellungen-compliance.md)
- [Standards für Dokumentation und Veröffentlichung](dokumentation-und-veroeffentlichung.md),
  [Datenbank- und SQL-Standards](datenbanken-und-sql.md)
