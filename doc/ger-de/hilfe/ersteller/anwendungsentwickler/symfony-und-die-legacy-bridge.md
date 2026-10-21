---
title: Symfony und die Legacy Bridge
audience: app-creator, developer
category: app-creation
order: 640
summary: Wann eine Anwendung Symfony braucht - die Legacy Bridge, die den Kernel von Exponential 6 innerhalb der Symfony-Plattform ausführt, welche Linie zu welcher Plattform passt, was in Legacy bleibt und wie Sie einen schrittweisen Umstieg planen.
keywords: Symfony, Legacy Bridge, Plattform, Migration, hybrid, schrittweiser Umstieg, Anwendungsentwickler, legacy bridge, legacy-bridge, Twig, platform, migration, legacy_admin, ngsymfonytools, site-legacy-bundle, hybrid, app creator
type: help
---
# Symfony und die Legacy Bridge

Diese Seite richtet sich an Anwendungsentwickler, die Symfony für einen Teil einer Anwendung in Betracht ziehen oder ein
Projekt betreuen, das beides ausführt. Die Legacy Bridge ist ein Symfony-Bundle, das den Kernel von Exponential 6
innerhalb einer Installation der Symfony-Plattform lädt; eine einzige Webanwendung bedient dann die Legacy-Administration
und die Legacy-Templates sowie den Symfony-Stack, auf derselben Datenbank
([Legacy Bridge](../../../../eng-us/features/6.0/legacy-bridge.md)).

## Inhalt

1. [Wann es sich lohnt](#wann-es-sich-lohnt)
2. [Die Bestandteile](#die-bestandteile)
3. [Welche Linie für welche Plattform](#welche-linie-für-welche-plattform)
4. [Einen schrittweisen Umstieg planen](#einen-schrittweisen-umstieg-planen)

## Wann es sich lohnt

| Verwenden Sie die Bridge, wenn | Bleiben Sie allein beim Legacy-Kernel, wenn |
|---|---|
| Sie eine Website auf die Symfony-Plattform umziehen und die Redaktion weiterarbeiten muss | Ihre Anwendung aus Erweiterungen, Modulen und Templates gebaut ist |
| Sie Twig oder das Symfony-Routing für die öffentliche Website wollen, mit der Legacy-Administration für die Redaktion | Sie keine Symfony-Funktionen brauchen |
| Sie Legacy-Erweiterungen in einem Plattformprojekt verfügbar halten | ein einziger Stack einfacher zu betreiben und zu besetzen ist |

## Die Bestandteile

Laut der Funktionsseite: `se7enxweb/legacy-bridge` (das Bundle), `se7enxweb/ibexa-legacy-bridge` (eine Portierung für
eine spätere Plattformgeneration), `se7enxweb/site-legacy-bundle`, `se7enxweb/ngsymfonytools` und
`se7enxweb/exponential-legacy-installer`. Die Legacy-Seite wird über einen Siteaccess namens `legacy_admin` erreicht.
Die Spezifikation ist [Legacy-Bridge-Bundle](../../../spezifikationen/6.0/legacy-bridge-bundle.md).

## Welche Linie für welche Plattform

Das Bundle hat eine Linie je Plattformgeneration (die Linie für Plattform 2.5 auf `master`, spätere Linien auf eigenen
Branches); die Tabelle mit den Symfony- und PHP-Versionen steht unter
[Legacy Bridge](../../../../eng-us/features/6.0/legacy-bridge.md#which-version-for-which-platform). Prüfen Sie die Produktfamilie,
bevor Sie einen Stack zusagen: Nicht jedes Plattformprodukt enthält den Legacy-Kernel oder den Layouts-Editor
([Was Exponential ist: die Produktfamilie](../../fuehrung/was-exponential-ist.md)).

## Einen schrittweisen Umstieg planen

1. Behalten Sie Inhaltsklassen, Rollen und die Bearbeitung im Legacy-Kernel.
2. Verlagern Sie das öffentliche Rendern Seitentyp für Seitentyp nach Twig.
3. Halten Sie die Geschäftslogik in Diensten, die beide Seiten aufrufen können.
4. Geben Sie Legacy-Templates erst auf, wenn nichts sie mehr rendert.
5. Testen Sie bei jedem Release beide Stacks.

## Verwandte Seiten

- [Architektur für Anwendungsentwickler](architektur-fuer-anwendungsentwickler.md)
- [Migration von eZ Platform und Ibexa](../../../installation/16-migration-von-ez-platform-und-ibexa.md)
- [Site-Bundles der Plattform](../../../../eng-us/features/6.0/platform-site-bundles.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
