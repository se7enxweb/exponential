---
title: Dateiformate für die Langzeiterhaltung
audience: government, editor, project-manager, admin
category: public-sector
order: 220
summary: Für öffentliche Stellen, die Web-Inhalte und Dokumente langfristig aufbewahren - allgemeine Hinweise zu offenen, dokumentierten Dateiformaten, welche Formate Exponential speichert und exportiert (Formatierter Text als XML, Bilder, Uploads so wie hochgeladen, JSON, PDF-Exporte) und was Sie mit Ihrem Archiv vereinbaren sollten.
keywords: Dateiformate, digitale Langzeiterhaltung, Langzeitarchivierung, offene Formate, PDF/A, XML, JSON, CSV, Bilder, PNG, JPEG, TIFF, Uploads, Rich-Text-XML, Konvertierungen, Archiv, öffentlicher Sektor, file formats, digital preservation, long-term preservation, open formats, images, uploads, rich text XML, conversions, archive, public sector
type: help
---
# Dateiformate für die Langzeiterhaltung

Diese Seite richtet sich an Zuständige für Schriftgut, Redaktionen und Projektleitungen. Ob ein Schriftstück in zwanzig
Jahren noch lesbar ist, hängt stark von seinem Format ab. Die allgemeine Praxis der Langzeiterhaltung bevorzugt offene,
dokumentierte, weit verbreitete Formate ohne Verschlüsselung und ohne Abhängigkeit von einem bestimmten Programm.
Maßgeblich ist die Liste der Formate, die Ihr Archiv annimmt; diese Seite setzt die allgemeine Praxis in Bezug zu dem,
was Exponential speichert und exportiert.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Was Exponential speichert](#was-exponential-speichert)
2. [Was es exportiert](#was-es-exportiert)
3. [Allgemeine Hinweise](#allgemeine-hinweise)
4. [Absprachen mit Ihrem Archiv](#absprachen-mit-ihrem-archiv)
5. [Verwandte Seiten](#verwandte-seiten)

## Was Exponential speichert

| Inhalt | Gespeichert als |
|---|---|
| Formatierter Text | XML, von Templates gerendert (kein freies HTML) |
| Einfache Felder | Werte in der Datenbank |
| Bilder | die hochgeladene Datei, anhand ihres Inhalts nach einer Positivliste angenommen (JPEG, PNG, GIF, WebP, AVIF, TIFF, BMP, SVG in der Auslieferung); SVG beim Hochladen bereinigt; Varianten aus dem Original erzeugt |
| Andere Uploads | die Datei so, wie sie hochgeladen wurde; Exponential konvertiert oder prüft sie nicht |
| Audit-Einträge | JSON-Zeilen, komprimiert archiviert (gzip, bzip2, xz, zstd oder zip) mit signierten Manifesten |

## Was es exportiert

| Export | Format |
|---|---|
| REST-API | JSON |
| Pakete | `.ezpkg` (mit gzip komprimiertes tar mit Definitionen in XML oder JSON) |
| PDF-Export | PDF; keine dokumentierte Unterstützung für Tags oder PDF/A |
| Formulareinsendungen, Kontoliste, Einwilligungsprotokoll | CSV, UTF-8 mit Byte Order Mark |
| Audit-Export | CSV, JSON-Zeilen, JSON, signiertes Bündel |

## Allgemeine Hinweise

- Textliches Schriftgut: reiner Text, XML oder JSON mit einer Dokumentation der Struktur; PDF/A (eine ISO-Familie von
  PDF-Profilen für die Archivierung), wo Ihr Archiv PDF verlangt.
- Bilder: verlustfreie Formate (PNG, TIFF) für Originale; JPEG ist für Fotos üblich.
- Tabellen: CSV mit einer Beschreibung der Spalten.
- Vermeiden Sie Formate, die ein bestimmtes Programm, Makros, Verschlüsselung oder externe Verknüpfungen brauchen.
- Bewahren Sie zu jeder Datei Prüfsummen auf.

Der PDF-Export von Exponential erzeugt, soweit das Repository es dokumentiert, kein PDF/A; konvertieren und validieren
Sie mit einem Werkzeug, das Ihr Archiv akzeptiert, wenn Sie PDF/A brauchen.

## Absprachen mit Ihrem Archiv

1. Lassen Sie sich die Liste der angenommenen Formate und die Anforderungen an Metadaten des Archivs geben.
2. Sagen Sie der Redaktion, welche Formate sie für Dokumente hochladen soll, die zu Schriftgut werden (zum Beispiel ein
   vom Office-Programm erzeugtes PDF/A statt einer Textverarbeitungsdatei).
3. Legen Sie fest, wer nicht angenommene Formate konvertiert, und wann (beim Hochladen, bei der Abgabe).
4. Testen Sie früh eine Abgabe mit einigen Schriftstücken.

## Verwandte Seiten

- [Dokumente und PDFs (Barrierefreiheit)](../../../../eng-us/help/government/accessibility/documents-and-pdfs.md)
- [Inhalte exportieren](inhalte-exportieren.md)
- [SVG-Bilder](../../../../eng-us/bc/6.0/svg-images.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
