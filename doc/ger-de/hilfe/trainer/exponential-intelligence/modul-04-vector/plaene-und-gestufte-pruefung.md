---
title: "Lektion 4.2: Pläne und gestufte Prüfung"
audience: trainer, editor
category: training
order: 42
summary: Die Teilnehmenden bitten einen Vector-Thread um einen Plan, lesen seine Punkte und die Kostenschätzung, geben ihn frei und führen ihn aus, prüfen dann die vorgeschlagenen Änderungen Teil für Teil im Tab Changes und schreiben die angenommenen in Entwürfe. 60 Minuten.
keywords: exponential intelligence, vector, planmodus, tab plan, approve and run, ask for changes, reject, schätzung, gestufte prüfung, tab changes, annehmen, ablehnen, accept field, write accepted changes to drafts, konflikt, plan mode, plan tab, estimate, staged review, changes tab, accept, conflict
type: help
---
# Lektion 4.2: Pläne und gestufte Prüfung

Für alles, was größer ist als eine kleine Änderung, arbeitet Vector mit zwei Schranken. Zuerst ein **Plan** mit einer
Kostenschätzung, den der Redakteur freigibt. Dann **gestufte Änderungen**, die der Redakteur Teil für Teil annimmt oder
ablehnt, bevor etwas geschrieben wird. Selbst dann sind das Ergebnis Entwürfe. Diese Lektion geht durch beide
Schranken.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Exponential Intelligence](../README.md) |
| Modul | [Modul 4: Vector](README.md) |
| Dauer | 60 Minuten (20 Präsentation, 40 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Erfahrene Redakteure mit der Richtlinie `expai/vector` |
| Voraussetzungen | [Lektion 4.1](threads-und-umfang.md): ein auf den Kursordner der Person beschränkter Thread |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- eine Anfrage im Planmodus ausführen und die Punkte, die Schätzung, die Annahmen, Fragen und Risiken des Plans lesen;
- einen Plan mit **Approve and run** freigeben, mit **Ask for changes** ändern lassen oder mit **Reject** ablehnen und
  Punkte abwählen;
- gestufte Änderungen im Tab **Changes** prüfen: Teile und Felder annehmen, ablehnen oder bearbeiten;
- angenommene Änderungen mit **Write accepted changes to drafts** schreiben;
- erklären, was ein Konflikt ist, und die drei Auswege nennen.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: die Seiten zum Planmodus und zur Prüfung unter **EI: Help** (`extension/expai/doc/plan-mode.md`,
`extension/expai/doc/review.md`).

1. **Plan** (5 Minuten). Im Planmodus liest der Thread und schlägt einen Plan vor; er schreibt nichts. Der Tab **Plan**
   zeigt eine Tabelle von Punkten (ein Kontrollkästchen Run, Aktion, Ziel, Sprachen, Felder, Schätzung) mit
   bearbeitbaren Anweisungen, die Schätzung als Spanne, die berührten Limits, Annahmen, Fragen und Risiken. **Approve
   and run**, **Ask for changes**, **Reject**. Das Abwählen eines Punkts schätzt neu.
2. **Gestufte Änderungen** (5 Minuten). Die Schreibwerkzeuge schlagen Änderungen vor, statt zu schreiben: eine pro
   Element, Sprache und Feld. Der Tab **Changes** gruppiert sie nach Element und Sprache, dann nach Feld, dann nach
   Teilen: was wegfällt in Rot, was hinzukommt in Grün, Wort für Wort. Aktionen: **Accept**, **Reject** (Tasten `a` und
   `x`), **Accept field**, **Reject field**, **Edit** für reinen Text, **Accept all**, **Reject all**, dann **Write
   accepted changes to drafts**.
3. **Schutz** (3 Minuten). Ein Teil, der aus Rich Text Einbettungen, Formeln oder Anker entfernen würde, sagt das und
   lässt sich nicht annehmen. Datentypen, die der Assistent nicht schreibt (Bilder, Verknüpfungen, Dateien), werden
   übersprungen.
4. **Konflikte** (4 Minuten). Hat eine Kollegin das Element inzwischen veröffentlicht, wird die Änderung zu einem
   Konflikt und wird nicht geschrieben. Auswege: **Keep current**, mit der aktuellen Version vergleichen oder die KI
   bitten, neu aufzusetzen (Rebase).
5. **Das Ergebnis** (3 Minuten). Entwürfe mit der Kennzeichnung, aufgeführt unter *Drafts written*, wie gewohnt
   veröffentlicht oder zur Freigabe geschickt.

### Häufige Fragen

**Kann ich den Plan vor dem Ausführen ändern?** Bearbeiten Sie die Anweisung eines Punkts, wählen Sie Punkte ab, oder
fordern Sie mit **Ask for changes** einen neuen Plan an.

**Was, wenn ich nichts annehme?** Dann wird nichts geschrieben.

**Ist die Schätzung die Kosten?** Eine Spanne aus Tokenzahlen und der Preistabelle; die tatsächlichen Kosten erscheinen
unter **Cost**.

### Stolperfallen

- **Accept all aus Gewohnheit.** Lesen Sie die Teile; das Werkzeug ist zum Lesen gebaut.
- **Große Pläne im Kurs.** Halten Sie den Kursordner klein (drei bis fünf Artikel), damit ein Plan in Minuten läuft.
- **Konflikte im Kurs.** Zwei Teilnehmende in einem Ordner erzeugen Konflikte; halten Sie die Umfänge getrennt (oder
  erzeugen Sie absichtlich einen, um ihn zu zeigen).

## Praktische Übung

Verwenden Sie Ihren Thread aus Lektion 4.1 (Umfang: Ihr Kursordner als Teilbaum, *Article*, eine Sprache; Plan;
Staged).

1. Geben Sie in das Nachrichtenfeld ein: `Make the intro of every article in this folder at most two sentences, keep
   the meaning. Plan first.` Klicken Sie auf **Run**.
2. Wenn der Thread unter *Needs you* wartet, öffnen Sie den Tab **Plan**. Lesen Sie die Punkte, die Schätzung und die
   Risiken.
3. Wählen Sie einen Punkt ab und beobachten Sie, wie sich die Schätzung ändert. Wählen Sie ihn wieder an.
4. Klicken Sie auf **Approve and run**.
5. Wenn Änderungen gestuft sind, öffnen Sie den Tab **Changes**. Lesen Sie für jeden Artikel die Teile.
6. Nehmen Sie die Teile an, denen Sie zustimmen (**Accept** oder `a`), lehnen Sie einen ab (**Reject** oder `x`) und
   bearbeiten Sie einen reinen Textteil mit **Edit**, falls angeboten.
7. Klicken Sie auf **Write accepted changes to drafts**.
8. Öffnen Sie einen der Entwürfe aus *Drafts written* im normalen Bearbeitungsformular und lesen Sie ihn.
   Veröffentlichen Sie ihn oder verwerfen Sie ihn.

## Erwartetes Ergebnis

- Ein Plan mit einem Punkt pro Artikel wurde freigegeben; Änderungen wurden gestuft, teilweise angenommen und in
  Entwürfe geschrieben.
- Abgelehnte Teile haben den veröffentlichten Text behalten; Vector hat nichts veröffentlicht.
- Der Trainer prüft das, indem er den Tab **Changes** einer Person und den entstandenen Entwurf am Beamer öffnet.

## Quiz

1. Was tut ein Thread im Planmodus?
2. Nennen Sie die drei Schaltflächen des Tabs **Plan**.
3. Was bewirken die Tasten `a` und `x` im Tab **Changes**?
4. Richtig oder falsch: **Write accepted changes to drafts** veröffentlicht die Änderungen.
5. Eine Kollegin hat einen Artikel veröffentlicht, nachdem die Änderung gestuft wurde. Was geschieht, und was können
   Sie tun?

<details>
<summary>Antworten</summary>

1. Er liest und schlägt einen Plan vor; er schreibt nichts.
2. **Approve and run**, **Ask for changes**, **Reject**.
3. Den aktuellen Teil annehmen und ablehnen.
4. **Falsch.** Es schreibt Entwürfe; nichts wird veröffentlicht.
5. Die Änderung wird zu einem Konflikt und wird nicht geschrieben; die aktuelle Version behalten, mit ihr vergleichen
   oder die KI bitten, neu aufzusetzen.

</details>

## Weiterführende Informationen

- Die Seiten zum Planmodus, zur Prüfung, zu parallelen Läufen und zum Diff unter **EI: Help**.

## Verwandte Seiten

- [Modul 4: Vector](README.md)
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
