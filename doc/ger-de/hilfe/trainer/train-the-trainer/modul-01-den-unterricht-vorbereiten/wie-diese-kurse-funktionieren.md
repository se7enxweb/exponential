---
title: "Lektion 1.1: Wie diese Kurse funktionieren"
audience: trainer
category: training
order: 11
summary: Wie die Trainerkurse aufgebaut sind (Kurse, Module, Lektionen), was jede Lektionsseite enthält und die eine Regel hinter allen: jede Angabe aus der Dokumentation, dem Code oder einer echten Installation. 30 Minuten.
keywords: train the trainer, kursaufbau, module, lektionen, lektionsseite, lernziele, hinweise für trainer, übung, quiz, quellen, fakten, course structure, lessons, learning objectives, trainer notes, exercise, sources
type: help
---
# Lektion 1.1: Wie diese Kurse funktionieren

Bevor Sie einen der Exponential-Kurse dieser Hilfe unterrichten, müssen Sie wissen, wie sie zusammengesetzt sind,
damit Sie sie wieder auseinandernehmen können: einen ganzen Kurs durchführen, einzelne Lektionen für eine kurze
Einheit auswählen oder Lektionen aus mehreren Kursen zu Ihrem eigenen Programm mischen. Diese erste Lektion des Kurses
Train the Trainer erklärt die Bausteine und die Regel, die sie vertrauenswürdig hält.

> **Hinweis**
> In dieser Lektion geht es um das Material, nicht um Exponential selbst. Sie können sie in einem Besprechungsraum
> ohne Computer präsentieren; die Übung braucht einen Rechner mit einem Browser, der die Administration einer
> Exponential-Installation öffnen kann.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Train the Trainer](../README.md) |
| Modul | [Modul 1: Den Unterricht von Exponential vorbereiten](README.md) |
| Dauer | 30 Minuten (15 Präsentation, 15 Übung) |
| Niveau | Einsteiger |
| Zielgruppe | Menschen, die Exponential-Kurse durchführen werden: Trainer, Teamleiter, Berater, Lehrkräfte |
| Voraussetzungen | Ein Konto, das das Hilfe-Center öffnen kann (jeder Administrator; auf einer neuen Installation auch die Rolle Editor) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die drei Ebenen des Trainermaterials (Kurs, Modul, Lektion) benennen und sagen, wofür jede da ist;
- die Abschnitte aufzählen, die jede Lektionsseite hat, und sagen, welche für den Trainer und welche für die
  Teilnehmenden sind;
- erklären, woher die Angaben einer Lektion stammen und was zu tun ist, wenn sich eine Angabe nicht überprüfen lässt;
- in der Hilfe die Überblicksseite eines Kurses, eine Modulseite und eine Lektionsseite finden.

## Hinweise für Trainer

### So präsentieren Sie es

1. Beginnen Sie mit dem Problem, das diese Seiten lösen (3 Minuten). Ein Kurs über ein Content-Management-System
   veraltet, wenn sich die Software ändert. Die Kurse hier liegen im selben Repository wie der Code von Exponential und
   der Rest der Dokumentation (`doc/eng-us/help/trainers/`), ändern sich also mit und können von allen mit
   **Auf GitHub bearbeiten** korrigiert werden.
2. Zeichnen Sie die Struktur an eine Tafel (5 Minuten):

   ```text
   Course  (a README "course overview" with the reading path and schedules)
     Module  (a README "module overview" with the lesson list and the timing plan)
       Lesson  (one page: objectives, notes, exercise, expected result, quiz, reading)
   ```

3. Öffnen Sie eine Lektion auf dem Projektor und gehen Sie ihre Abschnitte von oben nach unten durch (5 Minuten).
   Weisen Sie darauf hin, dass die **Hinweise für Trainer** für Sie sind und die **Praktische Übung** und das **Quiz**
   für die Teilnehmenden.
4. Erklären Sie die Quellenregel (2 Minuten): Jede Aussage stammt aus der Dokumentation in `doc/`, aus dem Code oder
   aus der echten Oberfläche einer Installation. Wenn ein Kurs und die Software nicht übereinstimmen, hat die Software
   recht, und die Kursseite wird korrigiert.

### Häufige Fragen

**Darf ich die Lektionen für meinen eigenen Kurs ändern?** Ja. Die Seiten sind freie Dokumentation unter der GNU Free
Documentation License 1.3; Sie dürfen sie anpassen, solange Ihre Fassung die Lizenz behält und sagt, dass sie geändert
wurde. Lektion 1.4 behandelt die Einzelheiten.

**Gibt es eine offizielle Exponential-Zertifizierung?** Das Exponential-Partnerprogramm nennt Zertifizierungen für
Partnerteams, aber deren Lehrplan, Prüfungen und Prüfer sind nicht veröffentlicht
([Zertifizierungen](../../../partner/zertifizierungen.md)). Die Prüfungen in diesen Kursen sind Ihr eigenes Material als
Trainer; geben Sie sie niemals als offizielle Zertifizierung aus.

