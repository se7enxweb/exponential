---
title: "Lektion 4.1: Vector-Threads und ihr Umfang"
audience: trainer, editor
category: training
order: 41
summary: Die Teilnehmenden öffnen Vector, beginnen einen Thread, legen mit dem Umfang fest, was er berühren darf (Knoten, Teilbäume, Klassen, Sprachen), wählen Modus, Prüfung, Modell und Spur und finden Threads in den Regalen Active, Settled und Archived. 45 Minuten.
keywords: exponential intelligence, vector, thread, new thread, umfang, teilbaum, klassen, sprachen, modus, plan, write, prüfung, staged, direct, modell, spur, regale, settled, archived, scope, subtree, classes, languages, mode, review, model, lane, shelves
type: help
---
# Lektion 4.1: Vector-Threads und ihr Umfang

**Vector** ist der kontrollierte Weg, mit dem Assistenten Änderungen in Auftrag zu geben: ein Arbeitsbereich aus
**Threads**, in dem der Redakteur zuerst entscheidet, was ein Thread berühren darf, und dann, wie er arbeitet. Diese
Lektion richtet einen Thread für den eigenen Ordner der Person ein.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Exponential Intelligence](../README.md) |
| Modul | [Modul 4: Vector](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Erfahrene Redakteure mit der Richtlinie `expai/vector` |
| Voraussetzungen | [Modul 2](../modul-02-der-intelligence-tab/README.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- **EI: Vector** öffnen und mit **+ New thread** (oder der Taste `N`) einen Thread beginnen;
- den Umfang mit **Change** festlegen: Knoten, Teilbäume, Klassen und Sprachen;
- den Modus (**Plan** oder **Write**), die Prüfung (**Staged** oder **Direct**), das Modell und die Spur wählen;
- erklären, warum Massenarbeit immer mit einem Plan beginnt und immer gestuft ist;
- Threads mit **Filter threads** und in den Regalen **Active**, **Settled** und **Archived** finden.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: die Vector-Seite unter **EI: Help** (`extension/expai/doc/vector.md`).

1. **Die Seite** (3 Minuten). Reiter Dashboard > **EI: Vector** (mit `expai/write` und `expai/vector`). Auf einer leeren
   Seite **Start a thread**: *Choose what it may touch (nodes, subtrees, classes, languages), then say what to do. Bulk
   work starts with a plan.* (Wählen Sie, was er berühren darf, und sagen Sie dann, was zu tun ist; Massenarbeit beginnt
   mit einem Plan.) Drei Bereiche: links die Threads, in der Mitte der Thread, rechts **Plan, changes and cost** (auf
   schmaleren Bildschirmen eine Schublade). **Search threads, commands, nodes...** (Strg+K) ist die Befehlspalette.
2. **Umfang** (4 Minuten). **Change** (Alt+S) öffnet den Dialog für den Umfang: Knoten nach Name oder Nummer suchen
   (nur, was Sie lesen dürfen), einen Knoten zum **Teilbaum** machen, **Klassen** und **Sprachen** ankreuzen. Die
   Einschränkungen der Rolle gelten (Lektion 1.3).
3. **Modus und Prüfung** (4 Minuten):

   | | Plan | Write |
   |---|---|---|
   | Staged | liest und schlägt einen Plan vor; schreibt nichts | schlägt Änderungen vor, die Sie pro Feld und Teil annehmen oder ablehnen, und schreibt dann Entwürfe |
   | Direct | (wie Plan) | schreibt sofort Entwürfe (nie veröffentlicht); nur ein Element in einer Sprache, wo die Rolle es erlaubt |

   Massenarbeit (mehr als ein Element oder eine Sprache, oder ein Teilbaum oder eine Klasse im Umfang) beginnt in
   **Plan** und ist immer **Staged**.
4. **Modell und Spur** (2 Minuten). Nur Modelle, die Sie verwenden dürfen, nach Anbieter gruppiert; „reads only“ bei
   Modellen, die nicht schreiben dürfen. Die Spur ist für Läufe im Hintergrund wichtig (Lektion 7.2).
5. **Regale** (2 Minuten). Die Thread-Liste hat Abschnitte wie *Working*, *Needs you*, *Pinned* und *Recent* sowie die
   Regale **Active**, **Settled**, **Archived**. Threads sind privat für ihre Besitzer.

### Häufige Fragen

**Was ist der Unterschied zwischen Vector und dem Tab Intelligence?** Der Tab ist ein Gespräch zu einem Element mit
direkten Entwürfen für kleine Änderungen; Vector legt einen Umfang fest, plant Massenarbeit, stuft Änderungen zur
Prüfung und zeigt die Kosten.

**Kann ich den Thread einer Kollegin sehen?** Nein; Threads sind privat. Freigebende einer Version, die ein Thread
geschrieben hat, dürfen ihn lesen.

**Warum kann ich Direct nicht wählen?** Ihre Rolle erlaubt nur die gestufte Prüfung, oder der Umfang ist Massenarbeit.

### Stolperfallen

- **Zu weite Umfänge.** Eine ganze Website als Teilbaum lädt zu einem riesigen Plan ein. Beginnen Sie mit einem Ordner.
- **Die Sprache vergessen.** Ein Übersetzungs-Thread braucht die Zielsprache im Umfang.
- **Schmale Bildschirme.** Der rechte Bereich wird unter etwa 1040 Pixeln zu einer Schublade; maximieren Sie das
  Fenster.

## Praktische Übung

Verwenden Sie die Schulungsinstallation, eine Rolle mit `expai/vector` und Ihren Kursordner.

1. Öffnen Sie **EI: Vector** in der linken Spalte des Reiters Dashboard. Lesen Sie den Text **Start a thread**.
2. Klicken Sie auf **+ New thread**.
3. Klicken Sie auf **Change**, um den Umfang festzulegen. Suchen Sie Ihren Kursordner und fügen Sie ihn als **subtree**
   hinzu. Kreuzen Sie die Klasse *Article* und die Hauptsprache Ihrer Website an.
4. Stellen Sie den Modus auf **Plan** und die Prüfung auf **Staged**.
5. Behalten Sie das Standardmodell; lesen Sie die Modellliste und notieren Sie, welche „reads only“ anzeigen.
6. Geben Sie dem Thread einen Titel wie `Course folder tidy-up` (wenn der Titel bearbeitbar ist) und lassen Sie ihn ohne
   Nachricht.
7. Öffnen Sie die Befehlspalette mit Strg+K (Cmd+K auf einem Mac), geben Sie `thread` ein und schließen Sie sie mit
   Esc.
8. Suchen Sie Ihren Thread mit **Filter threads** und sehen Sie sich die Regale **Settled** und **Archived** an.

## Erwartetes Ergebnis

- Alle Teilnehmenden haben einen Thread, dessen Umfang ihr Kursordner (Teilbaum), *Article* und eine Sprache ist, im
  Modus **Plan** mit der Prüfung **Staged**.
- Es wurde noch keine Anfrage geschickt.
- Der Trainer prüft das, indem er den Kopf des Threads einer Person am Beamer öffnet.

## Quiz

1. Welche vier Arten von Einträgen kann ein Umfang haben?
2. Was macht Arbeit zu „Massenarbeit“?
3. In welchem Modus und mit welcher Prüfung beginnt Massenarbeit immer?
   A. Write, Direct B. Plan, Staged C. Write, Staged D. Plan, Direct
4. Wer kann Ihre Threads sehen?
5. Was bedeutet „reads only“ neben einem Modell?

<details>
<summary>Antworten</summary>

1. Knoten, Teilbäume, Klassen und Sprachen.
2. Mehr als ein Element oder mehr als eine Sprache, oder ein Teilbaum oder eine Klasse im Umfang.
3. **B.**
4. Nur Sie; Freigebende einer Version, die ein Thread geschrieben hat, dürfen diesen Thread lesen.
5. Das Modell darf antworten, aber keine Entwürfe schreiben.

</details>

## Weiterführende Informationen

- Die Seiten zu Vector, Threads und Berechtigungen unter **EI: Help** (`extension/expai/doc/vector.md`,
  `extension/expai/doc/threads.md`, `extension/expai/doc/permissions.md`).

## Verwandte Seiten

- [Modul 4: Vector](README.md)
- [Exponential Intelligence: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
