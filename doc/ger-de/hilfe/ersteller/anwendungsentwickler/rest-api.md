---
title: Die REST-API
audience: app-creator, developer
category: app-creation
order: 330
summary: Die REST-Content-API nutzen und erweitern - die Routen von ezprestapi in v1 und v2, Authentifizierung mit OAuth2 oder persönlichen API-Schlüsseln, Rechte, curl-Beispiele, Konfiguration in rest.ini und ein eigener REST-Provider.
keywords: REST, API, Routen, API-Schlüssel, Inhaltsknoten, anlegen, löschen, REST-Provider, Anwendungsentwickler, ezprestapi, v1, v2, OAuth2, API keys, Bearer, routes, content node, create, delete, rest.ini, REST provider, app creator
type: help
---
# Die REST-API

Diese Seite richtet sich an Anwendungsentwickler, die Inhalte aus einem anderen Programm lesen oder schreiben: einer
mobilen App, einem Frontend, einer Integration. Die REST-Content-API stammt aus der Erweiterung `ezprestapi`; lesende
Aufrufe antworten in v1 und v2, schreibende nur in v2 ([REST-API v1 und v2](../../../anleitungen/rest-api-v1-und-v2.md)).

## Inhalt

1. [Routen](#routen)
2. [Authentifizierung](#authentifizierung)
3. [Beispiele](#beispiele)
4. [Rechte](#rechte)
5. [Konfiguration](#konfiguration)
6. [Ein eigener REST-Provider](#ein-eigener-rest-provider)

## Routen

Relativ zu `/api/ezp/<version>`:

| Methode | Pfad | v1 | v2 | Bewirkt |
|---|---|---|---|---|
| `GET` | `/content/node/<id>` | ja | ja | einen Knoten (`ResponseGroups=Metadata,Locations,Fields`, `OutputFormat=xhtml`) |
| `GET` | `/content/node/<id>/list(/offset/<n>)(/limit/<n>)` | ja | ja | die Kinder |
| `GET` | `/content/node/<id>/childrenCount` | ja | ja | die Anzahl der Kinder |
| `GET` | `/content/node/<id>/fields`, `/field/<identifier>` | ja | ja | Felder |
| `GET` | `/content/object/<id>`, `.../fields`, `.../field/<identifier>` | ja | ja | nach Objekt-ID |
| `POST` | `/content/node/create` | nein | ja | anlegen und veröffentlichen |
| `DELETE` | `/content/node/delete/<id>` | nein | ja | entfernen |

## Authentifizierung

| Zugangsdaten | Wofür |
|---|---|
| OAuth2-Token (`POST /api/auth/v1/oauth/token`, Grants `authorization_code` und `refresh_token`) | eine Anwendung, die für ihre Benutzer handelt; registrieren Sie sie unter **Setup > oAuth-Verwaltung** |
| Persönlicher API-Schlüssel (`Authorization: Bearer expk_...`) | das eigene Skript eines Benutzers ([Persönliche API-Schlüssel](../../../anleitungen/api-schluessel.md)) |

`rest.ini [Authentication] RequireAuthentication=enabled` ist die Voreinstellung ([OAuth und API-Schlüssel](oauth-und-api-schluessel.md)).

## Beispiele

```bash
export EXP_API_KEY='expk_...'
curl -s -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v1/content/node/2
curl -s -H "Authorization: Bearer $EXP_API_KEY" \
     "https://example.com/api/ezp/v1/content/node/<node id>/list/offset/0/limit/20?ResponseGroups=Fields"
curl -s -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v2/content/node/create \
     -d parentNodeID=<parent node id> -d classIdentifier=folder -d languageLocale=eng-US -d name='From a script'
```

Bewahren Sie Schlüssel in Umgebungsvariablen oder einem Geheimnisspeicher auf, niemals im Code.

## Rechte

Jeder lesende und schreibende Aufruf prüft die Richtlinien des Benutzers (`content/read`, `content/create`,
`content/edit`, `content/remove`), gleich mit welchen Zugangsdaten, über `expRestContentPermission` des Kernels.
Antworten: `200`, `201` für ein Anlegen, `400`, `401` ohne gültige Zugangsdaten, `403`, wenn der Benutzer es nicht darf,
`404` für einen unbekannten Knoten.

## Konfiguration

`settings/rest.ini` enthält das REST-Framework (Präfix, Routen, Versionen, Authentifizierung, den Antwort-Cache,
Ausgabe); `[OAuthSettings] TokenTTL` die Laufzeit der Tokens; `[ApiKeySettings]` die API-Schlüssel. Unter Apache mit
FastCGI stellen Sie sicher, dass der Header `Authorization` bei PHP ankommt.

## Ein eigener REST-Provider

Eine Erweiterung kann Routen hinzufügen: eine für ein Präfix registrierte Provider-Klasse mit Routentabelle und
Controllern, so wie `ezprestapi` `ezp7xRestApiProvider` für `ezp` und `ezpl` registriert. Die Registrierungspunkte
(REST-Provider, Routenfilter, Request-, Result- und Response-Filter) sind mit ihren Einstellungen in
[Erweiterungspunkte](../../../bc/6.0/rad-erweiterungspunkte.md#rest-provider) aufgeführt. Prüfen Sie die Rechte in jeder
Controller-Aktion; das Framework authentifiziert, es autorisiert nicht.

**Testen gegen eine öffentliche Demo.** Eine Demo läuft im Demo-Modus: Passwortänderungen und -zurücksetzungen für Administratorkonten antworten mit HTTP 403 („This installation is a public demo: ...“); alles andere funktioniert wie auf jeder Website. [FAQ zum Demo-Modus](../../demomodus-faq.md).

## Verwandte Seiten

- [OAuth und API-Schlüssel](oauth-und-api-schluessel.md)
- [Headless Exponential](headless-exponential.md)
- [Standard für APIs und Protokolle](../../../../eng-us/standards/apis-and-protocols.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
