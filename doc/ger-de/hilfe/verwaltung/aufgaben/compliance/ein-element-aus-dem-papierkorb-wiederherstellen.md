---
title: Ein Element aus dem Papierkorb wiederherstellen
audience: government, editor, admin
category: public-sector
order: 480
summary: Schritt für Schritt - eine entfernte Seite im Papierkorb von Exponential danach finden, wer sie wann in den Papierkorb verschoben hat, sie an ihrer ursprünglichen Stelle wiederherstellen (oder eine andere wählen, wenn das übergeordnete Element fehlt) und prüfen, was das Audit aufgezeichnet hat.
keywords: Papierkorb, wiederherstellen, content/trash, in den Papierkorb verschoben von, ursprüngliche Stelle, content/restore, entfernte Seite, Löschen rückgängig machen, content.object.restore, öffentlicher Sektor, trash, restore, trashed by, original placement, removed page, undo delete, public sector
type: help
---
# Ein Element aus dem Papierkorb wiederherstellen

Für Redakteure und Administratoren, wenn eine Seite versehentlich entfernt wurde. Entfernte Inhalte bleiben im
Papierkorb, bis er geleert oder bereinigt wird
([Der Papierkorb und das Wiederherstellen von Inhalten](../../../../../eng-us/help/government/records-and-archiving/trash-and-restore.md)).
Benutzerkonten liegen nie im Papierkorb.

## Inhalt

1. [Schritte](#schritte)
2. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
3. [Wenn es nicht dort ist](#wenn-es-nicht-dort-ist)
4. [Verwandte Seiten](#verwandte-seiten)

## Schritte

1. Öffnen Sie den Papierkorb in der Administration (`content/trash`).
2. Filtern Sie danach, wer es in den Papierkorb verschoben hat, nach seiner Klasse und dem Zeitraum; die Filter bleiben
   in der Adresse erhalten: `content/trash/(trashed_by)/<user id>/(from)/<YYYY-MM-DD>/(to)/<YYYY-MM-DD>`.
3. Lesen Sie **Original placement**: ob das übergeordnete Element noch **vorhanden** ist, **ebenfalls im Papierkorb**
   liegt (stellen Sie es zuerst wieder her), **verschoben** wurde oder **nicht mehr existiert**.
4. **Wiederherstellen** (`content/restore/<object id>`). Wenn das übergeordnete Element fehlt, fragt die Seite zum
   Wiederherstellen nach einer neuen Stelle.

## Prüfen, ob es funktioniert hat

- Das Element ist mit seinem Inhalt und seinen Versionen wieder im Baum.
- Das Audit zeichnet `content.object.restore` auf; das Entfernen ist als `content.node.remove.*` mit der handelnden
  Person sichtbar.
- Wenn es einen URL-Alias hatte, prüfen Sie, ob die Adresse wieder funktioniert.

## Wenn es nicht dort ist

- Der Papierkorb wurde geleert oder bereinigt (`content.trash.empty` im Audit; oder der Cronjob `trashpurge` mit
  `KeepItemsForDays`): Nur eine Wiederherstellung aus der Sicherung bringt es zurück.
- Es war ein Benutzerkonto: endgültig entfernt.
- Es wurde vielleicht verschoben oder versteckt, nicht entfernt: Durchsuchen Sie den Inhaltsbaum, einschließlich der
  versteckten Elemente.

## Verwandte Seiten

- [Die Ansicht des Papierkorbs](../../../../../eng-us/bc/6.0/trash.md)
- [Elemente im Papierkorb 90 Tage aufbewahren](elemente-im-papierkorb-90-tage-aufbewahren.md)
- [Sicherungen und Wiederherstellung](../../../../../eng-us/help/government/security/backups-and-recovery.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
