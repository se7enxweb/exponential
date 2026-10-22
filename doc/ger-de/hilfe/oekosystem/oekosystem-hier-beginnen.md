---
title: "Ökosystem: hier beginnen"
audience: ecosystem, community, partner, developer
category: ecosystem
order: 1
summary: Die Einstiegsseite für das Ökosystem von Exponential - die größere Welt von Exponential innerhalb des PHP-Stacks, ihre Anwender und Ersteller. Eine Übersicht der Produkte, Pakete, Organisationen, Menschen und PHP-Projekte rund um Exponential, wie sie mit Partnern und der Community zusammenhängen und wo jede Seite dieses Bereichs steht.
keywords: Ökosystem, hier beginnen, Welt von Exponential, PHP-Stack, Produkte, Distributionen, Pakete, Erweiterungen, Packagist, Composer, Velocity, Layouts, Platform, Nexus, DXP, 7x, Exponential Foundation, Partner, Community, Anwender, Ersteller, ecosystem, start here, Exponential world, PHP stack, products, distributions, packages, extensions, partners, community, users, creators
featured: true
type: help
---
# Ökosystem: hier beginnen

**Das größere Ökosystem reicht über die Community hinaus und umfasst alle Aspekte der Welt von Exponential innerhalb
des PHP-Stacks: ihre Anwender und Ersteller.**

Das Ökosystem ist alles rund um Exponential: der Kernel und seine Schwesterprodukte, die Erweiterungen und Themes, die
Server und Datenbanken, auf denen es läuft, die PHP-Projekte, auf denen es steht und denen es etwas zurückgibt, die
Unternehmen und die gemeinnützige Organisation dahinter, die Menschen, die damit bauen, und die Menschen, die nutzen,
was sie bauen. Viele von ihnen schreiben nie in einem Forum und gehören trotzdem dazu.

Dieser Bereich ist eine Karte. Er sagt Ihnen, was es gibt, wie die Teile zusammenpassen und wo Sie mehr lesen.

> **Hinweis**
> Jeder Fakt in diesem Bereich stammt aus dem Repository (`README.md`, `composer.json`, `doc/`) oder von einer anderen
> Seite dieser Hilfe, und die Quelle wird genannt. Zahlen wie die Anzahl der Pakete wurden mit dem Datum aus den
> Quellseiten übernommen, das diese nennen; sie ändern sich mit der Zeit.

## Inhalt

