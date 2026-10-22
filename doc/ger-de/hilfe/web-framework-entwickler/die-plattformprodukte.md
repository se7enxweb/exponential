---
title: Die Produkte von Exponential Platform
audience: web-framework-developer, developer
category: web-frameworks
order: 420
summary: Die Symfony-basierte Seite der Exponential-Familie, für Framework-Entwickler, die einen Stack auswählen - Exponential Platform 3.2.9, Legacy, Nexus, DXP und der Nexus Starter, welche davon den Kernel von Exponential 6 enthalten, welche den Layout-Editor, GraphQL oder SQLite mitbringen, und wie Sie eine Distribution prüfen, bevor Sie eine Funktion zusagen.
keywords: Exponential Platform, Platform Legacy, Nexus, DXP, Nexus Starter, Nexus starter, Symfony, Twig, GraphQL, zwei Kernel, dual kernel, Legacy-Kernel, legacy kernel, Layouts, layouts, SQLite, create-project, Skeleton, skeleton, Produktfamilie, product family, Web-Framework-Entwickler, web framework developer
type: help
---
# Die Produkte von Exponential Platform

Diese Seite richtet sich an Entwickler, die Symfony kennen und fragen, ob es ein Exponential gibt, das sie als
gewöhnliches Symfony-Projekt aufbauen können. Das gibt es: Neben Exponential 6 pflegt 7x die Symfony-basierte Linie
**Exponential Platform**, die von eZ Platform und Ibexa OSS abstammt. Einige ihrer Distributionen enthalten den Kernel
von Exponential 6, andere nicht. Die folgenden Beschreibungen stammen aus der Dokumentation und der `composer.json`
der jeweiligen Distribution, wie unter [Die Plattformlinie](../wer-ist-7x.md#die-platform-linie-exponential-platform-legacy-nexus-und-dxp)
zusammengefasst.

## Inhalt

1. [Die Distributionen](#die-distributionen)
2. [Was jede enthält](#was-jede-enthält)
3. [Auswählen](#auswählen)
4. [Prüfen, bevor Sie zusagen](#prüfen-bevor-sie-zusagen)

## Die Distributionen

| Distribution | Was sie ist | Enthält Exponential 6? |
|---|---|---|
| **Exponential Platform 3.2.9** | ein CMS auf den letzten freien Versionen von eZ Platform, „Symfony / New Stack Only“ | Nein |
| **Exponential Platform Legacy** | die Symfony-Plattform plus Legacy Bridge und `se7enxweb/exponential` („zwei Kernel“) | Ja |
| **Exponential Platform Nexus** | Plattform, Exponential 6, Netgen Media Site und Netgen Layouts, mit Demo-Inhalten | die Linien 1.0.0.x bis 1.2.0.x und Nexus 2.5 ja; 1.3.0.x (Platform v5) nein |
| **Nexus Starter** | die Nexus-Site von Platform v5 als eigenes Repository | Nein |
| **Exponential Platform DXP** | eine reine Symfony-Plattform für digitale Erlebnisse, die Fortsetzung der Linie Ibexa DXP OSS | Nein |

Details pro Linie: [Nexus Starter](../../funktionen/6.0/plattform-nexus-starter.md),
[DXP-Skeleton](../../funktionen/6.0/plattform-dxp-skeleton.md),
[Legacy Bridge](../../funktionen/6.0/legacy-bridge.md).

## Was jede enthält

| | Platform 3.2.9 | Legacy | Nexus | DXP |
|---|---|---|---|---|
| Symfony, Twig, REST der Plattform | ja | ja | ja | ja |
| GraphQL | in seinen Abhängigkeiten | `composer.json` der Linie prüfen | ja, mit der REST-API | ja, mit GraphiQL |
| Kernel und Legacy-Administration von Exponential 6 | nein | ja | Linien bis 1.2.0.x und 2.5 | nein |
| Netgen-Layouts-Editor | nein | nicht angegeben | ja | nein (nur `se7enxweb/layouts-core`) |
| Demo-Site | nein | nein | ja | nein (leeres Skeleton) |

SQLite-Installationen gibt es für mehrere Plattformlinien ([SQLite für die Plattform](../../funktionen/6.0/plattform-sqlite-installation.md));
der Nexus Starter für v5 verwendet SQLite als Standard für die Entwicklung.

Die DXP liefert **keinen** Layout-Editor, keinen Freigabe-Workflow, keinen Scheduler und keinen Legacy-Kernel mit;
ihre Abhängigkeiten enthalten nichts davon ([Was Exponential ist: die Produktfamilie](../fuehrung/was-exponential-ist.md#die-produktfamilie)).

## Auswählen

| Sie möchten | Beginnen Sie mit |
|---|---|
| Eine PHP-Anwendung mit Inhaltsmodell, Berechtigungen und Administrationsoberfläche, ohne Symfony | Exponential 6 |
| Ein neues Symfony-Projekt mit CMS, REST und GraphQL, ohne Legacy-Kernel | Exponential Platform DXP ([DXP-Skeleton](../../funktionen/6.0/plattform-dxp-skeleton.md)) |
| Eine fertige Demo-Site mit Netgen Layouts auf Symfony | Exponential Platform Nexus |
| Eine Site mit Exponential 6 und Symfony daneben | Exponential Platform Legacy ([Symfony Legacy Bridge](symfony-und-die-legacy-bridge.md)) |
| Ein Team auf eZ Platform 3, das ein aktuelles PHP möchte | Exponential Platform 3.2.9 |

Die übrigen Seiten für diese Zielgruppe beschreiben Exponential 6. Die Plattformprodukte folgen den Konventionen von
Symfony, sodass Ihr Symfony-Wissen direkt darauf anwendbar ist ([PHP 8.4 und 8.5 für den älteren Symfony-Stack](../../funktionen/6.0/plattform-php85-framework-forks.md),
[Administrationsoberfläche der Plattform](../../funktionen/6.0/plattform-admin-ui-fork.md),
[Layouts auf der Plattform](../../funktionen/6.0/plattform-layouts-core-fork.md),
[Site-Bundles](../../funktionen/6.0/plattform-site-bundles.md)).

## Prüfen, bevor Sie zusagen

- Lesen Sie die `composer.json` der Distribution für die Version, die Sie installieren werden; genau das erhalten Sie.
- Geben Sie bei `composer create-project` eine Version an: Manche Repositories tragen auch Upstream-Tags, und
  fünfteilige Tags gibt es auf Packagist nicht ([Nexus Starter](../../funktionen/6.0/plattform-nexus-starter.md)).
- Die Paketübersicht zeigt, welcher Fork welches Upstream-Paket ersetzt
  ([Paketübersicht der Plattform](../../spezifikationen/6.0/platform-paketzuordnung.md)).

## Verwandte Seiten

- [Wer ist die Exponential Foundation? Die Produktfamilie](../wer-ist-die-exponential-foundation.md#die-produktfamilie-im-überblick)
- [Wann Sie Exponential wählen sollten](wann-sie-exponential-waehlen-sollten.md)
- [Konsolenbefehle der Plattform](../../spezifikationen/6.0/platform-konsolenbefehle.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
