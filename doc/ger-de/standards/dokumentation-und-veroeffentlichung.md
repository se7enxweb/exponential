---
title: Standards für Dokumentation und Veröffentlichung
audience: developer, admin
category: standards
order: 110
summary: Welchen Standards für Dokumentation und Dokumentformate Exponential folgt (DITA 1.3, CommonMark und GFM, GNU FDL 1.3, GPL v2, EPUB 3.3, EPUB Accessibility 1.1, PDF, OpenDocument, Doxygen), mit Nachweisen und Lücken.
keywords: Dokumentation, Veröffentlichung, Lizenz, Barrierefreiheit, Dokumentformat, API-Dokumentation, Hilfe, documentation, publishing, DITA, DITA 1.3, OASIS, CommonMark, GFM, GitHub Flavored Markdown, Markdown, GFDL, GNU Free Documentation License, GPL, GPL-2.0-or-later, license, EPUB, EPUB 3.3, EPUB Accessibility 1.1, PDF, ISO 32000, PDF/UA, tagged PDF, OpenDocument, ODF, ezodf, Doxygen, API documentation, help
type: reference
---
# Standards für Dokumentation und Veröffentlichung

Diese Seite richtet sich an Entwickler, die Dokumentation für Exponential schreiben oder rendern, und an
Administratoren, die wissen müssen, in welchen Formaten und unter welchen Lizenzen die Hilfe, die Handbücher und
exportierte Inhalte herauskommen. Sie führt die geltenden Standards für Dokumentation, Auszeichnung, Lizenzen und
Dokumentformate auf, sagt für jeden, was Exponential 6.0.x tatsächlich tut, verweist auf die Dateien und Tests, die es
belegen, und benennt die Lücken.

> **Hinweis** "Erfüllt" bedeutet auf dieser Seite, dass der geprüfte Code bzw. die geprüften Dateien für den Teil, den
> Exponential verwendet, das tun, was der Standard verlangt. Keines der unten genannten Formate wurde von einer externen
> Stelle zertifiziert, und kein externer Validator (epubcheck, Ace by DAISY, veraPDF, ein ODF-Validator) läuft in der CI
> des Projekts.

## Zusammenfassung

| Standard | Version | Status | Nachweis (kurz) |
|---|---|---|---|
| DITA (Darwin Information Typing Architecture), Informationsmodell | 1.3 | **Teilweise erfüllt** | Thementypen concept, task, reference, troubleshooting als Inhaltsklassen in `kernel/classes/help/content/exphelpcontentschema.php`; noch kein DITA-XML-Export |
| CommonMark + GitHub Flavored Markdown | CommonMark 0.31, GFM 0.29-gfm | **Teilweise erfüllt** | eigener Renderer `kernel/classes/help/exphelpmarkdownrenderer.php`, eine dokumentierte Teilmenge; rohes HTML wird absichtlich nicht durchgereicht; kein Lauf der offiziellen Spezifikationstests |
| GNU Free Documentation License (Dokumentation) | 1.3 oder später | **Erfüllt** | [README-LICENSE.md](../README-LIZENZ.md), [LICENSE-GFDL-1.3.txt](../../eng-us/LICENSE-GFDL-1.3.txt), `settings/exphelp.ini [License]`, `tests/tests/kernel/classes/help/expHelpLicenseTest.php` |
| GNU General Public License (Code) | 2.0 oder später | **Erfüllt** | `LICENSE`, `composer.json` `"license": "GPL-2.0-or-later"`, Dateiköpfe; Formulierung der Köpfe nicht einheitlich |
| EPUB | 3.3 | **Teilweise erfüllt** | `expHelpContentExport::writeEpub()` schreibt ein EPUB-3-Paket eines Hilfehandbuchs; nicht mit epubcheck geprüft |
| EPUB Accessibility | 1.1 | **Teilweise erfüllt** | Barrierefreiheits-Metadaten im Paketdokument geschrieben; nicht mit Ace by DAISY geprüft |
| PDF (ISO 32000) | Dateien deklarieren PDF 1.3 | **Teilweise erfüllt** | `lib/ezpdf` schreibt `%PDF-1.3`; Hilfe-PDFs betten TrueType-Schriften als Teilmenge mit `ToUnicode`-Maps ein |
| PDF/UA (ISO 14289), getaggtes PDF | 1 | **Nicht erfüllt** | kein Strukturbaum, kein `MarkInfo`, keine Dokumentsprache in irgendeinem PDF-Writer |
| OpenDocument Format (ODF) | 1.0 geschrieben | **Teilweise erfüllt** | Extension `ezodf` 6.1.11 importiert und exportiert OpenDocument-Text, schreibt `office:version='1.0'` |
| Doxygen-API-Dokumentation | Doxyfile im Format 1.5.4 | **Teilweise erfüllt** | `doc/eng-us/doxygen/Doxyfile` existiert, ist veraltet und läuft nicht in CI; seine Ausgabe ist von der Hilfe ausgeschlossen |

