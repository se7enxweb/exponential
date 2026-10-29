---
title: Briefing für die Fachpresse
audience: press
category: press-media
order: 123
summary: Ein Hintergrund-Briefing für Entwickler- und IT-Medien über Exponential - der Stack, PHP-Versionen, Datenbanken, Engines, Installation, der Entwicklungsprozess, APIs und die Produktfamilie - jeder Fakt mit seiner Quelle, dazu die Fragen, die technische Leser stellen werden.
keywords: Fachpresse, technische Presse, Entwicklermedien, PHP, Datenbanken, SQLite, PostgreSQL, MongoDB, Velocity, FrankenPHP, Composer, Git, CI, REST API, Architektur, Briefing, technical press, developer media, databases, architecture, briefing
type: help
---
# Briefing für die Fachpresse

Diese Seite ist für Entwicklermagazine, IT-Nachrichtenseiten, technische Podcasts und Engineering-Blogger. Sie gibt
Ihnen die technischen Fakten zu Exponential an einem Ort, jeden mit seiner Quelle, damit Sie sicher schreiben und Ihre
Interviewzeit für Designentscheidungen nutzen können. Die Fakten haben den Stand vom 18. Oktober 2026; prüfen Sie sie,
bevor Sie veröffentlichen.

## Inhalt

