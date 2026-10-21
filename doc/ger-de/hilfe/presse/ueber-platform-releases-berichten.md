---
title: Über Releases von Exponential Platform berichten
audience: press
category: press-media
order: 109
summary: Wie Sie über Releases der Symfony-basierten Familie Exponential Platform berichten - Platform 3.2.9, Legacy, Nexus, der Nexus-Starter und DXP - was jedes Produkt ist, ihre neuesten getaggten Releases, wo die Notes stehen und welche Verwechslungen Sie vermeiden sollten.
keywords: Exponential Platform, Platform Legacy, Platform Nexus, Nexus-Starter, Platform DXP, Symfony, Release, Release Notes, Dual Kernel, Layout-Editor, Ibexa, eZ Platform, Nexus starter, release, release notes, dual kernel, layouts editor
type: help
---
# Über Releases von Exponential Platform berichten

Diese Seite ist für Fach- und Branchenpresse, die über die Familie Exponential Platform berichtet. Das sind
Distributionen auf Basis des Symfony-Frameworks, getrennt vom Kernel Exponential 6 und nach einem eigenen Zeitplan
veröffentlicht. Sie lassen sich leicht miteinander verwechseln; prüfen Sie daher, zu welchem Produkt ein Release gehört,
bevor Sie schreiben.

## Inhalt

