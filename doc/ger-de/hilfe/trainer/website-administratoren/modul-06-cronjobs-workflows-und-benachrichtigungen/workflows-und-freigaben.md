---
title: "Lektion 6.3: Workflows und Freigaben"
audience: trainer, admin
category: training
order: 63
summary: Die Teilnehmenden bauen aus einem Workflow, einem Ereignis Freigeben und einem Trigger eine Freigabe vor dem Veröffentlichen, beobachten unter Setup > Workflow-Prozesse einen wartenden Prozess, erteilen die Freigabe und lassen den Workflow-Cronjob den Artikel veröffentlichen. 50 Minuten.
keywords: website-administration, workflow, trigger, freigabe-ereignis, freigabe, workflow-prozess, zusammenarbeit, vor dem veröffentlichen von inhalten, workflow-cronjob, exp:collaborationsampledata, site administration, approve event, approval, workflow process, collaboration, Before publishing content, workflow cronjob
type: help
---
# Lektion 6.3: Workflows und Freigaben

Ein Workflow hält einen Vorgang zurück, bis etwas geschehen ist: meistens wartet ein Artikel, bis ein Chefredakteur
ihn freigibt. Workflows hängen von einem Cronjob ab, deshalb schließt diese Lektion direkt an die Lektionen 6.1 und 6.2
an: Ohne den Workflow-Cronjob werden freigegebene Inhalte nie veröffentlicht. Die Teilnehmenden bauen eine Freigabe,
sehen einen wartenden Prozess und lassen den Cronjob ihn abschließen.

> **Hinweis**
> Ein Trigger wirkt auf jedes Veröffentlichen in der Installation. Richten Sie ihn nur auf Ihrer
> Schulungsinstallation ein.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Website-Administration](../README.md) |
| Modul | [Modul 6: Cronjobs, Workflows und Benachrichtigungen](README.md) |
| Dauer | 50 Minuten (15 Präsentation, 35 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Website-Administratoren, die redaktionelle Workflows einrichten |
| Voraussetzungen | [Lektion 6.2](die-cronjobs-seite.md); ein Administratorkonto und ein Test-Redakteurskonto (Adresse unter `example.invalid`) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- Workflow, Ereignis, Trigger und Workflow-Prozess in je einem Satz erklären;
- einen Workflow mit einem Ereignis **Freigeben** anlegen und ihn mit **Vor dem Veröffentlichen von Inhalten**
  verbinden;
- unter **Setup > Workflow-Prozesse** einen wartenden Prozess lesen und seine Freigabeanfrage öffnen;
- erklären, warum der Workflow-Cronjob laufen muss, damit ein freigegebener Artikel veröffentlicht wird;
- einen hängenden Prozess abbrechen und sagen, was das Abbrechen mit dem Inhalt macht.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Workflows: Freigaben, Trigger und die Prozesse, die warten](../../../../anleitungen/workflows.md), Abschnitte 1
bis 7.

1. **Die vier Begriffe** (4 Minuten). Ein *Workflow* ist eine geordnete Liste von *Ereignissen* (Freigeben, Warten bis
   Datum, Multiplexer und andere). Ein *Trigger* verbindet einen Workflow mit einem Vorgang, davor oder danach. Ein
   *Workflow-Prozess* ist ein noch nicht abgeschlossener Lauf eines Workflows für einen Inhalt. Zeichnen Sie den
   Ablauf: **Zur Veröffentlichung schicken**, der Trigger startet einen Prozess, das Freigabe-Ereignis wartet, der
   Freigebende entscheidet, der Cronjob macht weiter.
2. **Das Formular des Freigabe-Ereignisses** (3 Minuten). Betroffene Sektionen, betroffene Sprachen, betroffene
   Versionen, Benutzer und Gruppen, die freigeben, und ausgenommene Benutzergruppen. Wer freigibt, muss seine eigenen
   Inhalte nie freigeben lassen.
3. **Ein Workflow pro Trigger** (1 Minute). Um mehrere auszuführen, legen Sie ein Ereignis *Multiplexer* in den
   ausgelösten Workflow.
4. **Die Prozessliste** (4 Minuten). **Setup > Workflow-Prozesse** zeigt jeden wartenden Prozess mit dem, worauf er
   wartet, dem Inhalt und der Version, wer ihn gestartet hat, dem Workflow und dem Schritt. Die Statusleiste sagt, ob
   der Workflow-Cronjob eingeplant ist und wann er das nächste Mal läuft.
