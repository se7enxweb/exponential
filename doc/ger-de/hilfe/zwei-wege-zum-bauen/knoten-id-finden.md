---
title: Eine Knoten-ID finden
audience: editor, website-creator, admin, designer, developer
category: build-two-ways
order: 400
summary: Menüs, Layouts und Templates nennen Seiten über ihre Knoten-ID (Orts-ID). Wo die Administration sie zeigt, wie man sie von der Objekt-ID unterscheidet und wie man eine Seite über ID, Pfad oder URL nachschlägt.
keywords: Knoten-ID, Orts-ID, Objekt-ID, Inhalts-ID, Knoten finden, Inhaltsfinder, node ID, location ID, object ID, content ID, find node, content finder, MainMenuID, FooterMenuID, value_id, URL alias, content/view/full
type: help
---
# Eine Knoten-ID finden

Jede Seite im Inhaltsbaum hat zwei Nummern:

| Nummer | Bedeutet | Verwendet von |
|---|---|---|
| **Knoten-ID** (Orts-ID) | der Platz der Seite im Baum | Menüs (`MainMenuID[]`, `FooterMenuID[]`), Layout-Zuordnungen, Einträge von Sammlungen in Layouts-Blöcken, `fetch('content','node', ...)`, `/content/view/full/<node id>` |
| **Objekt-ID** (Inhalts-ID) | der Inhalt selbst, an allen seinen Orten | Beziehungen, `fetch('content','object', ...)`, `/content/edit/<object id>` |

Eine Seite mit einem Ort hat von jeder eine, und sie unterscheiden sich: **Showcase** ist Knoten 192, Objekt 190.

## In der Administration

Öffnen Sie die Seite in der **Inhaltsstruktur** (oder unter **Medien**). Die Zeile unter den Schaltflächen lautet
`Geändert ... · Veröffentlicht ... · Version ... · <Sektion> · Knoten 192 · Objekt 190 · Remote-ID`.

![Die Ansicht von Showcase in der Administration; die Zeile unter den Schaltflächen zeigt Knoten 192 und Objekt 190](../../../eng-us/help/build-two-ways/images/hm-02-find-node-id.webp)

Außerdem:

- die Adresse der Seite in der Administration endet auf `/content/view/full/<node id>`;
- der Reiter **Orte** listet jeden Ort einer Seite mit seiner Knoten-ID (eine Seite mit zwei Orten hat zwei Knoten-IDs;
  Menüs verwenden die, die Sie wählen);
- der Kasten **Inhaltsfinder** unter dem Baum (Platzhalter „ID, Pfad oder URL“, Schaltfläche **Finden**) schlägt eine Seite über jede der drei nach.

## Auf der Site

Die Menüeinträge der Site tragen die ID in ihrem Markup: `id="menu-item-main_menu-location-id-<id>"` und
`data-location-id="<id>"` (in diesem Theme zeigen die ersten Einträge dort die IDs der Referenz-Site, siehe
[Wie die Kopfzeile aufgebaut ist](../../../eng-us/help/build-two-ways/how-the-header-is-built.md)).

## Verwandte Seiten

- [Einen Eintrag zum Kopfmenü hinzufügen](../../../eng-us/help/build-two-ways/add-a-header-menu-item.md)
- [Die Einträge des Fußzeilenmenüs](eintraege-des-fusszeilenmenues.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
