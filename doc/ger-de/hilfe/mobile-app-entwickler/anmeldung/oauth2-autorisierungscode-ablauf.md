---
title: OAuth2-Autorisierungscode-Ablauf
audience: mobile-app-developer, app-creator, developer, admin
category: mobile-apps
order: 220
summary: App-Benutzer mit OAuth2 bei Exponential anmelden - den Client in der oAuth-Verwaltung registrieren, die Autorisierungsseite und ihre exakte Weiterleitungsadresse, die Richtlinie oauth/authorize, den Code bei /api/auth/v1/oauth/token eintauschen, das Token senden, es erneuern, und die Grenzen der Implementierung (Client-Secret erforderlich, kein PKCE, kein state).
keywords: OAuth2, Autorisierungscode, oauth/authorize, redirect_uri, client_id, client_secret, Token-Endpunkt, /api/auth/v1/oauth/token, access_token, refresh_token, expires_in, TokenTTL, grant_type, PKCE, state, oAuth-Verwaltung, Mobile App, authorization code, token endpoint, oAuth admin, mobile app
type: help
---
# OAuth2-Autorisierungscode-Ablauf

Diese Seite richtet sich an App-Entwickler, deren Benutzer sich mit ihrem eigenen Exponential-Konto anmelden, damit die
App als der jeweilige Benutzer liest und schreibt. Sie geht den Ablauf so durch, wie der Code ihn implementiert, und
nennt die Teile, die von dem abweichen, was aktuelle OAuth-Empfehlungen für mobile Apps erwarten.

## Inhalt