## DITA 1.3

### Was es ist

Die Darwin Information Typing Architecture (DITA) 1.3 ist ein OASIS-Standard (Dezember 2015) zum Schreiben und
Veröffentlichen themenbasierter technischer Dokumentation. Sie definiert Thementypen (concept, task, reference und in
1.3 ein Thema für die Fehlerbehebung, troubleshooting), Maps, die Themen zu Handbüchern ordnen, und Filterung über
Attribute wie `audience` und `platform`.

### Wie Exponential den Standard erfüllt

Exponential verwendet DITA 1.3 als **Informationsmodell** der als Inhalt gepflegten Hilfe, nicht als Dateiformat:

- Der Plan nennt DITA 1.3 als Dokumentationsstandard ([Plan des Hilfesystems](../spezifikationen/6.0/hilfesystem.md),
  "Decisions", Zeile 1), und der [Styleguide der Hilfe](../spezifikationen/6.0/hilfe-stilrichtlinien.md) besagt, dass jedes
  Thema genau einen DITA-Typ hat.
- `kernel/classes/help/content/exphelpcontentschema.php` ordnet die Inhaltsklassen in
  `expHelpContentSchema::TOPIC_TYPES` den DITA-Thementypen zu: `help_concept` zu concept, `help_task` zu task,
  `help_reference` zu reference, `help_troubleshooting` zu troubleshooting. `help_manual` und `help_section` sind die
  Maps (`MAP_CLASSES`; die Klassenbeschreibung von `help_manual` lautet "A manual (DITA bookmap)").
- Die Felder folgen den DITA-Elementen: Eine Aufgabe hat genau eine nummerierte Liste von Schritten, ein Thema zur
  Fehlerbehebung hat `condition`, `cause` und `remedy`, jedes Thema hat eine `shortdesc` und eine stabile `help_id`
  (die geplante DITA-`id`), `related` ist das geplante `related-links`.
- Zielgruppen und Plattformen sind als Filterwerte modelliert (`DEFAULT_AUDIENCES`, `DEFAULT_PLATFORMS` in derselben
  Klasse).
- `kernel/classes/help/content/exphelpcontentvalidator.php` prüft die maschinell prüfbaren Regeln des Styleguides
  (zum Beispiel das Muster der `help_id`); `./console exp:help:content check` gibt die Befunde aus und endet mit 1,
  solange ein Fehler übrig ist.
- Tests: `tests/tests/kernel/classes/help/expHelpContentTest.php` (Suite `kernel-classes`).

Die Dokumentationsseiten in `doc/` (wie diese) sind Markdown mit Front Matter, keine DITA-Themen. Ihr Schlüssel `type`
ist nur eine Beschriftung für den Hilfe-Viewer.

### Status

**Teilweise erfüllt.** Die Thementypen und ihre Pflichtbestandteile folgen DITA 1.3. Es gibt nirgends DITA-XML: Keine
Topic-, Map-, Bookmap- oder DITAVAL-Datei wird geschrieben oder gelesen.

### Bekannte Lücken

- Der Export als DITA-Bookmap, die DITAVAL-Filterung, die Verarbeitung mit DITA-OT und der regelmäßige DITA-Export in
  ein Git-Repository sind geplant, nicht umgesetzt ([Hilfe als Inhalt gepflegt](../funktionen/6.0/hilfeinhalte.md),
  "E-books": "Not yet"). Der Exportbefehl akzeptiert nur `--format=epub` oder `--format=pdf`
  (`kernel/private/classes/commands/exphelpcontent.php`).
- Der Plan der Hilfe nennt außerdem IEC/IEEE 82079-1:2019 und ISO/IEC/IEEE 26514 als Regeln für die Inhalte. Das sind
  Schreibregeln; nichts prüft sie automatisch. **Nicht überprüft.**

