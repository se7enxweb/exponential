---
title: "Lektion 5.3: Gemeinsame Inhalte sicher ändern"
audience: trainer, editor, website-creator
category: training
order: 53
summary: Die Teilnehmenden ändern einen Block eines gemeinsamen Layouts über dessen eigenen Entwurf, prüfen vorher die Zahl der Verweise, veröffentlichen und kontrollieren mehrere Seiten und wissen, wie man ein gemeinsames Layout außer Dienst stellt. 45 Minuten.
keywords: exponential layouts, gemeinsames layout, edit linked layout, verweise, entwurf, veröffentlichen, fußzeile ändern, gemeinsames layout außer dienst stellen, caches, kontrolle, shared layout, references, draft, publish, footer change, retire shared layout, verification
type: help
---
# Lektion 5.3: Gemeinsame Inhalte sicher ändern

Wer ein gemeinsames Layout ändert, ändert jede Seite, die damit verknüpft ist. Das ist der Zweck, und das Risiko.
Diese Lektion vermittelt eine sichere Routine: prüfen, wer es verwendet, es im eigenen Entwurf ändern, veröffentlichen,
kontrollieren. Die Übung verwendet ein gemeinsames Schulungs-Layout, niemals die echte Kopfzeile der Website.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Exponential Layouts](../README.md) |
| Modul | [Modul 5: Gemeinsame Layouts und Zonenverknüpfung](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Redakteure und Website-Ersteller, die gemeinsame Inhalte pflegen |
| Voraussetzungen | [Lektion 5.2](zonen-verknuepfen-und-trennen.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Verweise eines gemeinsamen Layouts finden, bevor sie es ändern;
- ein gemeinsames Layout aus einer verknüpften Zone mit **Edit linked layout** öffnen und es in seinem Entwurf ändern;
- es veröffentlichen und mehrere verknüpfende Seiten kontrollieren;
- beschreiben, wie eine Seite ohne die gemeinsame Fußzeile bleibt und wie man ein gemeinsames Layout außer Dienst
  stellt.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Gemeinsame Layouts: Ändern und Entfernen](../../../ersteller/website-ersteller/gemeinsamer-kopf-und-fussbereich.md#ändern-und-entfernen)
und [Exponential Layouts: Gemeinsame Layouts und verknüpfte Zonen](../../../../bc/6.0/layouts.md#gemeinsame-layouts-und-verknüpfte-zonen).

1. **Die Routine** (5 Minuten). Schreiben Sie sie an die Tafel:
   1. die Verweise unter **Gemeinsame Layouts** nachsehen;
   2. das gemeinsame Layout öffnen (aus der Liste oder mit **Edit linked layout** in einem verknüpfenden Layout) und in
      seinem Entwurf arbeiten;
   3. ändern, dann veröffentlichen;
   4. zwei oder drei verknüpfende Seiten abgemeldet laden und kontrollieren.
2. **Blöcke lassen sich nicht im verknüpfenden Layout bearbeiten** (2 Minuten). Der Editor verweigert Änderungen an
   Blöcken, die zu einem veröffentlichten gemeinsamen Layout gehören, wenn Sie sich in einem verknüpfenden Layout
   befinden. Öffnen Sie das gemeinsame Layout selbst.
3. **Varianten** (4 Minuten):

   | Sie möchten | Tun Sie |
   |---|---|
   | Die Fußzeile überall ändern | Entwurf des gemeinsamen Layouts anlegen, bearbeiten, veröffentlichen |
   | Eine Seite ohne die gemeinsame Fußzeile | diese Zone im Layout dieser Seite trennen |
   | Ein gemeinsames Layout außer Dienst stellen | zuerst seine Verweise prüfen; jede Zone trennen, dann löschen |

4. **Caches** (2 Minuten). Nach dem Veröffentlichen fragt der Editor möglicherweise nach dem Leeren von Caches
   (Modul 6). Die Seiten zeigen die Änderung, wenn ihre zwischengespeicherten Kopien erneuert werden.

### Häufige Fragen

**Wer sollte gemeinsame Layouts ändern?** Vereinbaren Sie das im Team (Lektion 1.3); oft ist es eine Person.

**Kann ich eine Änderung erst auf einer Seite testen?** Kopieren Sie das gemeinsame Layout, kennzeichnen Sie die
Kopie als gemeinsam, verknüpfen Sie die Zone einer Seite mit der Kopie und prüfen Sie; ändern Sie danach das echte.

**Warum haben sich manche Seiten nicht geändert?** Sie sind mit einem anderen gemeinsamen Layout verknüpft, oder ihre
zwischengespeicherten Kopien sind noch nicht erneuert.

### Stolperfallen

- **An der echten Kopfzeile üben.** Verwenden Sie das gemeinsame Schulungs-Layout, das der Trainer vorbereitet hat.
- **Die Zahl der Verweise vergessen.** Ein gemeinsames Layout mit 36 Verweisen ändert 36 Zonen.
- **Ein referenziertes gemeinsames Layout löschen.** Trennen Sie zuerst jede Zone.

## Praktische Übung

Verwenden Sie die Schulungsinstallation. Der Trainer hat ein gemeinsames Layout `Training footer` vorbereitet (Typ
`layout_1`, mit einem Rich-text-Block in `main`) und veröffentlicht, und alle Teilnehmenden haben die Zone *FOOTER*
ihres Kurs-Layouts anstelle der Website-Fußzeile damit verknüpft (oder tun es jetzt mit den Schritten aus
Lektion 5.2).

1. Öffnen Sie **Layouts** > **Gemeinsame Layouts** und lesen Sie die Verweise von `Training footer`.
2. Öffnen Sie Ihr Kurs-Layout mit **Öffnen**. Suchen Sie bei der verknüpften Zone *FOOTER* **Edit linked layout** und
   verwenden Sie es (oder öffnen Sie `Training footer` aus der Liste **Gemeinsame Layouts**).
3. Der Trainer bestimmt pro Runde eine Person, die den Rich text von `Training footer` ändert (eine Zeile mit dem
   heutigen Datum hinzufügen) und mit **Layout veröffentlichen** veröffentlicht. Die anderen schauen am Beamer zu.
4. Alle öffnen die **Vorschau** ihres eigenen Kurs-Layouts (oder den Editor) und prüfen die Fußzeile.
5. Besprechen Sie: Wie viele Layouts haben sich mit einer einzigen Veröffentlichung geändert?
6. Trennen Sie in Ihrem Kurs-Layout *FOOTER* und verknüpfen Sie die Zone wieder (Lektion 5.2), um „eine Seite ohne die
   gemeinsame Fußzeile“ zu üben.
7. Wählen Sie **Entwurf speichern und schließen**.

## Erwartetes Ergebnis

- Die einmal in `Training footer` vorgenommene Änderung erscheint in jedem Kurs-Layout, das damit verknüpft ist.
- Alle Teilnehmenden können die Schritte der Routine nennen.
- Der Trainer prüft das, indem er nach der Änderung die Vorschau der Layouts von zwei Teilnehmenden am Beamer zeigt.

## Quiz

1. Was prüfen Sie, bevor Sie ein gemeinsames Layout ändern?
2. Warum können Sie einen verknüpften Block nicht im verknüpfenden Layout ändern?
3. Wie bleibt eine Seite ohne die gemeinsame Fußzeile?
   A. Das gemeinsame Layout löschen B. Diese Zone im Layout dieser Seite trennen C. Den Fußzeilenblock verbergen D. Die
   Regel ändern
4. Bringen Sie die Routine in die richtige Reihenfolge: veröffentlichen, Verweise prüfen, Seiten kontrollieren, im
   Entwurf ändern.
5. Was muss geschehen, bevor ein gemeinsames Layout gelöscht wird?

<details>
<summary>Antworten</summary>

1. Seine Verweise auf dem Bildschirm **Gemeinsame Layouts**.
2. Blöcke eines veröffentlichten gemeinsamen Layouts werden im eigenen Entwurf dieses Layouts geändert; das
   verknüpfende Layout leiht sie nur aus.
3. **B.** ([Gemeinsame Layouts](../../../ersteller/website-ersteller/gemeinsamer-kopf-und-fussbereich.md#ändern-und-entfernen)).
4. Verweise prüfen, im Entwurf ändern, veröffentlichen, Seiten kontrollieren.
5. Jede Zone, die damit verknüpft ist, wird getrennt.

</details>

## Weiterführende Informationen

- [Gemeinsame Layouts: Kopfzeile, Fußzeile und Prefooter](../../../ersteller/website-ersteller/gemeinsamer-kopf-und-fussbereich.md)
- [Exponential Layouts: Gemeinsame Layouts und verknüpfte Zonen](../../../../bc/6.0/layouts.md#gemeinsame-layouts-und-verknüpfte-zonen)

## Verwandte Seiten

- [Modul 5: Gemeinsame Layouts und Zonenverknüpfung](README.md)
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
