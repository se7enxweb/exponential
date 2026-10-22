---
title: Wie richte ich eine Freigabe vor dem Veröffentlichen ein?
audience: admin
category: manual
order: 630
summary: Legen Sie einen Workflow mit dem Ereignis Freigeben an, wählen Sie die Sektionen, Versionen, Freigebenden und ausgeschlossenen Gruppen, verbinden Sie ihn unter Auslöser mit Vor dem Veröffentlichen von Inhalten, geben Sie den Freigebenden das Modul collaboration und prüfen Sie, ob der Workflow-Cronjob läuft.
keywords: Freigabe, Ereignis Freigeben, Vier-Augen-Prinzip, Workflow, Auslöser, vor dem Veröffentlichen von Inhalten, Freigebende, Zusammenarbeit, Workflow-Cronjob, wartend, approval, approve event, ezapprove, four-eyes principle, trigger, before publishing content, approvers, collaboration, workflow cronjob, pending
context: workflow/edit
type: help
---
# Wie richte ich eine Freigabe vor dem Veröffentlichen ein?

Mit einem Freigabe-Workflow wartet eine Version, die ein Redakteur zur Veröffentlichung schickt, als wartend, bis eine
freigebende Person sie annimmt. Freigebende finden die Anfrage unter **Dashboard** > **Zusammenarbeit**. Eine neue
Installation hat keinen solchen Workflow.

## Wer das tun kann

Ein Administrator oder eine Rolle mit `workflow/*` und `trigger/*`, dazu `role/*`, um den Freigebenden ihre Richtlinie
zu geben.

## Schritte

1. Klicken Sie im oberen Menü auf **Setup** und wählen Sie **Workflows**.
2. Öffnen Sie eine Workflow-Gruppe, zum Beispiel „Standard“, und klicken Sie auf **Neuer Workflow**. Benennen Sie ihn,
   zum Beispiel „Freigabe“.
3. Wählen Sie in der Liste der Ereignisse **Ereignis / Freigeben** und klicken Sie auf **Ereignis hinzufügen**.
4. Wählen Sie unter **Betroffene Sektionen** die Sektionen, deren Inhalte eine Freigabe brauchen, oder **Alle
   Sektionen**.
5. Wählen Sie unter **Betroffene Versionen** **Alle Versionen**, **Neues Objekt publizieren** oder **Objekt
   aktualisieren**.
6. Fügen Sie die Freigebenden mit **Benutzer hinzufügen** unter **Benutzer, die den Inhalt bewilligen** oder mit
   **Gruppen hinzufügen** unter **Gruppen, die Content freigeben** hinzu.
7. Fügen Sie bei Bedarf unter **Gruppen ausschließen** Gruppen hinzu, deren Mitglieder ohne Freigabe veröffentlichen,
   und klicken Sie dann auf **OK**.
8. Wählen Sie im Setup-Menü **Auslöser**, wählen Sie in der Zeile **Vor dem Veröffentlichen von Inhalten** den Workflow
   und klicken Sie auf **Änderungen anwenden**.
9. Geben Sie der Rolle der Freigebenden eine Richtlinie für das Modul **collaboration** (alle Funktionen), damit sie
   die Anfragen öffnen können ([Wie gebe ich einer Gruppe Bearbeitungsrechte nur für eine Sektion?](wie-gebe-ich-einer-gruppe-bearbeitungsrechte-fuer-eine-sektion.md)
   zeigt, wie man eine Richtlinie hinzufügt).

Probieren Sie es dann aus: Melden Sie sich als Redakteur an, der nicht freigibt, veröffentlichen Sie einen Artikel in
einer betroffenen Sektion und prüfen Sie, ob er in **Meine Warteliste** erscheint und, bei der freigebenden Person, in
**Zusammenarbeit**.

## Was schiefgehen kann

- **Inhalte werden ohne Freigabe veröffentlicht.** Der Redakteur gibt selbst frei oder ist in einer ausgeschlossenen
  Gruppe, oder die Inhalte liegen außerhalb der betroffenen Sektionen, Sprachen oder Versionen. Inhalte, die eine
  freigebende Person anlegt, warten nie auf ihre eigene Freigabe.
- **Freigegebene Inhalte gehen nicht online.** Nach **Freigeben** schließt der Workflow-Cronjob (`workflow.php`, Teil
  frequent) das Veröffentlichen ab. Läuft er nicht, bleibt die Version wartend. **Setup** > **Workflow Prozesse** zeigt,
  ob er eingeplant ist.
- **Die freigebende Person sieht Zusammenarbeit nicht.** Ihrer Rolle fehlt das Modul `collaboration`.
- **Freigebende bekommen keine E-Mail.** Jede freigebende Person wählt die Benachrichtigung zur Freigabe unter
  **Dashboard** > **Meine Benachrichtigungseinstellungen**, und der Benachrichtigungs-Cronjob muss laufen
  ([Wie wähle ich, welche Benachrichtigungen ich bekomme?](wie-waehle-ich-meine-benachrichtigungen.md)).
- **Sie möchten auch zu einem Datum veröffentlichen.** Ein Auslöser startet einen Workflow. Setzen Sie **Freigeben**
  und **Bis Datum warten** in denselben Workflow
  ([Wie veröffentliche ich Inhalte zu einem späteren Zeitpunkt?](wie-veroeffentliche-ich-inhalte-zu-einem-spaeteren-zeitpunkt.md)).

## Verwandte Seiten

- [Workflows](../../anleitungen/workflows.md): Abschnitt 3 ist ein vollständiges durchgearbeitetes Beispiel, mit
  Beispieldaten zum Ausprobieren
- [Wie sende ich Inhalte zur Freigabe?](wie-sende-ich-inhalte-zur-freigabe.md)
- [Wie gebe ich Inhalte frei oder lehne sie ab?](wie-gebe-ich-inhalte-frei-oder-lehne-sie-ab.md)
- [Wo sehe ich meine Elemente, die auf Freigabe warten?](wo-sehe-ich-meine-wartenden-elemente.md)
- [Benutzerhandbuch](README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
