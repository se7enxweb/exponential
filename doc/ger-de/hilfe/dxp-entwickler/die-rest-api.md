---
title: Die REST-API
audience: dxp-developer
category: dxp-development
order: 25
summary: Die REST-API v2 der DXP (ibexa/rest) für Entwickler - wo sie liegt und wie Sie das Präfix prüfen, was sie abdeckt, wie sich Clients authentifizieren (Session, JWT, Basic), CORS und wie Sie sie mit curl ausprobieren.
keywords: REST-API, ibexa/rest, REST v2, api/ibexa/v2, Pfadpräfix, JWT, CORS, curl, Headless, Inhalte, Platzierungen, Authentifizierung, Accept-Header, REST API, path prefix, content, locations, authentication, Accept header
type: help
---
# Die REST-API

Diese Seite richtet sich an Entwickler, die Inhalte aus einer anderen Anwendung lesen oder schreiben: einer mobilen App,
einem JavaScript-Frontend, einer Integration. Die DXP liefert die REST-API v2 (`ibexa/rest`) mit, mit den Worten des
Skeletons "a headless CRUD REST API".

## Inhalt

1. [Wo sie liegt](#wo-sie-liegt)
2. [Was sie abdeckt](#was-sie-abdeckt)
3. [Authentifizierung](#authentifizierung)
4. [Ausprobieren](#ausprobieren)
5. [CORS](#cors)
6. [Bevor Sie einen Client bauen](#bevor-sie-einen-client-bauen)

## Wo sie liegt

Die Routen werden unter dem Präfix-Parameter `ibexa.rest.path_prefix` importiert (siehe
`config/routes/ibexa_rest.yaml`). Im installierten v5-Paket ist sein Standard **`/api/ibexa/v2`**. Anleitung und README
des Skeletons zeigen `/api/ezp/v2/`, das Präfix der älteren Linien. Prüfen Sie, was Ihr Projekt wirklich ausliefert:

```bash
php bin/console debug:container --parameter=ibexa.rest.path_prefix
php bin/console debug:router | grep -m5 "ibexa.rest"
```

Verwenden Sie in jedem Client den Wert, den Ihr Projekt ausgibt, und halten Sie ihn in einem einzigen
Konfigurationswert des Clients.

## Was sie abdeckt

Die REST-API stellt das Inhalts-Repository bereit: Inhaltselemente und ihre Versionen, Inhaltstypen, Platzierungen und
den Baum, Sektionen, Objektzustände, Sprachen, Benutzer, Benutzergruppen, Rollen, URL-Aliase, Papierkorb, Lesezeichen
und Suche (Views). Jede Ressource hat ihren eigenen Medientyp, und Clients fordern mit dem Header `Accept` JSON oder XML
an.

## Authentifizierung

| Methode | Wann | Einrichtung |
|---|---|---|
| Session-Cookie | ein Frontend auf demselben Host, das Benutzer anmeldet | die Session der Website |
| JWT | Apps und Dienste | `lexik/jwt-authentication-bundle` mit dem Schlüsselpaar; die JWT-Firewalls einschalten ([JWT-Authentifizierung](jwt-authentifizierung.md)) |
| HTTP Basic | nur Tests und interne Skripte, über HTTPS | eine auskommentierte Firewall in `security.yaml` |

Anonyme Anfragen sehen, was die Rolle Anonymous lesen darf.

## Ausprobieren

```bash
BASE="https://127.0.0.1:8000/api/ibexa/v2"       # use your prefix
curl -s -H "Accept: application/vnd.ibexa.api.Root+json" "$BASE/"
curl -s -H "Accept: application/vnd.ibexa.api.Location+json" "$BASE/content/locations/1/2"
```

Die Root-Ressource listet die Einstiegspunkte der API. Weicht der Name eines Medientyps in Ihrer Version ab, zeigt die
Antwort der Root-Ressource die Namen, die sie verwendet.

## CORS

Browser auf einem anderen Origin brauchen CORS. Die Anleitung des Skeletons setzt `CORS_ALLOW_ORIGIN` in `.env.local`;
erweitern Sie es nur auf die Origins Ihrer eigenen Clients
([Umgebungsvariablen](umgebungsvariablen.md#suche-cache-e-mail-und-der-rest)).

## Bevor Sie einen Client bauen

- Entscheiden Sie, was ein Client schreiben darf; geben Sie seinem Benutzer eine Rolle mit nur diesen Richtlinien.
- Planen Sie das Caching: anonyme `GET`-Antworten laufen durch den HTTP-Cache ([HTTP-Cache](http-cache-und-varnish.md)).
- Halten Sie das API-Präfix und die Medientypen aus fest codierten Zeichenketten heraus, die über den Client verstreut
  sind.
- Vergleichen Sie bei Frontends, die vor allem lesen, mit GraphQL ([GraphQL und GraphiQL](graphql-und-graphiql.md)).

## Verwandte Seiten

- [JWT-Authentifizierung](jwt-authentifizierung.md)
- [Headless mit der DXP](headless-mit-der-dxp.md)
- [Headless mit der REST-API (für Organisationen)](../unternehmen/mobil-und-headless/headless-mit-der-rest-api.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
