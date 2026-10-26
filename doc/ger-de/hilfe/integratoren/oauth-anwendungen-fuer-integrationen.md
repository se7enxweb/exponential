---
title: OAuth-Anwendungen für Integrationen
audience: integrator, developer, admin
category: integration
order: 60
summary: Wie eine externe Anwendung Benutzer über den OAuth-2.0-Autorisierungsserver von Exponential anmeldet und für sie handelt - die Anwendung registrieren, der Authorization-Code-Ablauf, Tokens erneuern, die Richtlinie oauth/authorize und die Lücken, um die ein Integrator herum entwerfen muss.
keywords: OAuth, OAuth 2.0, Authorization Code, Refresh Token, oauthadmin, Client-ID, Client-Geheimnis, Redirect-URI, oauth/authorize, Token-Endpunkt, TokenTTL, PKCE, state, client_credentials, authorization code, refresh token, client id, client secret, redirect uri, token endpoint
type: help
---
# OAuth-Anwendungen für Integrationen

Diese Seite ist für Entwickler von Anwendungen, mit denen sich **viele Menschen** mit ihrem Exponential-Konto anmelden
und als sie selbst handeln: eine mobile App für Reporter, ein Partnerportal, ein Intranet-Werkzeug. Exponential ist
ein OAuth-2.0-Autorisierungsserver für seine REST-API (`kernel/private/modules/oauth/`,
`kernel/private/rest/classes/oauth/`). Für ein eigenes Programm, das als ein Dienstkonto handelt, ist ein
[persönlicher API-Schlüssel](persoenliche-api-schluessel-fuer-integrationen.md) einfacher.

## Inhalt

