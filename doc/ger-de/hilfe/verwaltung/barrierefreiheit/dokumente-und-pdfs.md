---
title: Dokumente und PDFs
audience: government, editor, project-manager
category: public-sector
order: 200
summary: Für Redaktion und Projektverantwortliche öffentlicher Stellen - wann Sie statt eines Dokuments eine Webseite veröffentlichen, wie Sie PDF- und Office-Dateien vor dem Hochladen prüfen, wie Exponential Dateien ausliefert und was sein eingebauter PDF-Export verspricht und was nicht.
keywords: Dokumente, PDF, Office-Dateien, Word, barrierefreies PDF, getaggtes PDF, Datei-Upload, PDF-Export, Redaktion, Formulare als Dokumente, gescannte Dokumente, documents, office files, accessible PDF, tagged PDF, file upload, content/download, PDF export, EN 301 549 clause 10, editors, forms as documents, scanned documents
type: help
---
# Dokumente und PDFs

Diese Seite richtet sich an die Redaktion, die Dokumente hochlädt, und an Projektverantwortliche, die die Regeln
festlegen. Dokumente sind Inhalte: Ein nicht barrierefreies PDF, das von einer barrierefreien Seite verlinkt ist, bleibt
eine Barriere. EN 301 549 hat einen eigenen Abschnitt für Nicht-Web-Dokumente (Abschnitt 10), und viele öffentliche
Stellen veröffentlichen eine große Zahl von Berichten, Formularen und Protokollen als Dateien.

## Inhalt

1. [Zuerst die Seite, dann das Dokument](#zuerst-die-seite-dann-das-dokument)
2. [Bevor Sie ein Dokument hochladen](#bevor-sie-ein-dokument-hochladen)
3. [Wie Exponential Dateien ausliefert](#wie-exponential-dateien-ausliefert)
4. [Der eingebaute PDF-Export](#der-eingebaute-pdf-export)
5. [Gescannte Dokumente und alte Dateien](#gescannte-dokumente-und-alte-dateien)
6. [Verwandte Seiten](#verwandte-seiten)

## Zuerst die Seite, dann das Dokument

Veröffentlichen Sie Informationen, wann immer möglich, als Webseite: Sie passt sich auf Telefonen an, folgt den
Zoom- und Farbeinstellungen der Besucher, wird von der Suche gefunden und lässt sich in den Sprachversionen von
Exponential übersetzen. Bieten Sie zusätzlich ein Dokument nur an, wenn Menschen eines brauchen (zum Drucken, zum
Unterschreiben, als förmliche Fassung).

## Bevor Sie ein Dokument hochladen

Prüfen Sie im Programm, mit dem das Dokument erstellt wurde, vor dem Export als PDF:

- [ ] Echte Überschriften (die Überschriftenformatvorlagen des Programms), in der richtigen Reihenfolge.
- [ ] Alternativtext für Bilder und Diagramme.
- [ ] Tabellen mit gekennzeichneter Kopfzeile.
- [ ] Dokumenttitel und Sprache in den Dokumenteigenschaften gesetzt.
- [ ] Aussagekräftige Linktexte.
- [ ] Ausreichender Kontrast; Farbe nicht das einzige Signal.
- [ ] Als **getaggtes** PDF exportiert (eine Option zur Barrierefreiheit im Exportdialog der meisten Office-Programme).
- [ ] Mit einem Prüfwerkzeug für die Barrierefreiheit von PDFs geprüft.

Nennen Sie im Link auf der Seite Format und Größe: „Ratsprotokoll, 12. Mai (PDF, 300 KB)“.

## Wie Exponential Dateien ausliefert

- Eine Datei wird in ein Dateifeld hochgeladen (oder als Dateiobjekt in die Medienbibliothek) und über
  `content/download` ausgeliefert, das prüft, ob der Leser sie lesen darf; Uploads außer Bildern werden nie direkt aus
  dem Speicherverzeichnis ausgeliefert
  ([13.2 Was der Webserver nie herausgeben darf](../../../installation/13-sicherheitshaertung.md#132-was-der-webserver-niemals-ausliefern-darf)).
- Exponential verändert die Datei nicht. Es prüft oder repariert ihre Barrierefreiheit nicht: Was Sie hochladen, erhalten
  die Besucher.
- Das Hochladen vieler Dateien auf einmal und die Optionen des Uploads sind unter
  [Bilder, Medien und Uploads](../../medien-und-uploads.md) beschrieben.

## Der eingebaute PDF-Export

**Setup > PDF-Export** erzeugt ein PDF eines Knotens oder eines Teils des Baums, einmalig oder bei Bedarf
([PDF-Exporte](../../../anleitungen/pdf-exporte.md), [PDF-Export](../../../../eng-us/features/6.0/pdf-export.md)). Das Repository
dokumentiert für diesen Export keine Tags und keine Unterstützung von PDF/UA; behandeln Sie seine Ausgabe daher als
ungetaggtes PDF: nützlich zum Drucken und Archivieren, aber nicht als barrierefreie Fassung Ihrer Inhalte. Behalten Sie
die Webseiten als primäre, barrierefreie Form.

## Gescannte Dokumente und alte Dateien

- Ein Scan ohne Texterkennung ist ein Bild: mit einem Screenreader unbrauchbar. Führen Sie eine Texterkennung durch und
  prüfen Sie das Ergebnis, oder veröffentlichen Sie den Text auf einer Seite.
- Für große Archive alter Dokumente kann Ihr Recht Ausnahmen für Dokumente erlauben, die vor einem bestimmten Datum
  veröffentlicht wurden; fragen Sie Ihre Rechtsberatung und halten Sie die Entscheidung in Ihrer Erklärung zur
  Barrierefreiheit fest. Bieten Sie die Inhalte auf Anfrage in barrierefreier Form an.

## Verwandte Seiten

- [Barrierefreie Inhalte für die Redaktion](barrierefreie-inhalte-fuer-die-redaktion.md)
- [Dateien und Downloads](../../../../eng-us/help/creators/website-creators/files-and-downloads.md)
- [Bilder, Medien und Uploads](../../medien-und-uploads.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
