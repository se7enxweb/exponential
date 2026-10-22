---
title: Die Linie Exponential Platform
audience: ecosystem, developer, partner, hosting
category: ecosystem
order: 17
summary: Die auf Symfony basierende Linie Exponential Platform im Ökosystem - Platform 3.2.9, die Dual-Kernel-Distributionen Legacy und Nexus mit enthaltenem Exponential 6, die Starter und Skeletons, die Legacy Bridge und die Forks der Frameworks, die sie auf aktuellem PHP halten.
keywords: Exponential Platform, Platform Legacy, Platform Nexus, Dual-Kernel, Symfony, Legacy Bridge, Nexus-Starter, eZ Platform, Netgen, Media Site, Forks, Distributionen, dual kernel, legacy bridge, Nexus starter, forks, distributions
type: help
---
# Die Linie Exponential Platform

"Exponential Platform is the direct continuation of the software stack known as eZ Platform", die direkte Fortführung
des als eZ Platform bekannten Software-Stacks, und "currently comes in nine different distributions", derzeit in neun
verschiedenen Distributionen (`README.md`, "What is Exponential Platform?"). Die DXP hat eine eigene Seite:
[Exponential Platform DXP](platform-dxp.md).

## Inhalt

1. [Die Distributionen](#die-distributionen)
2. [Dual-Kernel: Legacy und Nexus](#dual-kernel-legacy-und-nexus)
3. [Starter und Skeletons](#starter-und-skeletons)
4. [Die Forks darunter](#die-forks-darunter)
5. [Wo Sie darüber sprechen](#wo-sie-darüber-sprechen)

## Die Distributionen

| Distribution | Was die README sagt |
|---|---|
| Exponential Platform Legacy 5.0.x, 4.6.x, 3.3.x, 2.5.x | "Dual Kernel Symfony CMS which also includes a full copy of Exponential 6", ein Dual-Kernel-Symfony-CMS mit einer vollständigen Kopie von Exponential 6 |
| Exponential Platform 3.2.9 | "Symfony only CMS", ein reines Symfony-CMS |
| Exponential Platform Nexus 1.3.0.x | "Platform v5.x Dual Kernel Symfony 7.4+ CMS which also includes a full copy of Exponential 6.x+" |
| Exponential Platform Nexus 1.2.0.x, 1.1.0.x, 1.0.0.x | Dual-Kernel-Linien für Platform v4.x, v3.x und v2.5.x, jede mit einer vollständigen Kopie von Exponential 6 |

## Dual-Kernel: Legacy und Nexus

- **Legacy**: "Its `composer.json` requires `se7enxweb/exponential` and `se7enxweb/legacy-bridge`, so Exponential 6
  runs next to the Symfony platform", seine `composer.json` verlangt also beide Pakete, sodass Exponential 6 neben der
  Symfony-Plattform läuft ([Exponential Platform Legacy](../../wer-ist-die-exponential-foundation.md#exponential-platform-legacy)).
  Für Websites, die "want Symfony beside them and move forward step by step", Symfony daneben möchten und Schritt für
  Schritt vorangehen ([Legacy Bridge](../../../funktionen/6.0/legacy-bridge.md)).
- **Nexus**: "Platform v2/v3/v4/v5 + Exponential v6.x (Legacy) + Netgen Media Site + NG Layouts"; liefert den
  Layout-Editor, mit einer CJW-Variante und einem Starter ([Exponential Platform Nexus](../../wer-ist-die-exponential-foundation.md#exponential-platform-nexus)).
  Für "Agencies and teams who want a complete website with demo content, layouts and an administration in minutes",
  Agenturen und Teams, die in Minuten eine vollständige Website mit Demo-Inhalten, Layouts und Administration möchten.

## Starter und Skeletons

| Sie möchten | Verwenden Sie | Lesen Sie |
|---|---|---|
| Eine Website mit Symfony 7.4 / PHP 8.4+ mit Layouts, REST und GraphQL | `exponential-platform-nexus-starter` oder `exponential-platform-dxp-skeleton` | [Nexus-Starter](../../../funktionen/6.0/plattform-nexus-starter.md), [DXP-Skeleton](../../../funktionen/6.0/plattform-dxp-skeleton.md) |
| Eine Exponential-6-Website behalten und Symfony ergänzen | `se7enxweb/legacy-bridge` | [Legacy Bridge](../../../funktionen/6.0/legacy-bridge.md) |
| Eine Plattform-Datenbank ohne Server | Installation mit SQLite | [SQLite für die Plattform](../../../funktionen/6.0/plattform-sqlite-installation.md) |

Quelle: [Das Ökosystem der Exponential-Plattform](../../../geschichte/oekosystem.md), "How to choose".

## Die Forks darunter

Die Linie läuft auf aktuellem PHP, weil 7x Forks der Pakete pflegt, von denen sie abhängt: die Kernels des
Content-Repositorys, die Admin-UIs, Feldtypen, Suche, HTTP-Cache, GraphQL, die Legacy Bridge, layouts-core sowie die
Forks von Symfony 3.4, Twig 2.x und Doctrine für PHP 8.4 und 8.5. Jeder hat eine Seite in
[Das Ökosystem der Exponential-Plattform](../../../geschichte/oekosystem.md); welcher Fork welches Ursprungspaket ersetzt,
steht in der [Paketübersicht](../../../spezifikationen/6.0/platform-paketzuordnung.md).

## Wo Sie darüber sprechen

- Website: <https://platform.exponential.earth>.
- Forum: <https://share.exponential.earth/forums/exponential-platform> (`README.md`).

## Verwandte Seiten

- [Produkte](README.md)
- [Exponential Platform DXP](platform-dxp.md)
- [Kunden von eZ Platform und Ibexa migrieren](../../hosting/migrationen/kunden-von-ez-platform-und-ibexa-migrieren.md)
- [Upstream-Projekte und Forks](../php-stack/upstream-projekte-und-forks.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