## CommonMark und GitHub Flavored Markdown

### Was es ist

CommonMark ist eine streng spezifizierte Markdown-Syntax mit Testsuite (Spezifikation Version 0.31.2, Januar 2024).
GitHub Flavored Markdown (GFM, Spezifikation 0.29-gfm, GitHub) ist eine strikte Obermenge, die Tabellen, Aufgabenlisten,
Durchstreichung, automatische Links und die Erweiterung für unzulässiges rohes HTML hinzufügt.

### Wie Exponential den Standard erfüllt

Jede Seite der Hilfe in der Administrationsoberfläche ist Markdown aus `doc/`, auf dem Server gerendert vom eigenen
Renderer von Exponential, `expHelpMarkdownRenderer` (`kernel/classes/help/exphelpmarkdownrenderer.php`). Sein
Klassenkommentar dokumentiert die Teilmenge:

| Teil | Unterstützt |
|---|---|
| CommonMark-Blöcke | ATX- und Setext-Überschriften, Absätze, Blockzitate, geordnete und ungeordnete Listen, eingezäunter und eingerückter Code, thematische Umbrüche, Link-Referenzdefinitionen |
| GFM-Erweiterungen | Tabellen mit Spaltenausrichtung, Aufgabenlisten, Durchstreichung, Fußnoten, automatisch verlinkte URLs |
| Hervorgehobene Kästen | GitHub-Alerts (`> [!NOTE]`), Zitate im Stil `> **Note** ...` und Blöcke `:::tip Title ... :::`; Arten note, tip, important, warning, caution |
| Überschriften | IDs mit den Slugs von GitHub, sodass ein für GitHub geschriebener Link auf derselben Überschrift landet; ein Inhaltsverzeichnis |
| Code | auf dem Server hervorgehoben (`kernel/classes/help/exphelphighlighter.php`) |
| Front Matter | flaches YAML am Anfang der Seite (`kernel/classes/help/exphelpfrontmatter.php`) |

Rohes HTML wird nie durchgereicht. Der Renderer maskiert die Zeichen des Dokuments und gibt nur sein eigenes Markup
aus. Eine kurze Allowlist von Inline-Formatierungselementen ohne Attribute (`b`, `strong`, `i`, `em`, `code`, `kbd`,
`sub`, `sup` und einige weitere), `<a>` und `<img>` über die Link- und Bildrichtlinien, `<details>`/`<summary>` und
`<abbr title>` werden vom Renderer neu aufgebaut; HTML-Kommentare werden verworfen; jedes andere Tag (`<script>`,
`<style>`, `<iframe>`, `on*`-Attribute) wird als Text angezeigt. Links außer http(s), mailto, Seitenanker und bekannten
relativen Pfaden (zum Beispiel `javascript:` oder `data:`) werden als Text angezeigt.

Tests: `tests/tests/kernel/classes/help/expHelpMarkdownRendererTest.php` (13 Testmethoden, darunter
`testRawHtmlIsHarmless`, `testBadLinksDropped`, `testMarkdownSubset`, `testPathologicalInputs` und
`testEveryDocPageRendersSafely`). `./console exp:help check` (`kernel/classes/help/exphelplinkcheck.php`) meldet
fehlende Linkziele, unsichere Links und unbekannte Anker in den Markdown-Seiten.

### Status

**Teilweise erfüllt.** Der Renderer deckt die Block- und Inline-Konstrukte ab, die die Dokumentation verwendet, und
verarbeitet nicht vertrauenswürdige Eingaben sicher. Er ist keine vollständige CommonMark-Implementierung und wird
nicht gegen die Testsuiten der CommonMark- oder GFM-Spezifikation getestet.

### Bekannte Lücken

- Kein Lauf der offiziellen Spezifikationsbeispiele von CommonMark oder GFM; die Konformität bei den Randfällen der
  Auswertung von Hervorhebungen, Listen und Links ist **nicht überprüft**.
- HTML-Blöcke und das meiste Inline-HTML werden als Text angezeigt statt durchgereicht. Das weicht absichtlich von
  CommonMark ab (Sicherheit); die eigene GFM-Erweiterung für unzulässiges rohes HTML filtert nur eine feste Liste von
  Tags.
