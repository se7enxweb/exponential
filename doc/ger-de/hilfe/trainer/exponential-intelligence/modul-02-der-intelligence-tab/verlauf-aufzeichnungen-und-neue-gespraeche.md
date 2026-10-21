---
title: "Lektion 2.3: Verlauf, Aufzeichnungen und neue Gespräche"
audience: trainer, editor
category: training
order: 23
summary: Die Teilnehmenden nutzen den Gesprächsverlauf, öffnen die Aufzeichnung eines Durchgangs, verfolgen einen Lauf mit Watch this run, setzen nach einem fehlgeschlagenen Schritt fort und beginnen ein neues Gespräch. 30 Minuten.
keywords: exponential intelligence, verlauf, expand all, collapse all, show older, aufzeichnung, watch this run, continue, new conversation, lebensdauer eines gesprächs, log, history, record, conversation lifetime
type: help
---
# Lektion 2.3: Verlauf, Aufzeichnungen und neue Gespräche

Ein Gespräch hält seinen Verlauf auf dem Server, und jeder Durchgang hat eine Aufzeichnung: welches Modell geantwortet
hat, was es nachgeschlagen hat, was es gekostet hat. Diese Lektion zeigt den Teilnehmenden, wie sie beides lesen und
wann sie neu beginnen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Exponential Intelligence](../README.md) |
| Modul | [Modul 2: Der Tab Intelligence](README.md) |
| Dauer | 30 Minuten (10 Präsentation, 20 Übung) |
| Niveau | Einsteiger |
| Zielgruppe | Redakteure |
| Voraussetzungen | [Lektion 2.2](entwuerfe-behalten-senden-und-verwerfen.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- Austausche des Verlaufs öffnen und schließen, mit **Expand all**, **Collapse all** und **Show older**;
- die Aufzeichnung eines Durchgangs öffnen: Modell, Schritte, Tokens, Kosten;
- eine laufende Anfrage mit **Watch this run** verfolgen;
- nach einem fehlgeschlagenen Schritt **Continue** verwenden und mit **New conversation** neu beginnen.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: die Seite zum Tab Intelligence unter **EI: Help** (`extension/expai/doc/full-view-panel.md`).

1. **Der Verlauf** (3 Minuten). Eine Zeile pro Austausch: die Uhrzeit, Ihre Nachricht und der Anfang der Antwort, die
   Anzahl der Nachschlagevorgänge und die Entwürfe als Links. Klicken Sie auf eine Zeile, um sie zu öffnen (Ihre
   vollständige Nachricht, jeder Schritt, die vollständige Antwort). **Expand all**, **Collapse all**, **Show older**
   (die vorherigen 20). Die letzten 200 Austausche werden aufbewahrt.
2. **Die Aufzeichnung** (3 Minuten). Unter jedem Austausch hält eine eingeklappte Zeile den Durchgang fest: das Modell,
   das geantwortet hat (und das angefragte, wenn ein Ersatzmodell es abgelöst hat, mit dem Grund), die Schritte mit
   Dauer, Werkzeugen, Tokens und Kosten, jeweils mit ihrer Anfrage im Log verlinkt.
3. **Watch this run** (2 Minuten). Sobald eine Nachricht abgeschickt ist, zeigt eine Zeile ihren aktuellen Zustand (in
   der Warteschlange, läuft Schritt *k*, fertig, fehlgeschlagen) mit Links zum Log.
4. **Continue und New conversation** (2 Minuten). **Continue** erscheint, wenn ein Schritt fehlgeschlagen ist (zum
   Beispiel nach einer Zeitüberschreitung), und setzt mit dem Gespeicherten fort. **New conversation** entfernt das
   Gespräch dieses Redakteurs zu diesem Element; Entwürfe und das Log bleiben. Ein Gespräch ohne Nachricht über eine
   festgelegte Zeit (mit den mitgelieferten Einstellungen sieben Tage) beginnt neu.

### Häufige Fragen

**Ist mein Verlauf für andere sichtbar?** Ein Gespräch gehört zu einem Benutzer und einem Element.

**Wann sollte ich ein neues Gespräch beginnen?** Wenn sich das Thema ändert oder das Gespräch lang geworden ist: Ein
neues ist günstiger und fokussierter.

**Warum hat ein anderes Modell geantwortet?** Das gewählte konnte nicht (es war zum Beispiel gestoppt oder ist
fehlgeschlagen), und das Standardmodell der Website hat geantwortet; die Aufzeichnung sagt, warum.

**Schickt das Öffnen von Verlaufszeilen etwas ab?** Nein. Das Öffnen und Schließen von Zeilen ändert nichts auf dem
Server; der Browser merkt sich nur, welche Zeilen Sie geöffnet haben.

### Stolperfallen

- **Endlose Gespräche.** Lange Verläufe machen jede Anfrage größer und teurer. Beginnen Sie neue.
- **Die Kosten pro Durchgang als genau lesen.** Kosten sind Schätzungen.

## Praktische Übung

Verwenden Sie die Schulungsinstallation und den Tab **Intelligence** Ihres Kursordners.

1. Schicken Sie eine kurze Folgeanfrage ab, zum Beispiel `Suggest three titles for a third article on the same topic.
   Do not write it.`
2. Klicken Sie im Verlauf auf **Collapse all** und öffnen Sie dann Ihren ersten Austausch, indem Sie auf seine Zeile
   klicken.
3. Öffnen Sie die Aufzeichnungszeile unter dem neuesten Austausch. Notieren Sie das Modell, die Anzahl der Schritte,
   die Tokens und die geschätzten Kosten.
4. Folgen Sie einem der Links der Aufzeichnung zum Log und kehren Sie zurück.
5. Klicken Sie auf **New conversation**. Achten Sie darauf, dass die Entwürfe und Ihre Logeinträge noch da sind (prüfen
   Sie **Recent drafts** auf der Dashboard-Seite).
6. Wenn während des Kurses ein Schritt fehlschlägt (der Trainer zeigt vielleicht einen), verwenden Sie **Continue**.

## Erwartetes Ergebnis

- Die Notizen enthalten Modell, Schritte, Tokens und Kosten eines Durchgangs.
- Nach **New conversation** ist der Verlauf leer, aber Entwürfe und Logeinträge bleiben.
- Der Trainer prüft das, indem er eine Person bittet, am Beamer eine Aufzeichnung zu zeigen.

## Quiz

1. Wie viele Austausche bewahrt der Verlauf auf?
2. Was zeigt die Aufzeichnungszeile eines Durchgangs?
3. Was entfernt **New conversation**?
   A. Die Entwürfe B. Das Log C. Das Gespräch dieses Redakteurs zu diesem Element D. Das Element
4. Wann erscheint **Continue**?
5. Nennen Sie einen Grund, ein neues Gespräch zu beginnen.

<details>
<summary>Antworten</summary>

1. Die letzten 200.
2. Das Modell, das geantwortet hat (und ein etwaiges Ersatzmodell), die Schritte mit Dauer, Werkzeugen, Tokens und
   Kosten, mit dem Log verlinkt.
3. **C.** Entwürfe und das Log bleiben.
4. Wenn ein Schritt fehlgeschlagen ist, zum Beispiel nach einer Zeitüberschreitung.
5. Ein neues Thema oder ein langes Gespräch, das Anfragen größer und teurer macht.

</details>

## Weiterführende Informationen

- Die Seiten zum Tab Intelligence, zu Threads und zur Nutzung unter **EI: Help** (`extension/expai/doc/full-view-panel.md`,
  `extension/expai/doc/usage-tracking.md`).

## Verwandte Seiten

- [Modul 2: Der Tab Intelligence](README.md)
- [Exponential Intelligence: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
