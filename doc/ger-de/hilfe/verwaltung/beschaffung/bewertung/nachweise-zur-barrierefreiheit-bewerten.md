---
title: Nachweise zur Barrierefreiheit in einem Angebot für ein CMS bewerten
audience: government, project-manager, partner
category: public-sector
order: 90
summary: Wie Bewertende die Nachweise zur Barrierefreiheit eines auf Exponential beruhenden Angebots beurteilen - was die Produktdokumentation für WCAG 2.2, EN 301 549 und Section 508 angibt, was nicht belegt ist, was der Bieter für die öffentliche Website ergänzen muss und wie Sie selbst testen.
keywords: Bewertung der Barrierefreiheit, WCAG 2.2, EN 301 549, Section 508, VPAT, ACR, Konformitätsbericht, WCAG-EM, Erklärung zur Barrierefreiheit, Tastaturtest, Nachweise, accessibility evaluation, accessibility conformance report, accessibility statement, keyboard test, evidence
type: help
---
# Nachweise zur Barrierefreiheit in einem Angebot für ein CMS bewerten

Diese Seite richtet sich an die Fachkraft für Barrierefreiheit in einem Bewertungsgremium. Sie trennt die
Barrierefreiheit der **Software** (die Administration, die die Redaktion verwendet) von der Barrierefreiheit der
**Website** (die öffentlichen Seiten, die Bürgerinnen und Bürger nutzen), gibt an, was die Dokumentation von Exponential
für beide belegt, und listet die Tests auf, die Sie selbst durchführen können. Die Fakten stammen aus
[Standards zur Barrierefreiheit](../../../../standards/barrierefreiheit.md); diese Seite wertet deren Statusangaben nie auf.

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt
1. [Zwei Dinge zu bewerten](#zwei-dinge-zu-bewerten)
2. [Was die Produktdokumentation belegt](#was-die-produktdokumentation-belegt)
3. [Was nicht belegt ist](#was-nicht-belegt-ist)
4. [Was der Bieter liefern soll](#was-der-bieter-liefern-soll)
5. [Tests, die Sie selbst durchführen können](#tests-die-sie-selbst-durchführen-können)
6. [Hinweise zur Punktevergabe](#hinweise-zur-punktevergabe)

## Zwei Dinge zu bewerten

| Teil | Wer ihn nutzt | Wer ihn barrierefrei macht |
|---|---|---|
| Administration (admin4, admin4l, klassische Administration) | Ihre Beschäftigten | das Projekt, plus Ihre Konfiguration |
| Design und Templates der öffentlichen Website | Bürgerinnen und Bürger | der Bieter, der das Design baut |
| Veröffentlichte Inhalte | Bürgerinnen und Bürger | Ihre Redaktion (Alternativtext, Überschriften, Linktext) |

Gesetze für öffentliche Stellen betreffen meist die öffentliche Website und Dokumente; Werkzeuge der Beschäftigten
können unter Regeln für den Arbeitsplatz oder die Beschaffung fallen. In der EU erfasst die
[Richtlinie über barrierefreie Websites (EU) 2016/2102](https://eur-lex.europa.eu/eli/dir/2016/2102/oj) Websites des
öffentlichen Sektors und verweist auf
[EN 301 549](https://www.etsi.org/deliver/etsi_en/301500_301599/301549/03.02.01_60/en_301549v030201p.pdf); in den
Vereinigten Staaten gilt [Section 508](https://www.section508.gov/) für Bundesbehörden.

## Was die Produktdokumentation belegt

| Standard | Status in der Dokumentation | Genannte Nachweise |
|---|---|---|
| [WCAG 2.2](https://www.w3.org/TR/WCAG22/) Stufe AA | **Teilweise erfüllt** (admin4 und admin4l gegen AA-Kriterien entworfen und durch Regressionstests abgesichert) | Designtests in `tests/tests/design/` für Beschriftungen, Landmarks, Fehler, Kontrast im hellen und dunklen Modus, Tastatur, Zielgröße |
| [WAI-ARIA 1.2](https://www.w3.org/TR/wai-aria-1.2/) | **Teilweise erfüllt** für admin4/admin4l | Muster für Reiter, Aufklappen und Menüs an Template-Quellen getestet |
| EN 301 549 v3.2.1 | **Nicht geprüft** | keine Bewertung gegen die Norm; Abschnitt 11.8 (Autorenwerkzeuge) nicht geprüft |
| Section 508 | **Nicht geprüft** | kein VPAT und kein Accessibility Conformance Report |

Manuelle axe-Läufe und ihre Ergebnisse sind im [Changelog von 6.0.15](../../../../../changelogs/6.0/6.0.15.md)
festgehalten; axe läuft nicht in der CI.

## Was nicht belegt ist

Aus den bekannten Lücken der [Standards zur Barrierefreiheit](../../../../standards/barrierefreiheit.md):

- Designs öffentlicher Websites (standard, base, plain, die Websitepakete und Themes) sind **nicht geprüft**.
- Die klassische Administration und admin3 sind nicht von den Tests für Kontrast, Landmarks und Formularfehler
  abgedeckt.
- Der Online-Editor (Integrationen von TinyMCE) ist **nicht geprüft**.
- Separat ausgelieferte Erweiterungen werden einzeln überprüft; es gibt keine Gesamterklärung.
- Es gibt keine Bewertung nach WCAG-EM, kein VPAT und keinen Accessibility Conformance Report.
- Mit dem Produkt wird keine Vorlage für eine Erklärung zur Barrierefreiheit ausgeliefert.
- Nichts zwingt die Redaktion, Alternativtext auszufüllen.

Ein Angebot, das behauptet, „Exponential ist WCAG-konform“ oder „hat ein VPAT“, widerspricht der eigenen Dokumentation
des Produkts. Halten Sie es als nicht belegte Behauptung fest.

## Was der Bieter liefern soll

1. Einen Plan zur Barrierefreiheit für das öffentliche Design: die Kriterien von WCAG 2.2 AA, die es anstrebt, und wie
   es sie testet ([Die Barrierefreiheit testen](../../../ersteller/website-ersteller/barrierefreiheit-testen.md),
   [Barrierefreiheit für Website-Ersteller](../../../ersteller/website-ersteller/barrierefreiheit-fuer-website-ersteller.md)).
2. Einen Konformitätsbericht für die gelieferte Website, erstellt mit einer Methode wie
   [WCAG-EM](https://www.w3.org/TR/WCAG-EM/), vor der Abnahme.
3. Einen Entwurf einer Erklärung zur Barrierefreiheit für die Website, nach dem
   [Leitfaden von W3C WAI zu Erklärungen zur Barrierefreiheit](https://www.w3.org/WAI/planning/statements/).
4. Welches Administrationsdesign Ihre Redaktion verwenden wird (admin4 ist das getestete), und jede Erweiterung, die das
   Angebot hinzufügt, mit eigenen Nachweisen zur Barrierefreiheit.
5. Schulung der Redaktion zu barrierefreien Inhalten
   ([Editors 201, Qualität und Barrierefreiheit](../../../trainer/redakteure-201/README.md)).

## Tests, die Sie selbst durchführen können

| Test | Wie | Erwartet für admin4 |
|---|---|---|
| Nur Tastatur | mit Tab durch das Dashboard und ein Bearbeitungsformular | Sprunglink zuerst, sichtbarer Fokusrahmen, alle Aktionen erreichbar |
| Zoom | 200 % Zoom bei etwa 960 px Breite | kein horizontales Scrollen des Seitenrahmens |
| Dunkelmodus | den Farbmodus wechseln | Text bleibt lesbar |
| Formularfehler | ein Bearbeitungsformular mit leerem Pflichtfeld speichern | eine Fehlerübersicht, die den Fokus erhält und auf das Feld verlinkt |
| Screenreader | eine kurze Sitzung mit dem Screenreader, den Ihre Beschäftigten verwenden | Landmarks und Beschriftungen werden angesagt; die Dokumentation gibt an, dass es kein Testprotokoll mit assistiven Technologien gibt |
| Öffentliche Seite | das Design des Bieters, dieselben Tests | hängt von der Arbeit des Bieters ab |

## Hinweise zur Punktevergabe

- Bewerten Sie die Administration nach dem dokumentierten Status plus Ihren eigenen Tests.
- Bewerten Sie die öffentliche Website nach dem Plan und den Nachweisen des Bieters, nicht nach der
  Produktdokumentation.
- Eine genaue Antwort, die Lücken benennt und sagt, wie sie geschlossen werden, ist mehr wert als eine allgemeine
  Behauptung.

## Verwandte Seiten
- [Bewertung: Übersicht](README.md)
- [Beschaffung: Übersicht](../README.md)
- [Standards zur Barrierefreiheit](../../../../standards/barrierefreiheit.md)
- [Das Design admin4](../../../../funktionen/6.0/admin4-design.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
