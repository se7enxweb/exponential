---
title: "Lektion 5.1: Wenn Veröffentlichen eine Freigabe braucht"
audience: trainer, editor
category: training
order: 51
summary: Die Teilnehmenden erklären, wie ein Freigabe-Workflow eine Version zurückhält, erkennen eine ausstehende Version unter Meine Warteliste und lesen, was sie zurückhält und wer entscheidet. 30 Minuten.
keywords: redakteure 201, freigabe, workflow, ausstehend, meine warteliste, wartet auf freigabe, freigebende person, trigger, workflow-cronjob, zusammenarbeit, editors 201, approval, pending, my pending items, waiting for approval, approver, workflow cronjob, collaboration
type: help
---
# Lektion 5.1: Wenn Veröffentlichen eine Freigabe braucht

Auf vielen Websites veröffentlicht **Zur Veröffentlichung schicken** eines Redakteurs nicht sofort: Eine Kollegin muss
zuerst freigeben. Diese Lektion erklärt, was in der Zwischenzeit mit der Version geschieht und wo der Autor sie sieht.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Redakteure 201](../README.md) |
| Modul | [Modul 5: Freigaben und Zusammenarbeit](README.md) |
| Dauer | 30 Minuten (15 Präsentation, 15 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Redakteure auf Websites mit Freigabe vor dem Veröffentlichen |
| Voraussetzungen | Redakteure 101, Lektion 6.1 (Versionszustände) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- erklären, was ein Freigabe-Workflow ist und wann er greift;
- eine ausstehende Version erkennen und sie unter **Meine Warteliste** finden;
- eine Karte eines ausstehenden Elements lesen: den Stand der Freigabe, wer sie geschickt hat, was sie zurückhält;
- erklären, warum eine freigegebene Version möglicherweise einige Minuten braucht, bis sie veröffentlicht ist.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Workflows](../../../../anleitungen/workflows.md#kurz-gesagt) und
[Meine Warteliste](../../../../anleitungen/entwuerfe-und-ausstehende-objekte.md#2-meine-ausstehenden-elemente).

1. **Die Idee** (4 Minuten). Ein **Workflow** ist eine Liste von Schritten; ein Schritt *Approve* lässt das
   Veröffentlichen warten, bis eine freigebende Person entscheidet. Ein **Trigger** verbindet ihn mit dem
   Veröffentlichen („vor dem Veröffentlichen von Inhalten“). Ein Administrator richtet ihn ein und kann ihn auf einige
   Sektionen, Sprachen, neue Elemente oder Aktualisierungen und auf einige Benutzergruppen beschränken.
2. **Was der Autor sieht** (4 Minuten). Nach **Zur Veröffentlichung schicken** ist die Version **ausstehend**:
   geschickt, aber zurückgehalten. Die Website zeigt weiter die vorherige veröffentlichte Version (oder bei einem
   neuen Element nichts). **Meine Warteliste** listet sie auf: den Stand ihrer Freigabe, Klasse, Ort, Übersetzung und
   Version, wer sie wann geschickt hat, was sie zurückhält, und Links wie **Freigabe öffnen**, wenn Sie daran
   beteiligt sind.
3. **Wer entscheidet** (3 Minuten). Freigebende sind die im Workflow genannten Benutzer oder Gruppen. Die eigenen
   Inhalte einer freigebenden Person brauchen keine Freigabe.
4. **Die paar Minuten** (2 Minuten). Nach der Entscheidung setzt der Workflow-Cronjob fort und veröffentlicht die
   freigegebene Version; auf den meisten Websites läuft er alle paar Minuten. Bis dahin bleibt die Version ausstehend.

### Häufige Fragen

**Mein Element ist seit Tagen ausstehend.** Fragen Sie die freigebende Person. Wenn keine Freigabe es mehr
zurückhält, sagt das die Karte; dann sieht sich ein Administrator **Setup** > **Workflow-Prozesse** an.

**Kann ich eine ausstehende Version ändern?** Diese Version nicht. Wird sie abgelehnt, wird sie wieder ein Entwurf, und
Sie bearbeiten sie; wenn Sie sie vor einer Entscheidung ändern müssen, bitten Sie die freigebende Person, sie
abzulehnen.

**Braucht jede Änderung eine Freigabe?** Nur, was der Workflow abdeckt: bestimmte Sektionen, Sprachen,
Benutzergruppen, neue Elemente oder Aktualisierungen.

### Stolperfallen

- **Kein Workflow auf der Schulungs-Website.** Führen Sie vor dem Kurs `exp:collaborationsampledata` aus (siehe die
  Modulübersicht) oder richten Sie das Beispiel aus dem Leitfaden ein.
- **Freigebende in derselben Gruppe wie Autoren.** Die eigenen Inhalte einer freigebenden Person gehen ohne Freigabe
  durch; geben Sie Autoren ein eigenes Konto.
- **Ungeduld.** Erklären Sie die Verzögerung durch den Cronjob, bevor die Teilnehmenden denken, es sei kaputt.

## Praktische Übung

Verwenden Sie die Schulungsinstallation mit einem Freigabe-Workflow, der die Gruppe der Teilnehmenden abdeckt (der
Trainer hat ihn eingerichtet).

1. Legen Sie in Ihrem Kursordner einen Artikel `Needs approval` an und klicken Sie auf **Zur Veröffentlichung
   schicken**.
2. Lesen Sie die Meldung oder Seite, die folgt.
3. Öffnen Sie Ihren Kursordner: Steht der Artikel unter **Unterelemente**? Öffnen Sie die Website in einem privaten
   Fenster: Ist er sichtbar?
4. Öffnen Sie auf dem Dashboard **Meine Warteliste**. Suchen Sie `Needs approval`.
5. Lesen Sie seine Karte und notieren Sie: den Stand seiner Freigabe, wer ihn geschickt hat, wann und was ihn
   zurückhält.
6. Wenn die Karte **Freigabe öffnen** oder **Freigabe und Kommentare** anbietet, öffnen Sie sie und lesen Sie die Seite.
7. Öffnen Sie **Versionen verwalten** des Artikels (wenn er in Ihrem Ordner aufgeführt ist) und suchen Sie den Status
   der Version.

## Erwartetes Ergebnis

- Der Artikel ist nicht auf der Website erschienen; seine Version ist ausstehend.
- **Meine Warteliste** listet ihn mit einer wartenden Freigabe auf, geschickt von der Person selbst.
- Die Freigabeseite (falls geöffnet) sagt, dass der Inhalt auf eine Freigabe wartet, bevor er veröffentlicht werden
  kann.
- Der Trainer prüft das, indem er **Meine Warteliste** einer Person am Beamer öffnet.

![Meine Warteliste, in der eine Version aufgeführt ist, die auf ihre Freigabe wartet](../../../../../eng-us/help/images/trainers/e201-pending.webp)

## Quiz

1. Was hält eine Version auf einer Website mit Freigabe vom Veröffentlichen ab?
2. Wo sieht der Autor eine Version, die auf ihre Freigabe wartet?
   A. **Meine Entwürfe** B. **Meine Warteliste** C. **Papierkorb** D. **Lesezeichen**
3. Warum braucht eine freigegebene Version möglicherweise einige Minuten, bis sie auf der Website ist?
4. Richtig oder falsch: Die eigenen Inhalte einer freigebenden Person brauchen immer die Freigabe einer anderen Person.
5. Was tun Sie mit einer Version, die seit einer Woche ausstehend ist?

<details>
<summary>Antworten</summary>

1. Ein Workflow mit einem Freigabeschritt, gestartet vom Trigger „vor dem Veröffentlichen von Inhalten“
   ([Workflows](../../../../anleitungen/workflows.md#kurz-gesagt)).
2. **B.**
3. Der Workflow-Cronjob setzt nach der Entscheidung fort; auf den meisten Websites läuft er alle paar Minuten.
4. **Falsch.** Wenn die veröffentlichende Person freigeben darf, lässt der Freigabeschritt die Version durch.
5. Die freigebende Person fragen; wenn nichts sie mehr zurückhält, einen Administrator bitten, sich die
   Workflow-Prozesse anzusehen.

</details>

## Weiterführende Informationen

- [Workflows](../../../../anleitungen/workflows.md): Freigaben, Trigger und Prozesse.
- [Meine Entwürfe und meine Warteliste](../../../../anleitungen/entwuerfe-und-ausstehende-objekte.md#2-meine-ausstehenden-elemente): die Seite der
  ausstehenden Elemente.
- [Veröffentlichen, Versionen und der Papierkorb](../../../veroeffentlichen-und-versionen.md#meine-warteliste): ausstehende
  Elemente in Kürze.

## Verwandte Seiten

- [Modul 5: Freigaben und Zusammenarbeit](README.md)
- [Redakteure 201: Kursübersicht](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
