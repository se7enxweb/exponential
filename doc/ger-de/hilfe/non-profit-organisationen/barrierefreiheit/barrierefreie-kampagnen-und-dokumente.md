---
title: Barrierefreie Kampagnen und Dokumente
audience: non-profit, editor, sales-marketing, government
category: non-profits
order: 30
summary: Wie eine Non-Profit-Organisation ihre Spendenaufrufe, Kampagnen-Landingpages, Jahresveröffentlichungen, PDFs, Videos, Bilder mit Text und Newsletter mit Exponential barrierefrei macht - was Sie an einer Kampagnenseite mit Layouts prüfen, wie Sie Dokumente mit einer HTML-Alternative veröffentlichen, Untertitel, Kontrast in Kampagnenfarben und ein Kampagnentest vor dem Start.
keywords: barrierefreie Kampagnen, Landingpages, Layouts, PDFs, Dokumente, Jahresveröffentlichungen, Untertitel, Video, Bilder von Text, Kontrast, barrierefreier Newsletter, Kampagnentest, Spendenaufruf, accessible campaigns, landing pages, captions, images of text, newsletter accessibility, fundraising appeal
type: help
---
# Barrierefreie Kampagnen und Dokumente

Diese Seite richtet sich an Mitarbeitende der Kommunikation und des Fundraisings. Bei Kampagnen und Dokumenten gerät
die Barrierefreiheit am häufigsten aus dem Blick: ein auffälliges Bild, in das die Botschaft eingebrannt ist, ein
PDF-Jahresbericht direkt aus einem Gestaltungsprogramm, ein Video ohne Untertitel. Jedes davon schließt Menschen aus,
die Sie erreichen wollen.

## Inhalt

