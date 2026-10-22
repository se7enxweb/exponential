---
title: Eine Veröffentlichung planen
audience: podcaster
category: podcasting-streaming
order: 27
summary: Wie Sie eine Episode mit dem Workflow-Ereignis Bis Datum warten von Exponential zu einem festen Datum und einer festen Uhrzeit veröffentlichen - was der Administrator einmal einrichtet, was der Produzent pro Episode tut, wann die Episode wirklich auf der Website und im Feed erscheint und wie Sie es prüfen.
keywords: Episode planen, Veröffentlichungsdatum, später veröffentlichen, Bis Datum warten, ezwaituntildate, Workflow, Cronjob, workflow.php, frequent, ausstehend, Datum der Veröffentlichung, Sperrfrist, Zeitpunkt im Feed, schedule episode, release date, publish later, wait until date, pending, published date, embargo, feed timing
type: help
---
# Eine Veröffentlichung planen

Diese Seite ist für Produzenten, die eine Episode zu einer festen Zeit veröffentlichen möchten, ohne dass jemand auf
einen Knopf drückt. Exponential tut das mit dem Workflow-Ereignis **Bis Datum warten**
(`kernel/classes/workflowtypes/event/ezwaituntildate/ezwaituntildatetype.php`). Es ist im Auslieferungszustand nicht
aktiv: Ein Administrator richtet es einmal ein.

## Inhalt

1. [Wie es funktioniert](#wie-es-funktioniert)
2. [Einmalige Einrichtung](#einmalige-einrichtung)
3. [Eine Episode planen](#eine-episode-planen)
4. [Wann sie wirklich erscheint](#wann-sie-wirklich-erscheint)
5. [Ändern oder abbrechen](#ändern-oder-abbrechen)
6. [Prüfungen](#prüfungen)

## Wie es funktioniert

1. Die Episodenklasse hat ein Datumsfeld, zum Beispiel **Release date** (`ezdatetime`).
2. Ein Workflow mit dem Ereignis *Bis Datum warten* läuft vor dem Veröffentlichen.
3. Wenn ein Redakteur die Episode zur Veröffentlichung schickt und das Datum in der Zukunft liegt, vermerkt das
   Ereignis "Event delayed until" (Ereignis verzögert bis) das Datum, und die Version wartet.
4. Der Workflow-Cronjob (`workflow.php`, Cronjob-Teil `frequent`) prüft wartende Versionen und veröffentlicht die
   Episode bei seinem ersten Lauf nach dem Datum.
5. Ist die Option des Ereignisses eingeschaltet, die das Datum der Veröffentlichung ändert, wird das Datum der
   **Veröffentlichung** des Objekts auf das Veröffentlichungsdatum gesetzt. Dieses Datum bestimmt die Reihenfolge im
   Feed.

Ist das Datumsfeld leer oder bereits vergangen, wird die Episode sofort veröffentlicht.

## Einmalige Einrichtung

Für den Administrator, nach [dem Beispiel ezwaituntildate](../../beispiele/workflow-ereignisse/ezwaituntildate.md):

1. Fügen Sie der Episodenklasse das Datumsfeld hinzu ([Eine Episodenklasse aufbauen](eine-episodenklasse-aufbauen.md)).
2. **Setup > Workflows**: Legen Sie einen Workflow an und fügen Sie das Ereignis **Bis Datum warten** hinzu. Wählen Sie
   in seinen Einstellungen die Episodenklasse und ihr Datumsfeld, und entscheiden Sie über die Option, die das Datum
   der Veröffentlichung setzt.
3. **Setup > Trigger**: Verbinden Sie den Workflow mit `content / publish / before`.
4. Stellen Sie sicher, dass der Cronjob-Teil `frequent` `workflow.php` ausführt, und wissen Sie, wie oft er läuft
   ([Cronjobs](../../anleitungen/cronjobs.md)).

Das Gesamtbild zeitgesteuerter Inhalte, einschließlich Verbergen und Entfernen zu einem Termin, steht unter
[Zeitgesteuertes Veröffentlichen](../kunden/alltag/zeitgesteuertes-veroeffentlichen.md).

## Eine Episode planen

1. Bereiten Sie die Episode vollständig vor: Audio, Shownotes, Gäste ([Eine Episode veröffentlichen](eine-episode-veroeffentlichen.md)).
2. Tragen Sie unter **Release date** Datum und Uhrzeit der Veröffentlichung ein.
3. Drücken Sie **Zur Veröffentlichung schicken**. Die Version ist jetzt **ausstehend**: Sie steht in **Meine
   Warteliste**, nicht auf der Website.

Lassen Sie eine geplante Episode nicht als Entwurf liegen: Einen Entwurf veröffentlicht der Cronjob nie.

## Wann sie wirklich erscheint

| Schritt | Verzögerung |
|---|---|
| Die Veröffentlichungszeit verstreicht | - |
| Der nächste Lauf des Cronjob-Teils `frequent` | bis zu seinem Intervall, wie in der Crontab des Servers eingestellt |
| Die Episode wird auf der Website veröffentlicht | bei diesem Lauf |
| Der Podcast-Feed listet sie | wenn der Feed-Cache abläuft: bis zu `CacheTime`, im Auslieferungszustand 1200 Sekunden |
| Apps zeigen sie | nach dem eigenen Zeitplan jeder App |

"Veröffentlichung um 6:00 Uhr" heißt also "auf der Website beim ersten Cron-Lauf nach 6:00 Uhr, im Feed bis zu 20
Minuten später". Für eine Veröffentlichung, die auf die Minute genau sein muss, planen Sie ein paar Minuten früher oder
lassen Sie jemanden nachsehen.

Die Zeit, die Sie eingeben, liest die Website in ihrer eingestellten Zeitzone; vereinbaren Sie im Team, welche Zone
gemeint ist.

## Ändern oder abbrechen

- **Das Datum verschieben**: Während die Version wartet, steht der wartende Prozess unter **Setup >
  Workflow-Prozesse**. Fragen Sie Ihren Administrator, wie Änderungen an wartenden Versionen auf Ihrer Website
  gehandhabt werden; der sichere Weg ist, den wartenden Prozess abzubrechen und eine Version mit dem neuen Datum zu
  schicken.
- **Abbrechen**: Ein Administrator kann den wartenden Workflow-Prozess auf dieser Seite nach einer Bestätigung
  abbrechen; die Episode bleibt unveröffentlicht ([Workflows](../../anleitungen/workflows.md#7-prozesse-sicher-abbrechen-und-aufräumen)).

## Prüfungen

- [ ] Am Vortag: Die Episode steht in **Meine Warteliste**, und **Setup > Workflow-Prozesse** führt sie als wartend.
- [ ] Nach der Veröffentlichungszeit und dem nächsten Cron-Lauf: Die Episodenseite öffnet sich abgemeldet.
- [ ] Innerhalb der Cache-Zeit des Feeds: Der Feed listet die Episode oben.
- [ ] Das Datum der Veröffentlichung ist das Veröffentlichungsdatum, wenn die Option eingeschaltet ist.

Wenn am Tag nichts geschieht, ist höchstwahrscheinlich der Cronjob nicht gelaufen
([Fehlersuche bei Feeds und Medien](fehlersuche-bei-feeds-und-medien.md)).

## Verwandte Seiten

- [Zeitgesteuertes Veröffentlichen](../kunden/alltag/zeitgesteuertes-veroeffentlichen.md)
- [Workflows](../../anleitungen/workflows.md)
- [Ein Veröffentlichungskalender](ein-veroeffentlichungskalender.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
