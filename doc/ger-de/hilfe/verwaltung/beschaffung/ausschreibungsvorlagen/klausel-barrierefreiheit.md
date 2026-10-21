---
title: "Ausschreibungsvorlage: Klausel zur Barrierefreiheit"
audience: government, project-manager, partner
category: public-sector
order: 30
summary: Klauseltext für die Pflichten zur Barrierefreiheit in einem Vertrag über ein CMS - WCAG 2.2 AA für die öffentliche Website und die Administration, Verweise auf EN 301 549 und Section 508, Bewertung vor der Abnahme, Erklärung zur Barrierefreiheit und Regressionstests - mit Lücken zum Ausfüllen.
keywords: Klausel zur Barrierefreiheit, Ausschreibungsvorlage, WCAG 2.2, AA, EN 301 549, Section 508, VPAT, ACR, WCAG-EM, Erklärung zur Barrierefreiheit, Vertrag, Beschaffung, accessibility clause, tender template, accessibility statement, contract, procurement
type: help
---
# Ausschreibungsvorlage: Klausel zur Barrierefreiheit

Diese Seite richtet sich an Beschaffungsstellen und Verantwortliche für Barrierefreiheit, die Pflichten zur
Barrierefreiheit in einen Vertrag über ein CMS aufnehmen. Die Klausel trennt die öffentliche Website, die Administration
und die Unterstützung beim Erstellen, wie in [Anforderungen an die Barrierefreiheit](../anforderungen/anforderungen-an-die-barrierefreiheit.md)
erklärt, und verlangt Nachweise, die sich prüfen lassen.

**Vorlage** - an Ihre Vergaberegeln anpassen; keine Rechtsberatung

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt
1. [Die Klausel](#die-klausel)
2. [Die Verweise auf Standards wählen](#die-verweise-auf-standards-wählen)
3. [Was ein auf Exponential beruhendes Angebot zeigen kann](#was-ein-auf-exponential-beruhendes-angebot-zeigen-kann)
4. [Hinweise für die bewertende Person](#hinweise-für-die-bewertende-person)

## Die Klausel

```text
X. ACCESSIBILITY

X.1 Standard. In this contract "the Standard" means the Web Content Accessibility
    Guidelines (WCAG) 2.2, level AA, published by the W3C, [and EN 301 549 v3.2.1]
    [and the Revised Section 508 Standards], as applicable under <applicable rules>.

X.2 Public website. The Supplier shall deliver the public website so that the pages
    and templates listed in <annex> meet the Standard. Before acceptance the Supplier
    shall deliver an evaluation report following the W3C Website Accessibility
    Conformance Evaluation Methodology (WCAG-EM), listing every failure found and
    its correction.

X.3 Administration. The Supplier shall state, per success criterion of the Standard,
    the conformance status of the administration and editing interface used by the
    Customer's staff, with the evidence for each status. Where a criterion is not
    met, the Supplier shall state the workaround and the plan, if any.

X.4 Authoring support. The Supplier shall configure the content types so that
    images carry a required alternative text field where <Customer> decides, and
    shall train editors in accessible content as set out in <training annex>.

X.5 Conformance reports. The Supplier shall deliver any existing conformance report
    for the software (for example an Accessibility Conformance Report) or state in
    writing that none exists.

X.6 Accessibility statement. The Supplier shall deliver a draft accessibility
    statement for the public website in the form required by <applicable rules>,
    based on the evaluation in X.2.

X.7 Regression. Each release delivered under this contract shall not reduce
    conformance. The Supplier shall describe its regression tests and run them
    before every release to production.

X.8 Defects. A failure of the Standard on a page in scope is a defect under
    <defect clause> with severity <level>.
```

## Die Verweise auf Standards wählen

| Verweis | Wann aufnehmen | Link |
|---|---|---|
| WCAG 2.2 AA | immer; es ist die aktuelle W3C Recommendation | [WCAG 2.2](https://www.w3.org/TR/WCAG22/) |
| EN 301 549 v3.2.1 | öffentliche Stellen in der EU (Richtlinie über barrierefreie Websites) | [EN 301 549](https://www.etsi.org/deliver/etsi_en/301500_301599/301549/03.02.01_60/en_301549v030201p.pdf), [Richtlinie (EU) 2016/2102](https://eur-lex.europa.eu/eli/dir/2016/2102/oj) |
| Revised Section 508 Standards | Bundesbehörden der USA | [Section 508](https://www.section508.gov/), [US Access Board](https://www.access-board.gov/ict/) |
| WCAG-EM | für den Bewertungsbericht in X.2 | [WCAG-EM](https://www.w3.org/TR/WCAG-EM/) |
| Hinweise zur Erklärung zur Barrierefreiheit | für X.6 | [W3C WAI statements](https://www.w3.org/WAI/planning/statements/) |

## Was ein auf Exponential beruhendes Angebot zeigen kann

Aus [Standards zur Barrierefreiheit](../../../../../eng-us/standards/accessibility.md):

- **X.3**: Die Administration admin4 und admin4l ist gegen Kriterien von WCAG 2.2 AA gebaut und durch Regressionstests
  abgesichert; Status **Partly complies**. Die klassische Administration, admin3 und der Online-Editor sind nicht
  vollständig abgedeckt.
- **X.2**: Öffentliche Designs sind **nicht geprüft**; die Bewertung ist die Arbeit des Bieters an dem Design, das er
  liefert ([Die Barrierefreiheit testen](../../../../../eng-us/help/creators/website-creators/testing-accessibility.md)).
- **X.5**: Es gibt **kein VPAT und keinen Accessibility Conformance Report**; EN 301 549 und Section 508 sind
  **Unverified**.
- **X.6**: Im Produkt gibt es keine Vorlage für eine Erklärung zur Barrierefreiheit; der Bieter schreibt sie.
- **X.7**: Design-Tests in `tests/tests/design/` laufen für admin4 in der CI; Läufe mit axe erfolgen manuell.

## Hinweise für die bewertende Person

- X.5 akzeptiert bewusst „es gibt keinen“; eine Bewertung mit bestanden oder nicht bestanden würde die meisten
  Open-Source-Angebote ausschließen.
- Bewerten Sie X.2 nach der Methode und der Erfahrung des Teams, weil der Bericht erst nach dem Bau existiert.
- Verlangen Sie in der Vorführung einen Durchgang durch den Editor nur mit Tastatur und mit Screenreader.

## Verwandte Seiten
- [Ausschreibungsvorlagen](README.md)
- [Anforderungen an die Barrierefreiheit](../anforderungen/anforderungen-an-die-barrierefreiheit.md)
- [Klausel zum Abnahmetest](klausel-abnahmetest.md)
- [Barrierefreiheit für Website-Ersteller](../../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
