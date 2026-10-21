---
title: "Lektion 5.1: KI-Ketten und der menschliche Checkpoint"
audience: trainer, editor
category: training
order: 51
summary: Die Teilnehmenden starten eine KI-Kette aus einem Auftrag, verfolgen ihre Schritte (schreiben, prüfen, überarbeiten, SEO und QA) und entscheiden am menschlichen Checkpoint mit Approve and continue, Ask for changes oder Stop the run. 60 Minuten.
keywords: exponential intelligence, ketten, ki-kette, write with a chain, start the chain, prüfung, überarbeitung, seo, checkpoint, approve and continue, ask for changes, stop the run, auftragswarteschlange, laufseite, chains, ai chain, review, revise, job queue, run page
type: help
---
# Lektion 5.1: KI-Ketten und der menschliche Checkpoint

Eine **KI-Kette** schreibt in Schritten statt in einer Antwort: Ein Modell schreibt, ein anderes prüft den Text anhand
einer Bewertung und einer Checkliste, das erste überarbeitet, bis die Prüfung bestanden ist, ein drittes bereitet Titel
und Metadaten vor und prüft die Links, und dann entscheidet ein Redakteur, bevor irgendetwas anderes geschieht. Diese
Lektion führt pro Person eine Kette aus und übt den Checkpoint.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Exponential Intelligence](../README.md) |
| Modul | [Modul 5: Ketten und das Log](README.md) |
| Dauer | 60 Minuten (20 Präsentation, 40 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Redakteure mit der Richtlinie `expai/chain` |
| Voraussetzungen | [Modul 2](../modul-02-der-intelligence-tab/README.md); die Auftragswarteschlange läuft auf der Schulungsinstallation |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- eine Kette aus dem Tab Intelligence (**Write with a chain**) oder aus **Start writing** (**Use chain**, **Items**,
  **Start the chain**) starten;
- die Schritte, Bewertungen, Probleme und Kosten eines Laufs auf seiner Laufseite verfolgen;
- am Checkpoint entscheiden: **Approve and continue**, **Ask for changes** (mit einem Hinweis) oder **Stop the run**;
- erklären, dass das Ergebnis einer Kette ein Entwurf ist, nie eine veröffentlichte Seite.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: die Seite **Chains** und die Dokumentation zu Ketten unter **EI: Help** (`extension/expai/doc/chains.md`).

1. **Die Idee** (3 Minuten). Lesen Sie den Text der Seite **Chains** vor: *A chain writes in steps: a model writes,
   another reviews and the text is revised until it passes, a third prepares the title and summary, then an editor
   approves before anything else happens. Every step is a queued job with its cost; the result is a draft.* (Jeder
   Schritt ist ein Auftrag in der Warteschlange mit seinen Kosten; das Ergebnis ist ein Entwurf.)
2. **Die mitgelieferte Artikelkette** (4 Minuten). Zeichnen Sie sie:

   ```text
   write -> review --fail--> revise -> review ... (at most 3 reviews)
                |pass
                v
          SEO and QA -> checkpoint (editor) -> translate (hand-off) -> done
   ```

   Der Entwurf wird am Checkpoint geschrieben, unter dem gewählten Ort, als der Benutzer, der den Lauf gestartet hat.
3. **Starten** (3 Minuten). Im Tab Intelligence der Abschnitt „Write with a chain“: Kette wählen, Anzahl der Elemente,
   den Auftrag, **Start the chain**. Unter **Start writing** auf dem Dashboard: **Use chain**, **Items**, **Start the
   chain**. Ketten brauchen die Auftragswarteschlange; die Schritte laufen im Hintergrund.
4. **Der Checkpoint** (5 Minuten). Die Laufseite zeigt Zustand, Kosten, Tokens, Zeit, den Entwurf, jeden Schritt mit
   Modell, Urteil, Bewertung, Problemen und Checkliste, die Metadaten und die Linkprüfung. Drei Schaltflächen:
   **Approve and continue** (der Entwurf wird zurückgelesen, sodass Ihre Änderungen im Bearbeitungsformular erhalten
   bleiben), **Ask for changes** (ein Hinweis ist Pflicht; der Lauf überarbeitet und prüft erneut und kommt zurück),
   **Stop the run** (Aufträge in der Warteschlange werden abgebrochen; der Entwurf bleibt ein Entwurf).
5. **Eskalation** (2 Minuten). Nach drei fehlgeschlagenen Prüfungen läuft der Lauf weiter, und der Checkpoint zeigt
   die Gründe.

### Häufige Fragen

**Ist eine Kette besser als eine einzelne Anfrage?** Bei längeren Texten mit einem Qualitätsanspruch fängt der
Prüfschritt Probleme ab; er kostet mehr Anfragen.

**Wer darf meinen Lauf sehen?** Sie, Inhaber von `expai/log` und Benutzer der Kette, die das Element des Laufs
bearbeiten dürfen.

**Was macht der Übersetzungsschritt mit einem neuen Entwurf?** Er wird bei einem unveröffentlichten Entwurf
übersprungen; Übersetzungen folgen nach dem Veröffentlichen über den Übersetzungsabgleich.

### Stolperfallen

- **Keine Auftragswarteschlange.** Ohne laufende Warteschlange startet der Lauf seine Schritte nie. Prüfen Sie das vor
  dem Kurs.
- **Ungelesen freigeben.** Der Checkpoint ist zum Lesen da; öffnen Sie zuerst den Entwurf.
- **Viele Elemente.** Jedes Element ist ein Lauf mit allen seinen Schritten; beginnen Sie mit einem.

## Praktische Übung

Verwenden Sie die Schulungsinstallation, eine Rolle mit `expai/chain` und Ihren Kursordner.

1. Öffnen Sie den Tab **Intelligence** Ihres Kursordners und suchen Sie „Write with a chain“ (oder verwenden Sie
   **Start writing** auf der Dashboard-Seite mit **Use chain**).
2. Wählen Sie die Artikelkette, **Items**: 1, und den Auftrag: `An article for beginners on composting in a small
   garden, about 200 words, practical steps, no brand names.`
3. Klicken Sie auf **Start the chain**. Die Laufseite öffnet sich.
4. Verfolgen Sie die Schritte, während sie enden (neu laden oder warten). Notieren Sie die Bewertung der Prüfung und
   etwaige Probleme.
5. Wenn der Lauf am Checkpoint wartet, öffnen Sie den Entwurf von der Laufseite aus und lesen Sie ihn.
6. Klicken Sie auf **Ask for changes** mit dem Hinweis `Add one sentence on what not to compost.` Warten Sie, bis der
   Lauf wieder am Checkpoint ist, und lesen Sie den Entwurf erneut.
7. Klicken Sie auf **Approve and continue**.
8. Öffnen Sie **EI: Chains** und suchen Sie Ihren Lauf in der Liste; lesen Sie seinen Endzustand und seine Kosten.
9. Veröffentlichen Sie den Entwurf aus **Meine Entwürfe** oder verwerfen Sie ihn.

## Erwartetes Ergebnis

- Ein Kettenlauf pro Person hat den Checkpoint erreicht, eine Runde Änderungen durchlaufen und wurde freigegeben.
- Der Entwurf unter dem Ordner der Person enthält den gewünschten Satz; er ist von der Person veröffentlicht oder
  verworfen.
- Der Trainer prüft das, indem er die Laufseite einer Person am Beamer öffnet.

## Quiz

1. Nennen Sie die Schritte der mitgelieferten Artikelkette in der richtigen Reihenfolge.
2. Wann wird der Entwurf der Kette geschrieben?
3. Was verlangt **Ask for changes**?
   A. Nichts B. Einen Hinweis C. Einen neuen Auftrag D. Eine neue Kette
4. Was geschieht mit dem Entwurf, wenn Sie auf **Stop the run** klicken?
5. Was braucht eine Kette auf dem Server, um ihre Schritte auszuführen?

<details>
<summary>Antworten</summary>

1. Schreiben, prüfen (bei Misserfolg überarbeiten und erneut prüfen, höchstens drei Prüfungen), SEO und QA,
   Checkpoint, übersetzen.
2. Am Checkpoint.
3. **B.**
4. Er bleibt ein Entwurf; Aufträge in der Warteschlange werden abgebrochen.
5. Die Auftragswarteschlange (die Erweiterung expqueue mit einem Worker oder ihrem Cron-Teil).

</details>

## Weiterführende Informationen

- Die Seiten zu Ketten und zur Auftragswarteschlange unter **EI: Help**; [expqueue](../../../../../eng-us/features/6.0/extensions/expqueue.md).

## Verwandte Seiten

- [Modul 5: Ketten und das Log](README.md)
- [Exponential Intelligence: Kursübersicht](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
