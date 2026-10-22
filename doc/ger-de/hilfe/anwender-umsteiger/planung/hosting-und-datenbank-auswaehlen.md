---
title: Hosting und Datenbank auswählen
audience: adopter, project-manager, admin, hosting
category: adoption
order: 35
summary: Die technischen Entscheidungen, die ein Anwender vor dem Aufbau trifft - PHP-Version, Datenbank in der vom Projekt bevorzugten Reihenfolge, Webserver oder Velocity-Engine, Betriebssystem und die benötigten Umgebungen - mit den Gründen für jede und Links zu den Einzelheiten.
keywords: Hosting, Datenbank, SQLite, PostgreSQL, PHP-Version, Velocity, Apache, Nginx, Umgebungen, Staging, Produktion, Betriebssystem, hosting, database, PHP version, environments, staging, production, operating system
type: help
---
# Hosting und Datenbank auswählen

## Inhalt

1. [Die Entscheidungen](#die-entscheidungen)
2. [Umgebungen](#umgebungen)
3. [Fragen, die entscheiden](#fragen-die-entscheiden)

## Die Entscheidungen

| Entscheidung | Möglichkeiten | Leitlinie |
|---|---|---|
| PHP | 8.0 bis 8.5 | "please use the latest version of PHP available on your OS", also die neueste PHP-Version, die Ihr Betriebssystem bietet (`README.md`) |
| Datenbank | SQLite 3 (Standard), PostgreSQL, MongoDB, MariaDB, Oracle, MySQL, in der vom Projekt bevorzugten Reihenfolge | [Datenbankunterstützung](../../datenbankunterstuetzung.md) |
| Auslieferung | Velocity (Qbix empfohlen, FrankenPHP die Alternative) oder Apache, Nginx und andere | [Webserver und Engines](../../oekosystem/php-stack/webserver-und-engines.md) |
| Betriebssystem | Fast jedes mit PHP 8.0 oder neuer; siehe die Stufen | [Unterstützte Betriebssysteme](../../betriebssystemunterstuetzung.md) |
| Suche | Die eingebaute Suchmaschine oder Solr | [Such-Backends](../../hosting/grundlagen/such-backends.md) |

Die Seite für Projektleitungen behandelt dieselbe Wahl für ein Projekt: [Hosting, Datenbank und Betriebssystem wählen](../../fuehrung/hosting-datenbank-und-betriebssystem-waehlen.md).

## Umgebungen

| Umgebung | Für |
|---|---|
| Entwicklung | Die Arbeit der Entwickler, auf ihren eigenen Rechnern (SQLite ist bequem) |
| Staging | Proben, Abnahmetests, Schulung |
| Produktion | Die Live-Website |

Halten Sie den Code in Git und bewegen Sie ihn jedes Mal auf dieselbe Weise zwischen den Umgebungen
([Mit Git entwickeln](../../mit-git-entwickeln.md), [Ein Team mit Git führen](../../fuehrung/ein-team-mit-git-fuehren.md)).

## Fragen, die entscheiden

| Frage | Wenn ja |
|---|---|
| Ein Server, mäßiger Verkehr, wenige Redakteure? | SQLite ist "a full production database for a site served from one machine", eine vollwertige Produktionsdatenbank für eine Website auf einem Rechner |
| Mehrere Webserver an einer Datenbank? | Nicht SQLite: "Several web servers on one database: no" für SQLite ([Datenbankunterstützung](../../datenbankunterstuetzung.md)) |
| Organisation auf eine Datenbank festgelegt? | Verwenden Sie sie, wenn sie zu den sechs gehört |
| Shared Hosting ohne langlebige Prozesse? | Eher Apache oder LiteSpeed als Velocity |
| Mehrere Kunden oder Websites, die getrennt sein müssen? | Eine Velocity-Instanz pro Website oder eigene PHP-FPM-Pools pro Website |

## Verwandte Seiten

- [Die Einführung planen](README.md)
- [Anforderungen auf einen Blick](../../hosting/grundlagen/anforderungen-auf-einen-blick.md)
- [Wer den Betrieb übernimmt](../entscheiden/wer-den-betrieb-uebernimmt.md)
- [Sicherheitshärtung](../../sicherheitshaertung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