5. **Abbrechen** (3 Minuten). **Abbrechen...** fragt zuerst nach. Nach der Bestätigung wird eine wartende Version
   wieder zum Entwurf, die Freigabeanfrage wird geschlossen und der Prozess entfernt. Nichts wird veröffentlicht oder
   gelöscht.

### Häufige Fragen

**Kann ich Workflows ausprobieren, ohne einen zu bauen?** Ja. `php bin/php/console exp:collaborationsampledata` legt
einen Beispiel-Freigabe-Workflow, Beispiel-Redakteure und drei Artikel an, die auf ihre Freigabe warten; `--remove`
nimmt alles wieder weg. Führen Sie es zuerst mit `--dry-run` aus, um zu sehen, was es anlegen würde.

**Die Liste der Prozesse ist leer. Ist etwas kaputt?** Nein. Ein Prozess existiert nur, solange ein Workflow wartet.

**Was ist der Unterschied zwischen davor und danach?** *Davor* hält den Vorgang zurück, bis der Workflow fertig ist;
*danach* führt den Workflow aus, sobald der Vorgang geschehen ist. Freigaben verwenden *davor*.

**Ein freigegebener Artikel ist immer noch nicht veröffentlicht.** Der Workflow-Cronjob ist seit der Freigabe nicht
gelaufen. Führen Sie den Teil **Frequent** aus oder warten Sie auf die Crontab.

### Stolperfallen

- **Das eigene Konto des Trainers.** Testen Sie mit einem Administrator, der auch freigibt, lässt das Ereignis die
  Version direkt durch. Veröffentlichen Sie mit dem Test-Redakteurskonto.
- **Leere Gruppen von Freigebenden.** Kann niemand freigeben, wartet der Prozess ewig. Fügen Sie dem Ereignis
  Freigebende hinzu; eine bereits gestellte Anfrage behält ihre Beteiligten, brechen Sie den Prozess also ab und
  schicken Sie den Inhalt erneut.
- **Vergessen, den Trigger zu entfernen.** Stellen Sie den Trigger am Ende wieder auf **Kein Workflow**, sonst wartet
  jede spätere Übung auf eine Freigabe.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation. Sie brauchen ein Administratorkonto und ein Test-Redakteurskonto mit
einer Adresse unter `example.invalid` (legen Sie unter **Benutzer** eines an, falls Sie keines haben; siehe Lektion 2.5
von Train the Trainer).

1. Öffnen Sie **Setup**, dann **Workflows** in der linken Spalte. Öffnen Sie die Gruppe **Standard**.
2. Klicken Sie auf **Neuer Workflow** und nennen Sie ihn `Training approval`.
3. Wählen Sie in der Ereignisliste **Ereignis / Freigeben** und klicken Sie auf **Ereignis hinzufügen**.
4. Setzen Sie im Formular des Ereignisses **Betroffene Sektionen** auf die Sektion Ihrer Schulungsinhalte (oder alle
   Sektionen), fügen Sie unter **Gruppen, die Content freigeben** die Gruppe Ihres Administrators hinzu und klicken
   Sie auf **OK**.
5. Öffnen Sie **Setup > Trigger**. Wählen Sie in der Zeile **Vor dem Veröffentlichen von Inhalten** den Workflow
   `Training approval` und klicken Sie auf **Änderungen anwenden**.
6. Melden Sie sich in einem privaten Browserfenster mit dem Test-Redakteurskonto an und veröffentlichen Sie einen
   kurzen Artikel in einem Schulungsordner. Stellen Sie fest, dass er noch nicht veröffentlicht ist.
7. Öffnen Sie wieder als Administrator **Setup > Workflow-Prozesse**. Suchen Sie den Prozess unter
   **Vor dem Veröffentlichen von Inhalten** und lesen Sie, worauf er wartet, und die Statusleiste zum
   Workflow-Cronjob.
8. Klicken Sie auf **Freigabeanfrage öffnen** und geben Sie den Artikel frei.
9. Öffnen Sie **Setup > Cronjobs** und führen Sie den Teil **Frequent** für Ihren öffentlichen Siteaccess aus (oder
   führen Sie in einem Terminal `php runcronjobs.php -s <site> --script=workflow.php` aus).