1. [Partner, Community und Ökosystem](#partner-community-und-ökosystem)
2. [Das Ökosystem in einer Minute](#das-ökosystem-in-einer-minute)
3. [Die Karte](#die-karte)
4. [Übersicht der Seiten zum Ökosystem](#übersicht-der-seiten-zum-ökosystem)
5. [Benachbarte Bereiche](#benachbarte-bereiche)

## Partner, Community und Ökosystem

| Kreis | Wer | Lesen |
|---|---|---|
| Partner | Exponential-Partner tragen finanziell zur dauerhaften Zukunft des Projekts bei, geben Code und mehr an das Projekt zurück und arbeiten direkt mit 7x und der Exponential Foundation zusammen. | [Partner: hier beginnen](../partner/partner-hier-beginnen.md) |
| Community | Mitglieder der Exponential-Community nutzen Exponential weltweit, tragen dazu bei, werben dafür und entwickeln Software damit. | [Community: hier beginnen](../../../eng-us/help/community/community-start-here.md) |
| Ökosystem | Das größere Ökosystem reicht über die Community hinaus und umfasst alle Aspekte der Welt von Exponential innerhalb des PHP-Stacks: ihre Anwender und Ersteller. | dieser Bereich |

Wie die drei zusammenhängen: [Partner, Community und Ökosystem](../../../eng-us/help/community/partners-community-and-ecosystem.md) und
[Was das Ökosystem ist](was-das-oekosystem-ist.md).

## Das Ökosystem in einer Minute

- **Ein Kernel und eine Produktfamilie.** Exponential 6 ist der PHP-Kernel für CMS, CMF und Shop. Um ihn herum pflegt
  7x Exponential Basic, die auf Symfony basierende Linie Exponential Platform (Platform, Legacy, Nexus, DXP), den
  Anwendungsserver Velocity, Exponential Layouts, Exponential Intelligence und AdminUI ([Produkte](produkte/README.md)).
- **Offene Pakete.** Jedes Paket ist ein öffentliches GitHub-Repository unter se7enxweb und ein Composer-Paket; die
  `composer.json` des Kernels verlangte 66 Pakete von se7enxweb, und der Anbieter hatte 219 Pakete auf Packagist, beides
  gezählt am 18. Oktober 2026 ([Pakete](pakete/README.md)).
- **Auf dem PHP-Stack stehend.** PHP, Composer, Zeta Components, Symfony, Twig, Doctrine, die Projekte von Netgen und
  mehr, mit Korrekturen, die an die Ursprungsprojekte zurückgegeben werden ([Der PHP-Stack](php-stack/README.md)).
- **Zwei Organisationen und viele Unternehmen.** 7x führt die Entwicklung; die gemeinnützige Exponential Foundation
  betreibt das Projekt; Partner, Hoster und Agenturen bauen Geschäfte darauf auf ([Organisationen](organisationen/README.md)).
- **Anwender und Ersteller.** Website-Betreiber, Redakteure und Besucher auf der einen Seite; Entwickler, Designer,
  Übersetzer und Autoren auf der anderen ([Personen](personen/README.md)).
- **Durchgehend freie Software.** Code unter der GNU GPL Version 2 oder später, Dokumentation unter der GNU FDL 1.3 oder
  später.

## Die Karte

```text
                         PHP, Composer, Packagist
                                   |
   Zeta Components -- Exponential 6 kernel -- Velocity (application server)
                          |        |       \
         66 bundled se7enxweb   Layouts,    admin designs
         extensions             Intelligence, AdminUI
                          |
   Exponential Platform line (Symfony): Platform 3.2.9, Legacy, Nexus, DXP
                          |
   Sites, site packages, themes, integrations, apps
                          |
   Users: owners, editors, members, visitors
```

Die [Karte des Ökosystems](karte-des-oekosystems.md) erklärt jeden Kasten.

## Übersicht der Seiten zum Ökosystem

| Teil | Was er behandelt |
|---|---|
| [Was das Ökosystem ist](was-das-oekosystem-ist.md) | Die Definition und ihre Teile |
| [Die Karte des Ökosystems](karte-des-oekosystems.md) | Wie Produkte, Pakete, Stack, Organisationen und Menschen zusammenhängen |
| [Eine kurze Geschichte des Ökosystems](eine-kurze-geschichte-des-oekosystems.md) | Von eZ Publish zu Exponential, in der Vergangenheitsform |
| [Produkte](produkte/README.md) | Der Kernel, Velocity, Layouts, Intelligence, AdminUI, Basic, die Platform-Linie, ein Produkt auswählen |
| [Pakete](pakete/README.md) | Mitgelieferte Erweiterungen, Composer und Packagist, Versionen, Seitenpakete, Themes, Kompatibilität |
| [Der PHP-Stack](php-stack/README.md) | PHP-Versionen, Datenbanken, Webserver und Engines, Betriebssysteme, Ursprungsprojekte, Standards |
| [Organisationen](organisationen/README.md) | 7x, die Foundation, Partner, Begleitprojekte, Community-Websites |
| [Personen](personen/README.md) | Anwender und Ersteller und wie sie dem Ökosystem beitreten und darin veröffentlichen |
| [Lesepfad](lesepfad-zum-oekosystem.md), [Checklisten](checklisten-zum-oekosystem.md), [FAQ](faq-zum-oekosystem.md), [Glossar](glossar-zum-oekosystem.md) | Nachschlagen |

## Benachbarte Bereiche

| Bereich | Lesen Sie ihn, wenn |
|---|---|
| [Community](../../../eng-us/help/community/community-start-here.md) | Sie mitmachen möchten |
| [Partner](../partner/partner-hier-beginnen.md) | Ihr Unternehmen direkt mit 7x und der Foundation arbeiten möchte |
| [Entwickler: Mit Git entwickeln](../mit-git-entwickeln.md) | Sie auf dem Kernel aufbauen |
| [Erweiterungsentwicklung](../erweiterungsentwicklung/README.md) | Sie Erweiterungen schreiben und veröffentlichen |
| [Anwendungsersteller](../ersteller/anwendungsentwickler/anwendungsentwickler-hier-beginnen.md) | Sie Anwendungen und Integrationen bauen |
| [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md) | Sie die Produktfamilie auf einer Seite möchten |

## Verwandte Seiten

- [Was das Ökosystem ist](was-das-oekosystem-ist.md)
- [Lesepfad zum Ökosystem](lesepfad-zum-oekosystem.md)
- [Community: hier beginnen](../../../eng-us/help/community/community-start-here.md)
- [Partner: hier beginnen](../partner/partner-hier-beginnen.md)
- [Das Ökosystem der Exponential-Plattform (Geschichte)](../../geschichte/oekosystem.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
