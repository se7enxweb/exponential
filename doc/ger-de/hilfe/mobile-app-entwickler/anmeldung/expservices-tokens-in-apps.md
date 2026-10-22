---
title: expservices-Tokens in Apps
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 250
summary: Eine App bei den Remote-Diensten von expservices authentifizieren - Sitzungsanmeldung mit expsession::login, ein persönliches Token (expt_) mit expsession::tokenCreate erzeugen, es als Bearer und X-Exp-Token senden, Schreibzugriffe ohne Formular-Token, Tokens auflisten und widerrufen, und was ein 401 bedeutet.
keywords: expservices, Token, expt, expsession, login, tokenCreate, tokenList, tokenRevoke, whoami, X-Exp-Token, Bearer, Sitzungscookie, Formular-Token, 401, Mobile App, token, session cookie, form token, mobile app
type: help
---
# expservices-Tokens in Apps

Diese Seite richtet sich an App-Entwickler, deren App die Remote-Dienste (`expservices`) aufruft, zum Beispiel für die
Suche, Bildalias-URLs oder Aktualisierungen. Die Remote-Dienste haben eigene persönliche Tokens, getrennt von
REST-Schlüsseln und OAuth-Tokens. Quellen:
[Remote-Dienste und Apps, Abschnitt 5](../../../anleitungen/entfernte-dienste-und-apps.md#5-ein-persönliches-api-token-für-skripte-und-apps-3-minuten)
und die [expservices-Referenz](../../../bc/6.0/backend-dienste-ueber-ezjscore.md#authentifizierung-mit-einem-api-token).

## Inhalt

1. [Drei Arten des Aufrufs](#drei-arten-des-aufrufs)
2. [Mit einer Sitzung anmelden](#mit-einer-sitzung-anmelden)
3. [Ein Token erzeugen](#ein-token-erzeugen)
4. [Das Token senden](#das-token-senden)
5. [Tokens verwalten](#tokens-verwalten)
6. [Wenn das Token ungültig wird](#wenn-das-token-ungültig-wird)

## Drei Arten des Aufrufs

| Art | Wer | Für |
|---|---|---|
| Anonym | niemand angemeldet | Dienste mit dem Zugriff `public` |
| Sitzungscookie | nach `expsession::login` | die erste Anmeldung; Browser auf derselben Website |
| Persönliches Token `expt_...` | der Benutzer des Tokens | Apps und Skripte: kein Cookie, kein Formular-Token |

## Mit einer Sitzung anmelden

```bash
curl -c jar -d 'username=<login>' --data-urlencode 'password=<password>' \
     'https://example.com/ezjscore/call/expsession::login?ContentType=json'
curl -b jar 'https://example.com/ezjscore/call/expsession::whoami?ContentType=json'
```

Die Antwort von `login` setzt das Sitzungscookie und trägt das Formular-Token der neuen Sitzung (`data.token`). Eine
native App behält das Cookie nur für den nächsten Schritt in einem Cookie-Speicher.

## Ein Token erzeugen

Ein Token kann nur über eine Sitzungsanmeldung erzeugt werden, nicht mit einem anderen Token:

```bash
curl -b jar -H 'X-CSRF-Token: <form token>' --data-urlencode 'name=Android app on Pixel 8' \
     -d expires_in_days=90 'https://example.com/ezjscore/call/expsession::tokenCreate?ContentType=json'
```

`expires_in_days` liegt zwischen 1 und 365, Standard ist 90. Die Antwort trägt das Token ein einziges Mal, in
`data.token`: `expt_` gefolgt von 48 Hexadezimalzeichen. Gespeichert wird nur sein SHA-256-Hash. Legen Sie das Token
sofort im sicheren Speicher des Geräts ab ([Geheimnisse auf Geräten speichern](geheimnisse-auf-geraeten-speichern.md)), beenden
Sie dann die Sitzung mit `expsession::logout` und vergessen Sie das Passwort: Die App braucht es erst wieder, wenn das
Token ungültig wird.

Antwortet `tokenCreate` mit einem Datenbankfehler, fehlt der Installation die Tabelle `expservices_token`; das ist eine
Aufgabe für ihren Administrator
([Remote-Dienste und Apps](../../../anleitungen/entfernte-dienste-und-apps.md#5-ein-persönliches-api-token-für-skripte-und-apps-3-minuten)).

## Das Token senden

```text
Authorization: Bearer expt_...
X-Exp-Token: expt_...
```

Senden Sie beide, so wie es die mitgelieferten Clients tun: Manche Proxys und FastCGI-Konfigurationen reichen
`Authorization` nicht an PHP weiter, und `X-Exp-Token` war der Header, der auf der Referenzinstallation funktioniert
hat ([Der Authorization-Header und Proxys](der-authorization-header-und-proxys.md)). Das Token meldet die Anfrage für genau
diese eine Anfrage als seinen Benutzer an; es wird keine Sitzung angelegt. Schreibzugriffe brauchen weiterhin `POST`,
aber kein Formular-Token.

## Tokens verwalten

| Aufruf | Bewirkt |
|---|---|
| `expsession::tokenList` | die Tokens des Aufrufers: ID, Name, Hinweis, erstellt, zuletzt verwendet, Ablauf, widerrufen; nie das Token selbst |
| `expsession::tokenRevoke` (`POST id`) | widerruft eines der Tokens des Aufrufers |

Geben Sie jedem Gerät ein eigenes Token mit einem Namen, der das Gerät nennt, und bieten Sie in der App ein
„Abmelden“ an, das das Token mit `tokenRevoke` widerruft. Erzeugen, Widerrufen und Ablehnungen stehen im Prüfprotokoll
als `access.expservices.token.create`, `.revoke` und `.failed`.

## Wenn das Token ungültig wird

Ein unbekanntes, widerrufenes oder abgelaufenes Token oder eines, dessen Benutzer deaktiviert ist, antwortet bei jedem
Dienst mit `401`, auch bei den öffentlichen, sodass die App es bemerkt, statt unbemerkt anonym zu werden. Löschen Sie bei
`401` das gespeicherte Token und schicken Sie den Benutzer zur erneuten Anmeldung.

## Verwandte Seiten

- [expservices aus einer App aufrufen](../json-endpunkte/expservices-aus-einer-app-aufrufen.md)
- [Zugangsdaten auswählen](zugangsdaten-auswaehlen.md)
- [Remote-Dienste mit expservices](../../ersteller/anwendungsentwickler/entfernte-dienste-expservices.md#authentifizierung)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