10. Prüfen Sie den Artikel: Er ist veröffentlicht, und der Prozess ist aus der Liste verschwunden.
11. Veröffentlichen Sie als Redakteur einen zweiten Artikel. Klicken Sie als Administrator bei seinem Prozess auf
    **Abbrechen...** und lesen Sie die Bestätigungsseite, bevor Sie mit **Ja, Prozess abbrechen** bestätigen.
12. Öffnen Sie **Setup > Trigger** und stellen Sie **Vor dem Veröffentlichen von Inhalten** wieder auf
    **Kein Workflow**; klicken Sie auf **Änderungen anwenden**.

## Erwartetes Ergebnis

- Nach Schritt 6 sieht der Redakteur, dass der Artikel auf die Freigabe wartet; die Website zeigt ihn nicht.
- Schritt 7 listet einen wartenden Prozess mit einem Link **Freigabeanfrage öffnen**.
- Nach den Schritten 8 und 9 ist der Artikel veröffentlicht, und die Prozessliste zeigt ihn nicht mehr.
- Nach Schritt 11 ist der zweite Artikel wieder ein Entwurf des Redakteurs; nichts wurde veröffentlicht oder gelöscht.
- Nach Schritt 12 funktioniert das Veröffentlichen wieder ohne Freigabe.
- Der Trainer prüft das, indem er alle Teilnehmenden die auf **Kein Workflow** zurückgesetzte Trigger-Zeile zeigen
  lässt.

![Wartende Workflow-Prozesse, zum Beispiel auf eine Freigabe oder den Workflow-Cronjob](../../../../../eng-us/help/images/trainers/sa-workflow-processes.webp)

![Trigger: welcher Workflow vor oder nach dem Veröffentlichen und anderen Vorgängen läuft](../../../../../eng-us/help/images/trainers/sa-triggers.webp)

## Quiz

1. Was verbindet einen Workflow mit dem Vorgang des Veröffentlichens?
   A. Ein Ereignis B. Ein Trigger C. Eine Sektion D. Eine Rolle
2. Ein Artikel wurde vor einer Stunde freigegeben und ist immer noch nicht veröffentlicht. Was prüfen Sie zuerst?
3. Was geschieht mit dem Inhalt, wenn Sie einen wartenden Freigabeprozess abbrechen?
4. Ein Administrator, der auch freigibt, veröffentlicht einen Artikel. Wartet dieser auf die Freigabe?
5. Warum verwendet eine Freigabe *vor* und nicht *nach* dem Veröffentlichen?

<details>
<summary>Antworten</summary>

1. **B.** Ein Trigger verknüpft einen Workflow mit einem Vorgang, davor oder danach
   ([Workflows, Abschnitt 1](../../../../anleitungen/workflows.md#1-workflows-ereignisse-trigger-und-prozesse)).
2. Ob der Workflow-Cronjob läuft: die Statusleiste von **Setup > Workflow-Prozesse** oder der Teil **Frequent** unter
   **Setup > Cronjobs**.
3. Eine wartende Version wird wieder zum Entwurf ihres Autors, und die Freigabeanfrage wird geschlossen; nichts wird
   veröffentlicht oder gelöscht ([Workflows, Abschnitt 7](../../../../anleitungen/workflows.md#7-prozesse-sicher-abbrechen-und-aufräumen)).
4. Nein. Gehört der veröffentlichende Benutzer zu den Freigebenden, lässt das Ereignis die Version durch.
5. *Vor* hält das Veröffentlichen zurück, bis der Workflow fertig ist; *nach* würde ihn ausführen, wenn der Artikel
   schon öffentlich ist.

</details>

## Weiterführende Literatur

- [Workflows: Freigaben, Trigger und die Prozesse, die warten](../../../../anleitungen/workflows.md): jeder Status, die
  Trigger-Liste und die Fehlersuche.
- [Cronjobs, Abschnitt 16: Workflows, Schritt für Schritt](../../../../anleitungen/cronjobs.md#16-arbeitsabläufe-schritt-für-schritt):
  die Cronjob-Seite eines Workflows.
- [Meine Entwürfe und meine wartenden Elemente](../../../../anleitungen/entwuerfe-und-ausstehende-objekte.md): was Redakteure sehen,
  während Inhalte warten.

## Verwandte Seiten

- [Modul 6: Cronjobs, Workflows und Benachrichtigungen](README.md)
- [Website-Administration: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
