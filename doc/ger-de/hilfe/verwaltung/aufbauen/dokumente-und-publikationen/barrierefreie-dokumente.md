---
title: Barrierefreie Dokumente
audience: government, editor, website-creator, project-manager
category: public-sector
order: 70
summary: Wie Behörden mit Exponential barrierefreie Dokumente veröffentlichen - eine HTML-Zusammenfassung für jedes Dokument, barrierefreie Quelldateien, Dateiangaben in Links, Alternativen auf Anfrage - und was Exponential in hochgeladenen Dateien nicht prüft.
keywords: barrierefreie Dokumente, barrierefreies PDF, getaggtes PDF, HTML-Zusammenfassung, Barrierefreiheit von Dokumenten, alternatives Format, Dateiangaben, Linktext, accessible documents, accessible PDF, tagged PDF, PDF/UA, HTML summary, document accessibility, WCAG, EN 301 549, alternative format, file information, link text
type: help
---
# Barrierefreie Dokumente

Diese Seite richtet sich an Redakteure, Site-Ersteller und Projektleiter, die Dokumente veröffentlichen. Die Regeln zur
Barrierefreiheit von Websites des öffentlichen Sektors erfassen meist auch Dokumente: in der EU die Richtlinie über den
barrierefreien Zugang zu Websites, [Richtlinie (EU) 2016/2102](https://eur-lex.europa.eu/eli/dir/2016/2102/oj), und
[EN 301 549](https://www.etsi.org/deliver/etsi_en/301500_301599/301549/03.02.01_60/en_301549v030201p.pdf) (Abschnitt 10
behandelt Dokumente außerhalb des Webs); in den Vereinigten Staaten [Section 508](https://www.section508.gov/).
Exponential speichert und liefert die Dateien aus, die Sie hochladen; es prüft **nicht**, ob sie barrierefrei sind.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [HTML zuerst, Dokumente danach](#html-zuerst-dokumente-danach)
2. [Barrierefreie Quelldateien](#barrierefreie-quelldateien)
3. [Auf der Seite](#auf-der-seite)
4. [Alternativen auf Anfrage](#alternativen-auf-anfrage)
5. [Was Exponential prüft und was nicht](#was-exponential-prüft-und-was-nicht)

## HTML zuerst, Dokumente danach

| Inhalt | Praxis |
|---|---|
| Informationen, die Menschen zum Handeln brauchen | eine HTML-Seite, kein PDF |
| Ein förmliches Dokument | eine HTML-Zusammenfassung (ein Objekt Publication) mit dem angehängten Dokument |
| Ein langer Bericht | eine HTML-Zusammenfassung mit den wichtigsten Ergebnissen; der vollständige Bericht als barrierefreies PDF |
| Ein Formular | ein Online-Formular; das Dokument als Alternative |

## Barrierefreie Quelldateien

Über die Barrierefreiheit eines PDF wird in dem Programm entschieden, das es erzeugt. In der Quelle (einer
Textverarbeitung oder einem Layoutprogramm):

- echte Überschriften (Formatvorlagen), kein fetter Text;
- Alternativtext für Bilder und Diagramme;
- Tabellen mit Kopfzeilen;
- eine gesetzte Dokumentsprache und ein Titel in den Dateieigenschaften;
- eine Lesereihenfolge, die dem Inhalt folgt;
- ausreichender Kontrast;
- Export als getaggtes PDF.

Prüfen Sie das Ergebnis mit einer Barrierefreiheitsprüfung Ihres PDF-Werkzeugs. Der eigene PDF-Export von Exponential
ist eine Druckhilfe, kein getaggtes PDF ([PDF-Export](pdf-export.md#grenzen-der-barrierefreiheit)).

## Auf der Seite

| Praxis | Beispiel für die Form |
|---|---|
| Der Linktext sagt, was das Dokument ist | „Jahresbericht 2025“ statt „Download“ |
| Format und Größe im Link oder daneben | „(PDF, 2,4 MB)“ ([Dateien und die Klasse File](dateien-und-die-klasse-file.md#download-links)) |
| Sprache, wenn sie von der Seite abweicht | „(auf <Sprache>)“ |
| Eine Zusammenfassung auf der Seite | die Zusammenfassung der Klasse Publication |

## Alternativen auf Anfrage

Nicht jedes alte Dokument kann sofort barrierefrei gemacht werden. Sagen Sie auf der Seite oder in der Erklärung zur
Barrierefreiheit, wie man eine barrierefreie Fassung anfordert, und antworten Sie innerhalb einer genannten Frist.
Erklärungen zur Barrierefreiheit beschreibt das W3C:
[Developing an accessibility statement](https://www.w3.org/WAI/planning/statements/). Exponential liefert keine
Vorlage für eine solche Erklärung mit ([Standards zur Barrierefreiheit](../../../../../eng-us/standards/accessibility.md)).

## Was Exponential prüft und was nicht

| Geprüft | Nicht geprüft |
|---|---|
| Bildfelder können einen Alternativtext verlangen ([Inhaltsmodell und Bearbeitung, 9](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md#9-pflicht-alternativtext-gekürzte-textzeilen-und-von-php-erzeugte-namen)) | der Inhalt hochgeladener PDF-, Word- oder anderer Dateien |
| Gefährliche Dateinamen werden beim Upload im Editor abgelehnt | ob ein PDF getaggt ist |
| SVG-Uploads werden bereinigt | Lesereihenfolge, Kontrast oder Sprache von Dokumenten |

Die Liste *Missing alt text* der Erweiterung expai findet Bilder ohne Alternativtext; für Dokumente gibt es nichts
Vergleichbares. Ein Verzeichnis der Dokumente mit ihrem Stand der Barrierefreiheit ist eine Aufgabe für eine
Tabellenkalkulation, unterstützt durch den CSV-Export der Tabelle der Unterelemente.

## Verwandte Seiten

- [Dokumente und Publikationen](README.md)
- [Barrierefreiheit für Site-Ersteller](../../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md)
- [Barrierefreiheit testen](../../../../../eng-us/help/creators/website-creators/testing-accessibility.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
