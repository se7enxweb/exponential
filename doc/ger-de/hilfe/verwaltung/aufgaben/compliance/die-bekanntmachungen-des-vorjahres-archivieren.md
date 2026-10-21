---
title: Die Bekanntmachungen des Vorjahres archivieren
audience: government, editor, admin
category: public-sector
order: 460
summary: Schritt für Schritt - zu Beginn eines Jahres die Bekanntmachungen des Vorjahres in einen Jahresordner unter einem Archiv verschieben, sodass aktuelle Listen kurz bleiben, während die alten Bekanntmachungen ihren Inhalt, ihre Versionen und ihre weiterleitenden Adressen behalten, und festhalten, was Sie getan haben.
keywords: Archiv, archivieren, Bekanntmachungen, Jahresordner, verschieben, Inhaltsbaum, URL-Aliase leiten weiter, content.node.move, Sektionen, Objektzustände, Listen, Schriftgut, öffentlicher Sektor, archive, notices, year folder, move, content tree, URL aliases redirect, sections, object states, lists, records, public sector
type: help
---
# Die Bekanntmachungen des Vorjahres archivieren

Für Redakteure und Administratoren. Eine jährliche Routine für Bekanntmachungen, Ankündigungen oder Nachrichten: Sie
verlassen die aktuelle Liste, bleiben aber verfügbar und behalten ihren Verlauf
([Alte Bekanntmachungen archivieren](../../schriftgut-und-archivierung/alte-bekanntmachungen-archivieren.md)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Eine Vereinbarung mit den Verantwortlichen für das Schriftgut darüber, was archiviert wird (nach Datum, nach Klasse)
  und ob archivierte Bekanntmachungen öffentlich bleiben.
- Ein Ordner „Archiv“ unter den Bekanntmachungen (einmal anlegen); die Jahresordner kommen hinein.
- Rechte, in diesem Teil des Baums Ordner anzulegen und Inhalte zu verschieben.

## Schritte

1. Legen Sie unter dem Ordner der Bekanntmachungen den Ordner „Archiv“ > „<Vorjahr>“ an.
2. Sortieren Sie in der Liste der Unterelemente des Ordners der Bekanntmachungen nach Veröffentlichungsdatum; wählen Sie
   die Bekanntmachungen des Vorjahres aus (eine Seite nach der anderen).
3. **Verschieben** Sie sie in den Jahresordner (über das Kontextmenü oder die Schaltfläche Verschieben); bestätigen Sie
   das Ziel.
4. Wenn das Archiv nicht öffentlich sein darf, legen Sie es in eine Archivsektion (Inhalt zuweisen am Archivordner),
   deren Leserichtlinie Anonymous ausschließt, oder setzen Sie einen Archivzustand („Objektzustände für Schriftgut“).
5. Prüfen Sie, ob das Template der aktuellen Liste nur die direkten Unterelemente des Ordners der Bekanntmachungen
   abruft (nicht das Archiv), damit das Archiv dort nicht wieder auftaucht; fragen Sie im Zweifel den Entwickler.
6. Schreiben Sie eine Notiz: Datum, Zahl der Elemente, Ordner.

## Prüfen, ob es funktioniert hat

- Die aktuelle Liste zeigt nur die Bekanntmachungen dieses Jahres.
- Die frühere Adresse einer alten Bekanntmachung leitet an ihren neuen Platz weiter (verschobene Knoten behalten einen
  Verlaufs-Alias).
- Das Audit zeigt die Verschiebungen: `/audit/console/(name)/content.node.move`.

## Verwandte Seiten

- [Alte Bekanntmachungen archivieren](../../schriftgut-und-archivierung/alte-bekanntmachungen-archivieren.md)
- [Eine archivierte Seite wiederherstellen](eine-archivierte-seite-wiederherstellen.md)
- [URLs erhalten](../../schriftgut-und-archivierung/urls-erhalten.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