1. [Kampagnenseiten](#kampagnenseiten)
2. [Bilder mit Text](#bilder-mit-text)
3. [Dokumente und PDFs](#dokumente-und-pdfs)
4. [Video und Audio](#video-und-audio)
5. [Newsletter](#newsletter)
6. [Vorschauen in sozialen Medien](#vorschauen-in-sozialen-medien)
7. [Kampagnentest vor dem Start](#kampagnentest-vor-dem-start)

## Kampagnenseiten

Kampagnenseiten werden meist mit Exponential Layouts gebaut ([Kampagnen und Spendenaufrufe](../kommunikation/kampagnen-und-spendenaufrufe.md)).
Die Blöcke des Layouts werden von den Templates Ihres Designs gerendert, daher gelten die Regeln für Templates
([Barrierefreiheit für Website-Ersteller](../../ersteller/website-ersteller/barrierefreiheit-fuer-website-ersteller.md)). Prüfen
Sie insbesondere:

| Prüfung | Warum |
|---|---|
| Ein `h1`, Überschriften blockübergreifend in der richtigen Reihenfolge | Einzeln hinzugefügte Blöcke können die Reihenfolge durcheinanderbringen |
| Schaltflächentexte sagen, was geschieht („Für den Winteraufruf spenden“) | Mehrere Schaltflächen „Weiterlesen“ sind ohne Kontext bedeutungslos |
| Kampagnenfarben erfüllen die Kontrastanforderungen | Markenfarben auf Fotos scheitern oft daran ([Farbe und Kontrast](../../verwaltung/barrierefreiheit/farbe-und-kontrast.md)) |
| Keine automatisch laufenden Karussells oder ein Bedienelement zum Anhalten | Bewegung lenkt ab und kann schaden |
| Funktioniert mit der Tastatur | Jeder Block, jede Schaltfläche |

## Bilder mit Text

- Bringen Sie die Botschaft in echtem Text unter, nicht im Bild.
- Muss ein Bild Text enthalten (ein Plakat), wiederholen Sie den Text auf der Seite oder im Alternativtext
  ([Alternativtext für Bilder](../../verwaltung/barrierefreiheit/alternativtext-fuer-bilder.md)).
- Infografiken brauchen eine Textfassung: eine kurze Zusammenfassung und die wichtigsten Zahlen in einer Liste oder
  Tabelle.

## Dokumente und PDFs

Jahresveröffentlichungen, Jahresabschlüsse und Berichte sind meist PDFs
([Jahresveröffentlichungen als Downloads](../transparenz/jahresveroeffentlichungen-als-downloads.md)).

| Tun | Lesen |
|---|---|
| Getaggte, barrierefreie PDFs aus dem Quellprogramm erzeugen | [Dokumente und PDFs](../../verwaltung/barrierefreiheit/dokumente-und-pdfs.md) |
| Die wichtigsten Inhalte zusätzlich auf eine HTML-Seite stellen | [Gliederung der Seite für Jahresveröffentlichungen](../vorlagen/gliederung-der-seite-fuer-jahresveroeffentlichungen.md) |
| Besuchern vor dem Klick Format und Größe nennen | [Dateien und Downloads](../../ersteller/website-ersteller/dateien-und-downloads.md) |
| Gescannte Bilder von Text vermeiden | Für Screenreader und Suche nicht lesbar |

Der **PDF-Export** von Exponential (Setup > PDF-Export) kann Inhalte in PDF-Dateien umwandeln
([PDF-Export](../../../funktionen/6.0/pdf-export.md)); prüfen Sie das Ergebnis wie jedes andere PDF.

## Video und Audio

- Untertitel für jedes Video mit Sprache; ein Transkript für Audio
  ([Untertitel und Medien](../../verwaltung/barrierefreiheit/untertitel-und-medien.md)).
- Ein eingebetteter Player einer Videoplattform lädt die Skripte dieser Plattform; er braucht einen Eintrag in der
  Content Security Policy und eine Erwähnung in der Datenschutzerklärung ([Karten und Einbettungen](../../verwaltung/datenschutz/karten-und-einbettungen.md)).
- Podcasts können als RSS-Feeds veröffentlicht werden ([RSS- und Podcast-Feeds](../../kunden/wachstum/rss-und-podcast-feeds.md));
  veröffentlichen Sie das Transkript mit jeder Folge.

## Newsletter

Newsletter-Ausgaben in `cjw_newsletter` werden als Inhalte geschrieben ([Unterstützer-Newsletter](../kommunikation/unterstuetzer-newsletter-mit-cjw-newsletter.md)).
Es gelten dieselben Regeln: echter Text, Alternativtext für Bilder, klare Linktexte, ausreichender Kontrast, ein Link
„Im Browser ansehen“ zum Archiv.

## Vorschauen in sozialen Medien

Titel, Beschreibung und Bild zum Teilen stammen aus den Metadaten ([Metadaten und Teilen in sozialen Medien](../../ersteller/website-ersteller/metadaten-und-teilen-in-sozialen-medien.md)).
Halten Sie die Botschaft im Titel und in der Beschreibung fest, nicht nur im Vorschaubild.

## Kampagnentest vor dem Start

Zehn Minuten, bevor eine Kampagne live geht:

- [ ] Tastatur: jede Schaltfläche erreichen und bedienen, auch die zum Spenden.
- [ ] Zoom auf 200 %: nichts abgeschnitten.
- [ ] Automatisches Prüfwerkzeug auf der Seite: keine Fehler.
- [ ] Jedes Bild: Alternativtext oder dekorativ.
- [ ] Jedes Video: Untertitel.
- [ ] Jedes Dokument: barrierefrei oder mit HTML-Alternative, Format und Größe angegeben.
- [ ] Farben: Kontrast geprüft.
- [ ] Auf einem Smartphone: lesbar und bedienbar.

Die allgemeine Kampagnen-Checkliste ist [Checkliste für Kampagnen-Landingpages](../../kunden/wachstum/checkliste-kampagnen-landingpages.md).

## Verwandte Seiten

- [Barrierefreiheit für Non-Profit-Organisationen](README.md)
- [Kampagnen und Landingpages (Unternehmen)](../../unternehmen/gemeinnuetzige/kampagnen-und-landingpages.md)
- [Barrierefreie Inhalte für Redakteure](../../verwaltung/barrierefreiheit/barrierefreie-inhalte-fuer-die-redaktion.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