- Hervorgehobene Kästen mit `:::`-Einzäunung sind kein GFM und werden auf GitHub nicht gerendert.
- Dokumente über 4.000.000 Bytes oder tiefer als 16 Ebenen verschachtelt werden als reiner Text angezeigt
  (`expHelpMarkdownRenderer::MAX_LENGTH`, `MAX_DEPTH`).

## GNU Free Documentation License 1.3

### Was es ist

Die GNU Free Documentation License (GFDL), Version 1.3 vom 3. November 2008, herausgegeben von der Free Software
Foundation, ist eine Copyleft-Lizenz für Handbücher und andere Dokumentation.

### Wie Exponential den Standard erfüllt

- [README-LICENSE.md](../README-LIZENZ.md) enthält den Copyright- und Erlaubnishinweis: Version 1.3 "or any later
  version", ohne unveränderliche Abschnitte (Invariant Sections), ohne Vorderseitentexte (Front-Cover Texts) und ohne
  Rückseitentexte (Back-Cover Texts).
- [LICENSE-GFDL-1.3.txt](../../eng-us/LICENSE-GFDL-1.3.txt) ist der unveränderte Lizenztext.
- `settings/exphelp.ini [License]` steuert den Hinweis, den die Hilfe auf jeder Seite, dem Dashboard, in der
  Druckansicht und im PDF zeigt: `Name=GNU Free Documentation License`, `Version=1.3`, `OrLater=true`,
  `TextPage=LICENSE-GFDL-1.3.txt`, `NoticePage=README-LICENSE.md`, `ExplainerPage=help/free-documentation.md`. Die
  Logik steckt in `kernel/classes/help/exphelplicense.php`.
- Übernommene Seiten behalten ihre eigenen Bedingungen. `[License] OtherLicensePathRules[]` führt die Dokumentation
  auf, die aus der Codebasis von eZ Publish 4 übernommen wurde (`doc/eng-us/features/[2-5].*`, `doc/eng-us/bc/[2-5].*`,
  `doc/eng-us/specifications/[2-5].*`, `doc/eng-us/specifications/trunk/*`, `doc/changelogs/[2-5].*`,
  `doc/eng-us/shop/*` und weitere). Für diese zeigt die Hilfe `OtherCopyright` und
  `OtherLicenseName=GNU General Public License, version 2` statt des GFDL-Hinweises; das Projekt lizenziert sie nicht
  neu.
- Leser erhalten eine verständliche Erklärung in [Freie Dokumentation](../hilfe/freie-dokumentation.md).
- Tests: `tests/tests/kernel/classes/help/expHelpLicenseTest.php`, darunter `testLicenseTextIsTheOfficialText`,
  `testSettings`, `testTemplatesCarryTheLicenseBlock` und `testPagesAndTheirLicense`.

### Status

**Erfüllt.**

### Bekannte Lücken

- Der Hinweis wird vom Hilfe-Viewer hinzugefügt, nicht in jede Markdown-Datei geschrieben. Eine einzeln aus dem
  Repository kopierte Seite trägt keinen Hinweis; [README-LICENSE.md](../README-LIZENZ.md) ist der Beleg.

## GNU General Public License, Version 2 oder später (Code)

### Was es ist

Die GNU General Public License, Version 2 (Juni 1991, Free Software Foundation), ist die Copyleft-Lizenz des
Quellcodes. "Oder später" erlaubt einem Empfänger, auch den Bedingungen jeder späteren GPL-Version zu folgen.

### Wie Exponential den Standard erfüllt

- `LICENSE` im Wurzelverzeichnis des Repositorys besagt, dass der Quellcode unter der GNU General Public License,
  Version 2, bereitgestellt wird, und enthält den vollständigen Lizenztext. `COPYRIGHT.md` enthält die
  Copyright-Hinweise.
- `composer.json` deklariert `"license": "GPL-2.0-or-later"` (SPDX-Bezeichner).
- Neue Kernel-Dateien tragen `@license GNU General Public License v2.0 (or any later version)` (zum Zeitpunkt des
  Schreibens etwa 680 Dateien in `kernel/` und `lib/`); die meisten übernommenen Dateien verweisen stattdessen auf die
  Datei `LICENSE` ("For full copyright and license information view LICENSE file distributed with this source code").
- Extensions deklarieren ihre Lizenz in `ezinfo.php` und `extension.xml` (zum Beispiel `ezodf`:
  "GNU General Public License v2.0 (or any later version)").

### Status

**Erfüllt.**

### Bekannte Lücken

