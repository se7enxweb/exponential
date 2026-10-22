---
title: Wie verstecke ich ein Element, ohne es zu entfernen?
audience: editor
category: manual
order: 425
summary: Wählen Sie im Baummenü des Elements Fortgeschritten > Verstecken / Zeigen oder im Tab Orte Verstecken; der Ort und alles darunter verschwinden von der öffentlichen Website, bleiben aber in der Administration, und Aufdecken bringt sie zurück.
keywords: verstecken, verbergen, einblenden, aufdecken, Sichtbarkeit, versteckt, versteckt durch übergeordneten Eintrag, unsichtbar, Tab Orte, Sichtbarkeit nach Veröffentlichung, hide, unhide, reveal, visibility, hidden, hidden by superior, invisible, Locations tab, visibility after publishing, ShowHiddenNodes
context: content/hide
type: help
---
# Wie verstecke ich ein Element, ohne es zu entfernen?

Verstecken nimmt einen Ort für alle von der öffentlichen Website, ohne seinen Inhalt, seine Versionen oder seine Rechte
anzutasten. Alles darunter wird ebenfalls versteckt. Die Administration zeigt versteckte Elemente weiterhin an, als
solche gekennzeichnet, damit Sie an ihnen weiterarbeiten und sie später wieder aufdecken können. Verwenden Sie es für
Inhalte, die noch nicht fertig sind oder eine Weile nicht gebraucht werden.

## Wer das tun kann

Redakteure, deren Rolle `content/hide` für das Element hat.

## Schritte

1. Öffnen Sie im Baum das Menü des Elements und wählen Sie **Fortgeschritten** > **Verstecken / Zeigen**. Oder öffnen
   Sie das Element, klicken Sie auf den Tab **Orte** und klicken Sie in der Zeile des Ortes auf **Verstecken**.
2. Wenn eine Bestätigungsseite erscheint, lesen Sie sie und klicken Sie auf **OK**.
   Sie erscheint, wenn andere Elemente dieses verwenden oder wenn das Element weitere Orte hat; diese anderen Orte
   bleiben sichtbar.
3. Um das Element wieder anzuzeigen, verwenden Sie denselben Menüeintrag oder klicken Sie im Tab **Orte** auf
   **Aufdecken**.
4. Für mehrere Elemente haken Sie sie in der Liste **Unterelemente** an und wählen Sie **Weitere Aktionen** >
   **Auswahl verstecken** oder **Auswahl einblenden**.

Die Spalte **Sichtbarkeit** des Tabs **Orte** und das Abzeichen auf der Seite des Elements zeigen einen von drei
Zuständen:

| Zustand | Bedeutung |
|---|---|
| **Sichtbar** | auf der Website angezeigt (sofern der Leser es lesen darf) |
| **Versteckt** | selbst versteckt |
| **Versteckt durch übergeordneten Eintrag** | nicht selbst versteckt, aber ein Ort darüber ist es |

## Ein neues Element von Anfang an verstecken

1. Schalten Sie in den **Benutzereinstellungen** in der rechten Spalte **Orte** ein.
2. Erstellen Sie das Element. Das Bearbeitungsformular hat jetzt einen Kasten **Orte**.
3. Setzen Sie **Sichtbarkeit nach Veröffentlichung** auf **Versteckt** und veröffentlichen Sie.

Das funktioniert nur für die erste Version: Sobald das Element existiert, verwenden Sie die Schritte oben.

## Was schiefgehen kann

- **Ein Element bleibt nach Aufdecken versteckt.** Ein Ort darüber ist versteckt (**Versteckt durch übergeordneten
  Eintrag**). Decken Sie zuerst diesen auf.
- **Das Aufdecken eines übergeordneten Elements zeigt ein Unterelement nicht.** Ein Unterelement, das selbst versteckt
  wurde, bleibt versteckt, wenn sein übergeordnetes Element aufgedeckt wird. Decken Sie es gesondert auf.
- **Das Element ist immer noch auf der Website.** Verstecken wirkt auf einen Ort. Hat das Element weitere Orte,
  bleiben diese sichtbar. Verstecken Sie jeden einzelnen, oder prüfen Sie den Tab **Orte**.
- **Links auf das Element funktionieren auf der Website nicht mehr.** Andere Elemente, die darauf verlinken oder es
  einbetten, zeigen jetzt auf etwas, das Besucher nicht sehen können. Die Bestätigungsseite listet sie auf.
- **Verstecken ist keine Berechtigung.** Es versteckt den Ort für alle auf den öffentlichen Siteaccesses. Um Inhalte
  nur bestimmten Lesern zu zeigen, verwenden Sie stattdessen Sektionen und Rollen.

## Verwandte Seiten

- [Veröffentlichen, Versionen und der Papierkorb](../veroeffentlichen-und-versionen.md#ein-objekt-verstecken)
- [Entfernen und Verbergen: die Prüfung umgekehrter Relationen](../../funktionen/6.0/pruefung-umgekehrter-beziehungen.md)
- [Wie zeige ich ein Element an mehr als einer Stelle?](wie-zeige-ich-ein-element-an-mehr-als-einer-stelle.md)
- [Sektionen](../../anleitungen/sektionen.md)
- [Benutzerhandbuch](README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
