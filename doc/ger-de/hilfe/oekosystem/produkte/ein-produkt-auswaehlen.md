---
title: Ein Produkt aus dem Ökosystem auswählen
audience: ecosystem, project-manager, developer, businesses, partner
category: ecosystem
order: 19
summary: Welches Produkt des Ökosystems von Exponential zu welchem Bedarf passt - Exponential 6 mit SQLite, Velocity, Layouts, Intelligence, die Platform DXP, Nexus, Legacy oder Exponential Basic - mit den Fragen, die Sie stellen, und der Seite, die Sie zu jeder Wahl lesen.
keywords: auswählen, welches Produkt, Entscheidung, Exponential 6, DXP, Nexus, Legacy, Basic, Velocity, Layouts, Intelligence, Symfony, Legacy-Kernel, choosing, which product, decision, legacy kernel
type: help
---
# Ein Produkt aus dem Ökosystem auswählen

## Inhalt

1. [Die kurze Tabelle](#die-kurze-tabelle)
2. [Fragen, die entscheiden](#fragen-die-entscheiden)
3. [Kombinationen](#kombinationen)

## Die kurze Tabelle

| Sie möchten | Wählen Sie | Lesen Sie |
|---|---|---|
| Ein CMS mit Shop, mehreren Websites und vielen Sprachen, nur auf PHP, ohne Datenbankserver | Exponential 6 mit SQLite | [Erste Schritte](../../erste-schritte.md), [Datenbankunterstützung](../../datenbankunterstuetzung.md) |
| Den schnellsten Weg, es auszuliefern | Exponential Velocity | [Exponential Velocity](exponential-velocity.md) |
| Redakteure, die Seiten selbst bauen | Exponential Layouts | [Der Layout-Editor](../../layout-editor.md) |
| Hilfe beim Schreiben und Übersetzen von Inhalten | Exponential Intelligence | [Exponential Intelligence](exponential-intelligence.md) |
| Ein Symfony-Projekt mit REST und GraphQL, ohne Legacy-Kernel | Exponential Platform DXP | [Exponential Platform DXP](platform-dxp.md) |
| Eine vollständige Demo-Website mit Layouts auf der Symfony-Plattform | Exponential Platform Nexus | [Die Platform-Linie](die-platform-linie.md) |
| Eine Exponential-6-Website behalten und Symfony daneben ergänzen | Exponential Platform Legacy | [Legacy Bridge](../../../funktionen/6.0/legacy-bridge.md) |
| Ein kleines, einfaches CMS zum Lernen | Exponential Basic | [Exponential Basic](exponential-basic.md) |

Quelle: [Welches Produkt zu Ihnen passt](../../wer-ist-die-exponential-foundation.md#welches-produkt-zu-ihnen-passt).

## Fragen, die entscheiden

| Frage | Wenn ja |
|---|---|
| Haben Sie eine bestehende Exponential-6-Website? | Bleiben Sie bei Exponential 6; ergänzen Sie Symfony mit Legacy oder Nexus nur, wenn Sie es brauchen |
| Arbeitet Ihr Team schon mit Symfony und Twig? | Sehen Sie sich DXP oder Nexus an |
| Brauchen Sie den eingebauten Shop, Workflows oder die klassische Template-Sprache? | Exponential 6 (die DXP liefert keinen Workflow und keinen Zeitplaner) |
| Brauchen Sie einen visuellen Seitenbaukasten? | Exponential 6 mit Layouts oder Nexus; nicht die DXP allein |
| Möchten Sie gar keinen Datenbankserver? | Exponential 6 auf SQLite; DXP und Plattform unterstützen SQLite ebenfalls |
| Ist es zum Lernen oder für eine winzige Website? | Exponential Basic oder Exponential 6 auf SQLite |

Prüfen Sie jede Aussage an der eigenen `composer.json` der Distribution, bevor Sie sie versprechen
([Checklisten zum Ökosystem](../checklisten-zum-oekosystem.md)).

## Kombinationen

- Exponential 6 + Velocity + Layouts + Intelligence: die Standardinstallation von 6.0.15, da alle in `composer.json`
  verlangt werden (Intelligence sendet nichts, bevor ein Schlüssel eines Anbieters eingetragen ist).
- Exponential 6 + Symfony: Legacy oder Nexus.
- Nexus + Exponential 6 + Netgen Layouts: was Nexus mitliefert.

## Verwandte Seiten

- [Produkte](README.md)
- [Für wen Exponential ist](../../geschaeft/fuer-wen-exponential-ist.md)
- [Hosting, Datenbank und Betriebssystem wählen](../../fuehrung/hosting-datenbank-und-betriebssystem-waehlen.md)
- [Anwender und Umsteiger: hier beginnen](../../anwender-umsteiger/einfuehrung-hier-beginnen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
