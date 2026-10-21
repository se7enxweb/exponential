---
title: Anforderungen an die Barrierefreiheit
audience: government, project-manager, admin, partner
category: public-sector
order: 40
summary: Wie eine öffentliche Stelle Anforderungen an die Barrierefreiheit für die Ausschreibung eines CMS schreibt - WCAG 2.2 AA, EN 301 549, Section 508, öffentliche Website gegenüber Administration, Unterstützung beim Erstellen und Konformitätsnachweise - und welchen dokumentierten Status Exponential jeweils hat.
keywords: Barrierefreiheit, Anforderungen, WCAG 2.2, AA, EN 301 549, Section 508, VPAT, ACR, Konformitätsbericht, Erklärung zur Barrierefreiheit, Autorenwerkzeug, öffentlicher Sektor, Ausschreibung, accessibility, requirements, conformance report, accessibility statement, authoring tool, public sector, tender
type: help
---
# Anforderungen an die Barrierefreiheit

Diese Seite richtet sich an Beschaffungsteams und Verantwortliche für Barrierefreiheit, die den Teil zur
Barrierefreiheit einer Ausschreibung für ein CMS schreiben müssen. Sie erklärt, welche Teile eines CMS eine Anforderung
an die Barrierefreiheit abdecken muss, bietet Formulierungen für Anforderungen und gibt an, was Exponential zu jedem
dokumentiert, ausschließlich mit den Statusangaben der [Standards zur Barrierefreiheit](../../../../standards/barrierefreiheit.md).

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt
1. [Der rechtliche und normative Hintergrund](#der-rechtliche-und-normative-hintergrund)
2. [Drei Dinge, die eine Anforderung an die Barrierefreiheit abdecken muss](#drei-dinge-die-eine-anforderung-an-die-barrierefreiheit-abdecken-muss)
3. [Formulierung der Anforderungen](#formulierung-der-anforderungen)
4. [Der dokumentierte Status von Exponential](#der-dokumentierte-status-von-exponential)
5. [Nachweise, nach denen Sie fragen sollten](#nachweise-nach-denen-sie-fragen-sollten)
6. [Fallstricke](#fallstricke)

## Der rechtliche und normative Hintergrund

- **WCAG 2.2** ([W3C Recommendation](https://www.w3.org/TR/WCAG22/)) ist der technische Standard, auf den die meisten
  Gesetze und Vergaberegeln verweisen; Stufe AA ist das übliche Ziel.
- **In der EU** verpflichtet die Richtlinie über barrierefreie Websites
  ([Richtlinie (EU) 2016/2102](https://eur-lex.europa.eu/eli/dir/2016/2102/oj)) öffentliche Stellen, ihre Websites
  barrierefrei zu gestalten und eine Erklärung zur Barrierefreiheit zu veröffentlichen. Ihre harmonisierte Norm ist
  [EN 301 549 v3.2.1](https://www.etsi.org/deliver/etsi_en/301500_301599/301549/03.02.01_60/en_301549v030201p.pdf),
  deren Web-Klausel WCAG 2.1 AA übernimmt.
- **In den Vereinigten Staaten** verpflichtet [Section 508](https://www.section508.gov/) Bundesbehörden, barrierefreie
  IKT zu beschaffen; die [Revised 508 Standards](https://www.access-board.gov/ict/) übernehmen WCAG 2.0 AA durch Verweis.
- Wie eine Konformitätsaussage bewertet wird, beschreibt [WCAG-EM](https://www.w3.org/TR/WCAG-EM/).

## Drei Dinge, die eine Anforderung an die Barrierefreiheit abdecken muss

| Teil | Warum er zählt | Wer verantwortlich ist |
|---|---|---|
| **Die öffentliche Website** (was Besucher sehen) | Das misst das Gesetz. Es hängt vom Design, den Templates und den Inhalten ab. | Der Bieter, der das Design baut, und Ihre Redaktion |
| **Die Administration und der Editor** (was Beschäftigte nutzen) | Beschäftigte mit Behinderungen müssen veröffentlichen können. Viele Gesetze behandeln auch Werkzeuge für Beschäftigte als IKT. | Die Software und der Bieter |
| **Unterstützung beim Erstellen** (hilft das Werkzeug der Redaktion, barrierefreie Inhalte zu veröffentlichen?) | Klausel 11.8 der EN 301 549 betrifft Autorenwerkzeuge: Aufforderungen zu Alternativtext, barrierefreie Ausgabe. | Die Software |

Eine Anforderung, die nur sagt „das CMS ist WCAG-konform“, vermischt alle drei und lässt sich nicht bewerten.

## Formulierung der Anforderungen

```text
ACC-01 (Must)  The public website delivered under this contract meets WCAG 2.2 level AA on the
               pages listed in <annex>. The supplier provides an evaluation following WCAG-EM
               before acceptance.
ACC-02 (Must)  The administration and editing interface used by staff meets WCAG 2.2 level AA.
               State the conformance status per success criterion and the evidence.
ACC-03 (Should) The editing tool supports authors in producing accessible content
               (EN 301 549 clause 11.8): describe alternative text fields, heading structure,
               link text and table markup in the editor.
ACC-04 (Should) The supplier provides a conformance report (for example an Accessibility
               Conformance Report) or states that none exists and how conformance is shown instead.
ACC-05 (Must)  The supplier delivers a draft accessibility statement for the public website
               in the form required by <applicable rules>.
ACC-06 (Should) Accessibility is regression-tested; describe the tests and how often they run.
```

## Der dokumentierte Status von Exponential

Aus [Standards zur Barrierefreiheit](../../../../standards/barrierefreiheit.md):

| Standard | Status | Was das für Ihre Ausschreibung bedeutet |
|---|---|---|
| WCAG 2.2 AA | **Teilweise erfüllt** | Die Administrationsdesigns admin4 und admin4l sind gegen AA-Kriterien gebaut und durch Regressionstests abgesichert (Beschriftungen, Landmarks, Kontrast im hellen und dunklen Modus, Tastatur, Zielgröße). Öffentliche Designs sind **nicht geprüft**; die klassische Administration, admin3 und der Online-Editor sind nicht vollständig abgedeckt. |
| WAI-ARIA 1.2 | **Teilweise erfüllt** | Muster für Tabs, Aufklappbereiche und Menüs in admin4, anhand der Template-Quellen geprüft. |
| EN 301 549 v3.2.1 | **Nicht geprüft** | Keine Bewertung gegen die Norm; Klausel 11.8 (Autorenwerkzeuge) nicht geprüft. |
| Section 508 | **Nicht geprüft** | Es gibt kein VPAT und keinen Accessibility Conformance Report. |

Weitere dokumentierte Lücken: keine Vorlage für eine Erklärung zur Barrierefreiheit für Websites; keine automatisierte
Prüfung der Barrierefreiheit in der CI (axe wird von Hand ausgeführt); Alternativtext hängt von der Redaktion ab, und
nichts zwingt sie, ihn auszufüllen.

Für Exponential lässt sich ACC-02 also für admin4 und admin4l mit Nachweisen beantworten, ACC-01 ist die Arbeit dessen,
der das öffentliche Design baut ([Barrierefreiheit für Website-Ersteller](../../../ersteller/website-ersteller/barrierefreiheit-fuer-website-ersteller.md),
[Die Barrierefreiheit testen](../../../ersteller/website-ersteller/barrierefreiheit-testen.md)), und ACC-04 wird mit „es gibt
keinen“ beantwortet.

## Nachweise, nach denen Sie fragen sollten

- Namen der Tests und wo sie laufen (Exponential: `tests/tests/design/`, die Testsuite `design`).
- Ergebnisse manueller Prüfungen mit assistiven Technologien, mit Datum und Versionen, sofern vorhanden.
- Einen Bewertungsbericht der gelieferten öffentlichen Website nach WCAG-EM, vor der Abnahme.
- Die Liste der bekannten Lücken und einen Plan für jede.

## Fallstricke

- „Voll konform“ ohne Bericht akzeptieren. Bewerten Sie Nachweise, nicht Adjektive.
- Ein VPAT als Kriterium mit bestanden oder nicht bestanden verlangen, wenn der Markt, den Sie erreichen wollen, selten
  eines hat; machen Sie es zu einem bewerteten Kriterium und fragen Sie stattdessen nach einer gleichwertigen
  Bewertung.
- Die Inhalte vergessen: Keine Software bringt die Redaktion dazu, guten Alternativtext zu schreiben. Planen Sie
  Schulungen ([Editors 201, Qualität und Barrierefreiheit](../../../trainer/redakteure-201/modul-08-qualitaet-und-barrierefreiheit/README.md)).
- Dokumente vergessen: Von der Software erzeugte PDFs sind nicht getaggt (PDF/UA **Nicht erfüllt** in
  [Standards zu Dokumentation und Veröffentlichung](../../../../standards/dokumentation-und-veroeffentlichung.md)).

## Verwandte Seiten
- [Anforderungen an ein CMS für den öffentlichen Sektor](README.md)
- [Was Exponential nachweisen kann und was nicht](was-exponential-nachweisen-kann-und-was-nicht.md)
- [Ausschreibungsvorlage: Klausel zur Barrierefreiheit](../ausschreibungsvorlagen/klausel-barrierefreiheit.md)
- [Das Design admin4](../../../../funktionen/6.0/admin4-design.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