1. [Die Familie in einer Tabelle](#die-familie-in-einer-tabelle)
2. [Neueste getaggte Releases](#neueste-getaggte-releases)
3. [Wo die Notes stehen](#wo-die-notes-stehen)
4. [Verwechslungen, die Sie vermeiden sollten](#verwechslungen-die-sie-vermeiden-sollten)
5. [Namen und andere Unternehmen](#namen-und-andere-unternehmen)

## Die Familie in einer Tabelle

Beschreibungen aus [Wer ist die Exponential Foundation?, Die Produkte im Einzelnen](../wer-ist-die-exponential-foundation.md#die-produkte-im-einzelnen);
die Nexus-Linien aus [Exponential Platform Nexus: eine fertige Website in Minuten](../../../eng-us/features/6.0/platform-nexus-starter.md).

| Produkt | Was es ist | Enthält Exponential 6? |
|---|---|---|
| Exponential Platform 3.2.9 | "CMS / DXP based on the last free versions of eZ Platform. Symfony / New Stack Only" | Nein ("No Exponential 6.x Legacy Support") |
| Exponential Platform Legacy | Eine Distribution von Exponential Platform "with Exponential (Legacy) Included"; "Dual Kernel" | Ja |
| Exponential Platform Nexus | Platform plus Exponential 6, Netgen Media Site und Netgen Layouts, eine Linie pro Platform-Generation | In den Linien 1.0.0.x bis 1.2.0.x und 2.5; nicht in 1.3.0.x (Platform v5) |
| Nexus-Starter | Die v5-Nexus-Website als eigenes Repository, laut Beschreibung "Currently In Public Beta" | Nein |
| Exponential Platform DXP | "a free, open-source Digital Experience Platform built on the Symfony framework" | Nein |

## Neueste getaggte Releases

Aus dem [Index der Release Notes](../../../changelogs/extensions/README.md#platform-packages) und der Seite jedes Pakets,
mit Stand 18. Oktober 2026:

| Paket | Neuestes Release | Datum | Notes |
|---|---|---|---|
| exponential-platform | v3.2.9 | 2025-09-28 | [Release Notes](../../../changelogs/extensions/exponential-platform.md) |
| exponential-platform-legacy | v2.5.0.3 | 2026-04-21 | [Release Notes](../../../changelogs/extensions/exponential-platform-legacy.md) |
| exponential-platform-nexus | 1.0.0.6 | 2026-04-21 | [Release Notes](../../../changelogs/extensions/exponential-platform-nexus.md) |
| exponential-platform-nexus-starter | 1.0.0.0 | 2026-04-26 | [Release Notes](../../../changelogs/extensions/exponential-platform-nexus-starter.md) |
| exponential-platform-dxp | v0.0.0.2 | 2026-03-21 | [Release Notes](../../../changelogs/extensions/exponential-platform-dxp.md) |

Manche Tags tragen ein `v`, manche nicht (`1.0.0.6`); drucken Sie die Nummer im Fließtext ohne es
([Wie Versionsnummern funktionieren](wie-versionsnummern-funktionieren.md)). Prüfen Sie die Tags vor der Veröffentlichung erneut
([Ein Release selbst prüfen](ein-release-selbst-pruefen.md)).

## Wo die Notes stehen

- Die Release Notes der Platform führen "Releases made by the se7enxweb team"; "Upstream history carried by the fork is
  not repeated here" (zum Beispiel die [Release Notes von Nexus](../../../changelogs/extensions/exponential-platform-nexus.md)).
  Die Upstream-Geschichte wird auf den Repository-Seiten des [Platform-Ökosystems](../../../eng-us/history/ecosystem.md) erzählt.
- Wie die Platform-Repositorys mit Exponential zusammenhängen: [Das Platform-Ökosystem](../../../eng-us/history/ecosystem.md).
- Wie Sie ein Projekt beginnen: [Nexus-Starter](../../../eng-us/features/6.0/platform-nexus-starter.md),
  [DXP-Skeleton](../../../eng-us/features/6.0/platform-dxp-skeleton.md).

## Verwechslungen, die Sie vermeiden sollten

| Falsch | Richtig | Quelle |
|---|---|---|
| „DXP enthält den Layout-Editor von Exponential Layouts“ | DXP liefert keinen Layout-Editor, keinen Freigabe-Workflow, keinen Scheduler und keinen Legacy-Kernel; "For Exponential 6 beside Symfony, choose Legacy or Nexus" | [Exponential Platform DXP](../wer-ist-die-exponential-foundation.md#exponential-platform-dxp) |
| „Ein Platform-Release ist ein Release von Exponential 6“ | Der Kernel hat eigene Versionen (6.0.x); die Platform-Pakete zählen getrennt | [Wo Changelogs und Release Notes stehen](wo-changelogs-und-release-notes-stehen.md) |
| „Nexus ist dasselbe wie DXP“ | Nexus ist eine vollständige Website mit Demo-Inhalten und Netgen Layouts, und die meisten seiner Linien enthalten Exponential 6; DXP ist eine reine Symfony-Plattform | [Exponential Platform Nexus](../wer-ist-die-exponential-foundation.md#exponential-platform-nexus), [Nexus-Starter](../../../eng-us/features/6.0/platform-nexus-starter.md) |
| „Jedes Nexus-Release enthält Exponential 6“ | Die Linie 1.3.0.x und der v5-Starter haben keinen Legacy-Kernel | [Nexus-Starter](../../../eng-us/features/6.0/platform-nexus-starter.md) |
| „Der Starter ist fertig“ | Seine Beschreibung sagt "Currently In Public Beta" | Derselbe Abschnitt |
| „DXP steht nur unter der GPL“ | Seine `composer.json` sagt "GPL-2.0-or-later or proprietary" | [Exponential Platform DXP](../wer-ist-die-exponential-foundation.md#exponential-platform-dxp) |

## Namen und andere Unternehmen

- Die README von DXP nennt es "the community continuation of the eZ Platform and Ibexa OSS line maintained by 7x".
  Zitieren Sie das als Wortlaut der README. Machen Sie daraus keine Partnerschaft oder Empfehlung: Exponential "is not
  associated with the original eZ Publish software or its original developer, eZ Systems or Ibexa"
  ([Wer ist 7x?, Kurz gesagt](../wer-ist-7x.md#kurz-gesagt)).
- Nexus baut auf der Open-Source-Arbeit von Netgen auf; nennen Sie Netgen, wenn Sie es beschreiben
  ([Wer ist 7x?, Beitragende und Freunde](../wer-ist-7x.md#mitwirkende-und-freunde-des-projekts)).
- Paketnamen wie `ezplatform-kernel` oder `ezpublish-kernel` im Index sind Namen von Forks und bleiben, wie sie sind;
  sie sind Bezeichner, keine Produktnamen.

## Verwandte Seiten

- [Über ein Release berichten](ueber-ein-release-berichten.md)
- [Die Produktfamilie kennen](../fuersprecher/die-produktfamilie-kennen.md)
- [Briefing für die Fachpresse](briefing-fuer-fachpresse.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