- Die Formulierung ist nicht einheitlich. Die historische Kopfvorlage `doc/eng-us/standards/header.txt` sagt
  "version 2.0" ohne "or later", und `LICENSE` selbst wiederholt die Erlaubnis "or later", die `composer.json`
  erteilt, nicht.
- In `lib/` gebündelter Code Dritter behält seine eigenen Hinweise; ein vollständiges Lizenzinventar des gebündelten
  Codes wurde für diese Seite nicht erstellt. **Nicht überprüft.**

## EPUB 3.3 und EPUB Accessibility 1.1

### Was es ist

EPUB 3.3 ist eine W3C-Empfehlung (Mai 2023) für E-Books: ein ZIP-Container (OCF) mit einem Paketdokument,
XHTML-Inhaltsdokumenten und einem Navigationsdokument. EPUB Accessibility 1.1 (W3C-Empfehlung, Mai 2023) definiert die
Barrierefreiheits-Metadaten und die Konformitätsanforderungen für EPUB-Publikationen, auf Grundlage der WCAG.

### Wie Exponential den Standard erfüllt

Ein EPUB-Export existiert für **Handbücher der als Inhalt gepflegten Hilfe** (nicht für beliebige Inhalte):

- `expHelpContentExport::writeEpub()` in `kernel/classes/help/content/exphelpcontentexport.php`, reines PHP auf Basis
  von `ZipArchive`. Befehl (eine Zeile):

  ```bash
  ./console exp:help:content export -s <admin siteaccess> --manual=<remote id> --format=epub [--output=file]
  ```

  In der Hilfe **EPUB herunterladen** auf der Seite des Handbuchs
  (`kernel/private/classes/views/exphelp/manual.php`). Siehe [Hilfe als Inhalt gepflegt](../funktionen/6.0/hilfeinhalte.md),
  "E-Books".
- Container: Die Datei `mimetype` steht an erster Stelle und wird unkomprimiert gespeichert, wie OCF es verlangt,
  gefolgt von `META-INF/container.xml`.
- Paket und Navigation: ein Paketdokument mit `xml:lang`, ein `nav.xhtml` mit `epub:type="toc"` und
  `role="doc-toc"`, XHTML-Inhaltsdokumente; enthalten sind nur veröffentlichte Kapitel und Themen, die der Benutzer
  lesen darf.
- Barrierefreiheits-Metadaten (`opf()` in derselben Klasse): `schema:accessMode` textual und visual,
  `schema:accessModeSufficient` textual, `schema:accessibilityFeature` structuralNavigation, tableOfContents und
  alternativeText, `schema:accessibilityHazard` none, eine `accessibilitySummary` und
  `dcterms:conformsTo` "EPUB Accessibility 1.1 - WCAG 2.2 Level AA". Abbildungen behalten ihren Alternativtext und ihre
  Bildunterschrift.
- Tests: `tests/tests/kernel/classes/help/expHelpContentExportTest.php` (`testXhtmlFragment`, `testXhtmlDocument`,
  `testAnchorsAndLanguage` und weitere).

Zu den WCAG selbst siehe [Standards für Barrierefreiheit](barrierefreiheit.md).

### Status

**Teilweise erfüllt** (beide). Struktur und Metadaten, die die Standards verlangen, werden geschrieben; kein erzeugtes
Buch wurde validiert.

### Bekannte Lücken

- epubcheck und Ace by DAISY werden nicht mitgeliefert und laufen nicht in CI; der Codekommentar sagt, dass die
  Prüfung der Site überlassen bleibt. Die Konformität eines erzeugten Buchs ist **nicht überprüft**.
- Das Paketelement deklariert `version="3.0"`, den Wert, den EPUB 3.3 weiterhin verwendet; der Export wurde nicht gegen
  die neueren Anforderungen von EPUB 3.3 geprüft.
- Die Angabe `dcterms:conformsTo` wird für jedes Buch geschrieben; sie wird nicht aus einer Prüfung dieses Buchs
  abgeleitet.
- Es gibt keinen EPUB-Export gewöhnlicher Inhalte oder der Hilfeseiten aus `doc/`.

## PDF (ISO 32000) und PDF/UA

### Was es ist

PDF ist als ISO 32000 standardisiert (ISO 32000-1:2008 für PDF 1.7, ISO 32000-2:2020 für PDF 2.0). PDF/UA (ISO 14289)
fügt Anforderungen für barrierefreies, getaggtes PDF hinzu.

