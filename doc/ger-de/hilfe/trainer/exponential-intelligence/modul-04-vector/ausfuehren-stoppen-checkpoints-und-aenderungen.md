---
title: "Lektion 4.3: Ausführen, stoppen, Checkpoints und What changed"
audience: trainer, editor
category: training
order: 43
summary: Die Teilnehmenden führen Nachrichten in einem Thread aus, stoppen sie und stellen sie in die Warteschlange, lesen seine Kosten, setzen ihn mit einer Vorschau auf einen Checkpoint zurück und verwenden What changed und Revert bei einem direkt geschriebenen Entwurf. 45 Minuten.
keywords: exponential intelligence, vector, run, stop, queue, steer, kosten, checkpoints, wiederherstellen, vorschau, what changed, revert, revert field, direkte prüfung, cost, restore, preview, direct review
type: help
---
# Lektion 4.3: Ausführen, stoppen, Checkpoints und What changed

Redakteure behalten die Kontrolle über einen laufenden Thread: Sie können ihn stoppen, die nächste Nachricht in die
Warteschlange stellen, sehen, was er kostet, und den Thread auf einen früheren Punkt zurücksetzen. Bei Entwürfen, die
ohne gestufte Prüfung geschrieben wurden, zeigt **What changed** den Unterschied und nimmt Teile zurück. Diese Lektion
übt die Bedienelemente.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Exponential Intelligence](../README.md) |
| Modul | [Modul 4: Vector](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Erfahrene Redakteure mit der Richtlinie `expai/vector` |
| Voraussetzungen | [Lektion 4.2](plaene-und-gestufte-pruefung.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- Nachrichten mit **Run**, **Stop** und **Queue** steuern und einen laufenden Durchgang lenken;
- die Kosten des Threads lesen: ausgegeben, Tokens, Anfragen, Modell, Spur;
- einen Thread nach dem Lesen der Vorschau auf einen Checkpoint zurücksetzen;
- bei einem Entwurf **What changed** öffnen und einen Teil oder ein Feld mit **Revert** zurücknehmen.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: die Seiten zu Vector, Checkpoints und zur Prüfung unter **EI: Help** (`extension/expai/doc/vector.md`,
`extension/expai/doc/checkpoints.md`, `extension/expai/doc/review.md`).

1. **Run, Stop, Queue, Steer** (4 Minuten). **Run** (Strg/Cmd+Enter) schickt ab; **Stop** (die Schaltfläche oder
   zweimal Esc) ruft den nächsten Schritt nicht mehr auf (ein bereits geschickter Aufruf wird zuerst beendet); **Queue**
   hält eine Nachricht zurück, bis der Durchgang endet; **Steer** (Alt+Enter während des Laufs) schickt einen Hinweis,
   den das Modell bei seinem nächsten Schritt liest.
2. **Kosten** (2 Minuten). **Cost** im rechten Bereich: ausgegeben, Tokens, Anfragen, Modell, Spur.
3. **Checkpoints** (5 Minuten). Ein Checkpoint wird zu Beginn jedes Durchgangs geschrieben, bevor angenommene
   Änderungen geschrieben werden und bevor ein freigegebener Plan läuft. **Restore** zeigt zuerst eine Vorschau:
   verworfene Entwürfe, zurückgesetzte Entwürfe, was unberührt bleibt (bereits veröffentlichte oder geschickte
   Entwürfe), beiseitegelegte gestufte Änderungen, entfernte Austausche, verworfene Nachrichten in der Warteschlange.
   Nur der Besitzer setzt zurück, und nur, solange nichts läuft. Ein Zurücksetzen schreibt seinen eigenen Checkpoint,
   sodass es rückgängig gemacht werden kann (ein verworfener Entwurf kommt nicht zurück).
4. **What changed** (3 Minuten). Bei einem direkt geschriebenen Entwurf (Tab Intelligence oder ein Thread mit der
   Prüfung **Direct**) zeigt **What changed** den Unterschied zur Version, auf der er beruht; **Revert** nimmt einen
   Teil zurück, **Revert field** ein Feld, in denselben Entwurf. Nichts wird veröffentlicht.

### Häufige Fragen

**Macht Stop rückgängig, was getan wurde?** Nein. Es stoppt weitere Schritte; verwenden Sie einen Checkpoint, um
zurückzugehen.

**Kann ich zurücksetzen, nachdem ich einen Entwurf veröffentlicht habe?** Veröffentlichte oder geschickte Entwürfe
lässt ein Zurücksetzen unberührt.

**Warum wird mir die Prüfung Direct nicht angeboten?** Die Rolle erlaubt nur die gestufte Prüfung (Lektion 1.3).

### Stolperfallen

- **Ohne Vorschau zurücksetzen.** Lesen Sie, was verworfen wird.
- **Stop zu spät drücken.** Ein bereits geschickter Aufruf wird zuerst beendet; warten Sie darauf.
- **Direct-Modus für viele Elemente.** Er ist aus gutem Grund auf ein Element in einer Sprache beschränkt.

## Praktische Übung

Verwenden Sie Ihren Thread aus Lektion 4.2 und Ihren Kursordner.

1. Schicken Sie in Ihrem Thread ab: `Suggest a better title for each article in this folder. Stage the changes.`
   Klicken Sie auf **Run**.
2. Während er läuft, geben Sie `Keep titles under 60 characters.` ein und schicken Sie es mit **Steer** ab (oder
   **Queue**, wenn Lenken nicht angeboten wird).
3. Klicken Sie auf **Stop**, sobald Sie den ersten Schritt enden sehen. Lesen Sie den Zustand des Threads.
4. Öffnen Sie **Cost** im rechten Bereich. Notieren Sie den ausgegebenen Betrag, die Tokens und die Anfragen.
5. Suchen Sie die Checkpoint-Leiste unter dem Gespräch. Klicken Sie beim Checkpoint vor Ihrer letzten Nachricht auf
   **Restore** und lesen Sie die Vorschau. Bestätigen Sie das Zurücksetzen, wenn die Vorschau nur verwirft, was Sie
   erwarten.
6. Bitten Sie im Tab Intelligence eines Artikels um eine kleine direkte Änderung, zum Beispiel `Fix the grammar of the
   intro.` Öffnen Sie dann bei dem geschriebenen Entwurf **What changed**.
7. Nehmen Sie mit **Revert** einen Teil zurück und veröffentlichen oder verwerfen Sie dann den Entwurf.

## Erwartetes Ergebnis

- Die Person hat einen Lauf gestoppt, seine Kosten gelesen und den Thread nach dem Lesen der Vorschau auf einen
  Checkpoint zurückgesetzt.
- Ein direkter Entwurf wurde mit **What changed** geprüft, teilweise zurückgenommen und veröffentlicht oder verworfen.
- Der Trainer prüft das, indem er zwei Teilnehmende fragt, was die Vorschau des Zurücksetzens aufgelistet hat.

## Quiz

1. Was ist der Unterschied zwischen **Stop** und dem Zurücksetzen auf einen Checkpoint?
2. Wann wird ein Checkpoint geschrieben?
3. Was lässt ein Zurücksetzen unberührt?
   A. Jeden Entwurf B. Bereits veröffentlichte oder zur Freigabe geschickte Entwürfe C. Das Gespräch D. Nichts
4. Wo sehen Sie, was ein direkter Entwurf geändert hat?
5. Richtig oder falsch: **Revert** veröffentlicht den zurückgenommenen Text.

<details>
<summary>Antworten</summary>

1. **Stop** stoppt weitere Schritte; ein Zurücksetzen bringt den Thread in einen früheren Zustand zurück.
2. Zu Beginn jedes Durchgangs, bevor angenommene Änderungen in Entwürfe geschrieben werden, und bevor ein freigegebener
   Plan läuft.
3. **B.**
4. Mit **What changed** beim Entwurf.
5. **Falsch.** Es schreibt in denselben Entwurf; nichts wird veröffentlicht.

</details>

## Weiterführende Informationen

- Die Seiten zu Checkpoints, Vector und zur Prüfung unter **EI: Help**.

## Verwandte Seiten

- [Modul 4: Vector](README.md)
- [Exponential Intelligence: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
