---
title: Authentifizierung für Apps
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 200
summary: Die Einstiegsseite der Authentifizierungsseiten für App-Entwickler - die drei Arten von Zugangsdaten, die Exponential von Programmen annimmt (OAuth2-Tokens, persönliche API-Schlüssel, expservices-Tokens), die Voreinstellung, dass jeder REST-Aufruf einen Benutzer braucht, und die Seiten in diesem Ordner.
keywords: Authentifizierung, Anmeldung, OAuth2, Token, API-Schlüssel, expk, expt, expservices-Token, RequireAuthentication, Zugangsdaten, anmelden, Mobile App, authentication, API key, expservices token, credential, sign in, mobile app
type: help
---
# Authentifizierung für Apps

Dieser Ordner richtet sich an App-Entwickler, die eine App oder ihre Benutzer bei Exponential anmelden müssen. Er
erklärt, welche Zugangsdaten der Server annimmt, wie Sie sie jeweils erhalten und senden und wie Sie sie auf einem
Gerät sicher aufbewahren.

## Inhalt

1. [Die Voreinstellung: Jeder Aufruf braucht einen Benutzer](#die-voreinstellung-jeder-aufruf-braucht-einen-benutzer)
2. [Drei Arten von Zugangsdaten](#drei-arten-von-zugangsdaten)
3. [Seiten in diesem Ordner](#seiten-in-diesem-ordner)

## Die Voreinstellung: Jeder Aufruf braucht einen Benutzer

`rest.ini [Authentication] RequireAuthentication=enabled` ist die Voreinstellung: Jede REST-Inhaltsroute braucht einen
Benutzer. Ein Administrator kann den Wert auf `disabled` setzen; dann läuft jede Anfrage als `DefaultUserID` (oder als
anonymer Benutzer), und die Berechtigungsprüfungen gelten weiterhin
([REST-API v1 und v2: Konfiguration](../../../anleitungen/rest-api-v1-und-v2.md#5-konfiguration)). Das ist eine
Serverentscheidung mit Folgen für jeden Client; verlangen Sie sie nicht nur, um in einer App die Anmeldung zu sparen.

Die Remote-Dienste arbeiten anders: Dienste mit dem Zugriff `public` beantworten anonyme Aufrufe, alle anderen brauchen
einen angemeldeten Benutzer ([expservices aus einer App aufrufen](../json-endpunkte/expservices-aus-einer-app-aufrufen.md)).

## Drei Arten von Zugangsdaten

| Zugangsdaten | Sehen so aus | Gesendet als | Angenommen von | Lesen |
|---|---|---|---|---|
| OAuth2-Zugriffstoken | eine alphanumerische Zeichenkette | `Authorization: OAuth <token>` oder `Bearer <token>` | REST | [OAuth2-Autorisierungscode-Ablauf](oauth2-autorisierungscode-ablauf.md) |
| Persönlicher API-Schlüssel | `expk_<12 characters>_<40 characters>` | `Authorization: Bearer expk_...` | REST | [Persönliche API-Schlüssel für Werkzeuge](persoenliche-api-schluessel-fuer-werkzeuge.md) |
| expservices-Token | `expt_` und 48 Hexadezimalzeichen | `Authorization: Bearer expt_...` und `X-Exp-Token: expt_...` | expservices | [expservices-Tokens in Apps](expservices-tokens-in-apps.md) |

Statt OAuth lässt sich für REST auch HTTP-Basic-Authentifizierung konfigurieren
(`AuthenticationStyle=ezpRestBasicAuthStyle`), aber dann wird bei jeder Anfrage ein Passwort gesendet; dieser Abschnitt
empfiehlt das für Apps nicht.

## Seiten in diesem Ordner

| Seite | Beantwortet |
|---|---|
| [Zugangsdaten auswählen](zugangsdaten-auswaehlen.md) | Welche Zugangsdaten für welche Art von App |
| [OAuth2-Autorisierungscode-Ablauf](oauth2-autorisierungscode-ablauf.md) | Den Client registrieren, die Autorisierungsseite, der Token-Endpunkt, das Erneuern |
| [Ein Backend für Ihre App](ein-backend-fuer-ihre-app.md) | Warum das Client-Secret auf einem Server bleibt und wie Sie diesen Server bauen |
| [Persönliche API-Schlüssel für Werkzeuge](persoenliche-api-schluessel-fuer-werkzeuge.md) | Schlüssel für Ihre eigenen Skripte, Tests und internen Werkzeuge |
| [expservices-Tokens in Apps](expservices-tokens-in-apps.md) | Bei den Remote-Diensten anmelden und ein Token erzeugen |
| [Geheimnisse auf Geräten speichern](geheimnisse-auf-geraeten-speichern.md) | Keychain, Keystore und was Sie nie speichern |
| [Der Authorization-Header und Proxys](der-authorization-header-und-proxys.md) | Warum gültige Zugangsdaten trotzdem als nichts ankommen können |

## Verwandte Seiten

- [OAuth und API-Schlüssel](../../ersteller/anwendungsentwickler/oauth-und-api-schluessel.md)
- [Apps und API-Schlüssel (für Unternehmen)](../../unternehmen/mobil-und-headless/apps-und-api-schluessel.md)
- [REST-API v1 und v2: Authentifizierung](../../../anleitungen/rest-api-v1-und-v2.md#3-authentifizierung)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