### Wie Exponential den Standard erfüllt

Es gibt zwei PDF-Writer, beide auf Basis der kernelseitigen Bibliothek `lib/ezpdf` (`lib/ezpdf/classes/class.pdf.php`):

- **Hilfeseiten und Hilfehandbücher.** "Als PDF herunterladen" einer Hilfeseite (`exphelp/pdf/<page id>`,
  `./console exp:help pdf <page id>`; `kernel/classes/help/exphelppdf.php`, `exphelppdfwriter.php`,
  `exphelppdfcanvas.php`) und das PDF eines Handbuchs (`expHelpContentExport::writePdf()`). Nichts wird aus dem Netz
  geholt und kein externes Programm läuft. Einstellungen: `settings/exphelp.ini [PdfSettings]` (`Enabled`, `PageSize`
  A4 oder Letter, `FontFiles[]`, `BoldFontFiles[]` und die anderen Schriftlisten).
  - Schriften: Eine TrueType-Schrift wird als zusammengesetzte Schrift (`/Encoding /Identity-H`) mit einer
    `ToUnicode`-Map eingebettet, sodass Text auswählbar und durchsuchbar bleibt, und auf die verwendeten Glyphen
    reduziert (`kernel/classes/help/exphelppdffontsubset.php`). Eine Ersatzschrift übernimmt Zeichen, die der
    Hauptschrift fehlen. Ganz ohne TrueType-Schrift werden die PDF-Standardschriften verwendet, und Text wird nach
    Windows-1252 transliteriert.
  - Der Dokumenttitel wird gesetzt (`addUnicodeInfo( 'Title', ... )`), Überschriften erhalten benannte Ziele, sodass
    Links innerhalb der Seite funktionieren, und Links sind anklickbar.
  - Tests: `tests/tests/kernel/classes/help/expHelpPdfTest.php` (`testOutputIsAPdfWithTitleAndText`,
    `testTrueTypeEmbeddedAndSubset`, `testRightToLeft` und weitere).
- **PDF-Exporte von Inhalten.** Setup > PDF-Export (`/pdf/list`), ein PDF aus einem Teil des Inhaltsbaums
  (`kernel/classes/ezpdfexport.php`, `kernel/classes/pdfexport/`, `settings/pdf.ini`). Siehe
  [PDF-Exporte](../anleitungen/pdf-exporte.md).

### Status

- PDF: **Teilweise erfüllt.** Die Dateien deklarieren `%PDF-1.3` (`lib/ezpdf/classes/class.pdf.php`). PDF 1.3 ist
  älter als ISO 32000, und seine Funktionen sind eine Teilmenge dessen, was ISO 32000-1 abdeckt, sodass konforme
  Reader die Dateien öffnen; die Writer zielen nicht ausdrücklich auf ISO 32000-1 oder -2, und keine Datei wurde mit
  einem Validator geprüft.
- PDF/UA und getaggtes PDF: **Nicht erfüllt.** Kein PDF-Writer im Kernel schreibt einen Strukturbaum
  (`StructTreeRoot`), `MarkInfo`, eine Dokumentsprache (`/Lang`) oder XMP-Metadaten; eine Suche in `lib/ezpdf`,
  `kernel/classes/help` und `kernel/classes/pdfexport` findet nichts davon.

### Bekannte Lücken

- PDFs sind nicht getaggt, sodass Screenreader weder Lesereihenfolge noch Überschriftenstruktur erhalten. Leser, die
  barrierefreie Ausgaben brauchen, sollten den EPUB-Export oder die Hilfe im Browser verwenden.
- Kein PDF/A-Profil (Archivierung).
- Arabische Buchstaben werden in ihren isolierten Formen gezeichnet, ohne kontextabhängige Formung (Klassenkommentar
  von `expHelpPdfWriter`).
- Das Hilfe-PDF schreibt keine Lesezeichengliederung; `lib/ezpdf` schreibt ein leeres `/Outlines`-Objekt.

## OpenDocument Format

### Was es ist

Das Open Document Format for Office Applications (ODF) ist ein OASIS-Standard, auch als ISO/IEC 26300
veröffentlicht. ODF 1.0 stammt aus dem Jahr 2005; spätere Versionen sind 1.1, 1.2 und 1.3.

### Wie Exponential den Standard erfüllt

