---
title: GraphQL und GraphiQL
audience: dxp-developer
category: dxp-development
order: 27
summary: Die GraphQL-API der DXP (ibexa/graphql) - der Endpunkt, das Schema, das aus Ihren Inhaltstypen nach config/graphql/types/ibexa erzeugt wird, wann Sie es neu erzeugen, der Explorer GraphiQL in der Entwicklung, Authentifizierung und gute Gewohnheiten bei Abfragen.
keywords: GraphQL, GraphiQL, ibexa/graphql, overblog/graphiql-bundle, /graphql, /graphiql, ibexa:graphql:generate-schema, Schema, Inhaltstypen, JWT, Headless, schema, content types, headless
type: help
---
# GraphQL und GraphiQL

Diese Seite richtet sich an Entwickler, die Inhalte mit GraphQL abfragen. Die DXP liefert `ibexa/graphql` und den
Explorer GraphiQL (`overblog/graphiql-bundle`) mit. Das Schema ist "auto-generated from the content type model"
(Anleitung des Skeletons, Abschnitt 11).

## Inhalt

1. [Der Endpunkt](#der-endpunkt)
2. [Das Schema](#das-schema)
3. [Der Explorer](#der-explorer)
4. [Authentifizierung](#authentifizierung)
5. [Eine erste Abfrage](#eine-erste-abfrage)
6. [Gute Gewohnheiten](#gute-gewohnheiten)

## Der Endpunkt

`/graphql`, festgelegt in `config/routes/graphql.yaml` (Route `overblog_graphql_endpoint`). Clients senden
`POST`-Anfragen mit einem JSON-Körper, der die Abfrage enthält.

## Das Schema

Erzeugen Sie es nach der Installation und nach jeder Änderung an Inhaltstypen oder Feldtypen:

```bash
php bin/console ibexa:graphql:generate-schema
php bin/console cache:clear
```

Der Befehl schreibt YAML-Typdateien in `config/graphql/types/ibexa/` Ihres Projekts (das Verzeichnis, das die
Erweiterungsklasse des Pakets festlegt). Committen Sie diese Dateien: Sie sind Teil Ihrer API. Ein Inhaltstyp wird zu
einem GraphQL-Typ und seine Felder zu typisierten Feldern; die Umbenennung eines Feldbezeichners ändert also Ihre API.

Der Befehl trägt noch seinen Upstream-Namen; es gibt keinen Alias `exponential:` dafür
([Konsolenbefehle](konsolenbefehle-und-umbenennungen.md)).

## Der Explorer

In `APP_ENV=dev` lädt die Routendatei `config/routes/dev/graphiql.yaml` die Routen des GraphiQL-Bundles; in einem
installierten v5-Projekt antwortet der Explorer unter **`/graphiql`**. Die Anleitung des Skeletons nennt
`/graphql/explorer`; prüfen Sie Ihr Projekt mit `php bin/console debug:router | grep graphiql`. In der Produktion ist
der Explorer nicht geroutet.

## Authentifizierung

Ohne Zugangsdaten laufen Abfragen als anonymer Benutzer und sehen, was diese Rolle lesen darf. Apps verwenden JWT: Die
`security.yaml` des Rezepts hat dafür eine auskommentierte Firewall `ibexa_jwt_graphql`
([JWT-Authentifizierung](jwt-authentifizierung.md)).

## Eine erste Abfrage

Öffnen Sie den Explorer, sehen Sie im Dokumentationsbereich die Typen an, die Ihr Inhaltsmodell erzeugt hat, und
beginnen Sie von dort. Die Namen von Typen und Feldern hängen von Ihren Inhaltstypen ab; kopieren Sie sie also aus dem
Bereich statt aus einem Beispiel.

## Gute Gewohnheiten

- Fragen Sie nur die Felder ab, die Sie brauchen; GraphQL macht das leicht, und es hält Antworten klein.
- Erzeugen und committen Sie das Schema in derselben Änderung wie eine Änderung eines Inhaltstyps, damit Code und API
  Schritt halten.
- Testen Sie Abfragen in der CI gegen ein Projekt, das aus Ihren Ausgangsdaten installiert wurde.
- Vergleichen Sie bei Integrationen, die viel schreiben, mit der REST-API ([Die REST-API](die-rest-api.md)).

## Verwandte Seiten

- [Headless mit der DXP](headless-mit-der-dxp.md)
- [Inhalte, Inhaltstypen und Platzierungen](inhaltstypen-und-platzierungen.md)
- [JWT-Authentifizierung](jwt-authentifizierung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
