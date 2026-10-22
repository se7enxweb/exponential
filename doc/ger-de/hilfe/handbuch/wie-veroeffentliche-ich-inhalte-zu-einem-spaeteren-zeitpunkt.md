---
title: Wie veröffentliche ich Inhalte zu einem späteren Zeitpunkt?
audience: admin, editor
category: manual
order: 625
summary: Geben Sie der Inhaltsklasse ein Feld für Datum und Zeit, legen Sie einen Workflow mit dem Ereignis Bis Datum warten an, das auf dieses Feld zeigt, verbinden Sie ihn mit Vor dem Veröffentlichen von Inhalten und lassen Sie den Workflow-Cronjob laufen; Redakteure setzen dann das Datum und schicken wie gewohnt zur Veröffentlichung.
keywords: zeitgesteuertes Veröffentlichen, später veröffentlichen, verzögertes Veröffentlichen, bis Datum warten, Datum und Zeit, wartend, Workflow, Auslöser, Cronjob, Datum für das Zurückziehen, Datum für das Verstecken, scheduled publishing, publish later, delayed publishing, wait until date, ezwaituntildate, date and time, pending, trigger, cronjob, unpublish date, hide date
context: workflow/edit
type: help
---
# Wie veröffentliche ich Inhalte zu einem späteren Zeitpunkt?

Exponential kann eine Version bis zu einem Datum und einer Uhrzeit zurückhalten, die im Objekt selbst gespeichert
sind. Die Version wird wie gewohnt zur Veröffentlichung geschickt, wartet mit dem Status „wartend“, und der
Workflow-Cronjob veröffentlicht sie, sobald die Zeit gekommen ist. Das funktioniert für neue Objekte und für neue
Versionen veröffentlichter Objekte: Die veröffentlichte Version bleibt auf der Website, bis die neue sie ersetzt. In
einer neuen Installation ist nichts davon eingerichtet.

## Wer das tun kann

- Einrichten: ein Administrator oder eine Rolle mit `class/*`, `workflow/*` und `trigger/*`.
- Verwenden: jeder Redakteur, der die Klasse bearbeiten darf.

## Schritte

1. Bearbeiten Sie unter **Setup** > **Klassen** die Klasse, zum Beispiel Article, und fügen Sie ein Attribut vom Typ
   **Datum und Zeit** mit dem Bezeichner `publish_date` hinzu ([Wie füge ich einer Inhaltsklasse ein Attribut hinzu?](wie-fuege-ich-einer-inhaltsklasse-ein-attribut-hinzu.md)).
   Lassen Sie **Erforderlich** aus, damit Inhalte ohne Datum sofort veröffentlicht werden.
2. Öffnen Sie unter **Setup** > **Workflows** eine Workflow-Gruppe und klicken Sie auf **Neuer Workflow**. Benennen Sie
   ihn, zum Beispiel „Zum Datum veröffentlichen“.
3. Wählen Sie in der Liste der Ereignisse **Ereignis / Bis Datum warten** und klicken Sie auf **Ereignis hinzufügen**.
4. Wählen Sie im Ereignis die Klasse unter **Klasse** und das Datumsattribut unter **Attribut**, und klicken Sie auf
   **Attribute auswählen**.
   Das Paar wird unter **Class/attribute combinations** aufgeführt. Wiederholen Sie das für jede Klasse, die ein
   solches Feld hat.
5. Haken Sie **Modify the objects' publishing dates** an, wenn das Veröffentlichungsdatum des Objekts das geplante
   Datum sein soll und nicht das Datum, an dem der Redakteur es abgeschickt hat. Klicken Sie auf **OK**.
6. Wählen Sie unter **Setup** > **Auslöser** in der Zeile **Vor dem Veröffentlichen von Inhalten** den Workflow und
   klicken Sie auf **Änderungen anwenden**.
7. Lesen Sie unter **Setup** > **Workflow Prozesse** die Statusleiste: Sie muss melden, dass der Workflow-Cronjob
   eingeplant ist.
8. Probieren Sie es aus: Bearbeiten Sie einen Artikel, setzen Sie das Datum einige Minuten in die Zukunft und klicken
   Sie auf **Zur Veröffentlichung schicken**.
   Die Version wird in **Meine Warteliste** aufgeführt und beim nächsten Lauf des Workflow-Cronjobs nach dieser Zeit
   veröffentlicht.

## Was schiefgehen kann

- **Die Inhalte werden sofort veröffentlicht.** Das Datum ist leer oder liegt in der Vergangenheit, das Paar aus Klasse
  und Attribut fehlt im Ereignis, oder der Auslöser ist nicht gesetzt.
- **Die Zeit ist vorbei, aber nichts wird veröffentlicht.** Der Workflow-Cronjob (`workflow.php`, im Teil *frequent*
  der Cronjobs) läuft nicht. Die Veröffentlichung geschieht bei seinem nächsten Lauf; ein Fünf-Minuten-Takt bedeutet
  also bis zu fünf Minuten Verzögerung.
- **Der Redakteur möchte eine wartende Version ändern.** Eine wartende Version lässt sich nicht bearbeiten. Öffnen Sie
  den Versionsverlauf, machen Sie daraus einen neuen Entwurf, ändern Sie ihn und schicken Sie ihn ab; die alte
  wartende Version wird ersetzt, wenn die neue veröffentlicht wird
  ([Wie kehre ich zu einer früheren Version zurück?](wie-kehre-ich-zu-einer-frueheren-version-zurueck.md)).
- **Es ist auch eine Freigabe nötig.** Ein Auslöser nimmt einen Workflow. Setzen Sie beide Ereignisse, **Freigeben**
  und **Bis Datum warten**, in diesen einen Workflow, oder verwenden Sie ein Ereignis **Multiplexer**, das mehrere
  Workflows startet.
- **Zwei Versionen warten gleichzeitig.** Jede wird veröffentlicht, wenn ihre eigene Zeit gekommen ist, also kann die
  ältere Version eine neuere ersetzen. Halten Sie pro Objekt nur eine wartende Version.

**Hinweis:** Inhalte zu einem Zeitpunkt von der Website zu nehmen funktioniert anders, ohne Workflow. Zwei Cronjobs im
Standardteil erledigen das: `unpublish.php` verschiebt Objekte in den Papierkorb, wenn ihr Attribut `unpublish_date`
verstrichen ist, für die Klassen und Teilbäume in `content.ini [UnpublishSettings]`; `hide.php` versteckt Knoten,
deren Datumsattribut verstrichen ist, wie in `content.ini [HideSettings] RootNodeList[]` und
`HideDateAttributeList[<class identifier>]=<attribute>` aufgeführt. Blöcke der Startseite, die nach einem Zeitplan
wechseln, gehören zur optionalen Erweiterung ezflow.

## Verwandte Seiten

- [Workflows](../../anleitungen/workflows.md): Ereignisse, Auslöser, Prozesse und der Cronjob im Detail
- [Wo sehe ich meine Elemente, die auf Freigabe warten?](wo-sehe-ich-meine-wartenden-elemente.md)
- [Wie richte ich eine Freigabe vor dem Veröffentlichen ein?](wie-richte-ich-eine-freigabe-vor-dem-veroeffentlichen-ein.md)
- [Cronjobs](../../anleitungen/cronjobs.md)
- [Benutzerhandbuch](README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
