---
title: Aussagen, die Sie vermeiden sollten
audience: dxp-developer
category: dxp-development
order: 47
summary: Aussagen über Exponential Platform DXP, die richtig klingen, aber für ihre Paketliste oder ihren installierten Code nicht stimmen - Layout-Editor, Workflows, Planung, Legacy-Kernel, URLs aus älteren Anleitungen, Twig-Funktionen "Exponential" - mit der richtigen Aussage und ihrer Quelle.
keywords: Aussagen, Genauigkeit, Angebote, Marketing, Layout-Editor, Workflow, Scheduler, Legacy-Kernel, REST-Präfix, GraphiQL, SOAP, Oracle, Funktionsliste, ehrlich, claims, accuracy, proposals, layouts editor, legacy kernel, REST prefix, feature list, honest
type: help
---
# Aussagen, die Sie vermeiden sollten

Diese Seite richtet sich an Entwickler, Architekten und alle, die ein Angebot, ein README oder einen Vortrag über
Exponential Platform DXP schreiben. Jede Zeile ist eine Aussage, die gemacht wurde oder leicht gemacht wird, mit der
richtigen Aussage.

## Inhalt

1. [Warum es diese Seite gibt](#warum-es-diese-seite-gibt)
2. [Funktionen](#funktionen)
3. [Technische Details](#technische-details)
4. [Namen und Beziehungen](#namen-und-beziehungen)
5. [Wie Sie eine Aussage prüfen](#wie-sie-eine-aussage-prüfen)

## Warum es diese Seite gibt

Die DXP ist durch die Pakete festgelegt, die ihr Metapaket verlangt. Das README des Skeletons enthält außerdem eine
lange, allgemeine Liste "Main Exponential Platform DXP Features", die die Familie über die Jahre angeboten hat; manche
Punkte darin beschreiben Exponential 6 oder andere Distributionen, nicht die Paketliste der DXP. Ein Angebot, das auf
dieser Liste aufbaut, verspricht Dinge, die der Kunde nicht erhält.

## Funktionen

| Vermeiden | Sagen Sie stattdessen | Quelle |
|---|---|---|
| "Redakteure bauen Seiten visuell mit Blöcken." | Die DXP liefert nur den Layouts Core, keinen Editor; Nexus und Exponential 6 haben Seitenbaukästen | [Layouts Core in der DXP](layouts-core-in-der-dxp.md) |
| "Ein Freigabe-Workflow für Inhalte und zeitgesteuertes Veröffentlichen sind enthalten." | Kein Workflow- oder Scheduler-Paket ist in der DXP | [Was die DXP mitliefert](was-die-dxp-mitliefert-und-was-nicht.md#was-sie-nicht-mitliefert) |
| "Führt Ihre Erweiterungen für Exponential 6 aus." | Kein Legacy-Kernel; verwenden Sie Legacy oder eine Nexus-Linie mit dem Legacy-Kernel | [Wählen](dxp-legacy-nexus-oder-exponential-6-waehlen.md) |
| "Enthält einen Shop." | Kein Commerce-Paket ist in der DXP | Anforderungen des Metapakets |
| "SOAP-Bibliothek" oder "Unterstützung für Oracle-Datenbanken" | Nicht Teil der Pakete der DXP; Oracle erscheint in der Anleitung nur als Exportziel | [Datenbanken](datenbanken-mysql-postgresql-sqlite.md#welche-engine) |
| "Kommt mit einer Demo-Website." | Das Skeleton hat kein Demo-Design; Nexus hat eines | [DXP-Skeleton](../../funktionen/6.0/plattform-dxp-skeleton.md#was-es-ist) |
| "Produktionsreif auf SQLite." | SQLite ist für Entwicklung, Tests, Demos und Offline-Installationen | [SQLite für die Entwicklung](sqlite-fuer-die-entwicklung.md#grenzen) |

## Technische Details

| Vermeiden | Sagen Sie stattdessen |
|---|---|
| "Die REST-API liegt unter `/api/ezp/v2/`." | Das Präfix ist die Einstellung `ibexa.rest.path_prefix`; das installierte v5-Paket hat als Standard `/api/ibexa/v2` ([Die REST-API](die-rest-api.md#wo-sie-liegt)) |
| "GraphiQL liegt unter `/graphql/explorer`." | In einem installierten v5-Projekt ist die Entwicklungsroute `/graphiql`; prüfen Sie mit `debug:router` ([GraphQL](graphql-und-graphiql.md#der-explorer)) |
| "Templates verwenden den Namensraum `@ezdesign`." | Auf v5 ist es `@ibexadesign` ([Twig und die Design-Engine](twig-templates-und-die-design-engine.md#der-namensraum-ibexadesign)) |
| "Es gibt Twig-Funktionen `exponential_*`." | Die Twig-Funktionen sind die Funktionen `ibexa_*` der Generation |
| "Jeder Befehl heißt `exponential:`." | `ibexa:cron:run` und `ibexa:graphql:generate-schema` behalten ihre Namen ([Konsolenbefehle](konsolenbefehle-und-umbenennungen.md)) |
| "Funktioniert mit Node.js 22." | Die Anleitung des DXP-Skeletons unterstützt nur Node.js 20; der Nexus-v5-Starter verwendet 22 |
| "Läuft auf PHP 8.2." | Das Metapaket verlangt PHP 8.3 oder neuer |

## Namen und Beziehungen

| Vermeiden | Sagen Sie stattdessen |
|---|---|
| "Ein Produkt von Ibexa" oder "von eZ Systems" | Gepflegt von 7x und der Gemeinschaft; steht nicht in Verbindung mit eZ Systems ([Wer ist 7x?](../wer-ist-7x.md#kurz-gesagt)) |
| "Exponential 6 auf Symfony" | Die DXP ist die Symfony-Plattformlinie; Exponential 6 ist ein eigener Kernel |
| "Legacy bedeutet veraltet" | "Legacy" ist der Kernel von Exponential 6 neben einer Symfony-Plattform |

## Wie Sie eine Aussage prüfen

1. Finden Sie in der `composer.json` des Metapakets das Paket, das die Funktion liefert.
2. Finden Sie den Code oder die Konfiguration in einem installierten Projekt (`vendor/`, `config/`).
3. Finden Sie beides nicht, lassen Sie die Aussage weg oder schreiben Sie "nicht geprüft".

## Verwandte Seiten

- [Was die DXP mitliefert und was nicht](was-die-dxp-mitliefert-und-was-nicht.md)
- [Aussagen, die Sie nicht machen dürfen (für Fürsprecher)](../fuersprecher/aussagen-die-sie-nicht-machen-duerfen.md)
- [Exponential sachlich vergleichen](../geschaeft/exponential-sachlich-vergleichen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
