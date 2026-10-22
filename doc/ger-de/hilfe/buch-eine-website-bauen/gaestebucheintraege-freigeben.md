---
title: "Gästebucheinträge mit einem Workflow freigeben"
audience: builder, admin
category: books
order: 160
summary: Spam und beleidigende Texte im Gästebuch stoppen: ein Freigabe-Workflow, beschränkt auf die Sektion Guestbook, verbunden mit dem Veröffentlichungs-Trigger, der Posteingang des Freigebenden und der Cronjob, der die Freigabe ausführt.
keywords: Workflow, Freigabe, freigeben, Trigger, Zusammenarbeit, Cronjob, Gästebuch, Sektion, workflow, approve, trigger, content publish before, collaboration, approval, cronjob, runcronjobs, guestbook, section, Building book
type: help
---
# Gästebucheinträge mit einem Workflow freigeben

Das bisher gebaute Gästebuch lässt jeden alles auf der Website des Clubs veröffentlichen. Manchmal ist das in Ordnung; für die
meisten öffentlichen Websites nicht. Das mitgelieferte Workflow-System kann jeden Eintrag zurückhalten, bis ein Administrator
ihn freigibt. Das Buch nennt das Workflow-System riesig und verwendet hier ein kleines Stück davon; diese Seite ebenso. Die
ausführliche Fassung ist [Workflows: Freigaben, Trigger und die Prozesse, die warten](../../anleitungen/workflows.md), und die
Ideen stehen in [Workflows und Trigger](../buch-lernen/workflows-und-trigger.md).

## Sagen Sie es den Besuchern

Ändern Sie die Eingabeseite des Gästebuchs so, dass sie sagt, was geschehen wird: „Ihr Eintrag ist nicht sofort sichtbar.
Alle Inhalte dieser Website werden vom Administrator freigegeben; schauen Sie in ein paar Stunden oder Tagen wieder vorbei.“
Besucher, denen das niemand sagt, glauben, die Website habe ihren Text verloren.

## Den Workflow anlegen

1. **Setup > Workflows**, öffnen Sie die Gruppe **Standard**, **Neuer Workflow**.
2. Nennen Sie ihn **Guestbook entry approval**.
3. Wählen Sie das Ereignis **Event / Approve** und klicken Sie auf **Ereignis hinzufügen**.
4. Im Ereignis: Unter **Betroffene Sektionen** steht die Sektion **Guestbook** (damit kein anderer Inhalt zurückgehalten
   wird), der Freigebende ist der Administrator (oder eine Gruppe von Vereinsverantwortlichen), und unter **Excluded user
   groups** stehen die Administratoren, damit ihre eigenen Einträge sofort durchgehen. Die Felder des Buches hießen „Editor“,
   „Section“ und „Users without approval“; heute sind es *Benutzer, die den Inhalt bewilligen*, *Betroffene Sektionen* und
   *Excluded user groups*.
5. Speichern Sie den Workflow.

## Mit einem Trigger verbinden

Ein Workflow tut nichts, bis ein Trigger ihn startet. **Setup > Trigger**, die Zeile **Vor dem Veröffentlichen von Inhalten**
(`content/publish`, before): Wählen Sie *Guestbook entry approval* und klicken Sie auf **Änderungen anwenden**. *Before* hält
die Veröffentlichung zurück, bis der Workflow fertig ist.

## Ausprobieren

Fügen Sie als Besucher einen Eintrag hinzu (in einem privaten Fenster). Er erscheint nicht in der Liste: Das Objekt wartet
unveröffentlicht auf die Freigabe. **Setup > Workflow-Prozesse** listet es auf. Der Administrator öffnet **Mein Konto >
Zusammenarbeit**, findet „a new guestbook entry awaits your approval“ (im Buch „Personal > Collaboration“), sieht sich die
Vorschau an und klickt auf **Freigeben** (oder **Ablehnen**).

## Der Cronjob

Nach **Freigeben** erscheint der Eintrag noch immer nicht. Workflows und Benachrichtigungen führt der Cronjob aus, nicht eine
Webanfrage: `runcronjobs.php` erledigt das und muss regelmäßig laufen ([Installieren, damals und heute](../buch-lernen/installieren-damals-und-heute.md),
[Cronjobs](../../anleitungen/cronjobs.md)). Das Buch startet ihn von Hand mit `php runcronjobs.php`. In Exponential 6 läuft der
Workflow im Teil **Frequent**, der Befehl lautet also

```bash
php runcronjobs.php -q -s tscm frequent
```

(oder klicken Sie auf der Seite **Cronjobs** auf die Ausführen-Schaltfläche des Teils). Dann wird der Eintrag
veröffentlicht. Fehlt die Zeile in der Crontab, warten die freigegebenen Einträge für immer; die Seite **Workflow-Prozesse**
sagt, wenn er nicht geplant ist.

Weiter: [die Linkseite](die-linkseite.md).

## Quelle und Lizenz

Bearbeitet nach den Kapiteln über das Gästebuch und die Freigabe des Tutorials *Building an eZ publish site* (geschrieben für
eZ publish 3.2, 2003), ursprünglich von ez.no, 2024 von 7x erneut unter der GNU Free Documentation License veröffentlicht,
[Book-Building-An-eZPublish-Site](https://github.com/se7enxweb/Book-Building-An-eZPublish-Site). Copyright (c)
1999-2024 7x und Graham Brookins. Lizenziert unter der GNU Free Documentation License, Version 1.3 oder einer späteren
Version, ohne unveränderliche Abschnitte, ohne vordere und ohne hintere Umschlagtexte ([die Lizenz](../../../eng-us/LICENSE-GFDL-1.3.txt)).
Änderungen: für Exponential 6 neu geschrieben: aktuelle Workflow-Seiten, der Cronjob-Befehl, Formular-Token und eine Warnung vor Spam.
