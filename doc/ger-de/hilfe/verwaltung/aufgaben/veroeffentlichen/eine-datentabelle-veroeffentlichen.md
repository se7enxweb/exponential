---
title: Eine Datentabelle veröffentlichen
audience: government, editor
category: public-sector
order: 390
summary: Für Redakteure - Zahlen (eine Haushaltsübersicht, Wartezeiten, Ergebnisse) als barrierefreie Tabelle im Online-Editor mit Beschriftung und Kopfzellen veröffentlichen, dazu die Daten als CSV-Datei zur Weiterverwendung, mit Quelle und Datum.
keywords: Datentabelle, Tabelle, Haushalt, Zahlen, barrierefreie Tabelle, Kopfzellen, Beschriftung, Online-Editor, CSV, offene Daten, Quelle, Datum, data table, table, budget, figures, accessible table, header cells, caption, online editor, open data, source, date
type: help
---
# Eine Datentabelle veröffentlichen

Diese Seite ist für Redakteure, die Zahlen veröffentlichen. Eine Tabelle auf der Seite zeigt sie allen; dieselben
Daten als CSV-Datei lassen andere sie weiterverwenden. Tabellen im Rich Text entstehen mit den Tabellenwerkzeugen des
Online-Editors ([Inhaltsmodell und Bearbeitung, 4](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md#4-der-online-editor-und-tinymce-8)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Das Ergebnis prüfen](#das-ergebnis-prüfen)
4. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Die Zahlen sind endgültig und freigegeben, mit ihrer Quelle und dem Datum, auf das sie sich beziehen.
- Die Tabelle ist klein genug, um sie auf einem Bildschirm zu lesen (einige Spalten, einige Dutzend Zeilen). Große
  Datenmengen gehören in eine Datei.
- Ein CSV-Export derselben Daten mit einer Kopfzeile.

## Schritte

1. **Bearbeiten** Sie die Seite; setzen Sie im Rich-Text-Feld den Cursor und fügen Sie mit der Tabellen-Schaltfläche
   des Editors eine Tabelle ein.
2. Geben Sie der Tabelle eine **Beschriftung** (was sie zeigt, die Einheit und das Datum), wenn Ihr Editor sie
   anbietet; sonst eine Überschrift direkt darüber.
3. Machen Sie die erste Zeile zu Kopfzellen (die Tabelleneigenschaften haben eine Option für Kopfzeilen); für
   Zeilenbeschriftungen machen Sie auch die erste Spalte zu Kopfzellen.
4. Tippen oder fügen Sie die Zahlen ein; richten Sie Zahlen einheitlich aus; schreiben Sie die Einheiten in die
   Kopfzellen („Betrag (Tausend)“) statt in jede Zelle.
5. Unter der Tabelle: „Quelle: <Quelle>, <Datum>.“ und ein kurzer Satz mit der Hauptaussage.
6. Hängen Sie die CSV-Datei an: **Neu hochladen** in einem Relationsfeld, oder laden Sie sie als Datei hoch und
   verlinken Sie sie („Die Daten herunterladen (CSV, 12 KB)“).
7. **Vorschau**, dann **Zur Veröffentlichung schicken**.

## Das Ergebnis prüfen

- [ ] Mit einem Screenreader werden für jede Zelle die Kopfzellen angesagt.
- [ ] Bei 200 % Zoom und in der Breite eines Telefons lässt sich die Tabelle lesen (eine breite Tabelle kann in ihrem
  Kasten scrollen).
- [ ] Die CSV-Datei öffnet sich in einer Tabellenkalkulation mit korrekten Zeichen.

## Fallstricke

| Fallstrick | So vermeiden Sie ihn |
|---|---|
| Eine Tabelle als Bild eingefügt | nur Tabellen aus Text |
| Verbundene Zellen und verschachtelte Kopfzeilen | halten Sie Tabellen einfach; teilen Sie sie in zwei Tabellen |
| Farbe als einziges Signal (Rot für Überschreitung) | ergänzen Sie Wörter oder Symbole |
| Daten ohne Lizenzhinweis zur Weiterverwendung | nennen Sie die Lizenz, unter der Sie die Daten veröffentlichen ([Open Definition](https://opendefinition.org/od/2.1/en/)) |

## Verwandte Seiten

- [Aufgaben zum Veröffentlichen](README.md)
- [Eine Zusammenfassung in einfacher Sprache schreiben](eine-zusammenfassung-in-einfacher-sprache-schreiben.md)
- [Barrierefreiheit für Site-Ersteller](../../../ersteller/website-ersteller/barrierefreiheit-fuer-website-ersteller.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
