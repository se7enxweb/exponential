---
title: Standards zur Barrierefreiheit
audience: developer, admin
category: standards
order: 20
summary: Wie Exponential im Vergleich zu WCAG 2.2 AA, WAI-ARIA 1.2, EN 301 549 und Section 508 abschneidet, mit den Templates, Stylesheets und Tests, die es belegen, und den verbleibenden Lücken.
keywords: Barrierefreiheit, Zugänglichkeit, Screenreader, Tastatur, Fokus, Kontrast, Sprunglink, Landmarks, Rechts-nach-links, dunkler Modus, erzwungene Farben, accessibility, a11y, WCAG, WCAG 2.2, AA, WAI-ARIA, ARIA 1.2, APG, ARIA Authoring Practices, EN 301 549, Section 508, VPAT, ACR, screen reader, keyboard, focus, contrast, skip link, landmarks, right-to-left, RTL, dark mode, forced colors, axe
type: reference
---
# Standards zur Barrierefreiheit

Diese Seite richtet sich an Entwickler, die Templates erstellen oder prüfen, und an Administratoren, die für eine
Beschaffung, ein Audit oder ihre eigenen Benutzer die Frage „Ist Exponential barrierefrei?“ beantworten müssen. Sie führt
die Standards zur Barrierefreiheit auf, die für ein Web-Content-Management-System gelten, sagt zu jedem, was
Exponential 6.0.x tut, verweist auf die Dateien und Tests, die es belegen, und benennt die Lücken ehrlich.

> **Hinweis** Exponential ist Software. Ob eine damit erstellte *Website* barrierefrei ist, hängt auch von ihrem eigenen
> Design, ihren Templates und den Inhalten ab, die ihre Redakteure veröffentlichen. Die folgenden Aussagen betreffen die
> Teile, die mit Exponential ausgeliefert werden.

## Zusammenfassung

| Standard | Version | Status | Nachweis (kurz) |
|---|---|---|---|
| Web Content Accessibility Guidelines (WCAG) | 2.2, Stufe AA | **Teilweise konform** | Administrationsoberfläche admin4 / admin4l nach AA-Kriterien gebaut und getestet; klassischer Admin und Standard-Bearbeitungsformular teilweise korrigiert; öffentliche Designs und Online-Editor nicht geprüft |
| WAI-ARIA und der ARIA Authoring Practices Guide (APG) | WAI-ARIA 1.2 | **Teilweise konform** | Landmarks, Reiter-, Aufklapp- und Menümuster in admin4, abgesichert durch `tests/tests/design/AdminKeyboardTest.php`, `AdminLandmarksTest.php`, `AdminContextMenuTest.php` |
| EN 301 549 (Europäische Anforderungen an die Barrierefreiheit von IKT) | v3.2.1 (2021-03) | **Nicht verifiziert** | ihr Web-Abschnitt 9 ist WCAG 2.1 AA, worauf die Arbeit an admin4 abzielt; kein Konformitätsbericht nach dem Standard |
| Section 508 (US Rehabilitation Act, Revised 508 Standards) | Überarbeitung 2017 (WCAG 2.0 AA) | **Nicht verifiziert** | inhaltlich durch die WCAG-Arbeit für admin4 abgedeckt; kein VPAT / Accessibility Conformance Report |

## WCAG 2.2

### Was es ist

Die Web Content Accessibility Guidelines 2.2 sind eine W3C-Empfehlung (erstmals im Oktober 2023 veröffentlicht),
herausgegeben von der W3C Web Accessibility Initiative. Stufe AA ist die Stufe, die Gesetze und Beschaffungsregeln
üblicherweise verlangen. WCAG 2.2 fügt WCAG 2.1 neun Erfolgskriterien hinzu, darunter 2.4.11 Focus Not Obscured,
2.5.7 Dragging Movements und 2.5.8 Target Size (Minimum).

### Wie Exponential sie erfüllt

