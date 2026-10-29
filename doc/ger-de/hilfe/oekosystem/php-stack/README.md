---
title: Der PHP-Stack des Ökosystems von Exponential
audience: ecosystem, developer, admin, hosting
category: ecosystem
order: 30
summary: Die Einstiegsseite des Teils PHP-Stack im Bereich Ökosystem - PHP-Versionen, Datenbanken, Webserver und Engines, Betriebssysteme, die Ursprungsprojekte, die Exponential verwendet und denen es etwas zurückgibt, und die Standards und Werkzeuge, denen es folgt.
keywords: PHP-Stack, PHP-Versionen, Datenbanken, Webserver, Velocity, FrankenPHP, Betriebssysteme, Zeta Components, Symfony, Twig, Doctrine, Standards, PSR, Werkzeuge, PHP stack, PHP versions, databases, web servers, operating systems, standards, tooling
type: help
---
# Der PHP-Stack des Ökosystems von Exponential

Das Ökosystem lebt "within the PHP stack", innerhalb des PHP-Stacks. Diese Seiten beschreiben die Teile dieses Stacks,
auf denen Exponential läuft und mit denen es arbeitet.

## Inhalt

1. [Die Seiten dieses Teils](#die-seiten-dieses-teils)
2. [Der Stack in einer Tabelle](#der-stack-in-einer-tabelle)

## Die Seiten dieses Teils

| Seite | Was sie beantwortet |
|---|---|
| [PHP-Versionen](php-versionen.md) | Welche PHP-Versionen welches Release betreiben |
| [Datenbanken](datenbanken.md) | Die sechs unterstützten Datenbanken in der Reihenfolge des Projekts |
| [Webserver und Engines](webserver-und-engines.md) | Velocity, FrankenPHP und die klassischen Server |
| [Betriebssysteme](betriebssysteme.md) | Die vier Stufen der Unterstützung für Betriebssysteme |
| [Upstream-Projekte und Forks](upstream-projekte-und-forks.md) | Was Exponential verwendet und wem es etwas zurückgibt |
| [Standards und Werkzeuge](standards-und-werkzeuge.md) | PSR, Programmierstandards, CI, Qualitätsprüfungen |

## Der Stack in einer Tabelle

| Schicht | Exponential 6.0.15 | Quelle |
|---|---|---|
| Sprache | PHP 7.4 bis 8.5 | `README.md` |
| Paketmanager | Composer, Packagist | `README.md` |
| Kernel-Bibliotheken | Zeta Components | `composer.json` |
| Datenbank | SQLite 3 (Standard), PostgreSQL, MongoDB, MariaDB, Oracle, MySQL | [Datenbankunterstützung](../../datenbankunterstuetzung.md) |
| Server | Velocity (Qbix, FrankenPHP oder der eingebaute Server von PHP), oder Apache, Nginx, LiteSpeed, Caddy, IIS und andere | `README.md` |
| Betriebssystem | Fast jedes, auf dem PHP 8.0 oder neuer läuft | [Unterstützte Betriebssysteme](../../betriebssystemunterstuetzung.md) |
| Suche | Die eingebaute Suchmaschine; Solr als Option | [Such-Backends](../../hosting/grundlagen/such-backends.md) |

## Verwandte Seiten

- [Ökosystem: hier beginnen](../oekosystem-hier-beginnen.md)
- [Anforderungen](../../../installation/02-voraussetzungen.md)
- [Hosting-Grundlagen](../../hosting/grundlagen/README.md)
- [Der PHP-Stack, auf dem es steht und dem es etwas zurückgibt](../../wer-ist-die-exponential-foundation.md#der-php-stack-auf-dem-es-steht-und-dem-es-etwas-zurückgibt)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