1. [Der Ablauf im Überblick](#der-ablauf-im-überblick)
2. [Schritt 1: den Client registrieren](#schritt-1-den-client-registrieren)
3. [Schritt 2: die Autorisierungsseite](#schritt-2-die-autorisierungsseite)
4. [Schritt 3: den Code eintauschen](#schritt-3-den-code-eintauschen)
5. [Schritt 4: die API aufrufen](#schritt-4-die-api-aufrufen)
6. [Tokens erneuern](#tokens-erneuern)
7. [Grenzen der Implementierung](#grenzen-der-implementierung)

## Der Ablauf im Überblick

```text
app  -> system browser: https://example.com/oauth/authorize?client_id=...&redirect_uri=...&response_type=code
user -> signs in on the site and allows the app
site -> redirect_uri?code=<code>&expires_in=<seconds>
back end -> POST /api/auth/v1/oauth/token (grant_type=authorization_code, client_id, client_secret, code, redirect_uri)
         <- {"access_token": "...", "refresh_token": "...", "expires_in": 3600}
app or back end -> GET /api/ezp/v2/... with Authorization: OAuth <access_token>
```

Weil der Token-Endpunkt das Client-Secret braucht, gehört der Austausch auf einen Server, den Sie betreiben
([Ein Backend für Ihre App](ein-backend-fuer-ihre-app.md)).

## Schritt 1: den Client registrieren

Ein Administrator öffnet **Setup > oAuth-Verwaltung** (`/oauthadmin/list`), Reiter **Anwendungen**, und legt eine
Anwendung an. Sie erhält eine zufällige Client-Kennung und ein Secret sowie die **Weiterleitungsadresse** (Endpunkt)
der Anwendung. Das Secret ist auf der Seite der Anwendung eingeklappt, bis **Das Geheimnis zeigen** gedrückt wird
([Persönliche API-Schlüssel, Abschnitt 7](../../../anleitungen/api-schluessel.md#7-die-administration-jeder-schlüssel-ein-benutzer-widerruf)).

Die Autorisierungsseite vergleicht die `redirect_uri` einer Anfrage **exakt**, Zeichen für Zeichen, mit dem
registrierten Endpunkt (`ezpRestClient::isEndPointValid()`). Registrieren Sie die Adresse genau so, wie die App oder
das Backend sie senden wird, und ohne Query-String: Die Website hängt `?code=...` daran an.

## Schritt 2: die Autorisierungsseite

Öffnen Sie die Autorisierungsseite im Systembrowser (einem Browser-Tab innerhalb der App, keiner eingebetteten Web
View, damit die App das Passwort des Benutzers nie sieht):

```text
https://example.com/oauth/authorize?client_id=<client id>&redirect_uri=<redirect uri>&response_type=code
```

- Ein Besucher, der nicht angemeldet ist, wird zuerst zur Anmeldung aufgefordert und kehrt dann zur Seite zurück.
- Der Benutzer lässt die App zu. Seit 6.0.15 braucht der Benutzer dafür die Richtlinie **OAuth / Authorize**
  (`oauth/authorize`), optional nach Siteaccess beschränkt; ohne sie leitet die Website mit `error=access_denied`
  weiter ([Richtlinie OAuth authorize](../../../bc/6.0/oauth-autorisierungsrichtlinie.md)). Geben Sie sie den Rollen der
  Personen, die die App benutzen.
- Fehler, bevor die Weiterleitungsadresse geprüft ist (unbekannter Client, falsche Adresse), antworten auf der Website
  mit `400` und leiten nicht weiter; spätere Fehler gehen als `error=...` an die Weiterleitungsadresse.
- Bei Erfolg leitet die Website an `<redirect uri>?code=<code>&expires_in=<seconds>` weiter.

## Schritt 3: den Code eintauschen

```bash
curl -s https://example.com/api/auth/v1/oauth/token \
     -d grant_type=authorization_code -d client_id='<client id>' -d client_secret='<client secret>' \
     -d code='<code>' -d redirect_uri='<redirect uri>'
```

Die Antwort trägt `access_token`, `refresh_token` und `expires_in` (den Wert von `rest.ini [OAuthSettings]
TokenTTL`, standardmäßig 3600 Sekunden). Auch der Code selbst läuft nach `TokenTTL` ab.

## Schritt 4: die API aufrufen

```text
Authorization: OAuth <access_token>
```

Die REST-Schicht akzeptiert für OAuth-Tokens auch `Authorization: Bearer <access_token>` sowie einen
Formularparameter `oauth_token` eines POST-Bodys. Ein Query-Parameter `oauth_token` wird abgelehnt, außer die Website
setzt `rest.ini [OAuthSettings] AllowTokenInQuery=enabled`: URLs werden von Servern und Proxys protokolliert.
Verwenden Sie den Header.

## Tokens erneuern

Wenn ein Aufruf mit `401` antwortet oder die Zeit von `expires_in` verstrichen ist, fordern Sie ein neues Token an:

```bash
curl -s https://example.com/api/auth/v1/oauth/token \
     -d grant_type=refresh_token -d client_id='<client id>' -d client_secret='<client secret>' \
     -d refresh_token='<refresh token>'
```

Die Antwort hat dieselben drei Schlüssel. Speichern Sie das neue Refresh-Token anstelle des alten: Ein Refresh-Token gilt
`rest.ini [OAuthSettings] RefreshTokenTTL` (standardmäßig 30 Tage) ab der Erneuerung, die es ausgestellt hat, und es
funktioniert nicht mehr, sobald der Benutzer deaktiviert oder gesperrt ist. Erneuern Sie einmal;
scheitert das, schicken Sie den Benutzer erneut über die Autorisierungsseite. Auch das Erneuern braucht das
Client-Secret, also läuft es ebenfalls im Backend.

## Grenzen der Implementierung

Geprüft in `kernel/private/rest/classes/controllers/oauth_token.php` und `kernel/private/modules/oauth/authorize.php`:

| Thema | Was der Code tut | Was zu tun ist |
|---|---|---|
| Grants | nur `authorization_code` und `refresh_token` | kein Password Grant, kein Client Credentials Grant |
| Client-Authentifizierung | `client_secret` ist am Token-Endpunkt Pflicht | auf einem Server halten |
| PKCE | nicht unterstützt (`code_verifier` wird nicht gelesen) | das Backend schützt den Austausch |
| `state` | wird nicht zurückgegeben („state, not implemented yet“) | einen Einmalwert in der App oder im Backend halten und gegen die Sitzung prüfen, die den Ablauf gestartet hat |
| `scope` | wird aus der Anfrage gelesen; einen anderen Scope zurückzugeben ist „not implemented yet“ | sich auf die Rollen des Benutzers verlassen, nicht auf OAuth-Scopes |
| `response_type=token` | der Code verarbeitet es ebenfalls und legt `access_token` und `refresh_token` in den Query-String der Weiterleitung | nicht aus einer App verwenden: Tokens in URLs geraten in Protokolle und den Verlauf |

Nichts davon ist für eine ältere OAuth2-Implementierung ungewöhnlich; es bedeutet nur, dass ein öffentlicher mobiler
Client nicht direkt mit dem Token-Endpunkt sprechen sollte.

## Verwandte Seiten

- [Ein Backend für Ihre App](ein-backend-fuer-ihre-app.md)
- [REST-API v1 und v2: OAuth2-Tokens](../../../anleitungen/rest-api-v1-und-v2.md#31-oauth2-tokens)
- [OAuth und API-Schlüssel](../../ersteller/anwendungsentwickler/oauth-und-api-schluessel.md#oauth2-für-anwendungen-von-drittanbietern)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