**Warum nennen die Lektionen genaue Schaltflächennamen?** Damit die Teilnehmenden ihnen allein folgen können. Zeigt
Ihre Installation eine andere Beschriftung (eine ältere Version, ein anderes Design der Administration), notieren Sie
den Unterschied auf Ihrem Handout.

### Stolperfallen

- **Aus dem Gedächtnis unterrichten.** Die Administration hat sich in Exponential 6.0 stark verändert (das Design
  Admin 4, das Hilfe-Center, der Layout-Editor). Proben Sie jede Übung mit der Version, die Sie unterrichten werden.
- **Designs vermischen.** Die Lektionen beschreiben das Design Admin 4, sofern sie nichts anderes sagen. Arbeiten Ihre
  Teilnehmenden mit der klassischen Administration, prüfen Sie die Übung zuerst dort.
- **Zu lange Einheiten.** Die Dauer einer Lektion schließt die Übung ein. Fügen Sie nicht drei „kurze Zusatzthemen“
  hinzu.

## Praktische Übung

Verwenden Sie eine beliebige Installation, auf der Sie sich in der Administration anmelden können.

1. Melden Sie sich in der Administration an (zum Beispiel `https://<your-site>/admin`).
2. Klicken Sie auf die Schaltfläche **Hilfemenü** (das „?“ in der Kopfzeile) und dann auf **Hilfe-Center öffnen**.
3. Geben Sie im Feld **Hilfe durchsuchen** `trainer` ein und klicken Sie auf **Suchen**.
4. Öffnen Sie die Seite **Trainer: hier beginnen** und suchen Sie den Link zum **Kurskatalog**.
5. Öffnen Sie im Katalog den Kursüberblick von **Train the Trainer**.
6. Öffnen Sie vom Überblick aus **Modul 1: Den Unterricht von Exponential vorbereiten** und dann diese Lektion.
7. Suchen Sie auf dieser Lektionsseite im Inhaltsverzeichnis der Seite die Abschnitte **Auf einen Blick**,
   **Hinweise für Trainer**, **Praktische Übung**, **Erwartetes Ergebnis**, **Quiz** und
   **Weiterführende Literatur**.
8. Schreiben Sie für diese Lektion auf: ihre Dauer, ihre Voraussetzungen und ihren ersten Link unter
   „Weiterführende Literatur“.

## Erwartetes Ergebnis

- Die Teilnehmenden haben die Lektion vom Hilfe-Center aus mit drei oder vier Klicks erreicht, über die Startseite,
  den Katalog, den Kursüberblick und den Modulüberblick.
- Ihre Notizen sagen: 30 Minuten; ein Konto, das das Hilfe-Center öffnen kann; die Funktionsseite des Hilfesystems.
- Der Trainer prüft die Notizen und bittet eine Teilnehmerin oder einen Teilnehmer, den Weg auf dem Projektor zu
  zeigen.

## Quiz

1. Welche Seite listet die Lektionen eines Moduls zusammen mit einem Zeitplan auf?
   A. Der Kursüberblick B. Der Modulüberblick C. Die Lektionsseite D. Der Kurskatalog
2. Welcher Abschnitt einer Lektionsseite ist für den Trainer statt für die Teilnehmenden geschrieben?
3. Eine Lektion sagt, Sie sollen auf eine Schaltfläche klicken, die Ihre Installation nicht zeigt. Was tun Sie?
4. Richtig oder falsch: Die Prüfungen in diesen Kursen sind eine offizielle Exponential-Zertifizierung.
5. Unter welcher Lizenz dürfen Sie diese Seiten für Ihren eigenen Kurs anpassen?

<details>
<summary>Antworten</summary>

1. **B.** Der Modulüberblick (`README.md` des Modulordners) hat die Tabelle der Lektionen und den Zeitplan.
2. **Hinweise für Trainer**: so präsentieren Sie es, häufige Fragen und Stolperfallen.
3. Prüfen Sie die Version und das Design der Administration Ihrer Installation, notieren Sie den Unterschied auf Ihrem
   Handout und melden oder korrigieren Sie die Seite mit **Auf GitHub bearbeiten**, wenn die Lektion für die aktuelle
   Version falsch ist.
4. **Falsch.** Die Prüfungen sind das eigene Material des Trainers; die Zertifizierungen des Partnerprogramms sind
   eine eigene Sache, deren Einzelheiten nicht veröffentlicht sind ([Zertifizierungen](../../../partner/zertifizierungen.md)).
5. Unter der GNU Free Documentation License 1.3 oder einer späteren Version
   ([Freie Dokumentation](../../../freie-dokumentation.md)).

</details>

## Weiterführende Literatur

- [Das Hilfesystem](../../../../../eng-us/features/6.0/help-system.md): was das Hilfe-Center zeigt und wie es Seiten findet.
- [Freie Dokumentation](../../../freie-dokumentation.md): was die Lizenz Ihnen mit diesen Seiten erlaubt.
- [Ablauf eines praktischen Workshops](../../../fuersprecher/ablaufplan-praxis-workshop.md): die kurzen Workshop-Formate
  der Fürsprecher, ein guter Vergleich für Ihre eigenen Kurse.

## Verwandte Seiten

- [Modul 1: Den Unterricht von Exponential vorbereiten](README.md)
- [Train the Trainer: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
