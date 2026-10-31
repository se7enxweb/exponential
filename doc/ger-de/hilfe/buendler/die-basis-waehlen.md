---
title: Die Basis eines Bundles wählen
audience: bundler, partner, developer
category: bundling
order: 30
summary: Wie Sie wählen, worauf Ihr Bundle aufbaut - Exponential 6 für sich allein oder eine Platform-Distribution, die es enthält (Legacy, Nexus) - mit den entscheidenden Fakten - PHP-Bereich, Legacy-Kernel, Layout-Editor, Installer, Datenbank - und einer Entscheidungstabelle.
keywords: Basis, wählen, Entscheidung, Legacy-Distribution, Legacy-Kernel, Layout-Editor, PHP-Version, base, choose, Exponential 6, Legacy distribution, Nexus, Platform, DXP, legacy kernel, Symfony, layouts editor, PHP version, decision
type: help
---
# Die Basis eines Bundles wählen

Diese Seite richtet sich an Bündler, die entscheiden, wovon ihr Bundle ausgeht. Exponential 6 gibt es in zwei Formen:
für sich allein (das Paket `se7enxweb/exponential`) und innerhalb der Exponential-Platform-Distributionen **Legacy**
und **Nexus**, die daneben einen Symfony-Stack ergänzen. Jede Form hat ihren eigenen Installer, PHP-Bereich und
Update-Weg, daher prägt die Wahl jede spätere Seite Ihres Bundles.

## Inhalt

1. [Die Kandidaten](#die-kandidaten)
2. [Die entscheidenden Fakten](#die-entscheidenden-fakten)
3. [Entscheidungstabelle](#entscheidungstabelle)
4. [Ein Hinweis zu Exponential Platform DXP](#ein-hinweis-zu-exponential-platform-dxp)
5. [Halten Sie Ihre Wahl fest](#halten-sie-ihre-wahl-fest)

## Die Kandidaten

| Basis | Was sie ist | Quelle |
|---|---|---|
| **Exponential 6** | Der CMS-Kernel, seine erforderlichen Erweiterungen und Designs; installiert mit dem Einrichtungsassistenten, dem Kickstarter oder `exp:install` | `composer.json` von `se7enxweb/exponential`; [Exponential 6 als Basis](exponential-6-als-basis.md) |
| **Exponential Platform Legacy** | "Dual Kernel Symfony CMS which also includes a full copy of Exponential 6" (README), also ein Symfony-CMS mit zwei Kerneln und einer vollständigen Kopie von Exponential 6; Linien 2.5.x bis 5.0.x | [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md#exponential-platform-legacy) |
| **Exponential Platform Nexus** | Ein fertiges Website-Projekt: Platform, Exponential Layouts, Demo-Inhalte; die Linien 1.0.0.x bis 1.2.0.x und die 2.5-Tags enthalten den Legacy-Kernel, die Linie 1.3.0.x (Platform v5) nicht | [Exponential Platform Nexus](../../funktionen/6.0/plattform-nexus-starter.md) |

## Die entscheidenden Fakten

| Fakt | Exponential 6 | Legacy | Nexus |
|---|---|---|---|
| Kernel von Exponential 6 enthalten | ja | ja (`se7enxweb/exponential` und `se7enxweb/legacy-bridge`) | 1.0.0.x bis 1.2.0.x und 2.5: ja; 1.3.0.x und der v5-Starter: nein |
| Symfony-Stack | nein | ja | ja |
| Installer | Einrichtungsassistent, Kickstarter, `exp:install` | der eigene der Distribution; siehe deren README | `exponential:install` (Typen wie `exponential-media`) |
| PHP | 8.0 bis 8.5 für 6.0.15 (`composer.json`) | je Linie, siehe deren README | je Linie: 1.3.0.x braucht PHP 8.4 oder neuer |
| Datenbanken | SQLite 3 (Standard), PostgreSQL, MongoDB, MariaDB, Oracle, MySQL | je Linie | der v5-Starter: SQLite, MySQL, MariaDB, PostgreSQL |
| Seitenbaukasten für Layouts | Exponential Layouts (seit 6.0.15 erforderlich) | je Linie | Exponential Layouts |
| Demo-Inhalte | Seitenpakete mit oder ohne Demo-Inhalte | je Linie | die Nexus-Demo (`exponential-media`) |

Fakten zu Nexus: [Exponential Platform Nexus](../../funktionen/6.0/plattform-nexus-starter.md). Fakten zu Exponential 6:
[Datenbankunterstützung](../datenbankunterstuetzung.md), [Kapitel 2: Voraussetzungen](../../installation/02-voraussetzungen.md).

> **Hinweis**
> Nennen Sie immer eine Version, wenn Sie Nexus mit Composer installieren: `composer create-project
> se7enxweb/exponential-platform-nexus` ohne Version wählt das Upstream-Paket `3.1.6`, das "das Upstream-Paket
> `netgen/media-site` ist, kein Nexus-Release" ([Nexus](../../funktionen/6.0/plattform-nexus-starter.md#was-es-ist)).

## Entscheidungstabelle

| Ihr Bundle | Basis |
|---|---|
| Eine Ein-Klick-Installation beim Hoster, ein Container, ein Server-Image "des CMS" | Exponential 6 |
| Ein Bundle für Shared Hosting ohne Shell | Exponential 6 (der Einrichtungsassistent braucht nur einen Browser) |
| Ein Bundle, das unter PHP 8.0 bis 8.3 laufen muss | Exponential 6 (Linie 6.0.15) oder eine Legacy- oder Nexus-Linie, die dieses PHP unterstützt |
| Ein Bundle für Teams, die Symfony und Twig neben Exponential 6 wollen | Legacy |
| Eine vollständige Demo-Website mit Layouts und Inhalten, für Agenturen | Nexus (wählen Sie die Linie nach der gewünschten Platform-Generation) |
| Ein Bundle für eine bestehende Exponential-Website, die weiterlaufen muss | Exponential 6 oder Legacy, wenn Symfony daneben gewünscht ist |

## Ein Hinweis zu Exponential Platform DXP

Exponential Platform DXP ist ein eigenständiges Produkt, das nur auf Symfony aufbaut. Es hat **keinen Legacy-Kernel**,
keinen Layout-Editor, keinen Freigabe-Workflow und keinen Zeitplaner
([Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md#exponential-platform-dxp)).
Die Seiten dieses Bereichs behandeln Bundles, die Exponential 6 enthalten; wenn Sie DXP bündeln, nehmen Sie dessen
Fakten aus seinem eigenen Repository und wenden Sie diese Seiten nicht ungeprüft darauf an.

## Halten Sie Ihre Wahl fest

```text
Base:                 Exponential 6 / Legacy <line> / Nexus <line>
Exact version:        <tag>
PHP range we support: <from> to <to>
Database(s):          <list>
Installer we call:    exp:install / kickstarter / exponential:install
Why this base:        <one sentence>
```

Übernehmen Sie es in das [Arbeitsblatt für Bundles](arbeitsblatt-fuer-bundles.md).

## Verwandte Seiten

- [Exponential 6 als Basis](exponential-6-als-basis.md)
- [Platform-Distributionen als Basis](platform-distributionen-als-basis.md)
- [Versionen, Tags und Pinning](versionen-tags-und-pinning.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
