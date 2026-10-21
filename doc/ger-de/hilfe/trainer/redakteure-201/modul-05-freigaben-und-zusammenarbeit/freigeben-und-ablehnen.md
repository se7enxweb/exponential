---
title: "Lektion 5.2: Freigeben und ablehnen"
audience: trainer, editor
category: training
order: 52
summary: Die Teilnehmenden handeln als Freigebende im Posteingang Zusammenarbeit, sehen sich eine wartende Version in der Vorschau an, geben sie frei oder lehnen sie mit einem Kommentar ab und handeln als Autoren bei einer abgelehnten Version. 45 Minuten.
keywords: redakteure 201, freigeben, ablehnen, freigebende person, zusammenarbeit, freigabe, kommentar, kommentar hinzufügen, vorschau, wartet auf freigabe, abgelehnt, autor, editors 201, approve, deny, approver, collaboration, approval, comment, add comment, preview, waiting for approval, denied, author
type: help
---
# Lektion 5.2: Freigeben und ablehnen

Freigebende entscheiden im Posteingang **Zusammenarbeit**. Diese Lektion wird zu zweit gespielt: Eine Person schickt,
die andere gibt frei oder lehnt mit einem Kommentar ab, dann wird getauscht. Beide Seiten lernen, was die andere sieht.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Redakteure 201](../README.md) |
| Modul | [Modul 5: Freigaben und Zusammenarbeit](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Redakteure und Freigebende (Chefredakteure, Prüfende) |
| Voraussetzungen | [Lektion 5.1](wenn-veroeffentlichen-eine-freigabe-braucht.md); Schulungskonten für Autoren und Freigebende |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- den Posteingang **Zusammenarbeit** und darin eine Freigabe öffnen;
- die wartende Version in der Vorschau ansehen und ihre Nachrichten und Teilnehmer lesen;
- mit einem Kommentar **Freigeben** oder **Ablehnen** (**Kommentar hinzufügen**);
- als Autor nach einer Ablehnung weitermachen: den Entwurf bearbeiten und erneut schicken.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Workflows: Ausprobieren](../../../../anleitungen/workflows.md#35-ausprobieren).

1. **Der Posteingang** (3 Minuten). Freigebende öffnen **Zusammenarbeit** (in der linken Spalte des Dashboards der
   Administration, wo ihre Rolle es hat). Dort stehen die Freigabeanfragen; ein leerer Posteingang meldet *Noch nichts
   zu bearbeiten*.
2. **Eine Freigabeseite** (5 Minuten). Titel **Freigabe** und *Das Inhalts-Objekt ... benötigt Ihre Freigabe, bevor es
   veröffentlicht werden kann.* Die Seite zeigt die Version (*Version n des Inhalts, gespeichert ... von ...*),
   **Vorschau**, **Inhalt öffnen**, die **Teilnehmer**, **Nachrichten** mit **Kommentar** und **Kommentar
   hinzufügen** und die Frage *Geben Sie die Veröffentlichung des Inhalts-Objekts frei?* mit **Freigeben** und
   **Ablehnen**. Nur eine freigebende Person kann entscheiden.
3. **Nach der Entscheidung** (4 Minuten). Freigegeben: *... wurde freigegeben und wird veröffentlicht, sobald der
   Veröffentlichungsprozess durchlaufen ist.* Abgelehnt: *... wurde nicht akzeptiert und steht den Autor nun wieder
   als Entwurf zur Verfügung.* Der Autor kann den Entwurf bearbeiten und erneut veröffentlichen; dann ist eine neue
   Freigabe nötig.
4. **Kommentare** (3 Minuten). Bringen Sie Freigebenden bei, mit Begründung abzulehnen. Ein Kommentar vor
   **Ablehnen** sagt dem Autor, was er ändern soll. Ist eine Freigabe abgeschlossen, lassen sich keine Kommentare mehr
   hinzufügen.

### Häufige Fragen

**Wo sieht der Autor die Ablehnung?** Die Version ist wieder ein Entwurf: unter **Meine Entwürfe**, und die Freigabe
mit ihren Nachrichten bleibt über **Meine Warteliste** oder den Posteingang Zusammenarbeit lesbar, solange sie
besteht.

**Kann es mehrere Freigebende geben?** Ja; der Workflow nennt Benutzer oder Gruppen. Eine Entscheidung genügt.

**Bekommen Freigebende eine E-Mail?** Wo Benachrichtigungen für Freigaben eingeschaltet sind (**Meine
Benachrichtigungseinstellungen** > **Benachrichtigung zur Zusammenarbeit**, **Freigabe**) und die Website E-Mails
verschickt.

### Stolperfallen

- **Freigebende, die auch Autoren sind.** Ihre eigenen Inhalte gehen ohne Freigabe durch; verwenden Sie getrennte
  Konten.
- **Der Workflow-Cronjob.** Nach **Freigeben** wird veröffentlicht, wenn der Cronjob läuft. Führen Sie auf einer
  Schulungsinstallation den Teil frequent selbst aus oder warten Sie ein paar Minuten.
- **Ohne Kommentar ablehnen.** Autoren können nicht erraten, was sie ändern sollen.

## Praktische Übung

Arbeiten Sie zu zweit mit einem Autorenkonto und einem Freigabekonto (der Trainer hat beide vorbereitet).

1. Autor: Legen Sie in Ihrem Kursordner `Approval test` an und klicken Sie auf **Zur Veröffentlichung schicken**.
2. Freigebende Person: Öffnen Sie **Zusammenarbeit**. Suchen Sie die Anfrage und öffnen Sie sie.
3. Freigebende Person: Klicken Sie auf **Vorschau** und lesen Sie die Version. Geben Sie dann einen Kommentar ein wie
   `Please add a second sentence to the intro.` und klicken Sie auf **Kommentar hinzufügen**.
4. Freigebende Person: Klicken Sie auf **Ablehnen**. Lesen Sie die Meldung.
5. Autor: Öffnen Sie **Meine Entwürfe** und suchen Sie `Approval test`. Öffnen Sie die Freigabe über **Meine
   Warteliste**, wenn sie dort noch aufgeführt ist, und lesen Sie den Kommentar.
6. Autor: Bearbeiten Sie den Entwurf, fügen Sie den Satz hinzu und klicken Sie erneut auf **Zur Veröffentlichung
   schicken**.
7. Freigebende Person: Öffnen Sie die neue Anfrage, sehen Sie sie in der Vorschau an und klicken Sie auf **Freigeben**.
8. Beide: Warten Sie auf den Workflow-Cronjob (der Trainer führt ihn aus oder sagt, wie lange es dauert), und prüfen
   Sie dann, dass `Approval test` veröffentlicht ist.
9. Tauschen Sie die Rollen und wiederholen Sie die Schritte 1 bis 8 mit einem zweiten Artikel.

## Erwartetes Ergebnis

- Die erste Version wurde mit einem Kommentar abgelehnt und kam als Entwurf zum Autor zurück.
- Die zweite Version wurde freigegeben und ist veröffentlicht, nachdem der Cronjob gelaufen ist.
- Beide Teilnehmenden haben die Freigabeseite von beiden Seiten gesehen.
- Der Trainer prüft das, indem er eine Freigabeseite mit ihren Nachrichten am Beamer öffnet.

## Quiz

1. Wo finden Freigebende die Anfragen, die auf sie warten?
2. Was geschieht mit einer abgelehnten Version?
   A. Sie wird gelöscht B. Sie kommt in den Papierkorb C. Sie wird ein Entwurf für den Autor D. Sie wird trotzdem
   veröffentlicht
3. Warum sollte eine freigebende Person vor dem Ablehnen einen Kommentar schreiben?
4. Richtig oder falsch: Nach **Freigeben** wird die Version in derselben Sekunde veröffentlicht.
5. Der Autor ändert einen abgelehnten Entwurf und schickt ihn erneut. Ist eine weitere Freigabe nötig?

<details>
<summary>Antworten</summary>

1. Im Posteingang **Zusammenarbeit** ([Workflows](../../../../anleitungen/workflows.md#35-ausprobieren)).
2. **C.**
3. Damit der Autor weiß, was er ändern soll.
4. **Falsch.** Sie wird veröffentlicht, wenn der Workflow-Cronjob fortsetzt.
5. Ja: Eine neue Freigabe ist nötig.

</details>

## Weiterführende Informationen

- [Workflows](../../../../anleitungen/workflows.md#3-durchgerechnetes-beispiel-freigabe-vor-dem-veröffentlichen): eine Freigabe einrichten,
  für den Trainer.
- [Meine Entwürfe und meine Warteliste](../../../../anleitungen/entwuerfe-und-ausstehende-objekte.md): beide Seiten einer Freigabe.
- [Ihr Konto und die Anmeldung](../../../konten-und-anmeldung.md#e-mails-und-benachrichtigungen): Benachrichtigungen.

## Verwandte Seiten

- [Modul 5: Freigaben und Zusammenarbeit](README.md)
- [Redakteure 201: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
