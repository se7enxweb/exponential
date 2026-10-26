---
title: Der Authorization-Header und Proxys
audience: mobile-app-developer, app-creator, developer, hosting
category: mobile-apps
order: 270
summary: Warum gültige Zugangsdaten trotzdem mit 401 antworten können - wie die REST-Schicht und expservices den Authorization-Header lesen, Server und Proxys, die ihn verwerfen (Apache mit PHP-FPM), die Servereinstellungen, die ihn durchreichen, die Alternative X-Exp-Token für expservices und wie Sie die Fälle von der App aus unterscheiden.
keywords: Authorization-Header, 401, Proxy, FastCGI, PHP-FPM, Apache, mod_proxy_fcgi, CGIPassAuth, SetEnvIf, HTTP_AUTHORIZATION, X-Exp-Token, WWW-Authenticate, CDN, Fehlersuche, Mobile App, Authorization header, debugging, mobile app
type: help
---
# Der Authorization-Header und Proxys

Diese Seite richtet sich an App-Entwickler, die mit Zugangsdaten, die anderswo funktionieren, `401` erhalten, und an
die Hosting-Verantwortlichen, mit denen sie sprechen werden. Die übliche Ursache sind nicht die Zugangsdaten, sondern
der Weg, den der Header bis zu PHP nimmt.

## Inhalt

1. [Wie der Server den Header liest](#wie-der-server-den-header-liest)
2. [Wer ihn verwirft](#wer-ihn-verwirft)
3. [Die Lösungen auf dem Server](#die-lösungen-auf-dem-server)
4. [Die Lösung im Client für expservices](#die-lösung-im-client-für-expservices)
5. [Die Fälle unterscheiden](#die-fälle-unterscheiden)

## Wie der Server den Header liest

Die REST-Schicht liest `Authorization` aus den rohen Apache-Request-Headern, ohne Beachtung der Groß- und
Kleinschreibung, und greift ersatzweise auf `$_SERVER['HTTP_AUTHORIZATION']` zurück
(`ezpOauthUtility::getAuthorizationHeader()`). Sie akzeptiert `OAuth <token>` und `Bearer <token>`; ein persönlicher
API-Schlüssel wird an seinem Präfix `expk_` erkannt. Die Remote-Dienste lesen `HTTP_AUTHORIZATION` oder
`REDIRECT_HTTP_AUTHORIZATION` für `Bearer <token>` und zusätzlich den Header `X-Exp-Token` (`expServiceBase`).

## Wer ihn verwirft

PHP stellt Skripten den Header `Authorization` nicht in jeder Konfiguration bereit. Die Quellen nennen den häufigen
Fall: Apache mit PHP-FPM über `mod_proxy_fcgi` reicht ihn ohne Konfiguration nicht durch, und „on the reference
installation `Authorization` does not arrive and `X-Exp-Token` is the header that works“ gilt für expservices
([expservices-Referenz](../../../bc/6.0/backend-dienste-ueber-ezjscore.md#authentifizierung-mit-einem-api-token)). Auch ein
Proxy, ein Load Balancer oder ein CDN vor dem Server kann ihn entfernen. Das Symptom: `401` nur auf einem Server oder
nur hinter dem Produktions-Proxy ([Persönliche API-Schlüssel: Fehlerbehebung](../../../anleitungen/api-schluessel.md#12-fehlerbehebung)).

## Die Lösungen auf dem Server

Laut den Quellen für Apache mit PHP-FPM eine der beiden Varianten:

```apache
CGIPassAuth On
```

```apache
SetEnvIf Authorization "(.*)" HTTP_AUTHORIZATION=$1
```

Einen Proxy oder ein CDN konfigurieren Sie so, dass `Authorization` an den Ursprungsserver weitergeleitet wird und
Antworten auf Anfragen mit diesem Header nicht zwischengespeichert werden. Das sind Aufgaben des Hostings; zeigen Sie
den Personen, die den Server betreiben, diese Seite und die Tabelle zur Fehlerbehebung der Anleitung.

## Die Lösung im Client für expservices

Senden Sie das Token zweimal, so wie es die mitgelieferten Clients tun:

```text
Authorization: Bearer expt_...
X-Exp-Token: expt_...
```

Die REST-API hat keinen solchen zweiten Header; bei ihr muss `Authorization` ankommen (ein OAuth-Token, aber kein
API-Schlüssel, kann auch als Parameter `oauth_token` eines POST-Formulars kommen; im Query-String nur, wenn die
Website `rest.ini [OAuthSettings] AllowTokenInQuery` einschaltet, was dieser Abschnitt nicht empfiehlt, weil URLs
protokolliert werden).

## Die Fälle unterscheiden

| Antwort | Wahrscheinliche Ursache |
|---|---|
| `401`, leerer Inhalt, `WWW-Authenticate: OAuth realm='Exponential REST'` | keine Zugangsdaten haben PHP erreicht: Der Header wurde verworfen oder nicht gesendet |
| `401` `{"error":"invalid_token"}` | die Zugangsdaten kamen an und wurden abgelehnt: falsch, widerrufen oder ein Schlüssel in der URL |
| `401` `{"error":"expired_token"}` | der Schlüssel kam an und ist über sein Ablaufdatum hinaus |
| funktioniert mit `curl` auf dem Server, scheitert vom Telefon aus | ein Proxy oder CDN dazwischen |

Vergleichen Sie eine Anfrage, die auf dem Server selbst gestellt wird, mit einer über die öffentliche Adresse; wenn nur
die zweite scheitert, geht der Header unterwegs verloren.

## Verwandte Seiten

- [Fehler und Statuscodes](../rest-api/fehler-und-statuscodes.md)
- [REST-API v1 und v2 für Anwendungen: Konfiguration](../../ersteller/anwendungsentwickler/rest-api.md#konfiguration)
- [REST- und JSON-Aufrufe (Kochbuch)](../../../beispiele/kochbuch/rest-json-abruf.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
