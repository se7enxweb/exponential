---
title: FAQ für DXP-Entwickler
audience: dxp-developer
category: dxp-development
order: 48
summary: Kurze Antworten auf die Fragen, die DXP-Entwickler am häufigsten stellen - was sie ist, wie sie zu Ibexa und Exponential 6 steht, Versionen, Datenbanken, Seitenbau, APIs, URLs, Befehle, Lizenzierung und wo es Hilfe gibt - jeweils mit der Seite, die es erklärt.
keywords: FAQ, Fragen, Antworten, DXP, Exponential Platform DXP, Ibexa, Exponential 6, PHP-Version, SQLite, Layouts, REST, GraphQL, Lizenz, Hilfe, questions, answers, PHP version, license, help
type: help
---
# FAQ für DXP-Entwickler

Diese Seite richtet sich an Entwickler, die eine schnelle Antwort möchten. Jede Antwort verweist auf die Seite mit den
Details.

## Inhalt

1. [Das Produkt](#das-produkt)
2. [Installieren und betreiben](#installieren-und-betreiben)
3. [Bauen](#bauen)
4. [APIs](#apis)
5. [Personen und Lizenzen](#personen-und-lizenzen)

## Das Produkt

**Was ist Exponential Platform DXP?**
Eine freie, quelloffene Inhaltsplattform auf Symfony, die von 7x gepflegte Fortführung der Linie eZ Platform und
Ibexa OSS durch die Gemeinschaft ([Was sie ist](was-exponential-platform-dxp-ist.md)).

**Ist das Exponential 6?**
Nein. Es ist ein eigener Kernel auf Symfony 7.4. Exponential 6 ist der PHP-CMS-Kernel ohne Symfony
([Umstieg von Exponential 6](umstieg-von-exponential-6.md)).

**Enthält sie einen Seitenbaukasten?**
Keinen Editor; nur den Layouts Core. Verwenden Sie Nexus oder Exponential 6 für einen Seitenbaukasten
([Layouts Core](layouts-core-in-der-dxp.md)).

**Enthält sie Workflows, zeitgesteuertes Veröffentlichen oder einen Shop?**
Nein ([Was die DXP mitliefert](was-die-dxp-mitliefert-und-was-nicht.md)).

**Ist sie mit Ibexa verbunden?**
Nein. Sie verwendet und forkt Open-Source-Pakete dieser Linie; sie wird von 7x und der Gemeinschaft gepflegt
([Die se7enxweb-Forks](die-se7enxweb-forks-und-replace.md)).

## Installieren und betreiben

**Welche PHP-Version?** 8.3 oder neuer; 8.3 oder 8.5 empfohlen ([Versionen](versionen-von-php-symfony-und-node.md)).

**Welche Node.js-Version?** 20 LTS für das DXP-Skeleton.

**Kann ich sie ohne Datenbankserver ausprobieren?** Ja, mit SQLite ([SQLite für die Entwicklung](sqlite-fuer-die-entwicklung.md)).

**Kann ich die Produktion auf SQLite betreiben?** Nicht für öffentliche Websites oder Websites mit mehreren Benutzern
([Grenzen](sqlite-fuer-die-entwicklung.md#grenzen)).

**Wie lautet das Administratorpasswort nach der Installation?**
Das Konto des Installers ist in der Anleitung öffentlich dokumentiert; deshalb ändern Sie es sofort
([Installation](installation-mit-dem-skeleton.md#nach-der-installation)).

**Brauche ich Varnish oder Redis?** Nein; beide sind optional ([HTTP-Cache](http-cache-und-varnish.md),
[Anwendungs-Cache](anwendungs-cache-und-redis.md)).

**Kann ich die DXP mit Exponential Velocity ausliefern?** Velocity ist als Engine von Exponential 6 dokumentiert; die
Anleitung der DXP dokumentiert Apache oder Nginx mit PHP-FPM und die Symfony CLI ([Webserver einrichten](webserver-einrichten.md)).

## Bauen

**Wohin gehören meine Templates?** Nach `templates/themes/<theme>/`, angesprochen als `@ibexadesign/...`
([Twig und die Design-Engine](twig-templates-und-die-design-engine.md)).

**Warum erscheint meine YAML-Änderung nicht?** Die Konfiguration wird kompiliert; führen Sie `cache:clear` aus
([Befehle für den Alltag](befehle-fuer-den-alltag.md)).

**Warum funktionieren die Befehle `ibexa:` noch?** Sie sind Aliase der Namen `exponential:`
([Konsolenbefehle](konsolenbefehle-und-umbenennungen.md)).

## APIs

**Wo liegt die REST-API?** Unter `ibexa.rest.path_prefix`, im installierten v5-Paket standardmäßig `/api/ibexa/v2`;
ältere Anleitungen zeigen `/api/ezp/v2/` ([Die REST-API](die-rest-api.md#wo-sie-liegt)).

**Wie melden sich Apps an?** Mit JWT, nachdem Sie die Firewalls eingeschaltet haben
([JWT-Authentifizierung](jwt-authentifizierung.md)).

**Wann muss ich das GraphQL-Schema neu erzeugen?** Nach jeder Änderung eines Inhaltstyps oder Feldtyps
([GraphQL](graphql-und-graphiql.md#das-schema)).

## Personen und Lizenzen

**Welche Lizenz?** `(GPL-2.0-or-later or proprietary)`, wie deklariert ([Lizenzierung](lizenzierung.md)).

**Wo melde ich einen Fehler?** Im betroffenen Repository; Sicherheitsprobleme vertraulich
([Beitragen](zu-den-forks-beitragen.md)).

**Wer macht sie?** 7x, das Unternehmen, das Exponential führt, mit der Gemeinschaft; die Exponential Foundation ist die
gemeinnützige Organisation ([Wer ist 7x?](../wer-ist-7x.md)).

## Verwandte Seiten

- [DXP-Entwickler: hier beginnen](dxp-entwickler-hier-beginnen.md)
- [Glossar für DXP-Entwickler](glossar-fuer-dxp-entwickler.md)
- [Fehlersuche](fehlersuche-in-der-dxp.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
