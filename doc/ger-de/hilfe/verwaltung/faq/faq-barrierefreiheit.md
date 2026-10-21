---
title: "FAQ: Barrierefreiheit"
audience: government, project-manager, admin, sales-marketing
category: public-sector
order: 20
summary: Antworten auf die Fragen öffentlicher Stellen zur Barrierefreiheit von Exponential - WCAG 2.2, EN 301 549, Section 508, VPATs, die Administration, öffentliche Designs, der Online-Editor, Inhalte und Erklärungen zur Barrierefreiheit -, belegt durch die Seite Standards zur Barrierefreiheit.
keywords: FAQ Barrierefreiheit, WCAG 2.2, AA, EN 301 549, Section 508, BITV, VPAT, ACR, Konformität, admin4, Screenreader, Tastatur, Erklärung zur Barrierefreiheit, öffentlicher Sektor, accessibility FAQ, conformance, screen reader, keyboard, accessibility statement, public sector
type: help
---
# FAQ: Barrierefreiheit

Diese Seite beantwortet die Fragen zur Barrierefreiheit, die Beschaffungsgremien und Beauftragte für Barrierefreiheit
in öffentlichen Stellen zu Exponential stellen. Jede Antwort ist durch die
[Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md) belegt; die dort verwendeten Statusangaben
(„Teilweise erfüllt“; „Nicht geprüft“) werden genau beibehalten.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Erfüllt Exponential WCAG 2.2 AA?](#erfüllt-exponential-wcag-22-aa)
2. [Gibt es ein VPAT oder einen Accessibility Conformance Report?](#gibt-es-ein-vpat-oder-einen-accessibility-conformance-report)
3. [Wie steht es um EN 301 549 und Section 508?](#wie-steht-es-um-en-301-549-und-section-508)
4. [Können Mitarbeitende mit Behinderungen die Administration nutzen?](#können-mitarbeitende-mit-behinderungen-die-administration-nutzen)
5. [Wird unsere öffentliche Website barrierefrei sein?](#wird-unsere-öffentliche-website-barrierefrei-sein)
6. [Ist der Online-Editor barrierefrei?](#ist-der-online-editor-barrierefrei)
7. [Hilft Exponential der Redaktion, barrierefreie Inhalte zu schreiben?](#hilft-exponential-der-redaktion-barrierefreie-inhalte-zu-schreiben)
8. [Gibt es eine Vorlage für eine Erklärung zur Barrierefreiheit?](#gibt-es-eine-vorlage-für-eine-erklärung-zur-barrierefreiheit)
9. [Wie wird die Barrierefreiheit getestet?](#wie-wird-die-barrierefreiheit-getestet)

## Erfüllt Exponential WCAG 2.2 AA?

Nicht als Ganzes, und das Projekt behauptet es auch nicht. Die Administrationsdesigns **admin4** und **admin4l** werden
gegen die Kriterien von WCAG 2.2 AA gebaut und mit Regressionstests geprüft; ihr Status ist **Teilweise erfüllt**
(teilweise erfüllt). Das ist keine Konformitätsaussage: Es gibt keine vollständige Prüfung jeder Seite und kein
Testprotokoll mit assistiven Technologien
([Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md#status)).

## Gibt es ein VPAT oder einen Accessibility Conformance Report?

Nein. Es gibt kein VPAT und keinen Accessibility Conformance Report für Exponential und keinen Bewertungsbericht nach
WCAG-EM ([Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md#known-gaps)). Wenn Ihre Regeln einen
verlangen, bitten Sie den Bieter, eine Bewertung der Website in Auftrag zu geben, die er liefert.

## Wie steht es um EN 301 549 und Section 508?

Beide sind **nicht geprüft**. Der Webabschnitt von EN 301 549 übernimmt WCAG 2.1 AA, das die Arbeit an
admin4 inhaltlich abdeckt, aber es wurde keine Bewertung gegen die Norm durchgeführt, und ihr Abschnitt zu
Autorenwerkzeugen wurde nicht geprüft. Section 508 übernimmt WCAG 2.0 AA; es gibt kein VPAT
([Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md#en-301-549)).

## Können Mitarbeitende mit Behinderungen die Administration nutzen?

Das Design admin4 ist für sie die beste Wahl: ein Sprunglink, Landmarks, beschriftete Bedienelemente,
Tastaturunterstützung für Reiter und Menüs, Fehlerzusammenfassungen, auf Kontrast geprüfte Farb-Token im hellen und
dunklen Modus, Zielflächen von mindestens 24 mal 24 Pixeln und Unterstützung für erzwungene Farben und reduzierte
Bewegung ([Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md#how-exponential-complies)). Die klassische
Administration und admin3 haben weniger Korrekturen erhalten. Siehe [Tastenkürzel](../../tastenkuerzel.md).

## Wird unsere öffentliche Website barrierefrei sein?

Das hängt von dem Design ab, das für Sie gebaut wird, und von Ihren Inhalten. Die öffentlichen Designs des Projekts sind
**nicht geprüft**; ihre Barrierefreiheit liegt in der Verantwortung des Websiteerstellers
([Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md#known-gaps)). Machen Sie WCAG 2.2 AA zur Anforderung
an die gelieferte Website, mit Tests
([Barrierefreiheit für Websiteersteller](../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md),
[Barrierefreiheit testen](../../../../eng-us/help/creators/website-creators/testing-accessibility.md)).

## Ist der Online-Editor barrierefrei?

**Nicht geprüft**. Die Integrationen von TinyMCE wurden nicht auf WCAG geprüft; die Barrierefreiheit des
Editors selbst ist die des Projekts TinyMCE
([Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md#known-gaps)). Testen Sie ihn bei der Bewertung mit
den assistiven Technologien Ihrer Mitarbeitenden.

## Hilft Exponential der Redaktion, barrierefreie Inhalte zu schreiben?

Teilweise. Bildfelder bieten ein Feld für den Alternativtext, aber nichts zwingt die Redaktion, es auszufüllen. Die
Schulung behandelt barrierefreie Inhalte
([Redaktion 201, Modul 8](../../../../eng-us/help/trainers/editors-201/module-08-quality-and-accessibility/README.md)). Wenn Sie Exponential
Intelligence einsetzen, kann es Alternativtexte als Entwurf vorschlagen, den eine Redakteurin oder ein Redakteur prüft
([Exponential Intelligence](../../../../eng-us/features/6.0/extensions/expai.md)).

## Gibt es eine Vorlage für eine Erklärung zur Barrierefreiheit?

Nein. Die EU-Richtlinie über den barrierefreien Zugang zu Websites verlangt, dass öffentliche Stellen eine solche
Erklärung veröffentlichen; Exponential liefert keine Vorlage mit
([Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md#known-gaps-2)). Das W3C bietet Hinweise und einen
Generator unter [w3.org/WAI/planning/statements](https://www.w3.org/WAI/planning/statements/).

## Wie wird die Barrierefreiheit getestet?

Designtests in der Suite `design` prüfen die Quellen von Templates und Stylesheets: Beschriftungen, Landmarks,
Tastaturmuster, Kontrast, Fokus und mehr. Automatische axe-Prüfungen werden bei Reviews von Hand ausgeführt, nicht in
der CI, und im Changelog festgehalten
([Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md#how-exponential-complies)).

## Verwandte Seiten

- [FAQ für Verwaltungen](README.md)
- [Checkliste Barrierefreiheit](../checklisten/checkliste-barrierefreiheit.md)
- [Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
