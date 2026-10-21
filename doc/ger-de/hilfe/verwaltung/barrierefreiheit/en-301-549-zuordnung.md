---
title: EN 301 549 auf Exponential abgebildet
audience: government, project-manager, developer
category: public-sector
order: 80
summary: Für öffentliche Stellen und Lieferanten in der EU - wie sich die Abschnitte von EN 301 549 v3.2.1, die für ein Web-CMS zählen (9 Web, 11 Software und Autorenwerkzeuge, 12 Dokumentation und Support), zu dem verhalten, was Exponential dokumentiert, mit dem Status Nicht geprüft und der Arbeit, die Ihnen bleibt.
keywords: EN 301 549, harmonisierte Norm, Abschnitt 9, Abschnitt 11, Autorenwerkzeug, Abschnitt 12, Dokumentation und Support, Richtlinie über barrierefreie Websites, Zuordnung, BITV, v3.2.1, ETSI, CEN, CENELEC, clause 9, clause 11, clause 11.8, authoring tool, clause 12, documentation and support, harmonized standard, Web Accessibility Directive, mapping
type: reference
---
# EN 301 549 auf Exponential abgebildet

Diese Seite richtet sich an die Koordination Barrierefreiheit, an Beschaffungsstellen und an Lieferanten, die eine mit
Exponential gebaute Website zu EN 301 549 „Barrierefreiheitsanforderungen für IKT-Produkte und -Dienstleistungen“,
v3.2.1 (2021-03), herausgegeben von ETSI, CEN und CENELEC, in Beziehung setzen müssen
(<https://www.etsi.org/deliver/etsi_en/301500_301599/301549/03.02.01_60/en_301549v030201p.pdf>). Der Status des
Projekts für diese Norm ist **Nicht geprüft**
([Standards zur Barrierefreiheit: EN 301 549](../../../standards/barrierefreiheit.md#en-301-549)).

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Welche Abschnitte für ein CMS zählen](#welche-abschnitte-für-ein-cms-zählen)
2. [Abschnitt für Abschnitt](#abschnitt-für-abschnitt)
3. [Eigene Nachweise erstellen](#eigene-nachweise-erstellen)
4. [Verwandte Seiten](#verwandte-seiten)

## Welche Abschnitte für ein CMS zählen

| Abschnitt | Gegenstand | Gilt für |
|---|---|---|
| 9 | Web | die öffentliche Website und die Administration (beide sind Webinhalte) |
| 10 | Nicht-Web-Dokumente | PDF- und Office-Dateien, die Ihre Website zum Herunterladen anbietet |
| 11 | Software, einschließlich 11.8 Autorenwerkzeuge | die Administration und den Online-Editor als Autorenwerkzeug |
| 12 | Dokumentation und Supportdienste | die Hilfe und Dokumentation, die Sie den Beschäftigten geben, und Ihren Supportkanal |

Die Erklärung des Projekts nennt Abschnitt 9 (der WCAG 2.1 AA übernimmt), Abschnitt 11 und Abschnitt 12 als relevant für
Autorenwerkzeuge und Abschnitt 11.8 als nicht geprüft.

## Abschnitt für Abschnitt

| Abschnitt | Was Exponential dokumentiert | Status | Was Sie tun |
|---|---|---|---|
| 9 Web (WCAG 2.1 AA) | admin4 / admin4l gegen WCAG 2.2 AA getestet, das 2.1 AA mit Ausnahme von 4.1.1 Syntaxanalyse enthält | Nicht geprüft gegenüber der Norm (Teilweise erfüllt gegenüber WCAG 2.2) | Ihr öffentliches Design und die Administrationsseiten prüfen, die Ihre Redaktion verwendet |
| 10 Nicht-Web-Dokumente | nichts Spezifisches; Dateien werden über `content/download` ausgeliefert | nicht bewertet | Dokumente vor dem Veröffentlichen prüfen (siehe „Dokumente und PDFs“) |
| 11 Software (die Administration als Software) | dieselben Nachweise wie bei Abschnitt 9 für admin4 | Nicht geprüft | die redaktionellen Aufgaben in Ihre Bewertung aufnehmen |
| 11.8 Autorenwerkzeuge | Bildfelder bieten ein Feld für Alternativtext; eine Klasse kann ihn verpflichtend machen („Alternativtext ist erforderlich“, seit 6.0.15); Rich Text hat Überschriften, Tabellenbeschriftungen und Kopfzellen; die optionale Erweiterung Exponential Intelligence kann Alternativtexte zur Prüfung durch die Redaktion vorschlagen | vom Projekt **nicht bewertet** | bewerten, ob das Werkzeug die Autorinnen und Autoren auffordert und die von ihnen eingegebenen Informationen zur Barrierefreiheit erhält |
| 12 Dokumentation und Support | das Hilfesystem folgt denselben Regeln (ein `h1`, benannte Landmarks, beschriftete Suche, sichtbarer Fokus, angekündigte Suchergebnisse) | gegenüber Abschnitt 12 nicht bewertet | Ihren eigenen Supportkanal auf barrierefreien Wegen erreichbar machen |

Quellen: [Standards zur Barrierefreiheit](../../../standards/barrierefreiheit.md),
[Hilfesystem: Sprachen und Barrierefreiheit](../../../funktionen/6.0/hilfesystem.md#sprachen-und-barrierefreiheit),
[Exponential Intelligence](../../../funktionen/6.0/erweiterungen/expai.md).

### Ein Hinweis zu Abschnitt 11.8 und Rich Text

Abschnitt 11.8 verlangt unter anderem, dass von einer Autorin oder einem Autor eingegebene Informationen zur
Barrierefreiheit in der Ausgabe erhalten bleiben. Fakten, die Sie im Repository und in Ihrer Prüfung kontrollieren
können:

- Der Rich-Text-Renderer des Standarddesigns schreibt `<caption>`, `scope` und `abbr` an Tabellen und Kopfzellen, wenn
  die Redaktion sie setzt (`design/standard/templates/content/datatype/view/ezxmltags/table.tpl`, `th.tpl`).
- Das Bild-Template des Standarddesigns verwendet den von der Redaktion eingegebenen Alternativtext und, **wenn er leer
  ist, den Namen des Objekts** (`design/standard/templates/content/datatype/view/ezimage.tpl`). Ein absichtlich leerer
  Alternativtext für ein dekoratives Bild wird von diesem Template daher nicht als `alt=""` geschrieben; Ihr Design muss
  es überschreiben, wenn Sie dieses Verhalten brauchen.

## Eigene Nachweise erstellen

1. Legen Sie den Umfang fest: Ihre Version von Exponential, das Administrationsdesign, das öffentliche Design, die
   aktivierten Erweiterungen.
2. Bewerten Sie Abschnitt 9 mit WCAG-EM (<https://www.w3.org/TR/WCAG-EM/>) auf der öffentlichen Website und an einer
   Stichprobe von Administrationsseiten (Dashboard, Inhaltsbaum, Bearbeitungsformular, Upload, Versionen).
3. Für Abschnitt 11.8 listen Sie die Autorenfunktionen auf, die Ihre Redaktion verwendet, und prüfen Sie für jede, ob
   sie eine barrierefreie Ausgabe fördert oder behindert.
4. Halten Sie das Ergebnis je Abschnitt als „Unterstützt“, „Teilweise unterstützt“, „Nicht unterstützt“ oder „Nicht
   bewertet“ fest, mit den Testnotizen. Das ist Ihr Bericht, nicht der des Projekts.

## Verwandte Seiten

- [Standards zur Barrierefreiheit](../../../standards/barrierefreiheit.md)
- [Die Barrierefreiheit Ihrer Website testen](../../ersteller/website-ersteller/barrierefreiheit-testen.md)
- [Bilder und Bildvarianten](../../ersteller/website-ersteller/bilder-und-bildvarianten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
