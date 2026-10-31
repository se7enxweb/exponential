---
title: Die Produktfamilie kennen
audience: advocate
category: advocacy
order: 6
summary: Jedes Produkt der Exponential-Familie in einem Satz, den ein Fürsprecher sagen kann, für wen es gedacht ist und welche Quelle Sie zitieren, dazu ein Quiz zur Selbstkontrolle.
keywords: Produktfamilie, Exponential 6, Exponential Basic, Exponential Platform, Legacy, Nexus, DXP, Velocity, Exponential Intelligence, Exponential Layouts, AdminUI, Hilfesystem, Erweiterungen, welches Produkt, product family, help system, extensions, which product
type: help
---
# Die Produktfamilie kennen

Diese Seite ist für Fürsprecher, die gefragt werden: „Welches Exponential meinen Sie?“ Sie nennt jedes Produkt der
Familie in einem Satz, den Sie sagen können, für wen es gedacht ist und wo die Tatsache steht. Die vollständigen
Beschreibungen mit ihren Quellen stehen in [Wer ist die Exponential Foundation?, Die Produkte im Einzelnen](../wer-ist-die-exponential-foundation.md#die-produkte-im-einzelnen).

## Inhalt

1. [Die Familie in einer Tabelle](#die-familie-in-einer-tabelle)
2. [Sätze, die Sie sagen können](#sätze-die-sie-sagen-können)
3. [Welches Produkt für wen](#welches-produkt-für-wen)
4. [Häufige Verwechslungen](#häufige-verwechslungen)
5. [Prüfen Sie sich selbst](#prüfen-sie-sich-selbst)

## Die Familie in einer Tabelle

| Produkt | Ein Satz | Für | Quelle |
|---|---|---|---|
| **Exponential 6** | Der Kernel für CMS, CMF und Shop: „ein professionelles PHP-Anwendungsframework mit fortgeschrittener CMS-Funktionalität“ | Alle, die damit eine Website bauen oder betreiben | [README](https://github.com/se7enxweb/exponential/blob/main/README.md), [Exponential 6](../wer-ist-die-exponential-foundation.md#exponential-6-der-kernel) |
| **Exponential Basic** | Das einfache CMS der Linie 2.x, „für alle, für Bildungszwecke, für den persönlichen Gebrauch“ | Lernende, kleine Websites | [Exponential Basic](../wer-ist-die-exponential-foundation.md#exponential-basic) |
| **Exponential Platform 3.2.9** | Ein reines Symfony-CMS auf Grundlage der letzten freien Versionen von eZ Platform | Teams auf diesem Stack | [Exponential Platform 3.2.9](../wer-ist-die-exponential-foundation.md#exponential-platform-329) |
| **Exponential Platform Legacy** | Eine Symfony-Plattform mit enthaltenem Exponential 6: ein doppelter Kernel | Websites mit Exponential 6, die Symfony hinzunehmen | [Exponential Platform Legacy](../wer-ist-die-exponential-foundation.md#exponential-platform-legacy) |
| **Exponential Platform Nexus** | Platform plus Exponential 6, Exponential Layouts und Media Site, mit Demo-Inhalten | Agenturen, die schnell eine vollständige Website möchten | [Exponential Platform Nexus](../wer-ist-die-exponential-foundation.md#exponential-platform-nexus) |
| **Exponential Platform DXP** | Eine freie Open-Source-DXP auf Symfony, die Fortführung der Linie eZ Platform und Ibexa OSS durch die Community | Neue Symfony-Projekte | [Exponential Platform DXP](../wer-ist-die-exponential-foundation.md#exponential-platform-dxp) |
| **Exponential Velocity** | Ein in PHP geschriebener Web- und Anwendungsserver, der die Anwendung zwischen Anfragen geladen hält | Alle, die Exponential ausliefern | [Exponential Velocity](../wer-ist-die-exponential-foundation.md#exponential-velocity) |
| **Exponential Intelligence** | Ein Schreibassistent in der Administration, der Entwürfe schreibt und nie selbst veröffentlicht | Redakteure und Inhaltsteams | [Exponential Intelligence](../wer-ist-die-exponential-foundation.md#exponential-intelligence) |
| **Exponential Layouts** | Ein visueller Seitenbaukasten: Layouts, Zonen, Blöcke und Regeln | Redakteure und Designer | [Exponential Layouts](../wer-ist-die-exponential-foundation.md#exponential-layouts) |
| **AdminUI** | Eine alternative Oberfläche der Administration, eine Portierung der Netgen AdminUI | Redakteure, die sie bevorzugen | [AdminUI](../wer-ist-die-exponential-foundation.md#adminui) |
| **Das Hilfesystem** | Die Dokumentation in der Administration, durchsuchbar, mit kontextbezogener Hilfe | Alle Benutzer der Administration | [Hilfesystem](../../funktionen/6.0/hilfesystem.md) |
| **Die Erweiterungen** | Pakete, die Funktionen hinzufügen; die `composer.json` des Kernels verlangt 66 Pakete von se7enxweb | Website-Ersteller | [Das Ökosystem der Erweiterungen](../wer-ist-die-exponential-foundation.md#das-ökosystem-der-erweiterungen) |

## Sätze, die Sie sagen können

- „**Exponential** ist ein PHP-CMS und Anwendungsframework mit einem vollständig anpassbaren Inhaltsmodell, eingebautem
  Shop, mehreren Websites und vielen Sprachen. Es ist freie Software unter der GNU GPL.“
- „Wenn Sie Symfony möchten, hat die Familie **Exponential Platform**: DXP nur für Symfony, Legacy und Nexus, wenn Sie
  auch Exponential 6 daneben möchten.“
- „**Velocity** ist der eigene PHP-Webserver von Exponential; die README nennt ihn die empfohlene Engine für jede Phase.“
- „Mit **Exponential Layouts** ordnen Redakteure Seiten selbst an; **Exponential Intelligence** hilft ihnen beim
  Schreiben, aber nur mit Entwürfen.“
- „**Exponential Basic** ist das kleine, einfache, gut zum Lernen.“

## Welches Produkt für wen

Nutzen Sie die Tabelle [Welches Produkt passt zu Ihnen](../wer-ist-die-exponential-foundation.md#welches-produkt-zu-ihnen-passt).
Stellen Sie im Gespräch zuerst zwei Fragen:

1. „Möchten Sie nur bei PHP bleiben, oder arbeiten Sie mit Symfony?“
2. „Brauchen Sie schnell eine vollständige Website mit Demo-Inhalten oder ein Framework, auf dem Sie aufbauen?“

| Antworten | Verweisen Sie auf |
|---|---|
| Nur PHP, darauf aufbauen | Exponential 6 ([Erste Schritte](../erste-schritte.md)) |
| Nur Symfony | Exponential Platform DXP ([DXP-Skeleton](../../funktionen/6.0/plattform-dxp-skeleton.md)) |
| Symfony, eine Website mit Exponential 6 behalten | Exponential Platform Legacy ([Legacy Bridge](../../funktionen/6.0/legacy-bridge.md)) |
| Vollständige Demo-Website mit Layouts, Symfony | Exponential Platform Nexus ([Nexus-Starter](../../funktionen/6.0/plattform-nexus-starter.md)) |
| Lernen, eine winzige Website | Exponential Basic ([basic.exponential.earth](https://basic.exponential.earth)) |

## Häufige Verwechslungen

| Verwechslung | Richtig |
|---|---|
| „DXP hat einen Layouts-Editor.“ | Es liefert weder einen Layouts-Editor noch einen Freigabe-Workflow, einen Zeitplaner oder den Legacy-Kernel ([Exponential Platform DXP](../wer-ist-die-exponential-foundation.md#exponential-platform-dxp)). Für Exponential 6 neben Symfony wählen Sie Legacy oder Nexus. |
| „Legacy bedeutet alt.“ | „Legacy“ ist der Kernel von Exponential 6, wenn er neben einer Symfony-Plattform läuft ([Glossar](../wer-ist-die-exponential-foundation.md#glossar)). Exponential 6 selbst ist die gepflegte Linie. |
| „Velocity ist Qbix.“ | Velocity begann als Fork der Webserver-Engine Qbix; der Produktname ist Exponential Velocity ([Marken- und Namensregeln](../geschaeft/marken-und-namensregeln.md#die-produktfamilie)). |
| „Die KI veröffentlicht Inhalte.“ | Exponential Intelligence schreibt nur Entwürfe ([expai](../../funktionen/6.0/erweiterungen/expai.md#kurz-gesagt)). |
| „request-shield ist Teil von Exponential.“ | Es ist ein Projekt von CJW Network; Exponential dokumentiert, wie man es nutzt ([Begleitprojekte](../wer-ist-die-exponential-foundation.md#begleitprojekte)). |

## Prüfen Sie sich selbst

Antworten Sie, ohne nachzusehen, und prüfen Sie dann mit der Tabelle oben.

```text
1. Which product would you suggest to a team that wants Symfony only?          ______________
2. What does Exponential Intelligence never do on its own?                     ______________
3. Which two distributions run Exponential 6 beside Symfony?                   ______________
4. What is Velocity, in one sentence?                                          ______________
5. Which product is for learning and personal sites?                           ______________
6. Does Exponential Platform DXP ship a layouts editor?                        ______________
```

## Verwandte Seiten

- [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md)
- [Kurzkarte zu den Namensregeln](kurzkarte-namensregeln.md)
- [Gesprächspunkte: das Grundset](gespraechspunkte-grundset.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
