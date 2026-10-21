---
title: "Lektion 4.3: Offset, Limit und Anheften"
audience: trainer, editor, website-creator
category: training
order: 43
summary: Die Teilnehmenden schneiden eine Collection mit Offset und Limit zu, um aus einer Abfrage einen Aufmacher mit Liste zu bauen, und heften ein von Hand ausgewähltes Element an einer festen Position einer dynamischen Liste an. 45 Minuten.
keywords: exponential layouts, offset, limit, anheften, angeheftetes element, aufmacher mit liste, featured, dynamische collection, manuelles element, position, pinning, pinned item, lead plus list, dynamic collection, manual item
type: help
---
# Lektion 4.3: Offset, Limit und Anheften

Zwei kleine Einstellungen und ein Kniff machen aus dynamischen Listen redaktionelle Werkzeuge. **Offset** und
**Limit** teilen eine Abfrage in einen Aufmacher und eine Liste darunter. **Anheften** setzt ein von Hand ausgewähltes
Element an eine feste Stelle in einer Liste, die sich ansonsten selbst pflegt.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Exponential Layouts](../README.md) |
| Modul | [Modul 4: Collections](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Redakteure und Website-Ersteller |
| Voraussetzungen | [Lektion 4.2](dynamische-collections-und-abfragen.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- **Offset** und **Limit** für eine Collection festlegen;
- aus zwei Blöcken mit derselben Abfrage einen Aufmacher mit Liste bauen;
- ein manuelles Element an einer Position einer dynamischen Collection anheften;
- den Grund für das Anheften im Namen des Blocks vermerken.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Collections](../../../../bc/6.0/layouts.md#collections-einem-block-seine-inhalte-geben) und
[Anheften: von Hand ausgewählte und dynamische Inhalte mischen](../../../../bc/6.0/layouts.md#anheften-ausgewählte-und-dynamische-inhalte-mischen).

1. **Offset und Limit** (4 Minuten). Sie wirken, nachdem die Abfrage gelaufen ist: Offset überspringt Elemente am
   Anfang, Limit zeigt höchstens so viele. Ein Block „Featured“ mit Limit 1 und ein Block „Weitere Beiträge“ mit
   Offset 1 und Limit 6 ergeben aus einer Sortierung den vertrauten Aufmacher mit Liste.
2. **Anheften** (5 Minuten). Manuelle Elemente, die einer dynamischen Collection hinzugefügt werden, werden zu
   **angehefteten Überschreibungen**: Jedes behält seine gespeicherte Position, und die Abfrage füllt die Plätze darum
   herum. Ein an Position 0 angeheftetes Element steht zuerst, egal was die Abfrage liefert. Angeheftete Positionen
   sind innerhalb der Collection absolut.
3. **Rat** (2 Minuten). Heften Sie so wenig wie möglich an und schreiben Sie den Grund in den **Namen** des Blocks,
   sonst versteht der nächste Redakteur nicht, warum sich Position 3 nie ändert.
4. **Meldungen** (2 Minuten). Der Editor kann übersprungene Elemente oder Elemente außerhalb des Bereichs melden (zum
   Beispiel *2 manual items out of range*), wenn ein angeheftetes Element hinter dem Limit liegt.

### Häufige Fragen

**Warum wird mein angeheftetes Element nicht angezeigt?** Seine Position liegt hinter dem Limit.

**Kann derselbe Artikel im Aufmacher und in der Liste erscheinen?** Nicht, wenn der Offset der Liste die Elemente des
Aufmachers überspringt und beide dieselbe Abfrage und Sortierung verwenden.

**Wie löse ich ein angeheftetes Element?** Entfernen Sie das manuelle Element aus der Collection.

### Stolperfallen

- **Unterschiedliche Sortierung in den beiden Blöcken.** Offset funktioniert nur, wenn beide Blöcke dieselbe Abfrage
  und Sortierung verwenden.
- **Vergessene angeheftete Elemente.** Ein angeheftetes Element bleibt, bis es entfernt wird. Der Vermerk im Namen
  hilft.
- **Positionen ab null oder ab eins gezählt.** Prüfen Sie vor der Übung, wie Ihre Installation Positionen nummeriert.

## Praktische Übung

Verwenden Sie die Schulungsinstallation und Ihr Kurs-Layout mit dem Block *Newest articles* aus Lektion 4.2.

1. Öffnen Sie Ihr Kurs-Layout mit **Öffnen** (setzen Sie Ihren Entwurf fort, wenn Sie gefragt werden).
2. Duplizieren Sie *Newest articles* mit **Duplicate block**. Benennen Sie den ersten in `Lead article` und den
   zweiten in `More articles` um.
3. Setzen Sie in *Lead article* das Limit auf 1 und den Ansichtstyp auf `grid_featured` (oder einen anderen
   Ansichtstyp, der hervorsticht).
4. Setzen Sie in *More articles* den Offset auf 1 und das Limit auf 4.
5. Prüfen Sie im Editor: Der Aufmacher zeigt den neuesten Artikel, die Liste die nächsten vier.
6. Klicken Sie in *More articles* auf **+ Add items** und wählen Sie einen Ihrer eigenen Artikel; setzen Sie ihn mit
   **Set position** an die erste Position.
7. Benennen Sie den Block in `More articles (pinned: course article, until end of course)` um.
8. Setzen Sie das Limit von *More articles* auf 2 und beobachten Sie, was mit dem angehefteten Element und den
   anderen geschieht; setzen Sie es wieder auf 4.
9. Wählen Sie **Entwurf speichern und schließen**.

## Erwartetes Ergebnis

- Der Entwurf hat einen Aufmacher-Block mit einem Artikel und darunter eine Liste mit vier, ohne Doppelungen.
- Ein angeheftetes Element steht an erster Stelle von *More articles*, mit dem Grund im Namen des Blocks.
- Der Trainer prüft das, indem er den Entwurf einer Person am Beamer öffnet.

## Quiz

1. Was bewirken Offset 1 und Limit 6?
2. Wie bauen Sie aus einer Abfrage einen Aufmacher mit Liste?
3. Was ist ein angeheftetes Element?
   A. Ein verborgenes Element B. Ein manuelles Element an einer festen Position einer dynamischen Collection C. Ein
   gelöschtes Element D. Ein gemeinsamer Block
4. Warum schreiben Sie den Grund für das Anheften in den Namen des Blocks?
5. Ein angeheftetes Element erscheint nicht. Was ist der wahrscheinlichste Grund?

<details>
<summary>Antworten</summary>

1. Das erste Element überspringen und danach höchstens sechs zeigen.
2. Zwei Blöcke mit derselben Abfrage und Sortierung: der erste mit Limit 1, der zweite mit Offset 1
   ([Exponential Layouts](../../../../bc/6.0/layouts.md#collections-einem-block-seine-inhalte-geben)).
3. **B.**
4. Damit der nächste Redakteur weiß, warum sich diese Position nie ändert.
5. Seine Position liegt hinter dem Limit der Collection.

</details>

## Weiterführende Informationen

- [Exponential Layouts: Anheften](../../../../bc/6.0/layouts.md#anheften-ausgewählte-und-dynamische-inhalte-mischen)
- [Layout-Typen, Zonen und Blöcke: Collections](../../../ersteller/website-ersteller/layouttypen-zonen-und-bloecke.md#collections)

## Verwandte Seiten

- [Modul 4: Collections](README.md)
- [Exponential Layouts: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
