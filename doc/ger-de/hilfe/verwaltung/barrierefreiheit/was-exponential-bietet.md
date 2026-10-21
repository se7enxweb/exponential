---
title: Was Exponential für die Barrierefreiheit bietet
audience: government, project-manager, admin, developer
category: public-sector
order: 20
summary: Für öffentliche Stellen, die die Frage „Ist Exponential barrierefrei?“ beantworten müssen - der Status von WCAG 2.2 AA, WAI-ARIA 1.2, EN 301 549 und Section 508, wie das Projekt ihn dokumentiert, was getestet wird und was in Ihrer Verantwortung bleibt.
keywords: Barrierefreiheit, barrierefrei, WCAG 2.2, AA, EN 301 549, Section 508, WAI-ARIA, Status, teilweise konform, nicht verifiziert, admin4, öffentlicher Sektor, Verwaltung, Konformität, accessibility, a11y, status, partly complies, unverified, public sector, government, conformance
type: reference
---
# Was Exponential für die Barrierefreiheit bietet

Diese Seite richtet sich an Projektverantwortliche, die Koordination Barrierefreiheit und die Administration einer
öffentlichen Stelle, die angeben müssen, wie weit Exponential ihre Pflichten zur Barrierefreiheit unterstützt. Sie fasst
die eigene Erklärung des Projekts zusammen, [Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md),
übernimmt deren Statusangaben genau so, wie sie dort stehen, und sagt, was das für eine Website bedeutet, die Sie bauen
und betreiben.

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Der Status in einer Tabelle](#der-status-in-einer-tabelle)
2. [Was „Partly complies“ und „Unverified“ bedeuten](#was-partly-complies-und-unverified-bedeuten)
3. [Was getestet wird und wie](#was-getestet-wird-und-wie)
4. [Software gegenüber Website](#software-gegenüber-website)
5. [Was Sie einem Beschaffungsgremium oder einer Prüferin sagen](#was-sie-einem-beschaffungsgremium-oder-einer-prüferin-sagen)
6. [Verwandte Seiten](#verwandte-seiten)

## Der Status in einer Tabelle

Die Tabelle wiederholt die Zusammenfassung von
[Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md#summary). Werten Sie diese Angaben in Ihren
eigenen Dokumenten nicht auf.

| Standard | Version | Status in der Erklärung des Projekts | Umfang der Nachweise |
|---|---|---|---|
| WCAG | 2.2, Stufe AA | **Partly complies** | die Administrationsdesigns admin4 und admin4l, gegen AA-Kriterien gebaut und durch Regressionstests abgesichert; klassische Administration und das Standard-Bearbeitungsformular teilweise korrigiert |
| WAI-ARIA und die ARIA Authoring Practices | WAI-ARIA 1.2 | **Partly complies** | Landmarks, Muster für Reiter, Baum, Aufklappen und Menüs in admin4 |
| EN 301 549 | v3.2.1 (2021-03) | **Unverified** | kein Konformitätsbericht gegen die Norm |
| Section 508 (Revised 508 Standards) | Überarbeitung von 2017 | **Unverified** | kein VPAT und kein Accessibility Conformance Report |

## Was „Partly complies“ und „Unverified“ bedeuten

- **Partly complies** (teilweise konform) bedeutet: Das Projekt hat die Administration gegen die Kriterien von WCAG 2.2
  AA entworfen und sichert die Ergebnisse mit automatischen Tests ab, aber es gibt keine vollständige Prüfung jeder
  Seite, kein Testprotokoll mit assistiven Technologien, und mehrere Teile wurden nicht überprüft. Die Erklärung sagt
  deutlich, dass dies „nicht dasselbe wie ein Konformitätsanspruch“ ist.
- **Unverified** (nicht verifiziert) bedeutet: Niemand hat Exponential gegen diesen Standard bewertet. Die Arbeit an
  WCAG mag inhaltlich vieles davon abdecken, aber es gibt keine Nachweise, die Sie einer Prüferin oder einem Prüfer
  übergeben könnten.
- Es gibt **kein VPAT, keinen Accessibility Conformance Report (ACR) und keinen Bewertungsbericht nach WCAG-EM** für
  Exponential. Verlangt eine Ausschreibung einen solchen, sagen Sie das; was Sie selbst erstellen können, steht unter
  „Einen Accessibility Conformance Report für Ihre Website vorbereiten“ in diesem Bereich.

## Was getestet wird und wie

Die Designtests in `tests/tests/design/` laufen ohne Datenbank in der Testsuite `design` von `phpunit.xml`. Sie lesen
die Quellen der Templates und Stylesheets; sie stellen keine Seiten in einem Browser dar. Darunter:

| Test | Sichert ab |
|---|---|
| `AdminFormLabelsTest.php` | jedes Formularelement in den Templates von admin4 hat einen zugänglichen Namen |
| `AdminLandmarksTest.php` | Sprunglink, Landmarks für Banner, Navigation, Hauptinhalt und Fußzeile, `scope="col"` |
| `AdminFormErrorsTest.php` | Fehlerübersicht, die den Fokus erhält, `aria-invalid`, `aria-describedby` |
| `AdminTokenContrastTest.php` | Farb-Tokens: 4,5:1 für Text, 3:1 für Fokusrahmen und Feldränder, heller und dunkler Modus |
| `AdminKeyboardTest.php`, `AdminContextMenuTest.php` | Reitermuster, Umschalten im Baum, Kontextmenüs per Tastatur |
| `AdminResponsiveTest.php` | admin4 in Breiten für Telefon, Tablet und Desktop |
| `AdminRightToLeftTest.php` | `dir="rtl"` und isolierte Namen in Oberflächensprachen von rechts nach links |

Automatische Prüfungen mit der Engine axe werden bei Reviews von Hand ausgeführt, **nicht in der CI**; ihre Ergebnisse
sind im [Changelog von 6.0.15](../../../../changelogs/6.0/6.0.15.md) festgehalten. Entwicklerinnen und Entwickler
können die Suite so ausführen:

```bash
php vendor/bin/phpunit --testsuite design
```

## Software gegenüber Website

Die Erklärung beginnt mit dem entscheidenden Punkt: Ob eine mit Exponential gebaute *Website* barrierefrei ist, hängt
auch von ihrem eigenen Design, ihren Templates und Inhalten ab. In der Praxis:

| Teil | Wer dafür einsteht | Status |
|---|---|---|
| Administration admin4 / admin4l (Werkzeug der Redaktion) | das Exponential-Projekt | Partly complies (WCAG 2.2 AA) |
| Klassische Administration (`design/admin`), admin3 | das Exponential-Projekt | teilweise korrigiert, nicht von den Tests für Kontrast, Landmarks und Formularfehler abgedeckt |
| Online-Editor (TinyMCE 8 und 3 in `extension/ezoe`) | das Projekt TinyMCE und Exponential | Unverified |
| Öffentliche Designs (`design/standard`, paketierte Website-Designs) | **Sie**, als Website-Ersteller | nicht geprüft |
| Inhalte: Alternativtext, Überschriften, Links, Dokumente | **Ihre Redaktion** | hängt von ihr ab |
| Als eigene Pakete ausgelieferte Erweiterungen | jede Erweiterung | einzeln überprüft, keine Gesamterklärung |

## Was Sie einem Beschaffungsgremium oder einer Prüferin sagen

Eine faire, belastbare Antwort, in Ihren eigenen Worten:

1. Die Administration der Redaktion (admin4) ist gegen WCAG 2.2 AA entworfen und durch Regressionstests abgesichert;
   das Projekt bewertet sie mit „Partly complies“ und erhebt keinen Konformitätsanspruch.
2. Die Konformität mit EN 301 549 und Section 508 ist „Unverified“; es gibt kein VPAT und keinen ACR.
3. Die öffentliche Website wird von uns (oder unserem Lieferanten) mit WCAG-EM bewertet, und wir veröffentlichen eine
   Erklärung zur Barrierefreiheit dafür.
4. Die uns bekannten Lücken stehen in der Erklärung des Projekts, und wir verfolgen sie in unserem eigenen Plan.

## Verwandte Seiten

- [Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md), die Quelle jedes Status auf dieser Seite
- [Das Design admin4](../../../../eng-us/features/6.0/admin4-design.md)
- [Die Barrierefreiheit Ihrer Website testen](../../../../eng-us/help/creators/website-creators/testing-accessibility.md)
- [Barrierefreiheit für Website-Ersteller](../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
