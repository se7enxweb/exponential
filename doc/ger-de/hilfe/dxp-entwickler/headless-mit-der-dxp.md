---
title: Headless mit der DXP
audience: dxp-developer
category: dxp-development
order: 28
summary: Ein Headless-Projekt auf Exponential Platform DXP planen - welche API für welchen Client, Authentifizierung je Client, CORS, Caching, Vorschauen, Bilder und das Inhaltsmodell als API-Vertrag - mit einer Checkliste für das erste Release.
keywords: Headless, entkoppelt, REST-API, GraphQL, JWT, CORS, mobile App, JavaScript-Frontend, API-Vertrag, Vorschau, Bildvariationen, Caching, Checkliste, headless, decoupled, mobile app, API contract, preview, image variations, checklist
type: help
---
# Headless mit der DXP

Diese Seite richtet sich an Entwickler und Architekten, die die DXP als Inhalts-Backend für andere Clients verwenden:
eine Single-Page-Anwendung, eine mobile App, einen Generator für statische Websites, einen Kiosk. Redakteure arbeiten
weiter in `/adminui/`; Ihre Clients lesen (und schreiben manchmal) über die APIs.

## Inhalt

1. [Welche API für welchen Client](#welche-api-für-welchen-client)
2. [Das Inhaltsmodell ist Ihre API](#das-inhaltsmodell-ist-ihre-api)
3. [Authentifizierung je Client](#authentifizierung-je-client)
4. [Bilder](#bilder)
5. [Caching](#caching)
6. [Vorschauen und Links](#vorschauen-und-links)
7. [Checkliste für das erste Release](#checkliste-für-das-erste-release)

## Welche API für welchen Client

| Der Client braucht | Wählen Sie | Warum |
|---|---|---|
| Genau die Felder eines Bildschirms, wenige Anfragen | GraphQL | eine Abfrage, die Felder, die Sie anfordern |
| Inhalte anlegen und aktualisieren, Dateien hochladen | REST | vollständiges Anlegen, Aktualisieren und Löschen im Repository |
| Eine serverseitig gerenderte Website | Twig in der DXP selbst | gar keine API |

Beide lassen sich nebeneinander verwenden.

## Das Inhaltsmodell ist Ihre API

Bezeichner von Inhaltstypen und Feldern werden zu GraphQL-Typen und -Feldern und erscheinen in REST-Antworten. Stimmen
Sie sie mit den Entwicklern der Clients ab, bevor Redakteure Inhalte eintragen, versionieren Sie Änderungen wie eine API
und erzeugen Sie das GraphQL-Schema in derselben Änderung neu ([GraphQL](graphql-und-graphiql.md#das-schema)).

## Authentifizierung je Client

- Öffentliche Inhalte nur zum Lesen: anonyme Anfragen, beschränkt durch die Rolle Anonymous.
- Apps und Dienste: ein Benutzer je Client, eine Rolle mit nur den nötigen Richtlinien, JWT-Tokens
  ([JWT-Authentifizierung](jwt-authentifizierung.md)).
- Browser auf einem anderen Origin: Erlauben Sie genau deren Origins mit `CORS_ALLOW_ORIGIN`
  ([Die REST-API, CORS](die-rest-api.md#cors)).

## Bilder

Clients sollten Variationen anfordern, nie Originale. Legen Sie Variationen für die Größen fest, die Ihre Clients zeigen
([Bildvariationen](bildvariationen.md)); senden Sie Breite und Höhe mit.

## Caching

Anonyme `GET`-Anfragen laufen durch den HTTP-Cache, den die DXP bei Inhaltsänderungen leert
([HTTP-Cache und Varnish](http-cache-und-varnish.md)). Authentifizierte Anfragen sind persönlich; speichern Sie sie im
Client zwischen, nicht in einem gemeinsamen Proxy.

## Vorschauen und Links

Ein entkoppeltes Frontend braucht eigene Routen für Inhalte. Entscheiden Sie früh, wie eine Platzierung auf eine URL des
Clients abgebildet wird (zum Beispiel über den URL-Alias, den die API liefert) und wie Redakteure Entwürfe in der
Vorschau sehen; die Vorschau der DXP in `/adminui/` rendert mit den Twig-Templates der Website, nicht mit Ihrem Client.

## Checkliste für das erste Release

- [ ] Das API-Präfix wird in jedem Client aus der Konfiguration gelesen ([Wo sie liegt](die-rest-api.md#wo-sie-liegt)).
- [ ] Jeder Client hat einen eigenen Benutzer und eine minimale Rolle.
- [ ] JWT-Schlüssel sind in jeder Umgebung erzeugt und gesichert.
- [ ] CORS listet nur Ihre Origins.
- [ ] Das GraphQL-Schema ist committet und passt zu den Inhaltstypen.
- [ ] Für jede Größe, die ein Client zeigt, gibt es Bildvariationen.
- [ ] Anonyme Antworten werden zwischengespeichert und beim Veröffentlichen geleert.

## Verwandte Seiten

- [Headless mit der REST-API (für Organisationen)](../unternehmen/mobil-und-headless/headless-mit-der-rest-api.md)
- [Die REST-API](die-rest-api.md)
- [GraphQL und GraphiQL](graphql-und-graphiql.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