1. [Der Ablauf](#der-ablauf)
2. [Die Anwendung registrieren](#die-anwendung-registrieren)
3. [Tokens erhalten und erneuern](#tokens-erhalten-und-erneuern)
4. [Wer eine Anwendung autorisieren darf](#wer-eine-anwendung-autorisieren-darf)
5. [Lücken, um die herum Sie entwerfen](#lücken-um-die-herum-sie-entwerfen)
6. [Checkliste](#checkliste)

## Der Ablauf

```text
 your app                         Exponential (example.com)
 --------                         -------------------------
 1. send the user to  ----------> /oauth/authorize?client_id=..&redirect_uri=..&response_type=code
                                  the user signs in and allows the app (needs oauth/authorize)
 2. receive ?code=..  <---------- redirect to redirect_uri
 3. POST code ------------------> /api/auth/v1/oauth/token  (grant_type=authorization_code)
 4. receive tokens    <---------- access_token, refresh_token, expires_in
 5. call the API -----------------> /api/ezp/v2/...  Authorization: Bearer <access_token>
 6. after expiry: POST ---------> /api/auth/v1/oauth/token  (grant_type=refresh_token)
```

In Worten: Ihre App schickt den Benutzer zu `/oauth/authorize`, er meldet sich an und erlaubt der App den Zugriff;
die App erhält einen Code, tauscht ihn per `POST` am Token-Endpunkt gegen Tokens, ruft mit dem Zugriffstoken die API
auf und erneuert es nach Ablauf mit dem Refresh-Token.

## Die Anwendung registrieren

1. Melden Sie sich als Administrator in der Administration an, und öffnen Sie **Setup > oAuth-Verwaltung**
   (`/oauthadmin/list`).
2. Drücken Sie **Neue Anwendung**, und geben Sie ihren Namen und die Weiterleitungsadresse (den Endpunkt) Ihrer
   Anwendung an.
3. Die Anwendung erhält eine zufällige Client-ID und ein Geheimnis. Das Geheimnis ist auf der Seite der Anwendung
   eingeklappt, bis **Das Geheimnis zeigen** gedrückt wird; kopieren Sie es in den Geheimnisspeicher Ihrer Anwendung.

Der Reiter **Anwendungen** zeigt pro Anwendung den Besitzer, den Endpunkt, wie viele Benutzer sie autorisiert haben und
wie viele ihrer Tokens noch gültig sind. Wenn Sie eine Anwendung entfernen, werden auch ihre Autorisierungen, Tokens
und Codes entfernt: So schneiden Sie eine Anwendung sofort ab.

## Tokens erhalten und erneuern

Tauschen Sie den Code (die Parameter sind Formularfelder eines `POST`):

```bash
curl -s https://example.com/api/auth/v1/oauth/token \
     -d grant_type=authorization_code -d client_id='<client id>' -d client_secret='<client secret>' \
     -d code='<code>' -d redirect_uri='<redirect uri>'
```

Erneuern Sie, bevor oder wenn ein Aufruf mit `401` `expired_token` antwortet:

```bash
curl -s https://example.com/api/auth/v1/oauth/token \
     -d grant_type=refresh_token -d client_id='<client id>' -d client_secret='<client secret>' \
     -d refresh_token='<refresh token>'
```

Das Zugriffstoken lebt `rest.ini [OAuthSettings] TokenTTL` Sekunden (standardmäßig 3600), das Refresh-Token
`RefreshTokenTTL` Sekunden (standardmäßig 30 Tage) ab der Erneuerung, die es ausgestellt hat. Senden Sie das
Zugriffstoken als `Authorization: Bearer <token>`; ein Token im Query-String wird abgelehnt, außer bei
`AllowTokenInQuery=enabled`. Jede Anfrage läuft als der Benutzer, der die Anwendung erlaubt hat, mit dessen
Richtlinien, und funktioniert nicht mehr, sobald dieser Benutzer deaktiviert oder durch fehlgeschlagene Anmeldungen
gesperrt ist.

## Wer eine Anwendung autorisieren darf

Der Benutzer braucht die Richtlinie **OAuth / Autorisieren** (`oauth/authorize`). Ohne sie leitet die Website mit
`error=access_denied` zurück. Geben Sie die Richtlinie nur den Rollen, deren Mitglieder die Anwendung nutzen sollen
([Hinweis zum Upgrade](../../bc/6.0/oauth-autorisierungsrichtlinie.md)). Ein unbekannter Client oder eine `redirect_uri`, die
nicht der Endpunkt des Clients ist, bekommt `400 Bad Request` ohne Weiterleitung.

## Lücken, um die herum Sie entwerfen

Aus [API- und Protokollstandards](../../standards/apis-und-protokolle.md#oauth-20-rfc-6749-und-bearer-tokens-rfc-6750):

| Lücke | Folge | Abhilfe |
|---|---|---|
| `state` wird nicht an den Client zurückgegeben | kein eingebauter Schutz der Weiterleitung gegen Cross-Site Request Forgery | binden Sie den Ablauf an Ihre eigene Sitzung (ein Wert, den Sie vor der Weiterleitung speichern und mit dem erhaltenen Code abgleichen) |
| Kein PKCE | ein öffentlicher Client (eine mobile App) kann nicht beweisen, dass er den Ablauf gestartet hat | den Codetausch auf Ihrem Server halten, nie in der App |
| Nur die Grants `authorization_code` und `refresh_token` | kein `client_credentials` für Maschine-zu-Maschine | einen persönlichen API-Schlüssel eines Dienstkontos verwenden |
| `response_type=token` (implizit) wird noch akzeptiert | Tokens im Query-String | nicht verwenden |
| Kein Endpunkt für Widerruf oder Introspektion | ein Token kann vom Client nicht widerrufen werden | die Anwendung entfernen oder die Lebensdauer des Tokens abwarten; `TokenTTL` kurz halten |
| Die Token-Antwort hat kein `token_type` | strenge Client-Bibliotheken können sich beschweren | das Token als Bearer-Token behandeln |

## Checkliste

- [ ] Die Anwendung ist mit der genauen Weiterleitungsadresse jeder Umgebung registriert.
- [ ] Das Geheimnis ist auf der Serverseite der Anwendung gespeichert, nie in einer mobilen App oder einem Browser.
- [ ] `oauth/authorize` ist nur den Rollen gegeben, die die Anwendung nutzen sollen.
- [ ] Codetausch und Erneuerung geschehen auf dem Server; Tokens werden pro Benutzer gehalten.
- [ ] `401` wird durch Erneuern behandelt, und eine fehlgeschlagene Erneuerung, indem der Benutzer erneut durch den
      Ablauf geschickt wird.
- [ ] HTTPS auf der Website und auf der Weiterleitungsadresse.

## Verwandte Seiten

- [REST-API v1 und v2, Authentifizierung](../../anleitungen/rest-api-v1-und-v2.md#3-authentifizierung)
- [Zugangsdaten für eine Integration wählen](zugangsdaten-fuer-eine-integration-waehlen.md)
- [Die REST-API für Integratoren](rest-api-fuer-integratoren.md)
- [OAuth und API-Schlüssel für App-Ersteller](../ersteller/anwendungsentwickler/oauth-und-api-schluessel.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