Die Extension `ezodf` (Paket `se7enxweb/ezodf`, von der `composer.json` im Wurzelverzeichnis als `~6.1.11` verlangt;
sie liegt in `extension/ezodf/` einer Installation, nicht im Kernel-Repository) importiert OpenDocument-Textdateien als
Inhalt und exportiert Inhalte als OpenDocument-Text:

- Module `ezodf/import` und `ezodf/export`; der Export antwortet mit `application/vnd.oasis.opendocument.text`.
- `classes/ezoogenerator.php` schreibt die Teile meta, settings und styles mit den OASIS-Namensräumen
  (`urn:oasis:names:tc:opendocument:xmlns:office:1.0`) und `office:version='1.0'`.
- Hochgeladene Dateien werden als ZIP-Archive geprüft, bevor sie gelesen werden (`tests/eZOOImportArchiveTest.php`,
  `eZOOImportZipSlipTest.php` in der Extension).
- Die Konvertierung hochgeladener Word-`.doc`-Dateien und Exporte nach PDF und Word braucht einen externen
  Konvertierungsdienst (`settings/odf.ini.append.php`, `OOConverterAddress`, `OOConverterPort`); er ist standardmäßig
  deaktiviert.

### Status

**Teilweise erfüllt.** Dokumente werden als ODF 1.0 geschrieben, nicht in einer aktuellen Version, und keine erzeugte
Datei wurde mit einem ODF-Validator geprüft.

### Bekannte Lücken

- Ausgabe in ODF 1.0; Funktionen von ODF 1.2 und 1.3 (zum Beispiel digitale Signaturen und die RDF-Metadaten von 1.2)
  werden nicht geschrieben.
- Die Tests der Extension gehören nicht zur CI des Kernel-Repositorys. **Nicht überprüft**, ob sie irgendwo laufen.

## Doxygen-API-Dokumentation

### Was es ist

Doxygen ist ein weit verbreiteter Open-Source-Generator für API-Dokumentation aus Quellcodekommentaren. Es ist ein
Werkzeug, kein formaler Standard; es wird hier aufgeführt, weil es festlegt, wie die API-Referenz erzeugt würde.

### Wie Exponential den Standard erfüllt

- `doc/eng-us/doxygen/Doxyfile` konfiguriert Doxygen für den Kernel: `PROJECT_NAME = "Exponential "`,
  `OUTPUT_DIRECTORY = doc/generated`, `INPUT` aus `lib/ezdb/classes`, `lib/ezpdf/classes`, `lib/ezsoap/classes`,
  `lib/eztemplate/classes`, `kernel/classes`, `kernel/common` und anderen Verzeichnissen in `lib/`.
- Die Hilfe liefert nie Doxygen-Ausgaben aus: `settings/exphelp.ini [IndexSettings] ExcludePatterns[]=doc/eng-us/doxygen/*`
  und derselbe Standard in `kernel/classes/help/exphelpallowlist.php`; `kernel/classes/help/exphelpcontextregistry.php`
  weist Seiten-IDs unter `doxygen/` ab.

### Status

**Teilweise erfüllt.** Eine Konfiguration existiert, ist aber veraltet, und das Projekt erzeugt oder veröffentlicht
keine API-Referenz.

### Bekannte Lücken

- Das Doxyfile liegt im Format von Doxygen 1.5.4 vor und sagt noch `PROJECT_NUMBER = 5.90.0alpha1`.
- `INPUT_ENCODING = latin1`, obwohl die Quellen UTF-8 sind.
- Zwei Eingabeverzeichnisse existieren nicht mehr (`lib/ezwebdav/classes`, `lib/ezxml/classes`).
- Kein CI-Workflow führt Doxygen aus (`.github/workflows/` wurde durchsucht).

## Verwandte Seiten

- [Die Lizenz dieser Dokumentation](../README-LIZENZ.md) und [Freie Dokumentation](../hilfe/freie-dokumentation.md)
- [Plan des Hilfesystems](../spezifikationen/6.0/hilfesystem.md) und [Styleguide der Hilfe](../spezifikationen/6.0/hilfe-stilrichtlinien.md)
- [Hilfe als Inhalt gepflegt](../funktionen/6.0/hilfeinhalte.md) und [Das Hilfesystem](../funktionen/6.0/hilfesystem.md)
- [PDF-Exporte](../anleitungen/pdf-exporte.md)
- [Standards für Barrierefreiheit](barrierefreiheit.md)
