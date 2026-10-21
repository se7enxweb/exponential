---
title: Klauseln zur Barrierefreiheit für die Beschaffung
audience: government, project-manager, partner
category: public-sector
order: 280
summary: Für Beschaffungsstellen und Projektverantwortliche - Musteranforderungen, Nachweisanforderungen und Vertragsklauseln zur Barrierefreiheit für den Einkauf von Design, Implementierung, Hosting oder Support rund um Exponential, so formuliert, dass ein Bieter sie auf Grundlage des dokumentierten Status des Projekts ehrlich beantworten kann.
keywords: Beschaffung, Ausschreibung, Anforderungen, Vertragsklauseln, Barrierefreiheit, Nachweise, Abnahmetest, Gewährleistung, Mängel, Lieferant, öffentlicher Sektor, procurement, tender, requirements, contract clauses, accessibility, WCAG 2.2 AA, EN 301 549, Section 508, VPAT, ACR, evidence, acceptance testing, warranty, defects, supplier, public sector
type: help
---
# Klauseln zur Barrierefreiheit für die Beschaffung

Diese Seite richtet sich an Beschaffungsstellen, Projektverantwortliche und Bewertungsgremien. Sie gibt
Musterformulierungen für den Teil zur Barrierefreiheit in einer Ausschreibung oder einem Vertrag über Leistungen rund um
Exponential (Design, Implementierung, Hosting, Support, Schulung). Exponential selbst ist freie Software ohne
Lizenzgebühr; was Sie einkaufen, ist die Arbeit, und die Barrierefreiheit muss Teil davon sein. Passen Sie die
Formulierungen mit Ihrer Beschaffungs- und Rechtsberatung an.

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Grundsätze](#grundsätze)
2. [Musteranforderungen](#musteranforderungen)
3. [Musteranforderungen an Nachweise](#musteranforderungen-an-nachweise)
4. [Mustervertragsklauseln](#mustervertragsklauseln)
5. [Die Antworten bewerten](#die-antworten-bewerten)
6. [Verwandte Seiten](#verwandte-seiten)

## Grundsätze

- Verlangen Sie prüfbare Ergebnisse (WCAG 2.2 AA auf der gelieferten Website), keine Adjektive („barrierefrei“).
- Trennen Sie die **Software** (die Erklärung des Projekts: admin4 „Teilweise erfüllt“, EN 301 549 und Section 508
  „Nicht geprüft“, kein VPAT; [Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md)) von der
  **Leistung** (Ihr Design, Ihre Konfiguration, Ihr Inhaltsmodell).
- Machen Sie die Abnahme von einem Test abhängig und Mängel von ihrer Behebung.

## Musteranforderungen

| Nr. | Anforderung |
|---|---|
| A1 | Die gelieferte öffentliche Website erfüllt WCAG 2.2 Stufe AA auf allen Seiten-Templates, Formularen und Funktionen im Umfang. |
| A2 | Die Administration ist für die gesamte Redaktion mit dem Design admin4 (oder admin4l) konfiguriert. |
| A3 | An Bildattributen von Inhaltsklassen, die die Redaktion für informative Bilder verwendet, ist „Alternativtext ist erforderlich“ eingeschaltet, sofern der Auftraggeber nichts anderes entscheidet. |
| A4 | Templates schreiben für jeden Sprach-Siteaccess `lang` (und, wo nötig, `dir`). |
| A5 | Es wird kein Template, Stylesheet oder Skript geliefert, das den sichtbaren Fokusindikator ohne Ersatz entfernt. |
| A6 | Medien-Templates unterstützen Untertitel und Transkripte auf die in der Designphase vereinbarte Weise. |
| A7 | Der Auftragnehmer liefert einen Redaktionsleitfaden zu Alternativtext, Überschriften, Links, Tabellen und Dokumenten. |
| A8 | Der Auftragnehmer liefert eine Liste der aktivierten Erweiterungen und gibt für jede an, ob ihre öffentlichen Bildschirme getestet wurden. |

## Musteranforderungen an Nachweise

- „Beschreiben Sie den Status der Barrierefreiheit des angebotenen CMS mit den eigenen Worten des Herstellers oder
  Projekts, mit einem Link.“ Eine gute Antwort zu Exponential wiederholt „Teilweise erfüllt“ und „Nicht geprüft“ und
  behauptet kein VPAT.
- „Beschreiben Sie Ihre Testmethode (Werkzeuge, assistive Technologien, Stichprobe) und legen Sie ein anonymisiertes
  Beispiel eines Testberichts aus einem früheren Projekt vor.“
- „Nennen Sie die Personen, die testen werden, und ihre Qualifikation.“

## Mustervertragsklauseln

- **Abnahme**: Der Auftraggeber (oder eine von ihm benannte prüfende Stelle) bewertet die Website vor der Abnahme mit
  WCAG-EM gegen WCAG 2.2 AA; Befunde des Schweregrads blockierend oder schwerwiegend verhindern die Abnahme.
- **Mängel**: Verstöße gegen die Barrierefreiheit sind Mängel im Sinne der Gewährleistung und der Supportvereinbarung,
  mit denselben Reaktionszeiten wie funktionale Mängel.
- **Regression**: Jedes Release, das Templates ändert, umfasst eine automatische Prüfung und einen Tastaturdurchgang auf
  den geänderten Templates; die Ergebnisse werden übergeben.
- **Nachweise**: Der Auftragnehmer übergibt Testberichte und hält sie für die Laufzeit des Vertrags aktuell.
- **Upgrades**: Aktualisiert der Auftragnehmer Exponential oder eine Erweiterung, testet er die betroffenen Seiten
  erneut.

## Die Antworten bewerten

Bewerten Sie eine Antwort niedriger, die ohne Nachweis volle Konformität des CMS oder ein VPAT für Exponential
behauptet: Das Projekt selbst erhebt keinen solchen Anspruch. Bewerten Sie eine Antwort mit einer konkreten
Testmethode, benannten Werkzeugen und Beispielen höher.

## Verwandte Seiten

- [Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md)
- [Die Barrierefreiheit Ihrer Website testen](../../../../eng-us/help/creators/website-creators/testing-accessibility.md)
- [Implementierungspartner](../../partner/implementierungspartner.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
