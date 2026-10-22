---
title: Lesepfade für DXP-Entwickler
audience: dxp-developer
category: dxp-development
order: 51
summary: Geordnete Leselisten durch die DXP-Seiten für fünf Arten von Lesern - den Evaluierenden, den Entwickler in der ersten Woche, den Theme-Bauer, den API-Integrator und das Team, das von Ibexa OSS oder Exponential 6 umzieht - mit der jeweils benötigten Zeit.
keywords: Lesepfade, Lernen, Einarbeitung, erste Woche, Evaluierung, Theme-Bauer, API-Integrator, Migration, Reihenfolge, Lehrplan, reading paths, learning, onboarding, first week, evaluator, theme builder, API integrator, curriculum
type: help
---
# Lesepfade für DXP-Entwickler

Diese Seite richtet sich an Entwickler, die die DXP-Seiten in einer sinnvollen Reihenfolge lesen möchten. Wählen Sie den
Pfad, der zu Ihnen passt; die Zeiten sind ungefähre Lesezeiten, ohne praktische Arbeit.

## Inhalt

1. [Die Evaluierung (45 Minuten)](#die-evaluierung-45-minuten)
2. [Die erste Woche (ein Tag Lektüre)](#die-erste-woche-ein-tag-lektüre)
3. [Der Theme-Bauer (zwei Stunden)](#der-theme-bauer-zwei-stunden)
4. [Der API-Integrator (zwei Stunden)](#der-api-integrator-zwei-stunden)
5. [Das umziehende Team (ein halber Tag)](#das-umziehende-team-ein-halber-tag)
6. [Der Betreiber (zwei Stunden)](#der-betreiber-zwei-stunden)

## Die Evaluierung (45 Minuten)

Für Architekten und Leiter, die entscheiden, ob die DXP verwendet wird.

1. [Was Exponential Platform DXP ist](was-exponential-platform-dxp-ist.md)
2. [Was die DXP mitliefert und was nicht](was-die-dxp-mitliefert-und-was-nicht.md)
3. [DXP, Legacy, Nexus oder Exponential 6 wählen](dxp-legacy-nexus-oder-exponential-6-waehlen.md)
4. [Aussagen, die Sie vermeiden sollten](aussagen-die-sie-vermeiden-sollten.md)
5. [Lizenzierung](lizenzierung.md)

## Die erste Woche (ein Tag Lektüre)

Für Entwickler, die zu einem DXP-Projekt stoßen.

1. [Versionen](versionen-von-php-symfony-und-node.md), [Installation](installation-mit-dem-skeleton.md), [Umgebungsvariablen](umgebungsvariablen.md)
2. [Der Verzeichnisaufbau des Projekts](der-verzeichnisaufbau-des-projekts.md), [Siteaccesses](siteaccesses-site-und-admin.md)
3. [Inhalte, Inhaltstypen und Platzierungen](inhaltstypen-und-platzierungen.md), [Feldtypen](die-mitgelieferten-feldtypen.md)
4. [Twig und die Design-Engine](twig-templates-und-die-design-engine.md), [Twig-Funktionen](twig-funktionen-fuer-den-alltag.md)
5. [Konsolenbefehle](konsolenbefehle-und-umbenennungen.md), [Befehle für den Alltag](befehle-fuer-den-alltag.md)
6. [Fehlersuche](fehlersuche-in-der-dxp.md)

## Der Theme-Bauer (zwei Stunden)

1. [Twig und die Design-Engine](twig-templates-und-die-design-engine.md)
2. [Das Standarddesign und Ihr Theme](das-standarddesign-und-ihr-theme.md)
3. [Twig-Funktionen für den Alltag](twig-funktionen-fuer-den-alltag.md), [Twig-Komponenten](twig-komponenten.md)
4. [Bildvariationen](bildvariationen.md), [Assets bauen](assets-mit-yarn-und-encore-bauen.md)
5. Aus der Zielgruppe Designer: [Grundlagen der Barrierefreiheit für Themes](../designer/theme-design/barrierefreiheit-grundlagen-fuer-themes.md),
   [CSP-sicheres Design](../designer/theme-design/csp-sicheres-design.md)

## Der API-Integrator (zwei Stunden)

1. [Die REST-API](die-rest-api.md), [JWT-Authentifizierung](jwt-authentifizierung.md)
2. [GraphQL und GraphiQL](graphql-und-graphiql.md)
3. [Headless mit der DXP](headless-mit-der-dxp.md)
4. [HTTP-Cache](http-cache-und-varnish.md)

## Das umziehende Team (ein halber Tag)

1. [Umstieg von Ibexa OSS oder eZ Platform](umstieg-von-ibexa-oss-oder-ez-platform.md) oder [Umstieg von Exponential 6](umstieg-von-exponential-6.md)
2. [Die se7enxweb-Forks und `replace`](die-se7enxweb-forks-und-replace.md)
3. [16. Migration von eZ Platform und Ibexa](../../installation/16-migration-von-ez-platform-und-ibexa.md)
4. [Das Skeleton 4.6.x](das-skeleton-v4x.md), wenn Ihre Quelle 4.x ist

## Der Betreiber (zwei Stunden)

1. [Webserver einrichten](webserver-einrichten.md), [Dateiberechtigungen](dateiberechtigungen.md)
2. [Datenbanken](datenbanken-mysql-postgresql-sqlite.md), [Anwendungs-Cache](anwendungs-cache-und-redis.md)
3. [Cron](cron.md), [Messenger](messenger-und-benachrichtigungen.md), [Neu indexieren](neu-indexieren.md)
4. [Bereitstellen](dxp-bereitstellen.md), [Aktualisieren](dxp-aktualisieren.md), [Checkliste für Projekte](checkliste-fuer-dxp-projekte.md)

## Verwandte Seiten

- [DXP-Entwickler: hier beginnen](dxp-entwickler-hier-beginnen.md)
- [Lesepfade für Entwickler (Exponential 6)](../erweiterungsentwicklung/lesepfade.md)
- [Glossar für DXP-Entwickler](glossar-fuer-dxp-entwickler.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
