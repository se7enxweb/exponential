---
title: Der Papierkorb und das Wiederherstellen von Inhalten
audience: government, editor, admin
category: public-sector
order: 100
summary: Für Redaktionen und Administratoren - wie der Papierkorb von Exponential funktioniert (wer was in den Papierkorb gelegt hat, wo es war, Wiederherstellen an seinen Platz), dass Benutzer nie in den Papierkorb kommen, wie lange Einträge bleiben (KeepItemsForDays und der Cronjob trashpurge) und warum der Papierkorb kein Archiv ist.
keywords: Papierkorb, wiederherstellen, content/trash, entfernt von, ursprünglicher Ort, KeepItemsForDays, trashpurge, Papierkorb leeren, Benutzer nie im Papierkorb, content.object.restore, content.trash.empty, Schriftgut, öffentlicher Sektor, trash, restore, trashed by, original placement, empty trash, users never in trash, records, public sector
type: help
---
# Der Papierkorb und das Wiederherstellen von Inhalten

Diese Seite richtet sich an Redaktionen und Administratoren. Das Entfernen von Inhalten verschiebt sie in Exponential
normalerweise in den Papierkorb, aus dem sie wiederhergestellt werden können, bis er geleert wird
([Die Ansicht des Papierkorbs](../../../../eng-us/bc/6.0/trash.md)).

## Inhalt

1. [Was der Papierkorb zeigt](#was-der-papierkorb-zeigt)
2. [Einen Eintrag wiederherstellen](#einen-eintrag-wiederherstellen)
3. [Wie lange Einträge bleiben](#wie-lange-einträge-bleiben)
4. [Was nie in den Papierkorb kommt](#was-nie-in-den-papierkorb-kommt)
5. [Der Papierkorb ist kein Archiv](#der-papierkorb-ist-kein-archiv)
6. [Verwandte Seiten](#verwandte-seiten)

## Was der Papierkorb zeigt

`content/trash` in der Administration: für jeden Eintrag seinen Namen, die Anzahl der Knoten, die darunter lagen, seinen
Typ und seine Sektion, den **ursprünglichen Ort** (den vollständigen Pfad und ob der übergeordnete Knoten noch
existiert, ebenfalls im Papierkorb liegt, verschoben wurde oder nicht mehr existiert) sowie das **Datum des Entfernens
mit der entfernenden Person** und von wo aus (`web admin`, `cli <script>`). Filter nach Benutzer, Typ und Zeitraum
werden in der Adresse gehalten:

```
content/trash/(trashed_by)/<user id>/(from)/<YYYY-MM-DD>/(to)/<YYYY-MM-DD>
```

## Einen Eintrag wiederherstellen

1. Suchen Sie den Eintrag (bei Bedarf mit Filter).
2. **Wiederherstellen** (`content/restore/<object id>`).
3. Wenn der ursprüngliche übergeordnete Knoten noch existiert, kehrt der Eintrag dorthin zurück; liegt der übergeordnete
   Knoten ebenfalls im Papierkorb, stellen Sie zuerst diesen wieder her; wurde er verschoben oder ist er weg, fragt die
   Seite zum Wiederherstellen nach einem Ort.

Wiederherstellungen werden im Audit als `content.object.restore` aufgezeichnet, das Entfernen in den Papierkorb als
`content.node.remove.*`.

## Wie lange Einträge bleiben

- Bis jemand den Papierkorb leert (**Papierkorb leeren** leert den gesamten Papierkorb, unabhängig vom Filter) oder der
  Cronjob-Teil `trashpurge` läuft.
- Der Teil `trashpurge` gehört standardmäßig zu keinem Cronjob-Teil. Mit
  `content.ini [TrashSettings] KeepItemsForDays=<days>` löscht er nur Einträge, die vor mindestens so vielen Tagen in den
  Papierkorb gelegt wurden; leer oder 0 löscht alles.
- Auf der Kommandozeile: `php bin/php/trashpurge.php --trashed-days=<days>`.

```ini
# settings/override/content.ini.append.php
[TrashSettings]
KeepItemsForDays=90
```

Das Leeren wird als `content.trash.empty` aufgezeichnet; endgültig gelöschte Objekte sind mitsamt ihren Versionen weg.

## Was nie in den Papierkorb kommt

Benutzerkonten: Ein Benutzerobjekt wird endgültig entfernt (`eZContentObjectTreeNode::isNodeTrashAllowed()`).
Verabschieden Sie ausscheidende Personen stattdessen mit Deaktivieren oder mit Deaktivieren und Verschieben
([Benutzerkonten prüfen](../../../../eng-us/features/6.0/user-hygiene.md)).

## Der Papierkorb ist kein Archiv

Einträge im Papierkorb sind auf dem Weg hinaus, für Besucher unsichtbar und können nach einem Zeitplan gelöscht werden,
den jemand anderes festgelegt hat. Inhalte, die aufbewahrt werden müssen, archivieren Sie (siehe „Alte
Bekanntmachungen archivieren“). Ein Aufbewahrungsplan sollte festlegen, wer den Papierkorb wann leeren darf.

## Verwandte Seiten

- [Die Ansicht des Papierkorbs](../../../../eng-us/bc/6.0/trash.md)
- [Alte Bekanntmachungen archivieren](alte-bekanntmachungen-archivieren.md)
- [Eine Seite und ihre Geschichte wiederherstellen](versionen-und-inhaltsgeschichte.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
