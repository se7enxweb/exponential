---
title: Wie zeige ich ein Element an mehr als einer Stelle?
audience: editor
category: manual
order: 420
summary: Geben Sie dem Element im Tab Orte mit Orte hinzufügen einen zusätzlichen Ort; ein Ort ist der Hauptort, zusätzliche Orte lassen sich wieder entfernen, und jeder Ort zeigt denselben Inhalt, sodass eine Änderung sofort überall erscheint.
keywords: Orte, Orte hinzufügen, Hauptort, als Haupt setzen, weiterer Ort, Ort entfernen, Knoten, mehrere Orte, Mehrfachplatzierung, locations, add locations, main location, set main, secondary location, remove location, node, multiple locations, cross-publishing
type: help
---
# Wie zeige ich ein Element an mehr als einer Stelle?

Ein Element kann mehrere **Orte** im Baum haben. Jeder Ort ist ein Knoten mit eigener Adresse und eigenen
Unterelementen, aber alle zeigen denselben Inhalt. Bearbeiten Sie das Element einmal, und jeder Ort zeigt die
Änderung. Ein Ort ist der **Hauptort**: Er bestimmt die Sektion des Elements und seine Hauptadresse.

## Wer das tun kann

Redakteure, deren Rolle `content/manage_locations` für das Element hat und die seine Klasse unter dem neuen
übergeordneten Element anlegen dürfen.

## Schritte

1. Öffnen Sie das Element und klicken Sie auf den Tab **Orte**.
   Der Tab listet jeden Ort mit seinem Pfad, der Anzahl seiner Unterelemente, seiner Sichtbarkeit und welcher der
   **Hauptort** ist auf.
2. Klicken Sie auf **Orte hinzufügen**.
3. Öffnen Sie das gewünschte übergeordnete Element, wählen Sie es aus und klicken Sie auf **Auswählen**.
   Der neue Ort erscheint in der Liste.
4. Um den Hauptort zu ändern, wählen Sie ihn mit dem Optionsfeld in der Spalte **Hauptort** und klicken Sie auf **Als
   Haupt setzen**.
5. Um einen zusätzlichen Ort wegzunehmen, haken Sie ihn an, klicken Sie auf **Ausgewählte entfernen** und bestätigen
   Sie.
6. Um mehreren Elementen auf einmal einen Ort hinzuzufügen, haken Sie sie in der Liste **Unterelemente** an und wählen
   Sie **Weitere Aktionen** > **Position hinzufügen**.

Sie können Orte auch vor der ersten Veröffentlichung wählen: Schalten Sie in den **Benutzereinstellungen** in der
rechten Spalte **Orte** ein, und das Bearbeitungsformular eines neuen Elements erhält einen Kasten **Orte** mit **Orte
hinzufügen**.

## Was schiefgehen kann

- **Orte hinzufügen ist ausgegraut.** Das Element gehört zu einem obersten Knoten (etwa der Wurzel der Inhalte oder
  der Medien), oder Ihrer Rolle fehlt `content/manage_locations`.
- **Der Hauptort lässt sich nicht entfernen.** Machen Sie zuerst einen anderen Ort zum Hauptort und entfernen Sie dann
  den alten.
- **Unterelemente folgen nicht.** Ein neuer Ort zeigt nur das Element selbst. Die Unterelemente unter dem Hauptort
  werden nicht unter dem neuen hinzugefügt.
- **Der Tab entfernt nie das Element selbst.** Der Hauptort bleibt immer. Um das Element vollständig wegzunehmen,
  verwenden Sie **Entfernen**: siehe [Wie entferne ich ein Element?](wie-entferne-ich-ein-element.md)
- **Verstecken gilt pro Ort.** Wird ein Ort versteckt, bleiben die anderen sichtbar. Siehe
  [Wie verstecke ich ein Element, ohne es zu entfernen?](wie-verstecke-ich-ein-element.md)

## Verwandte Seiten

- [Wie verschiebe oder kopiere ich ein Element?](wie-verschiebe-oder-kopiere-ich-ein-element.md), wenn Sie ein eigenständiges
  Element brauchen und nicht eine zweite Stelle für dasselbe
- [Objekte, Knoten und Orte](../cms-entwickler/objekte-knoten-und-platzierungen.md)
- [Inhalte erstellen und bearbeiten](../inhalte-bearbeiten.md#viele-objekte-auf-einmal)
- [Benutzerhandbuch](README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
