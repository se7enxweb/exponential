---
title: Als Ersteller dem Ökosystem beitreten
audience: ecosystem, developer, designer, website-creator, hosting
category: ecosystem
order: 53
summary: Erste Schritte für jemanden, der mit Exponential bauen möchte - eine lokale Installation aus Git, der Lernpfad für jede Art von Ersteller, die Konventionen, denen Sie vom ersten Tag an folgen, und wo Sie Ihre erste Arbeit zeigen.
keywords: Ersteller, Einstieg, Entwickler, Designer, lokale Installation, Git-Klon, SQLite, Lernpfad, Konventionen, erstes Projekt, creator, getting started, developer, designer, local installation, Git clone, learning path, conventions, first project
type: help
---
# Als Ersteller dem Ökosystem beitreten

## Inhalt

1. [Schritt 1: eine lokale Installation](#schritt-1-eine-lokale-installation)
2. [Schritt 2: Ihr Lernpfad](#schritt-2-ihr-lernpfad)
3. [Schritt 3: Konventionen vom ersten Tag an](#schritt-3-konventionen-vom-ersten-tag-an)
4. [Schritt 4: ein erstes Projekt](#schritt-4-ein-erstes-projekt)
5. [Schritt 5: zeigen Sie es](#schritt-5-zeigen-sie-es)

## Schritt 1: eine lokale Installation

Installieren Sie aus Git-Klonen auf SQLite: kein Datenbankserver, und jede Korrektur, die Sie machen, ist "a branch in
your fork and a pull request away", also nur einen Branch in Ihrem Fork und einen Pull-Request entfernt
([Mit Git entwickeln](../../mit-git-entwickeln.md), [Installation mit einem Befehl](../../../funktionen/6.0/installation-mit-einem-befehl.md)).
Liefern Sie sie mit Velocity aus ([Exponential Velocity](../produkte/exponential-velocity.md)).

## Schritt 2: Ihr Lernpfad

| Sie sind | Lesen Sie |
|---|---|
| PHP-Entwickler | [Erweiterungsentwicklung, Lesepfade](../../erweiterungsentwicklung/lesepfade.md) |
| Website-Ersteller | [Website-Ersteller](../../ersteller/website-ersteller/website-ersteller-hier-beginnen.md), [Template-Sprache](../../template-sprache/README.md) |
| Integrator oder App-Entwickler | [Anwendungsersteller](../../ersteller/anwendungsentwickler/anwendungsentwickler-hier-beginnen.md), [ezprestapi](../../../funktionen/6.0/erweiterungen/ezprestapi.md) |
| Designer | [Designer: hier beginnen](../../designer/designer-hier-beginnen.md) |
| Symfony-Entwickler | [Die Platform-Linie](../produkte/die-platform-linie.md), [Exponential Platform DXP](../produkte/platform-dxp.md) |
| Hoster | [Hosting-Unternehmen: hier beginnen](../../hosting/hosting-anbieter-hier-beginnen.md) |

## Schritt 3: Konventionen vom ersten Tag an

- Benennen Sie Ihren Code mit Ihrem eigenen Präfix, und neuen Code, der für den Kernel gedacht ist, mit `exp`
  ([Präfixe für Namen](../../../standards/namenspraefixe-sevenx-und-exp.md)).
- Halten Sie individuellen Code in Erweiterungen, nicht im Kernel ([Erweitern ohne Fork](../../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/erweitern-ohne-zu-forken.md)).
- Verwenden Sie Git, Commit-Nachrichten mit Präfix und dauerhafte Tags ([Standards und Werkzeuge](../php-stack/standards-und-werkzeuge.md)).

## Schritt 4: ein erstes Projekt

Eine kleine Erweiterung mit einem Modul und einem Template, ein Theme für eine Testwebsite oder eine Übersetzung. Die
Seite für Studierende listet Projektideen nach Niveau ([Partner für Studierende und Bildung](../../partner/partner-fuer-studierende-und-bildung.md)).

## Schritt 5: zeigen Sie es

Veröffentlichen Sie es und erzählen Sie davon ([Im Ökosystem veröffentlichen](im-oekosystem-veroeffentlichen.md));
bitten Sie in den Foren um eine Durchsicht; beantworten Sie das erste Issue, das jemand eröffnet.

## Verwandte Seiten

- [Personen](README.md)
- [Ersteller im Ökosystem](ersteller-im-oekosystem.md)
- [Ihr erster Beitrag](../../../../eng-us/help/community/contributing/your-first-contribution.md)
- [Die Community für Entwickler](../../../../eng-us/help/community/paths/community-for-developers.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
