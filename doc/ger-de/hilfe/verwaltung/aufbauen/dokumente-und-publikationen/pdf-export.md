---
title: PDF-Export
audience: government, admin, website-creator
category: public-sector
order: 60
summary: Wie Sie mit dem PDF-Export von Exponential unter Setup > PDF-Export ein PDF eines Teils des Inhaltsbaums erzeugen - einmal erzeugt oder bei Bedarf, Klassen in einem Baumexport, die öffentliche Adresse gespeicherter PDFs, Schriften und Zeichen und die Grenzen der Barrierefreiheit erzeugter PDFs.
keywords: PDF-Export, Setup PDF-Export, einmal erzeugt, bei Bedarf, Baumexport, öffentliche Adresse, Schriften, Barrierefreiheit, getaggtes PDF, PDF export, pdf/list, generated once, on the fly, tree export, public address, storage pdf, pdf.ini, fonts, UTF-8, ttf, accessibility, tagged PDF
type: help
---
# PDF-Export

Diese Seite richtet sich an Administratoren und Site-Ersteller, die ein druckbares PDF eines Handbuchs, einer Reihe von
Leitfadenseiten oder eines anderen Teils der Site möchten. **Setup > PDF-Export** (`/pdf/list`) erzeugt ein PDF aus
einem Quellknoten und, bei einem Baumexport, den Knoten darunter. Es braucht die Richtlinie `pdf/edit`. Quellen:
[PDF-Exporte](../../../../anleitungen/pdf-exporte.md) und [PDF-Export](../../../../../eng-us/features/6.0/pdf-export.md).

## Inhalt

1. [Zwei Arten von Export](#zwei-arten-von-export)
2. [Einen Export anlegen](#einen-export-anlegen)
3. [Öffentlich oder geschützt](#öffentlich-oder-geschützt)
4. [Zeichen und Schriften](#zeichen-und-schriften)
5. [Grenzen der Barrierefreiheit](#grenzen-der-barrierefreiheit)

## Zwei Arten von Export

| Art | Verhalten | Verwendung |
|---|---|---|
| Einmal erzeugt | das PDF wird beim Speichern des Exports geschrieben und erneut mit **Neu erzeugen**; gespeichert in `var/<site>/storage/pdf/<file name>` | eine feste Ausgabe, schnell herunterzuladen |
| Bei Bedarf erzeugt | bei jedem Download über `/pdf/edit/<id>/generate` neu erstellt | immer aktuell, langsamer bei großen Bäumen, nur für Benutzer mit `pdf/edit` |

## Einen Export anlegen

1. **Setup > PDF-Export**, legen Sie einen neuen Export an.
2. Wählen Sie den Quellknoten; kreuzen Sie für einen Baumexport die einzuschließenden Klassen an.
3. Wählen Sie einmal erzeugt oder bei Bedarf, geben Sie einen Dateinamen an.
4. Speichern; **Herunterladen** auf der Karte. Die Liste zeigt Kennzahlen, Kennzeichen (**Einmal erzeugt**, **Bei
   Bedarf**, **Datei bereit**, **Quelle fehlt**) und **Neu erzeugen**.

## Öffentlich oder geschützt

Ein gespeichertes PDF ist öffentlich: Der Webserver liefert `var/<site>/storage/pdf/<file name>` an jeden aus,
angemeldet oder nicht, und die Karte zeigt diese **Öffentliche Adresse**. Verwenden Sie für Inhalte, die nicht für alle
bestimmt sind, keinen erratbaren Dateinamen, und verwenden Sie für eingeschränkte Inhalte einen bei Bedarf erzeugten
Export: Er wird nur über `/pdf/edit/<id>/generate` von Benutzern mit `pdf/edit` heruntergeladen. Ein Server, der seine
Rewrite-Regeln anderswo hält (ein Vhost, nginx, Velocity), braucht dieselbe Regel für den Pfad.

## Zeichen und Schriften

`settings/pdf.ini [PDFGeneral]`:

| Schlüssel | Voreinstellung | Hinweis |
|---|---|---|
| `Font` | `lib/ezpdf/classes/fonts/Helvetica` | ein `.ttf`-Pfad bettet eine Unicode-Schrift mit markierbarem Text ein; DejaVu Sans deckt Lateinisch, Griechisch, Kyrillisch, Hebräisch, Armenisch und Georgisch ab; setzen Sie für Chinesisch, Japanisch oder Koreanisch eine Schrift, die diese enthält |
| `OutputCharset` | `windows-1252` | bei einer `.ttf`-Schrift ignoriert |
| `Format`, `Orientation`, Ränder | A4, Hochformat | Seitenlayout |

Setzen Sie für eine mehrsprachige Site eine `.ttf`-Schrift, die Ihre Sprachen abdeckt.

## Grenzen der Barrierefreiheit

Die Dokumentation des PDF-Exports beschreibt keine getaggte (strukturierte) PDF-Ausgabe, und nichts darin beansprucht
PDF/UA oder Konformität zur Barrierefreiheit. Behandeln Sie ein exportiertes PDF als druckbare Komfortkopie:

| Praxis | Warum |
|---|---|
| Behalten Sie die HTML-Seiten als Hauptveröffentlichung | sie sind die barrierefreie Fassung |
| Bieten Sie das PDF als „Druckfassung“ an | die Erwartungen sind klar |
| Erzeugen Sie ein förmliches barrierefreies PDF mit einem Autorenwerkzeug, das getaggte PDFs schreibt | und laden Sie es als File hoch |

Siehe [Barrierefreie Dokumente](barrierefreie-dokumente.md) und
[Standards zur Barrierefreiheit](../../../../../eng-us/standards/accessibility.md).

## Verwandte Seiten

- [Dokumente und Publikationen](README.md)
- [Downloads und Berechtigungen](downloads-und-berechtigungen.md)
- [Templates und Design](../../../../anleitungen/templates-und-design.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
