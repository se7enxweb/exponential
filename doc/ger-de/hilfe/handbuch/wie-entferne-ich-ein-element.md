---
title: Wie entferne ich ein Element?
audience: editor
category: manual
order: 435
summary: Klicken Sie auf der Seite des Elements auf Entfernen, prüfen Sie, was die Bestätigung auflistet (Unterelemente, andere Elemente, die es verwenden), lassen Sie Verschiebe in den Papierkorb angehakt und klicken Sie auf OK; das Element und alles darunter kommen in den Papierkorb, aus dem sie sich wiederherstellen lassen.
keywords: entfernen, löschen, in den Papierkorb verschieben, Papierkorb, Unterelemente, umgekehrte Relationen, ausgewählte entfernen, Löschen eines Ortes bestätigen, Benutzerkonten, stattdessen deaktivieren, remove, delete, move to trash, trash, sub items, reverse relations, remove selected, confirm location removal, user accounts, disable instead
type: help
---
# Wie entferne ich ein Element?

Entfernen nimmt ein Element und alles darunter von der Website und aus dem Baum. Standardmäßig kommt es zuerst in den
Papierkorb, sodass sich ein Fehler rückgängig machen lässt.

## Wer das tun kann

Redakteure, deren Rolle `content/remove` für das Element und für alles darunter hat. Wiederherstellen aus dem
Papierkorb erfordert `content/restore`.

## Schritte

1. Öffnen Sie das Element und klicken Sie auf **Entfernen**. Oder wählen Sie **Entfernen** in seinem Baummenü. Für
   mehrere Elemente haken Sie sie in der Liste **Unterelemente** an und wählen Sie **Weitere Aktionen** >
   **Ausgewählte entfernen**.
   Die Seite **Das Löschen eines Ortes bestätigen** öffnet sich.
2. Lesen Sie die Liste: jedes Element mit seinem Typ und der Anzahl seiner **Unterelemente**, die ebenfalls entfernt
   werden.
3. Lesen Sie die Warnungen. Wenn andere Elemente verwenden, was Sie entfernen (sie betten es ein, verlinken darauf oder
   sind damit verknüpft), werden sie aufgelistet.
4. Lassen Sie **Verschiebe in den Papierkorb** angehakt, es sei denn, Sie sind sicher, dass die Elemente endgültig
   gelöscht werden müssen.
5. Klicken Sie auf **OK**.
   Kleine Entfernungen geschehen sofort; eine große läuft im Hintergrund in Portionen und zeigt ihren Fortschritt.

Um ein Element zurückzuholen, öffnen Sie **Papierkorb** unten in der linken Spalte und klicken Sie in seiner Zeile auf
**Wiederherstellen**. Siehe
[Wie leere ich den Papierkorb oder entferne einzelne Elemente daraus?](wie-leere-ich-den-papierkorb.md)

## Was schiefgehen kann

- **Achtung: Ohne Verschiebe in den Papierkorb lässt sich das Entfernen nicht rückgängig machen.** Die Elemente, ihre
  Versionen und ihre Dateien werden sofort gelöscht.
- **Rote Zeilen und eine deaktivierte Schaltfläche OK.** Sie dürfen einige der Elemente in der Liste nicht entfernen,
  oder (auf Websites, die das sperren) andere Elemente verwenden sie noch. Brechen Sie ab und entfernen Sie nur, was
  Sie dürfen, oder beheben Sie zuerst die Verweise.
- **Andere Seiten zeigen jetzt defekte Links oder fehlende Bilder.** Die Elemente, die das entfernte verwendet haben,
  zeigen noch darauf. Bearbeiten Sie sie, oder stellen Sie das Element wieder her.
- **Nur ein Ort ist verschwunden.** Das Element hat weitere Orte; an diesen Stellen bleibt es. In den Papierkorb kommt
  es erst, wenn sein letzter Ort entfernt wird.
- **Benutzerkonten kommen nicht in den Papierkorb.** Objekte mit einem Benutzerkontofeld werden endgültig gelöscht.
  Deaktivieren Sie stattdessen das Konto: siehe [Wie deaktiviere oder entsperre ich ein Benutzerkonto?](wie-deaktiviere-oder-entsperre-ich-ein-benutzerkonto.md)
- **Sie möchten es nur eine Weile von der Website nehmen.** Verstecken Sie es stattdessen: siehe [Wie verstecke ich ein Element, ohne es zu entfernen?](wie-verstecke-ich-ein-element.md)

## Verwandte Seiten

- [Veröffentlichen, Versionen und der Papierkorb](../veroeffentlichen-und-versionen.md#entfernen-und-wiederherstellen)
- [Entfernen und Verbergen: die Prüfung umgekehrter Relationen](../../funktionen/6.0/pruefung-umgekehrter-beziehungen.md)
- [Archivieren und der Papierkorb](../kunden/alltag/archivieren-und-der-papierkorb.md)
- [Benutzerhandbuch](README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