1. [Der Stack in einer Tabelle](#der-stack-in-einer-tabelle)
2. [Datenbanken](#datenbanken)
3. [Engines und Webserver](#engines-und-webserver)
4. [Beschaffen und installieren](#beschaffen-und-installieren)
5. [Wie es entwickelt wird](#wie-es-entwickelt-wird)
6. [Blickwinkel für technische Leser](#blickwinkel-für-technische-leser)
7. [Technische Behauptungen, die Sie vermeiden sollten](#technische-behauptungen-die-sie-vermeiden-sollten)

## Der Stack in einer Tabelle

| Thema | Fakt | Quelle |
|---|---|---|
| Was es ist | "a professional PHP application framework with advanced CMS (content management system) functionality", mit einem "fully customizable and extendable content model" | [Exponential 6, der Kernel](../wer-ist-die-exponential-foundation.md#exponential-6-der-kernel) |
| Wichtigste Funktionen | Inhaltsklassen, Versionen, Mehrsprachigkeit, Suche, Rollen, Freigabe und zeitgesteuerte Veröffentlichung, Multisite, E-Commerce, Workflows, Templates, eine Headless-REST-API, Clustering, eine Plugin-API | Derselbe Abschnitt (aus der README) |
| PHP | 8.0 bis 8.5 werden auf der Entwicklungslinie unterstützt; 7.4 ist ohne Support nutzbar; PHP 7.4 ab 6.0.15 ohne Support nutzbar | [PHP-Versionen](../wer-ist-die-exponential-foundation.md#php-versionen) |
| Tests | Die CI führt die Testsuite auf PHP 8.1 bis 8.5 aus und prüft, dass 8.0 startet, bei jedem Push auf `main` und jedem Pull Request | Derselbe Abschnitt |
| Lizenz | GNU GPL v2 oder später; Dokumentation GNU FDL 1.3 oder später | [Wer ist 7x?, Open-Source-Lizenzen](../wer-ist-7x.md#open-source-lizenzen) |
| Pakete | Der Kernel ist das Composer-Paket `se7enxweb/exponential`; seine `composer.json` verlangte am 18. Oktober 2026 66 se7enxweb-Pakete | [7x in Zahlen](../wer-ist-7x.md#7x-in-zahlen) |
| Alter des Codes | Ältester Commit am 2. September 2002; 27.242 Commits auf `main` am 18. Oktober 2026 | Dieselbe Tabelle |

## Datenbanken

"Supported databases (Exponential 6.0.x), in order of preference: 1. SQLite 3 (the default) · 2. PostgreSQL ·
3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL" ([Datenbankunterstützung](../datenbankunterstuetzung.md#die-unterstützten-datenbanken-in-ihrer-reihenfolge)).

| Punkt | Quelle |
|---|---|
| SQLite braucht keinen Datenbankserver; "the whole content repository is one file" | [Datenbankunterstützung](../datenbankunterstuetzung.md#die-unterstützten-datenbanken-in-ihrer-reihenfolge) |
| MongoDB funktioniert "through an adapter that translates the kernel's SQL-shaped calls into MongoDB operations", mit dokumentierten Grenzen; es kam mit 6.0.14 | Dieselbe Seite; [Changelog 6.0.14](../../../changelogs/6.0/6.0.14.md) |
| Microsoft SQL Server hat keinen Treiber; er ist "known to be possible with additional work" | Dieselbe Seite |

## Engines und Webserver

| Engine | Status in der Dokumentation | Quelle |
|---|---|---|
| Exponential Velocity | "recommended for every stage; tier 1"; PHP 8.0+ | [Unterstützte Betriebssysteme, Webserver](../betriebssystemunterstuetzung.md#webserver) |
| Apache httpd 2.4 mit PHP-FPM | Stufe 1, "the classic setup and shared hosting" | Dieselbe Tabelle |
| FrankenPHP | "production-ready alternative" | Dieselbe Tabelle |
| nginx mit PHP-FPM | Stufe 2 | Dieselbe Tabelle |
| Der eingebaute Server von PHP | "development only" | Dieselbe Tabelle |

Betriebssysteme sind in vier Stufen dokumentiert, von "Tested here" bis "Not possible today"; Stufe 3 ist "a reasoned
expectation, not a test result" ([Unterstützte Betriebssysteme, Die vier Stufen](../betriebssystemunterstuetzung.md#die-vier-stufen)).
Die eigenen Fakten und Zahlen von Velocity, mit ihren Messbedingungen, stehen unter [Über Velocity-Releases berichten](ueber-velocity-releases-berichten.md).

## Beschaffen und installieren

- **Git zuerst.** "The recommended way is to get the kernel and every se7enxweb extension as Git clones with their full
  history" ([Git zuerst](../wer-ist-die-exponential-foundation.md#git-zuerst)).
- **Drei Wege zum Code**: `git clone` plus `composer install` (empfohlen), `composer create-project` oder
  `composer require` ([Den Code beschaffen](../../installation/03-den-code-beschaffen.md#32-drei-wege-zum-code)).
- **Installation mit einem Befehl** auf der Entwicklungslinie: `./console exp:install --apply` baut eine vollständige Website auf
  SQLite ([Installation mit einem Befehl](../../funktionen/6.0/installation-mit-einem-befehl.md)).
- **Veröffentlichte Tags ändern sich nie** ([Veröffentlichte Versionen ändern sich nie](veroeffentlichte-versionen-aendern-sich-nie.md)).

## Wie es entwickelt wird

| Praxis | Quelle |
|---|---|
| Commit-Nachrichten beginnen mit `Added:`, `Updated:`, `Fixed:`, `Removed:` oder `Renamed:`, geprüft durch einen Hook und die CI | [CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md) |
| "Every pull request is merged; while it is open, its author squashes and rebases it onto main as the reviews ask, and main's history is never rewritten after the merge" | [Wie das Projekt wächst](../wer-ist-die-exponential-foundation.md#wie-das-projekt-wächst) |
| Templates der Administration enthalten keine Inline-Styles, sodass eine strenge Content Security Policy erzwungen werden kann | [CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md), [Sicherheits-Header](../wer-ist-die-exponential-foundation.md#sicherheits-header-und-die-content-security-policy) |
| Jede Änderung seit Dezember 2023 ist in der Geschichte erklärt und im Ledger festgehalten | [Die Geschichtsseiten als Quelle](die-geschichtsseiten-als-quelle.md) |

## Blickwinkel für technische Leser

| Blickwinkel | Ausgangspunkt |
|---|---|
| Eine PHP-Codebasis von 2002 auf PHP 8.5 | [Die 6.0.x-Releases](../wer-ist-7x.md#die-releases-60x), [Dezember 2025](../../geschichte/2025/2025-12.md) |
| SQLite als Standarddatenbank eines CMS | [Datenbankunterstützung](../datenbankunterstuetzung.md) |
| Ein in PHP geschriebener Webserver | [Über Velocity-Releases berichten](ueber-velocity-releases-berichten.md) |
| Ein KI-Assistent, der nur Entwürfe schreibt | [Gesprächspunkte: Exponential Intelligence](../fuersprecher/gespraechspunkte-exponential-intelligence.md) |
| Jeden Pull Request übernehmen | [Wie das Projekt wächst](../wer-ist-die-exponential-foundation.md#wie-das-projekt-wächst) |

## Technische Behauptungen, die Sie vermeiden sollten

| Vermeiden Sie | Weil |
|---|---|
| „Läuft auf jedem Hosting“ | Betriebssysteme und Server sind in Stufen dokumentiert; Shared Hosting hat Grenzen |
| „Schneller als X“ | Es gibt keinen veröffentlichten Vergleich ([Faire Vergleiche](faire-vergleiche.md)) |
| „Ein Fork von Ibexa“ oder „von eZ Systems“ | Mit keinem der beiden verbunden ([Wer ist 7x?, Kurz gesagt](../wer-ist-7x.md#kurz-gesagt)) |
| „Auf Symfony gebaut“ (für Exponential 6) | Die Symfony-Linie ist Exponential Platform ([Über Platform-Releases berichten](ueber-platform-releases-berichten.md)) |

## Verwandte Seiten

- [Briefing für die Wirtschaftspresse](briefing-fuer-wirtschaftspresse.md)
- [Gesprächspunkte für Entwickler](../fuersprecher/gespraechspunkte-fuer-entwickler.md)
- [Ein Briefing vorbereiten](ein-briefing-vorbereiten.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
