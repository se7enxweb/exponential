---
title: Eine Anforderung an die Barrierefreiheit für eine Ausschreibung schreiben
audience: government, project-manager, sales-marketing
category: public-sector
order: 20
summary: Schritt für Schritt eine Anforderung an die Barrierefreiheit für eine Ausschreibung einer Exponential-Site schreiben, die Standard und Stufe nennt, die Administration von der öffentlichen Site trennt, nach Nachweisen fragt, die es gibt, und Tests und Korrekturen dem Lieferanten auferlegt.
keywords: Anforderung an die Barrierefreiheit, Ausschreibung, WCAG 2.2 AA, EN 301 549, Section 508, BITV, Nachweise, Abnahme, VPAT, öffentliche Site, Administration, Aufgabe zur Beschaffung, accessibility requirement, tender, evidence, acceptance, public site, administration, procurement task
type: help
---
# Eine Anforderung an die Barrierefreiheit für eine Ausschreibung schreiben

**Wer:** die Beschaffungsverantwortlichen oder der Projektleiter, mit den Verantwortlichen für Barrierefreiheit.
**Wann:** beim Entwurf der Anforderungen, bevor die Ausschreibung veröffentlicht wird. Die Fakten stammen aus den
[Standards zur Barrierefreiheit](../../../../standards/barrierefreiheit.md).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht zur Barrierefreiheit mit Ihrer Rechtsberatung.

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Beispiel](#beispiel)
4. [Ergebnis](#ergebnis)
5. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Wissen Sie, welches Recht für Sie gilt: in der EU die
  [Richtlinie über den barrierefreien Zugang zu Websites](https://eur-lex.europa.eu/eli/dir/2016/2102/oj) mit
  [EN 301 549](https://www.etsi.org/deliver/etsi_en/301500_301599/301549/03.02.01_60/en_301549v030201p.pdf); in der
  US-Bundesverwaltung [Section 508](https://www.section508.gov/).
- Lesen Sie die Übersichtstabelle der [Standards zur Barrierefreiheit](../../../../standards/barrierefreiheit.md#zusammenfassung):
  admin4 erfüllt WCAG 2.2 AA **teilweise**; EN 301 549 und Section 508 **Nicht geprüft**; kein VPAT.

## Schritte

1. **Nennen Sie Standard und Stufe**: WCAG 2.2 Stufe AA ([WCAG 2.2](https://www.w3.org/TR/WCAG22/)), dazu EN 301 549
   oder Section 508, wo Ihr Recht sie verlangt.
2. **Teilen Sie den Umfang** in zwei Teile, weil sich die Nachweise unterscheiden:
   - die **öffentliche Site**, die der Lieferant baut (Design, Templates, Formulare);
   - die **Administration**, die Ihre Mitarbeiter nutzen.
3. **Machen Sie für die öffentliche Site den Lieferanten verantwortlich**: Die öffentlichen Designs des Projekts sind
   nicht geprüft, also muss das gelieferte Design vom Lieferanten getestet und korrigiert werden
   ([Standards zur Barrierefreiheit](../../../../standards/barrierefreiheit.md#bekannte-lücken)).
4. **Fragen Sie für die Administration, was es gibt**: welches Design er vorschlägt (admin4 ist das getestete) und wie
   er mit den bekannten Lücken umgeht, etwa dem Online-Editor
   ([Das Design admin4](../../../../funktionen/6.0/admin4-design.md)).
5. **Fragen Sie nach Nachweisen, die Sie prüfen können**: ein Prüfbericht nach einer benannten Methode (zum Beispiel
   [WCAG-EM](https://www.w3.org/TR/WCAG-EM/)), keine Absichtserklärung. Verlangen Sie kein VPAT für das Produkt, sofern
   Sie nicht akzeptieren, dass es keines gibt; bitten Sie den Bieter stattdessen um eine Bewertung der gelieferten Site.
6. **Setzen Sie die Erklärung auf die Liste**: Der Lieferant entwirft Ihre Erklärung zur Barrierefreiheit; Exponential
   liefert keine Vorlage mit.
7. **Verknüpfen Sie es mit der Abnahme**: Die Anforderung ist erfüllt, wenn der vereinbarte Test bestanden ist
   ([Den Plan für den Abnahmetest schreiben](den-plan-fuer-den-abnahmetest-schreiben.md)).

## Beispiel

```text
ACC-01  The public website delivered under this contract shall meet WCAG 2.2 level AA
        [and EN 301 549 v3.2.1 clause 9]. The Supplier shall evaluate <list of pages and templates>
        with <method, e.g. WCAG-EM> before acceptance and deliver the report. Failures shall be fixed
        at the Supplier's cost before acceptance.
ACC-02  The Supplier shall state which administration design it proposes for staff, which known
        accessibility gaps of that design and of the online editor remain, and how it mitigates them.
ACC-03  The Supplier shall draft the accessibility statement of the website for <body> to approve.
```

Der Teil mit den Vorlagen für Ausschreibungen im Bereich Beschaffung hat die vollständige Klausel
([Klausel zur Barrierefreiheit](../../beschaffung/ausschreibungsvorlagen/klausel-barrierefreiheit.md)).

## Ergebnis

Eine Anforderung, die ein Bieter ehrlich beantworten kann und die Sie bei der Abnahme testen können.

## Fallstricke

- Das Open-Source-Projekt um eine Konformitätsaussage bitten, die es nicht macht.
- Eine Anforderung für „das System“, die Administration und öffentliche Site vermischt.
- Keine Methode und keine Liste von Seiten für den Test: Dann kann niemand sagen, ob sie erfüllt ist.

## Verwandte Seiten

- [Aufgaben zu Beschaffung und Schulung](README.md)
- [Anforderungen an die Barrierefreiheit](../../beschaffung/anforderungen/anforderungen-an-die-barrierefreiheit.md)
- [Checkliste zur Barrierefreiheit](../../checklisten/checkliste-barrierefreiheit.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
