---
title: Die Produktfamilie für Reporter
audience: press
category: press-media
order: 13
summary: Jedes Produkt der Exponential-Familie in einer Zeile, die ein Reporter drucken kann - Exponential 6, Basic, die vier Distributionen von Exponential Platform, Velocity, Exponential Intelligence, Exponential Layouts, AdminUI, die Hilfe und die Erweiterungen - mit Lizenz, Stand am 18. Oktober 2026 und den Verwechslungen, die Sie vermeiden sollten.
keywords: Produktfamilie, Produkte, Exponential 6, Exponential Basic, Exponential Platform, Legacy, Nexus, DXP, Velocity, Exponential Intelligence, Exponential Layouts, AdminUI, Erweiterungen, welches Produkt, product family, products, extensions, which product
type: help
---
# Die Produktfamilie für Reporter

Diese Seite ist für Journalisten, die das richtige Produkt nennen müssen. „Exponential“ meint meist den Kernel,
Exponential 6, aber die Familie hat weitere Mitglieder, und mehrere Berichte haben sie verwechselt. Die vollständigen
Beschreibungen stehen unter [Die Produkte im Einzelnen](../wer-ist-die-exponential-foundation.md#die-produkte-im-einzelnen).

## Inhalt

1. [Die Familie in einer Tabelle](#die-familie-in-einer-tabelle)
2. [Die beiden Linien: PHP und Symfony](#die-beiden-linien-php-und-symfony)
3. [Stand am 18. Oktober 2026](#stand-am-18-oktober-2026)
4. [Verwechslungen, die Sie vermeiden sollten](#verwechslungen-die-sie-vermeiden-sollten)
5. [Die Namen schreiben](#die-namen-schreiben)

## Die Familie in einer Tabelle

| Produkt | Eine Zeile, die Sie drucken können | Lizenz |
|---|---|---|
| **Exponential 6** | Das PHP-Content-Management-System, Framework und Shop; das, was „Exponential“ meist meint | GPL v2 oder später |
| **Exponential Basic** | Das kleine, einfache CMS der Linie 2.x, „for everyone, educational purposes, personal use“ | GPL v2 oder später |
| **Exponential Platform 3.2.9** | Ein reines Symfony-CMS auf Basis der letzten freien Versionen von eZ Platform | laut seinem Paket |
| **Exponential Platform Legacy** | Die Symfony-Plattform mit enthaltenem Exponential 6, ein „dual kernel“ (zwei Kernels) | laut seinem Paket |
| **Exponential Platform Nexus** | Platform, Exponential 6, Netgen Layouts und Media Site mit Demoinhalten | laut seinem Paket |
| **Exponential Platform DXP** | Eine freie Open-Source-Plattform für digitale Erlebnisse auf Symfony, die die Linie eZ Platform und Ibexa OSS fortführt | „GPL-2.0-or-later or proprietary“ (seine composer.json) |
| **Exponential Velocity** | Ein in PHP geschriebener Web- und Anwendungsserver, der die Anwendung zwischen den Anfragen geladen hält | MIT |
| **Exponential Intelligence** | Ein Schreibassistent in der Administration, der Entwürfe schreibt und nie selbst veröffentlicht | (Paket `se7enxweb/expai`) |
| **Exponential Layouts** | Ein visueller Seitenbaukasten: Layouts, Zonen, Blöcke und Regeln | (Pakete `explayouts*`) |
| **AdminUI** | Eine alternative Administration, eine Portierung der Netgen AdminUI | (Paket `exp_adminui`) |
| **Das Hilfesystem** | Die Dokumentation in der Administration | GFDL 1.3 für die Seiten |
| **Die Erweiterungen** | 66 Pakete von se7enxweb, die der Kernel verlangt | jeweils eigene |

Quellen: [Die Produktfamilie im Überblick](../wer-ist-die-exponential-foundation.md#die-produktfamilie-im-überblick),
[Open-Source-Lizenzen](../wer-ist-7x.md#open-source-lizenzen). Wo eine Zelle statt einer Lizenz ein Paket nennt, prüfen
Sie die eigene `composer.json` des Pakets, bevor Sie eine Lizenz drucken.

## Die beiden Linien: PHP und Symfony

```text
PHP line (Exponential 6)                    Symfony line (Exponential Platform)
  Exponential 6  <- the kernel               Platform 3.2.9   (Symfony only)
  + Velocity, Layouts, Intelligence,         DXP              (Symfony only)
    AdminUI, help, extensions                Legacy           (Symfony + Exponential 6)
  Exponential Basic (2.x, small)             Nexus            (Symfony + Exponential 6 + layouts + demo)
```

„Legacy“ und „Nexus“ betreiben Exponential 6 neben Symfony; „Platform 3.2.9“ und „DXP“ nicht
([Die Plattform-Linie](../wer-ist-7x.md#die-platform-linie-exponential-platform-legacy-nexus-und-dxp)). Eine Ausnahme:
Die Nexus-Linie 1.3.0.x für Platform v5 und der v5-Starter haben keinen Legacy-Kernel
([Nexus-Starter](../../../eng-us/features/6.0/platform-nexus-starter.md)).

## Stand am 18. Oktober 2026

| Produkt | Stand | Quelle |
|---|---|---|
| Exponential 6 | 6.0.14 veröffentlicht am 7. Juni 2026; 6.0.15 in Entwicklung | [Release-Übersicht](../../../eng-us/release-overview) |
| Exponential Basic | v2.4.0.3 | Beschreibung des Repositorys |
| Velocity | Teil von Exponential seit dem 22. September 2026; das README nennt es als erforderlich in `~0.0.4.54` | [Chronik von Velocity](../../../eng-us/history/velocity/README.md) |
| Intelligence, Layouts, AdminUI | Vom Kernel verlangt seit der Linie 6.0.15 | composer.json |
| Platform DXP | „Stable; Open Source“ | seine composer.json |
| Nexus-Starter | „Currently In Public Beta“ | die Beschreibung seines Repositorys |

## Verwechslungen, die Sie vermeiden sollten

| Verwechslung | Richtig |
|---|---|
| „Exponential Platform DXP enthält den Seitenbaukasten.“ | DXP liefert keinen Layout-Editor; Nexus schon. |
| „Legacy heißt die alte Version.“ | Legacy ist Exponential 6, das neben einer Symfony-Plattform läuft; Exponential 6 selbst ist die gepflegte Linie. |
| „Velocity ist Qbix.“ | Velocity begann als Fork der Webserver-Engine Qbix; das Produkt ist Exponential Velocity. |
| „Die KI veröffentlicht Artikel.“ | Exponential Intelligence schreibt nur Entwürfe. |
| „Exponential ist ein Symfony-CMS.“ | Exponential 6 ist ein eigenes PHP-Framework; die Symfony-Linie ist Exponential Platform. |

## Die Namen schreiben

Genaue Schreibweisen und Kurzformen: [Kurzreferenz zu Namen für Nachrichtenredakteure](namens-kurzreferenz-fuer-redaktionen.md).

## Verwandte Seiten

- [Die Produktfamilie kennen](../fuersprecher/die-produktfamilie-kennen.md)
- [Themenbeiträge: Übersicht](themenbeitraege-uebersicht.md)
- [Thema: Exponential Platform DXP](thema-exponential-platform-dxp.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
