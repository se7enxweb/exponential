---
title: PDF und Dokumentformate für Offenlegungen
audience: government, editor, admin
category: public-sector
order: 190
summary: Welche Formate Sie für veröffentlichte Auskünfte, Berichte und Register verwenden, wie der PDF-Export von Exponential einen Teil des Inhaltsbaums in ein PDF verwandelt und welche Punkte zu Barrierefreiheit und Maschinenlesbarkeit Sie prüfen sollten.
keywords: PDF, PDF-Export, Dokumentformate, barrierefreies PDF, CSV, ODF, Seitentext, Offenlegungen, Berichte, Setup PDF-Export, Einmal erzeugt, Bei Bedarf erzeugt, document formats, accessible PDF, page text, disclosures, reports, generated once, on the fly
type: help
---
# PDF und Dokumentformate für Offenlegungen

Diese Seite richtet sich an Redaktionen und Administratoren, die das Format veröffentlichter Dokumente wählen.
Exponential veröffentlicht jede Datei, die Sie hochladen, und kann mit **Setup > PDF-Export** aus einem Teil des
Inhaltsbaums ein PDF erzeugen ([PDF-Exporte](../../../anleitungen/pdf-exporte.md)).

## Inhalt

1. [Ein Format wählen](#ein-format-wählen)
2. [Zuerst Seitentext](#zuerst-seitentext)
3. [Der PDF-Export von Exponential](#der-pdf-export-von-exponential)
4. [Barrierefreiheit von Dokumenten](#barrierefreiheit-von-dokumenten)
5. [Checkliste](#checkliste)

## Ein Format wählen

| Inhalt | Bestes Format | Zusätzlich anbieten |
|---|---|---|
| Eine kurze Antwort, ein Hinweis | Seitentext | |
| Ein Bericht, den Menschen lesen | Seitentext oder ein barrierefreies PDF | |
| Ein unterzeichnetes oder amtliches Dokument | PDF | Zusammenfassung als Seitentext |
| Zahlentabellen | CSV | PDF oder Seite zum Lesen |
| Register | Inhaltsobjekte, als CSV/JSON exportiert | |
| Bearbeitbare Vorlagen | ein offenes Dokumentformat | |

## Zuerst Seitentext

Im Editor geschriebener Text ist durchsuchbar, übersetzbar, mit den Templates der Website barrierefrei, Feld für Feld
versioniert und hat keine versteckten Ebenen. Bevorzugen Sie ihn für alles Kurze; hängen Sie Dateien nur an, wo es auf
das Dokument selbst ankommt.

## Der PDF-Export von Exponential

**Setup > PDF-Export** (`/pdf/list`) erzeugt ein PDF eines Quellknotens: sein Name als erste Überschrift, dann seine
Attribute; ein Export als **Baum** ergänzt die Knoten darunter aus den Klassen, die Sie ankreuzen.

| Modus | Verhalten | Verwendung |
|---|---|---|
| Einmal erzeugt | geschrieben, wenn der Export gespeichert wird und bei **Neu erzeugen**; abgelegt in `var/<site>/storage/pdf/` und unter einer öffentlichen Adresse ausgeliefert | eine Jahreszusammenstellung, eingefroren zu einem Datum |
| Bei Bedarf erzeugt | bei jedem Download neu erzeugt | eine druckbare Fassung eines Bereichs, der sich ändert |

Das gespeicherte PDF zeigt den Inhalt so, wie er beim Erzeugen war; erzeugen Sie es nach Änderungen neu und vermerken
Sie das Datum darin. Prüfen Sie das Ergebnis auf Barrierefreiheit, bevor Sie sich darauf verlassen: Diese
Dokumentation sagt nicht, dass die erzeugten PDFs für assistive Technologien getaggt sind.

## Barrierefreiheit von Dokumenten

Viele öffentliche Stellen müssen Dokumente barrierefrei bereitstellen; in der EU erfasst die
[Richtlinie (EU) 2016/2102 über den barrierefreien Zugang zu Websites](https://eur-lex.europa.eu/eli/dir/2016/2102/oj)
die Websites öffentlicher Stellen und die Dokumente darauf, mit den in der Richtlinie festgelegten Ausnahmen.
Praktische Prüfungen: echter Text (keine Scans), Überschriften, Alternativtext für Bilder, eine Dokumentsprache,
Tabellen mit Kopfzeilen. Der eigene Stand der Barrierefreiheit von Exponential ist in den
[Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md) beschrieben; er umfasst nicht die Dateien, die
Sie hochladen.

## Checkliste

- [ ] Kurze Inhalte als Seitentext.
- [ ] Jedes PDF hat echten Text, Überschriften und eine eingestellte Sprache.
- [ ] Tabellen auch als CSV.
- [ ] Erzeugte PDFs nach Inhaltsänderungen neu erzeugt und datiert.
- [ ] Download-Links zeigen Format und Größe.

## Verwandte Seiten

- [PDF-Exporte](../../../anleitungen/pdf-exporte.md)
- [Dateien und Downloads](../../../../eng-us/help/creators/website-creators/files-and-downloads.md)
- [Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