Die Arbeit konzentriert sich auf das Administrationsdesign **admin4** (`design/admin4/`) und seine Layoutvariante
**admin4l** (`design/admin4l/`). Sie ist in [das Design admin4](../funktionen/6.0/admin4-design.md) und in der Referenz
für Mitwirkende [admin4-Styling](../bc/6.0/admin4-gestaltung.md) beschrieben. Die Design-Tests laufen ohne Datenbank in
der Testsuite `design` von `phpunit.xml` (`tests/tests/design/`), die der PHPUnit-Workflow
(`.github/workflows/phpunit.yml`) mit jeder Suite von `phpunit.xml` ausführt.

| WCAG-Bereich | Was ausgeliefert wird | Nachweis |
|---|---|---|
| 1.1.1 Nicht-Text-Inhalt, 4.1.2 Name, Rolle, Wert | Jedes Formularelement in den admin4-Templates hat einen zugänglichen Namen (`<label for>`, ein umschließendes `<label>`, `aria-label` oder `aria-labelledby`; ein `title` oder `placeholder` zählt nicht). Das Inhaltsbearbeitungsformular des Standard-Designs benennt seine Bedienelemente für Sortierung, Ort, Übersetzung und Beziehungen | `tests/tests/design/AdminFormLabelsTest.php`, `tests/tests/design/StandardContentEditAccessibleNamesTest.php`, `tests/tests/design/AdminRelationListLabelsTest.php` |
| 1.3.1 Info und Beziehungen, 2.4.1 Blöcke umgehen | Ein Sprunglink ist der erste Tab-Stopp (`.a4-skip-link` in `design/admin4/templates/pagelayout.tpl`, „Zum Hauptinhalt springen“); Landmarks für Banner, Navigation, Hauptinhalt und Fußzeile; ein Hauptinhalt-Landmark pro Seite; Tabellenkopfzellen mit `scope="col"` | `tests/tests/design/AdminLandmarksTest.php` |
| 1.3.1, 3.3.1 Fehlererkennung, 3.3.2 Beschriftungen oder Anweisungen | Pflichtfelder tragen eine sichtbare Markierung und das Wort für Screenreader (`.a4-sr-only`); ein fehlgeschlagenes Speichern zeigt eine Fehlerzusammenfassung, die den Fokus erhält und auf die Felder verlinkt; das Skript setzt `aria-required` / `aria-invalid` und richtet `aria-describedby` auf die Meldung | `tests/tests/design/AdminFormErrorsTest.php` |
| 1.4.1 Benutzung von Farbe | Hinweise zeigen ihren Zustand mit einem Icon sowie einer Farbe; Modulmeldungen tragen `role="alert"` oder eine Statusrolle; ein Paar Live-Regionen für Toasts | `tests/tests/design/AdminNoticesTest.php` |
| 1.4.3 Kontrast (Minimum), 1.4.11 Nicht-Text-Kontrast | Die Farb-Tokens von `design/admin4/stylesheets/components.css` (`--a4-*`) werden mit der WCAG-2-Formel im hellen **und** dunklen Modus geprüft: Text 4,5:1 auf jeder Fläche, Fokusring und Feldränder 3:1, Diagrammfarben 3:1 | `tests/tests/design/AdminTokenContrastTest.php` |
| 1.4.4 Textgröße ändern, 1.4.10 Umbruch | admin4 wird bei Telefon- (390 px), Tablet- (820 px), kleiner und breiter Desktop-Breite geprüft; das Projekt prüft Seiten bei 960 px mit 200 % Zoom. Der klassische Admin stapelt seine Spalten unter 760 px | `tests/tests/design/AdminResponsiveTest.php`, `tests/tests/design/ClassicAdminNarrowWindowTest.php` |
| 1.4.13 Inhalt bei Hover oder Fokus | Tooltips sind mit der Maus erreichbar, bleiben bestehen und lassen sich mit Escape schließen | [Design admin4](../funktionen/6.0/admin4-design.md) |
| 2.1.1 Tastatur, 2.4.3 Fokusreihenfolge | Knotenreiter folgen dem APG-Reitermuster (Pfeiltasten, Pos1, Ende); der Umschalter des Inhaltsbaums ist eine benannte Schaltfläche mit `aria-expanded`; Kontextmenüs funktionieren mit der Tastatur; die Kopfzeile hat in admin4 und admin4l dieselbe Tab-Reihenfolge | `tests/tests/design/AdminKeyboardTest.php`, `AdminContextMenuTest.php`, `AdminHeaderTabOrderTest.php`; [Tastenkürzel](../hilfe/tastenkuerzel.md) |
| 2.4.7 Fokus sichtbar | Ein gemeinsamer Fokusring: `.a4-page :focus-visible`, `.a4-focus:focus-visible` und `.a4-btn:focus-visible` in `components.css` (3 px Umriss in `--a4-ring`) | `design/admin4/stylesheets/components.css` |
| 2.5.8 Zielgröße (Minimum) | Ziele sind mindestens 24 x 24 px groß; wo das gezeichnete Bedienelement kleiner ist, vergrößert ein unsichtbares `::after` das Ziel | [admin4-Styling](../bc/6.0/admin4-gestaltung.md), [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md) |
| 3.1.1 Sprache der Seite | `<html lang="...">` aus dem Locale des Siteaccess in jedem Admin-Seitenlayout | `design/admin4/templates/pagelayout.tpl` |
| 1.3.2 Bedeutungstragende Reihenfolge (bidirektionaler Text) | Oberflächensprachen, die von rechts nach links geschrieben werden, setzen `dir="rtl"` auf `<html>`; Namen werden mit `<bdi>` isoliert; von links nach rechts gelesene Werte behalten ihre Reihenfolge | [Sprachen mit Schreibrichtung von rechts nach links](../funktionen/6.0/rechts-nach-links.md), `tests/tests/design/AdminRightToLeftTest.php` |
| Benutzereinstellungen (kein WCAG-Kriterium, unterstützt aber 1.4.3 und 2.3.3) | Heller, dunkler und „System“-Modus (`prefers-color-scheme`, `design/admin4/javascript/a4_head.js`); Regeln `@media (forced-colors: active)` und `@media (prefers-reduced-motion: reduce)` in `design/admin4/stylesheets/admin4.css` | `tests/tests/design/AdminThemeSwitchTest.php` |

