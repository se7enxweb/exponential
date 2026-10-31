---
title: DXP, Legacy, Nexus oder Exponential 6 wählen
audience: dxp-developer
category: dxp-development
order: 4
summary: Eine Entscheidungshilfe für Entwickler - welches Produkt der Exponential-Familie zu einem Projekt passt, mit den Fragen, die Sie stellen sollten, einer Vergleichstabelle, die aus den Paketen jeder Distribution gelesen ist, und den Fallen (Nexus v5 hat keinen Legacy-Kernel, die DXP hat keinen Layout-Editor).
keywords: wählen, Vergleich, DXP, Legacy, Nexus, Exponential 6, zwei Kernel, Legacy Bridge, Symfony, Layouts, welches Produkt, Entscheidung, choose, comparison, dual kernel, legacy bridge, which product, decision
type: help
---
# DXP, Legacy, Nexus oder Exponential 6 wählen

Diese Seite richtet sich an Entwickler und technische Leiter, die vor dem Start eines Projekts ein Produkt wählen müssen.
Sie vergleicht die vier Kandidaten anhand von Fakten aus ihren Paketen und ihrer Dokumentation und nennt die Fragen, die
entscheiden.

## Inhalt

1. [Zuerst drei Fragen](#zuerst-drei-fragen)
2. [Der Vergleich](#der-vergleich)
3. [Entscheidungen nach Situation](#entscheidungen-nach-situation)
4. [Fallen](#fallen)
5. [Später umentscheiden](#später-umentscheiden)

## Zuerst drei Fragen

1. **Symfony oder nur PHP?** Möchte das Team Symfony-Controller, Twig und Doctrine, passt die Plattformlinie. Möchte das
   Team einen CMS-Kernel ohne Framework darunter, passt Exponential 6.
2. **Gibt es eine Exponential-6-Website, die bleiben soll?** Dann halten die Distributionen mit zwei Kerneln (Legacy,
   die früheren Nexus-Linien) sie neben Symfony am Laufen.
3. **Müssen Redakteure Seiten mit Blöcken anordnen?** Die DXP liefert nur den Layouts Core mit, keinen Editor. Nexus
   liefert den Editor von Exponential Layouts für Symfony mit; Exponential 6 hat Exponential Layouts im Legacy-Kernel.

## Der Vergleich

| | Exponential Platform DXP | Exponential Platform Legacy | Exponential Platform Nexus | Exponential 6 |
|---|---|---|---|---|
| Kernel | Platform v5 | Platform plus der Kernel von Exponential 6 | Platform; die Linien 1.0 bis 1.2 und 2.5 auch der Kernel von Exponential 6 | Exponential 6 |
| Framework | Symfony 7.4 | Symfony (je Linie) | Symfony (je Linie; 7.4 für 1.3 und den v5-Starter) | keines |
| PHP | 8.3 oder neuer | je Linie | 8.4 oder neuer für 1.3 und den v5-Starter | siehe [Erste Schritte](../erste-schritte.md) |
| Templates | Twig | Twig und `.tpl` | Twig (und `.tpl` auf den Linien mit zwei Kerneln) | `.tpl` |
| Seitenbaukasten | nur Layouts Core, kein Editor | je Linie | Exponential Layouts mit seinem Editor | Exponential Layouts |
| Demo-Inhalte | keine | je Linie | ja | Seitenpakete |
| Beginnen mit | [DXP-Skeleton](../../funktionen/6.0/plattform-dxp-skeleton.md) | [Legacy Bridge](../../funktionen/6.0/legacy-bridge.md) | [Nexus-Starter](../../funktionen/6.0/plattform-nexus-starter.md) | [Erste Schritte](../erste-schritte.md) |

Quellen: die Anforderungen des Metapakets,
[Exponential Platform Nexus: eine fertige Website in Minuten](../../funktionen/6.0/plattform-nexus-starter.md#was-es-ist)
(die Tabelle der Linien), [Legacy Bridge](../../funktionen/6.0/legacy-bridge.md#welche-version-für-welche-plattform) und
[Welches Produkt zu Ihnen passt](../wer-ist-die-exponential-foundation.md#welches-produkt-zu-ihnen-passt).

## Entscheidungen nach Situation

| Situation | Wählen Sie | Warum |
|---|---|---|
| Neues Projekt, Symfony-Team, Inhalte über APIs und Twig | DXP | sauberer einzelner Kernel, REST und GraphQL enthalten |
| Neues Projekt, Redakteure müssen Seiten aus Blöcken bauen, Symfony-Team | Nexus (1.3 oder der v5-Starter) | liefert den Editor von Exponential Layouts und eine Demo-Website mit |
| Eine Exponential-6-Website, die weiterlaufen muss, während ein Symfony-Frontend wächst | Legacy | die Legacy Bridge bedient beide Kernel aus einer Datenbank |
| Eine Website auf eZ Platform 3.3 oder Ibexa OSS 4.6, die modernisiert werden soll | DXP (v5) oder das [Skeleton 4.6.x](das-skeleton-v4x.md) | dieselbe Abstammung des Inhaltsmodells ([Umstieg von Ibexa OSS oder eZ Platform](umstieg-von-ibexa-oss-oder-ez-platform.md)) |
| Ein CMS mit Shop, Workflows und Multisite nur auf PHP | Exponential 6 | diese Funktionen sind in seinem Kernel |
| Lernen, eine winzige Website | Exponential Basic | [Welches Produkt zu Ihnen passt](../wer-ist-die-exponential-foundation.md#welches-produkt-zu-ihnen-passt) |

## Fallen

- **"Nexus bedeutet Legacy."** Nicht bei v5: Nexus 1.3.0.x und der v5-Starter laufen auf Platform v5 ohne Legacy-Kernel.
  Nur die Linien 1.0, 1.1, 1.2 und 2.5 enthalten ihn ([Nexus-Starter](../../funktionen/6.0/plattform-nexus-starter.md#was-es-ist)).
- **"Die DXP hat einen Layout-Editor."** Hat sie nicht. `se7enxweb/layouts-core` ist der Kern für Modell und Ausgabe
  ([Layouts Core in der DXP](layouts-core-in-der-dxp.md)).
- **"Legacy bedeutet alt."** "Legacy" ist der Kernel von Exponential 6, der neben einer Symfony-Plattform läuft;
  Exponential 6 selbst ist die gepflegte Linie
  ([Die Produktfamilie kennen](../fuersprecher/die-produktfamilie-kennen.md#häufige-verwechslungen)).
- **Versionsnummern.** Nennen Sie immer eine Version, wenn Sie ein Nexus-Projekt anlegen; ohne sie kann Composer einen
  Upstream-Tag wählen ([Nexus-Starter](../../funktionen/6.0/plattform-nexus-starter.md#was-es-ist)).

## Später umentscheiden

- Von DXP zu Nexus: Beides sind Projekte auf Platform v5; die Layouts-Pakete hinzuzufügen, die ein
  Nexus-Projekt verwendet, ist eine Änderung in Composer, aber testen Sie sie an einer Kopie, denn Layouts 2.0 braucht
  PHP 8.4 ([Layouts auf der Plattform](../../funktionen/6.0/plattform-layouts-core-fork.md#grenzen)).
- Von Exponential 6 zur Plattform: Planen Sie eine Inhaltsmigration oder betreiben Sie während des Umzugs beide Kernel
  mit der Legacy Bridge ([Umstieg von Exponential 6](umstieg-von-exponential-6.md)).

## Verwandte Seiten

- [Was die DXP mitliefert und was nicht](was-die-dxp-mitliefert-und-was-nicht.md)
- [Die Produktfamilie kennen](../fuersprecher/die-produktfamilie-kennen.md)
- [Kunden von eZ Platform und Ibexa migrieren](../hosting/migrationen/kunden-von-ez-platform-und-ibexa-migrieren.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
