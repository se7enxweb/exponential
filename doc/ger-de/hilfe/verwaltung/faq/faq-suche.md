---
title: "FAQ: Suche"
audience: government, editor, admin, project-manager
category: public-sector
order: 160
summary: Antworten auf die Suchfragen öffentlicher Stellen zu Exponential - eingebaute Suche auf der Website, Solr mit ezfind, Suche in Dokumenten, Suche in mehreren Sprachen, Suchstatistiken und Datenschutz sowie die Suche in der Administration.
keywords: FAQ Suche, Suche auf der Website, Solr, ezfind, Facetten, Dokumentensuche, Suchstatistiken, Datenschutz, Suche in der Administration, Inhaltsfinder, öffentlicher Sektor, search FAQ, site search, facets, document search, search statistics, privacy, administration search, content finder, public sector
type: help
---
# FAQ: Suche

Diese Seite beantwortet die Suchfragen öffentlicher Stellen zu Exponential. Die Antworten stammen aus
[Suche auf der Website](../../ersteller/website-ersteller/suche-auf-der-website.md),
[Suche mit ezfind und Solr](../../ersteller/website-ersteller/suche-mit-ezfind-und-solr.md) und [Suchen](../../suchen.md).

## Inhalt

1. [Hat die Website eine Suche?](#hat-die-website-eine-suche)
2. [Wann brauchen wir Solr?](#wann-brauchen-wir-solr)
3. [Können Besucher in Dokumenten suchen?](#können-besucher-in-dokumenten-suchen)
4. [Können wir sehen, wonach Menschen suchen?](#können-wir-sehen-wonach-menschen-suchen)
5. [Wie finden Redaktionen Inhalte?](#wie-finden-redaktionen-inhalte)
6. [Gibt es eine Schulung?](#gibt-es-eine-schulung)

## Hat die Website eine Suche?

Ja. Exponential hat eine eingebaute Suchmaschine mit einem Suchfeld und Templates für Ergebnisse, Einstellungen, um sie
zu begrenzen und abzustimmen, verzögerte Indexierung und den Neuaufbau des Index
([Suche auf der Website](../../ersteller/website-ersteller/suche-auf-der-website.md)).

## Wann brauchen wir Solr?

Wenn Sie Facetten, bessere Relevanz, den Inhalt von Dateien oder mehr Skalierung brauchen. Die Erweiterung ezfind stellt
die Suche der Website auf Apache Solr um; `exp:solr` steuert sie
([Suche mit ezfind und Solr](../../ersteller/website-ersteller/suche-mit-ezfind-und-solr.md),
[Such-Backends](../../hosting/grundlagen/such-backends.md)). Solr ist ein eigener Dienst, den Ihr Hoster betreiben muss.

## Können Besucher in Dokumenten suchen?

Mit Solr über ezfind lassen sich Dateiinhalte indexieren
([Suche mit ezfind und Solr](../../ersteller/website-ersteller/suche-mit-ezfind-und-solr.md)). Klären Sie mit Ihrem
Dienstleister, welche Dateitypen Ihre Konfiguration indexiert.

## Können wir sehen, wonach Menschen suchen?

Ja: **Setup > Suchstatistiken** listet die Suchbegriffe auf
([Links, URL-Aliasse, Platzhalter und Suchstatistiken](../../../anleitungen/urls-und-aliase.md)). Suchbegriffe können
personenbezogene Daten enthalten; nehmen Sie sie in Ihre Aufbewahrungsregeln auf
([Datenschutzstandards](../../../standards/datenschutz.md#bekannte-lücken)).

## Wie finden Redaktionen Inhalte?

Die Administration hat eine Schnellsuche, einen Suchbereich, eine erweiterte Suche und das Öffnen eines Eintrags über
seine Nummer oder Adresse ([Suchen](../../suchen.md)); **Setup > Inhaltsfinder** findet Inhalte im ganzen Baum
([Inhaltsfinder](../../../funktionen/6.0/inhalts-finder.md)).

## Gibt es eine Schulung?

Ja: der [Kurs Suche](../../trainer/suche/README.md), 20 Lektionen, etwa 10 Stunden, für Administratoren und Entwickler.

## Verwandte Seiten

- [FAQ für Verwaltungen](README.md)
- [FAQ: Integrationen und APIs](faq-integrationen.md)
- [Suche auf der Website](../../ersteller/website-ersteller/suche-auf-der-website.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
