---
title: "FAQ: Hosting und Datenstandort"
audience: government, project-manager, hosting, admin
category: public-sector
order: 50
summary: Antworten auf die Hostingfragen öffentlicher Stellen zu Exponential - kann es auf unseren eigenen Servern oder in unserem Land laufen, welche Hostingmodelle, Datenbanken und Webserver, was der Server braucht, ausgehende Verbindungen und wer es betreibt.
keywords: FAQ Hosting, Datenstandort, Datenresidenz, eigenes Rechenzentrum, Cloud, souverän, Anforderungen, Datenbanken, Webserver, Velocity, Betriebssysteme, öffentlicher Sektor, hosting FAQ, data location, data residency, on-premise, sovereign, own data center, requirements, databases, web servers, operating systems, public sector
type: help
---
# FAQ: Hosting und Datenstandort

Diese Seite beantwortet die Hostingfragen, die öffentliche Stellen stellen, bevor sie wählen, wo eine Website mit
Exponential läuft. Die Antworten stammen aus [2. Anforderungen](../../../installation/02-voraussetzungen.md),
[Datenbankunterstützung](../../datenbankunterstuetzung.md) und dem [Bereich Hosting](../../hosting/hosting-anbieter-hier-beginnen.md)
dieser Hilfe.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Die Regeln zum
> Datenstandort hängen von Ihrer Rechtsordnung ab; prüfen Sie sie mit Ihrer Rechtsberatung.

## Inhalt

1. [Können wir es selbst hosten, in unserem eigenen Land?](#können-wir-es-selbst-hosten-in-unserem-eigenen-land)
2. [Gibt es einen Cloud-Dienst des Projekts?](#gibt-es-einen-cloud-dienst-des-projekts)
3. [Was braucht ein Server?](#was-braucht-ein-server)
4. [Welche Datenbanken werden unterstützt?](#welche-datenbanken-werden-unterstützt)
5. [Welche Webserver?](#welche-webserver)
6. [Welche Hostingmodelle passen?](#welche-hostingmodelle-passen)
7. [Meldet sich die Software beim Hersteller?](#meldet-sich-die-software-beim-hersteller)

## Können wir es selbst hosten, in unserem eigenen Land?

Ja. Exponential ist freie Software, die Sie installieren, wo Sie wollen: in Ihrem eigenen Rechenzentrum, in einer
nationalen Cloud, bei einem lokalen Hoster. Der Datenstandort ist eine Eigenschaft des Ortes, an dem Sie installieren
und wohin die Datensicherungen gehen, nicht der Software
([Ein Hostingmodell wählen](../../hosting/modelle/ein-hosting-modell-waehlen.md)). Legen Sie den Standort im Hostingvertrag
fest, einschließlich der Datensicherungen.

## Gibt es einen Cloud-Dienst des Projekts?

Das Projekt selbst betreibt keinen. 7x, das Unternehmen, das Exponential pflegt, bietet Hosting und Leistungen zu eigenen
Bedingungen an ([Wer ist 7x?](../../wer-ist-7x.md#dienstleistungen-und-hosting)); Partner und Hosting-Unternehmen bieten ihre
eigenen an ([Hosting-Partner](../../partner/hosting-partner.md)).

## Was braucht ein Server?

PHP 8.0 oder später (auch mit Velocity; 8.5 ist die neueste getestete Version), Composer 2, eine unterstützte Datenbank
oder SQLite sowie GD oder ImageMagick; ein Webserver ist mit Velocity optional
([2. Anforderungen](../../../installation/02-voraussetzungen.md#21-the-checklist),
[Anforderungen auf einen Blick](../../hosting/grundlagen/anforderungen-auf-einen-blick.md)). Die Bemessung behandelt
[Arbeitsspeicher, CPU und Festplatte bemessen](../../hosting/grundlagen/speicher-cpu-und-festplatte-dimensionieren.md).

## Welche Datenbanken werden unterstützt?

In der offiziellen Reihenfolge: SQLite 3 (Voreinstellung), PostgreSQL, MongoDB, MariaDB, Oracle, MySQL. Microsoft SQL
Server ist mit zusätzlichem Aufwand möglich ([Datenbankunterstützung](../../datenbankunterstuetzung.md)).

## Welche Webserver?

Exponential Velocity (sein eigener PHP-Anwendungsserver), Apache oder nginx mit PHP-FPM, FrankenPHP und weitere, die für
Hoster beschrieben sind (Caddy, LiteSpeed, IIS) ([8. Die Website ausliefern](../../../installation/08-die-website-ausliefern.md),
[Einen Webserver wählen](../../hosting/server/einen-webserver-waehlen.md)).

## Welche Hostingmodelle passen?

Shared Hosting, ein virtueller Server, dedizierte Server, Container, Kubernetes, Platform as a Service und Managed
Hosting sind alle beschrieben ([Hostingmodelle](../../hosting/modelle/README.md)). Für mehrere Webknoten brauchen Sie
einen Datenbankserver und gemeinsamen Speicher ([Mehrere Webknoten](../../hosting/betrieb/mehrere-web-knoten.md)).

## Meldet sich die Software beim Hersteller?

Kein Dokument im Repository beschreibt, dass Exponential Daten an das Projekt sendet. Die Administration lädt ihre
Skripte lokal ([Datenschutzstandards](../../../../eng-us/standards/privacy.md#gdpr)). Einige Funktionen bauen Verbindungen nach
außen auf, wenn Sie sie nutzen: Prüfungen auf Updates bei Paketservern (**Setup > Updates and packages**), der
KI-Assistent zu seinem Anbieter und Erweiterungen von Drittanbietern, wenn sie aktiviert sind. Bitten Sie Ihren
Dienstleister, jede ausgehende Verbindung Ihrer Konfiguration aufzulisten, und beschränken Sie den ausgehenden Verkehr
an der Firewall auf diese Liste.

## Verwandte Seiten

- [FAQ für Verwaltungen](README.md)
- [Hosting-Unternehmen: hier beginnen](../../hosting/hosting-anbieter-hier-beginnen.md)
- [Betriebssystemunterstützung](../../betriebssystemunterstuetzung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
