---
title: Von eZ Platform oder Ibexa
audience: adopter, developer, hosting, project-manager
category: adoption
order: 42
summary: Wie ein Anwender mit eZ Platform oder Ibexa DXP/OSS in die Exponential-Familie wechselt - die beiden Ziele (Exponential Platform Nexus als gleichwertiger Paketaustausch oder der Kernel von Exponential 6 als Migration von Daten und Templates), die Fragen, die entscheiden, und wo die Einzelheiten stehen.
keywords: eZ Platform, Ibexa, Ibexa OSS, Migration, Nexus, Platform Legacy, Exponential 6, Paketaustausch, Symfony, Twig, Kapitel 16, migration, package swap, chapter 16
type: help
---
# Von eZ Platform oder Ibexa

## Inhalt

1. [Zwei Ziele](#zwei-ziele)
2. [Fragen, die entscheiden](#fragen-die-entscheiden)
3. [Wo die Einzelheiten stehen](#wo-die-einzelheiten-stehen)

## Zwei Ziele

| | Weg A: Exponential Platform Nexus | Weg B: Exponential 6 (allein oder als Platform Legacy) |
|---|---|---|
| Was es ist | "the like-for-like path: the same Symfony stack, the same database schema generation and the same PHP namespaces, with the packages swapped for the se7enxweb forks", also der gleichwertige Weg: derselbe Symfony-Stack, dieselbe Generation des Datenbankschemas und dieselben PHP-Namespaces, mit den Paketen durch die Forks von se7enxweb ersetzt | Der Legacy-Kernel |
| Aufwand | "the lowest risk: same generation, a package swap", das geringste Risiko | "the highest effort: a data and template migration", der höchste Aufwand: eine Migration von Daten und Templates |
| Eigene Symfony-Bundles und Twig | Bleiben erhalten | Werden als Legacy-Erweiterungen und TPL-Templates neu geschrieben |

Quelle: [Kunden von eZ Platform und Ibexa migrieren](../../hosting/migrationen/kunden-von-ez-platform-und-ibexa-migrieren.md), mit Zitaten
aus Kapitel 16 des Installationsbuchs. Faustregeln aus demselben Kapitel: zuerst dieselbe Generation; Weg B ist "a
rewrite of the presentation and of custom code, not of the content", also ein Neuschreiben der Darstellung und des
eigenen Codes, nicht der Inhalte; mischen Sie die Wege nicht in einer Umstellung.

## Fragen, die entscheiden

- Haben Sie eigene Symfony-Bundles, Twig-Templates, REST- oder GraphQL-Clients, die Sie behalten möchten? Dann Weg A.
- Möchten Sie die Funktionen von Exponential 6: seine Template-Sprache, Workflows, den eingebauten Shop, Exponential
  Layouts? Dann Weg B, oder Legacy, um beides zu betreiben.
- Wie viel eigener Code für die Darstellung gibt es? Weg B schreibt ihn neu.

Siehe auch [Die Platform-Linie](../../oekosystem/produkte/die-platform-linie.md) und
[Exponential Platform DXP](../../oekosystem/produkte/platform-dxp.md) dazu, was jede Symfony-Distribution enthält.

## Wo die Einzelheiten stehen

- [Migration von eZ Platform und Ibexa](../../../installation/16-migration-von-ez-platform-und-ibexa.md), Kapitel 16 des
  Installationsbuchs.
- Die Sicht der Hoster: [Kunden von eZ Platform und Ibexa migrieren](../../hosting/migrationen/kunden-von-ez-platform-und-ibexa-migrieren.md).
- Welcher Fork welches Paket ersetzt: [Paketübersicht](../../../spezifikationen/6.0/platform-paketzuordnung.md).

## Verwandte Seiten

- [Umziehen](README.md)
- [Ein Produkt auswählen](../../oekosystem/produkte/ein-produkt-auswaehlen.md)
- [Legacy Bridge](../../../funktionen/6.0/legacy-bridge.md)
- [Checkliste zur Inhaltsmigration](checkliste-inhaltsmigration.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