Das Hilfesystem folgt denselben Regeln: ein `h1` pro Seite, benannte Navigations-Landmarks, beschriftete Suchfelder, ein
sichtbarer Fokusring, ein Hinweis bei Links, die ein neues Fenster öffnen, und Suchergebnisse, die Screenreadern
angesagt werden ([Hilfesystem: Sprachen und Barrierefreiheit](../funktionen/6.0/hilfesystem.md#sprachen-und-barrierefreiheit)).

Eine Zählung der ARIA-Attribute in `design/admin4/templates/` (Oktober 2026) gibt eine Vorstellung von der Abdeckung:
etwa 400 Verwendungen von `aria-labelledby`, 390 von `aria-hidden`, 380 von `aria-label`, 215 von `aria-describedby`,
110 von `aria-current` und 65 von `aria-live`.

Automatisierte Prüfungen mit der axe-Engine werden bei Reviews von Hand ausgeführt, nicht in der CI. Ihre Ergebnisse
sind im [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md) festgehalten: zum Beispiel „axe meldet im Seitenrahmen auf 10
klassischen Seiten bei 1440 px und bei 960 px mit 200 % Zoom keinen schwerwiegenden Befund“ (klassischer Admin, #522),
13 schwerwiegende Befunde im Standard-Inhaltsbearbeitungsformular behoben, ein Fehler bei der Zielgröße und ein
Kontrastfehler bei erzwungenen Farben behoben.

### Status

**Teilweise konform.** Die Administrationsoberfläche admin4 und admin4l ist nach den Kriterien von WCAG 2.2 AA gestaltet
und mit Regressionstests abgesichert. Das ist nicht dasselbe wie eine Konformitätserklärung: Es gibt kein vollständiges
Audit jeder Seite, kein Testprotokoll mit assistiven Technologien, und mehrere Teile des Produkts wurden nicht geprüft
(siehe unten).

### Bekannte Lücken

- **Öffentliche Website-Designs sind nicht geprüft.** `design/standard`, `design/base`, `design/plain`, `design/mysite`
  und die Website-Designs, die als Pakete kommen (ezwebin, die Medien-Themes, Starter-Websites), haben nur gezielte
  Korrekturen erhalten (das Standard-Inhaltsbearbeitungsformular). Ihre Barrierefreiheit liegt in der Verantwortung
  dessen, der die Website erstellt.
- **Die klassische Administrationsoberfläche (`design/admin`) und admin3** haben Korrekturen bei Beschriftungen und für
  schmale Fenster erhalten, werden aber nicht von den Kontrast-, Landmark- und Formularfehlertests abgedeckt, die admin4
  absichern.
- **Online-Editor.** Die Integrationen von TinyMCE 8 und TinyMCE 3 (`extension/ezoe`) wurden nicht auf WCAG geprüft;
  die Barrierefreiheit der Editor-Engine selbst ist die des TinyMCE-Projekts. **Nicht verifiziert.**
- **Extensions**, die als separate Pakete ausgeliefert werden (Layout-Editor, Newsletter, Shop), werden einzeln
  geprüft; einige Korrekturen sind im Changelog festgehalten, eine Gesamtaussage gibt es nicht.
- **Keine automatisierte Engine zur Barrierefreiheit in der CI.** Die Tests lesen Template- und Stylesheet-Quellen; sie
  rendern keine Seiten in einem Browser. axe-Läufe erfolgen manuell.
- **Kein veröffentlichter Konformitätsbericht** (WCAG-EM-Evaluierung, VPAT oder Accessibility Conformance Report).
- **Inhalte.** Alternativtext, Überschriftenreihenfolge und Linktext in veröffentlichten Inhalten hängen von den
  Redakteuren ab. Die Bild-Datentypen bieten ein Feld für Alternativtext; nichts zwingt Redakteure, es auszufüllen.

## WAI-ARIA 1.2 und die ARIA Authoring Practices

### Was es ist

Accessible Rich Internet Applications (WAI-ARIA) 1.2 ist eine W3C-Empfehlung (Juni 2023), die Rollen, Zustände und
Eigenschaften für assistive Technologien definiert. Der ARIA Authoring Practices Guide (APG), eine Note des W3C WAI,
zeigt, wie man damit gängige Widgets (Reiter, Menüs, Aufklappelemente, Dialoge) baut.

### Wie Exponential sie erfüllt

- **Reitermuster**: Knotenreiter in admin4 (`design/admin4/javascript/node_tabs.js`), abgesichert durch
  `tests/tests/design/AdminKeyboardTest.php`.
- **Aufklapp-Navigation**: Das optionale Mega-Menü (`menu.ini [TopAdminMenu] MegaMenuTabs[]`) belässt den Reiter als
  Link und fügt daneben eine Schaltfläche mit `aria-expanded` hinzu (`tests/tests/design/AdminMegaMenuTest.php`).
- **Menü**: Die Kontextmenüs (`ezpopupmenu.js`) haben Rollen und Tastaturunterstützung
  (`tests/tests/design/AdminContextMenuTest.php`).
- **Umschaltflächen**: `aria-pressed` für die Engine-Umschaltung des Editors und segmentierte Bedienelemente
  ([TinyMCE 8](../funktionen/6.0/online-editor-tinymce8.md)).
- **Live-Regionen**: Toasts und Ergebnisse der Hilfesuche (`aria-live`).
- **Erste Regel von ARIA**: Native Elemente werden bevorzugt. Der Test für Formularbeschriftungen akzeptiert keinen
  `title` als Namen, und `aria-label` darf keine sichtbare Beschriftung mit Text ersetzen (`AdminFormLabelsTest.php`).

### Status

**Teilweise konform** für admin4 und admin4l, wo die Muster an den Template-Quellen getestet werden. **Nicht
verifiziert** an anderer Stelle; klassische Designs enthalten noch ältere JavaScript-Widgets.

### Bekannte Lücken

- Die Tests prüfen das Markup und die Ansatzpunkte, die die Skripte verwenden, nicht das Laufzeitverhalten in einem
  Screenreader.
- Kein Test gleicht jedes Widget mit seinem APG-Muster ab.

## EN 301 549

### Was es ist

EN 301 549 „Accessibility requirements for ICT products and services“, v3.2.1 (2021-03), herausgegeben von ETSI, CEN
und CENELEC. Sie ist die harmonisierte Norm der EU-Richtlinie über den barrierefreien Zugang zu Websites (2016/2102)
und die Referenz für den European Accessibility Act (2019/882). Ihr Abschnitt 9 (Web) übernimmt WCAG 2.1 Stufe AA;
Abschnitt 11 (Software) und Abschnitt 12 (Dokumentation und Support) gelten auch für Autorenwerkzeuge.

### Wie Exponential sie erfüllt

Die Arbeit an WCAG 2.2 AA für admin4 deckt die Kriterien von WCAG 2.1 AA ab, die Abschnitt 9 verlangt (WCAG 2.2 ist
eine Obermenge, außer bei 4.1.1 Parsing, das 2.2 gestrichen hat). Die Dokumentation des Hilfesystems folgt denselben
Regeln.

### Status

**Nicht verifiziert.** Es wurde keine Evaluierung nach EN 301 549 durchgeführt, und Abschnitt 11.8 (Autorenwerkzeuge,
zum Beispiel, dass das Werkzeug Autoren hilft, barrierefreie Inhalte zu erstellen) wurde nicht geprüft.

### Bekannte Lücken

- Keine Vorlage für eine Erklärung zur Barrierefreiheit für Websites, wie sie die Richtlinie über den barrierefreien
  Zugang zu Websites von öffentlichen Stellen verlangt.
- Abschnitt 11.8 (Unterstützung durch das Autorenwerkzeug: Aufforderungen zu Alternativtext, barrierefreie Ausgabe von
  Rich Text) nicht bewertet.

## Section 508

### Was es ist

Section 508 des US Rehabilitation Act verpflichtet Bundesbehörden, barrierefreie IKT zu beschaffen. Die Revised 508
Standards (US Access Board, 2017) übernehmen WCAG 2.0 Stufe AA per Verweis. Anbieter dokumentieren die Konformität
üblicherweise in einem VPAT (Voluntary Product Accessibility Template), aus dem ein Accessibility Conformance Report
entsteht.

### Wie Exponential sie erfüllt

Inhaltlich durch die oben beschriebene WCAG-Arbeit; WCAG 2.0 AA ist eine Teilmenge dessen, wogegen admin4 getestet
wird.

### Status

**Nicht verifiziert.** Es gibt kein VPAT und keinen Accessibility Conformance Report für Exponential.

## Für Template-Autoren

- Folgen Sie dem [admin4-Styling](../bc/6.0/admin4-gestaltung.md): gemeinsame Komponenten (`.a4-btn`, `.a4-card`,
  `.a4-sr-only`), nur Tokens, ein dunkler Wert für jedes neue Farb-Token, kein Inline-CSS.
- Führen Sie nach einer Template-Änderung die Design-Tests aus:

  ```bash
  php vendor/bin/phpunit --testsuite design
  ```

- Prüfen Sie eine geänderte Seite im hellen und dunklen Modus, bei 1440 px und bei 960 px mit 200 % Zoom sowie allein
  mit der Tastatur.

## Verwandte Seiten

- [Sicherheitsstandards](sicherheit.md), [Datenschutzstandards](datenschutz.md), [Standards der Webplattform](webplattform.md)
- [Das Design admin4](../funktionen/6.0/admin4-design.md)
- [Sprachen mit Schreibrichtung von rechts nach links](../funktionen/6.0/rechts-nach-links.md)
- [Tastenkürzel](../hilfe/tastenkuerzel.md)
