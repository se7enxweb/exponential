---
title: "Lektion 4.5: Die Review-Checkliste und die Design-Tests"
audience: trainer, designer, developer
category: training
order: 45
summary: Die Teilnehmenden prüfen das Administrations-Template und das Stylesheet eines Partners mit der Code-Review-Checkliste, kontrollieren die Seite im hellen und im dunklen Modus bei 1440 px und bei 960 px mit 200 % Zoom und führen die Design-Tests ohne Datenbank aus. 45 Minuten.
keywords: theme-design, admin4, code-review-checkliste, AdminStylingRulesTest, design-testsuite, phpunit, testsuite design, heller und dunkler modus, 200 % zoom, 960 px, 1440 px, kontrast, horizontaler überlauf, konsolenfehler, allowlist, theme design, code review checklist, design test suite, light and dark mode, contrast, horizontal overflow, console errors
type: help
---
# Lektion 4.5: Die Review-Checkliste und die Design-Tests

Regeln helfen nur, wenn jemand sie prüft. Für die Administrationsdesigns tun das zwei Dinge: eine Testsuite, die
Templates und Stylesheets ohne Datenbank liest, und eine Checkliste, die ein Reviewer bei jeder Änderung durchgeht.
Diese Lektion schließt das Modul mit einem Peer-Review des eigenen Dashboard-Blocks der Teilnehmenden ab.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Theme-Design](../README.md) |
| Modul | [Modul 4: Die Administration gestalten](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Designer und Entwickler, die Administrationsseiten bauen oder prüfen |
| Voraussetzungen | [Lektion 4.4](modul-und-erweiterungs-stylesheets.md); ein Entwicklungs-Checkout mit den Entwicklungsabhängigkeiten (für Schritt 9) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- ein Administrations-Template und ein Stylesheet mit der Code-Review-Checkliste prüfen;
- eine Seite im hellen und im dunklen Modus bei 1440 px und bei 960 px mit 200 % Zoom kontrollieren;
- die Design-Testsuite ausführen und einen Fehlschlag lesen;
- erklären, warum die Allowlist des Tests nur kürzer wird.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Die Administration gestalten, Code-Review-Checkliste](../../../../bc/6.0/admin4-gestaltung.md#checkliste-für-das-code-review)
und [In CI geprüft](../../../../bc/6.0/admin4-gestaltung.md#in-der-ci-geprüft).

1. **Die Checkliste** (6 Minuten). Projizieren Sie sie und gliedern Sie sie: Markup (kein Inline-CSS, gemeinsame
   Komponenten, Icons als `.a4-icon` mit `.a4-bi-*`, Popups über das gemeinsame Kontextmenü), Namen (jedes
   Bedienelement und jedes Feld hat einen zugänglichen Namen, Kopfzellen haben `scope`), CSS (nur Tokens, neue Tokens
   mit einem dunklen Wert, `--a4-z-*`, Konturen für erzwungene Farben), Tabellen und Hinweise (`table.list` oder
   `.a4-table`; Hinweise mit `role="alert"` oder `role="status"`) und die abschließenden Prüfungen (hell und dunkel bei
   1440 px und 960 px mit Skalierung 2, der Design-Test, ein Deploy mit `--packer`).
2. **Die Sichtprüfung** (3 Minuten). Bei 1440 px und bei 960 px mit 200 % Zoom, im hellen und im dunklen Modus: keine
   helle Karte auf der dunklen Seite, Textkontrast mindestens 4,5:1, kein horizontaler Überlauf, keine Konsolenfehler.
3. **Die Tests** (4 Minuten). `tests/tests/design/AdminStylingRulesTest.php` (Testsuite `design`, keine Datenbank)
   schlägt fehl bei einem `<style>`-Block oder `style=""` in den Templates von `admin4`, `admin4l`, `admin3` und
   `admin`, bei Literalfarben und Radien in den Modul-Stylesheets, bei einem Farb-Token ohne dunklen Wert und mehr.
   Führen Sie ihn aus oder die ganze Suite:

   ```bash
   php vendor/bin/phpunit tests/tests/design/AdminStylingRulesTest.php
   php vendor/bin/phpunit --testsuite design
   ```

4. **Die Allowlist** (2 Minuten). Templates, die noch migriert werden, stehen im Test; die Liste wird nur kürzer.
   Niemand schwächt den Test ab oder ergänzt einen Eintrag, damit eine Änderung durchgeht: Stattdessen wird das CSS
   verschoben.

### Häufige Fragen

**Prüfen die Tests meine Erweiterung?** Sie prüfen die Kerndesigns. Die Checkliste deckt Erweiterungen ab; ein
Reviewer wendet sie genauso auf `extension/*/design/admin*` an.

**Die Schulungsinstallation hat kein `vendor/bin/phpunit`.** Die Tests brauchen einen Entwicklungs-Checkout mit den
Entwicklungsabhängigkeiten. Führen Sie Schritt 9 auf dem Rechner des Trainers aus und zeigen Sie das Ergebnis, oder
lassen Sie ihn aus.

**Wie bekomme ich 960 px bei 200 %?** Machen Sie das Browserfenster 1920 px breit und zoomen Sie auf 200 %, oder
verwenden Sie die Gerätesymbolleiste der Entwicklerwerkzeuge mit einer Breite von 960 und einem Pixelverhältnis von 2.

### Stolperfallen

- **Nur das Template prüfen.** Das Stylesheet und der Dunkelmodus sind die Hälfte der Checkliste.
- **Freigeben, „weil es gut aussieht“.** Suchen Sie die versteckten Fälle: ein fehlendes `scope`, ein `title` als
  einziger Name, ein `#fff` in einer Regel, die auf der getesteten Seite nicht angezeigt wird.
- **Die ganze Suite auf einem ausgelasteten gemeinsamen Server ausführen.** Führen Sie eine einzelne Testdatei aus, mit
  `nice -n 15`.

## Praktische Übung

Paarweise. Alle prüfen `training_notes.tpl`, `dashboard.ini.append.php` und `training_admin.css` ihres Partners aus den
Lektionen 4.2 bis 4.4, auf der Installation des Partners oder anhand der geteilten Dateien.

1. Öffnen Sie die Checkliste in [Die Administration gestalten](../../../../bc/6.0/admin4-gestaltung.md#checkliste-für-das-code-review).
2. Template: Prüfen Sie auf einen `<style>`-Block, ein Attribut `style=""` und Inline-Handler (`onclick` und
   Ähnliches).
3. Template: Prüfen Sie, dass jede Kopfzelle der Tabelle `scope="col"` hat, dass die leere Liste `.a4-empty` verwendet
   und dass Links sichtbaren Text haben.
4. Stylesheet: Prüfen Sie auf Literalfarben, Radien und Längen (außer Kanten von 1 bis 3 px), lokale Custom
   Properties, numerische `z-index`-Werte und `left`/`right`.
5. Stylesheet: Prüfen Sie, dass der aktuelle Zustand seine Kontur in `@media (forced-colors: active)` hat.
6. Stellen Sie den Browser auf 1440 px Breite bei 100 % ein, öffnen Sie das Dashboard des Partners und prüfen Sie den
   Block im hellen und im dunklen Modus.
7. Stellen Sie 960 px mit 200 % Zoom (oder einem Pixelverhältnis von 2) ein und prüfen Sie erneut: keine horizontale
   Scrollleiste, Text lesbar.
8. Öffnen Sie die Konsole und notieren Sie Fehler.
9. Wenn ein Entwicklungs-Checkout verfügbar ist, führen Sie
   `nice -n 15 php vendor/bin/phpunit tests/tests/design/AdminStylingRulesTest.php` aus und lesen Sie die
   Ergebniszeile.
10. Schreiben Sie Ihre Befunde als kurzes Review auf: jeden Befund mit dem Punkt der Checkliste und einem
    Korrekturvorschlag. Geben Sie es Ihrem Partner, der das Gefundene behebt und Sie bittet, erneut zu prüfen.

## Erwartetes Ergebnis

- Jedes Paar hat ein schriftliches Review mit keinem oder mehreren Befunden, jeder mit einem Punkt der Checkliste.
- Nach den Korrekturen bestehen beide Blöcke jeden zutreffenden Punkt der Checkliste, in beiden Modi und beiden Größen.
- Schritt 9 (wo ausgeführt): Der Test endet mit `OK`.
- Der Trainer sammelt ein Review pro Paar ein und bespricht den häufigsten Befund mit der Gruppe.

## Quiz

1. Nennen Sie die vier Dinge, auf die die Sichtprüfung bei jeder Größe und in jedem Modus achtet.
2. Welcher Test schlägt fehl, wenn ein Template von admin4 ein Attribut `style=""` bekommt?
   A. `AdminKeyboardTest` B. `AdminStylingRulesTest` C. `AdminLandmarksTest` D. `AdminLogoTest`
3. Warum wird die Allowlist dieses Tests nur kürzer?
4. Braucht die Design-Testsuite eine Datenbank?
5. Welche Deploy-Option gehört zum letzten Punkt der Checkliste?

<details>
<summary>Antworten</summary>

1. Keine helle Karte auf der dunklen Seite, Textkontrast mindestens 4,5:1, kein horizontaler Überlauf, keine
   Konsolenfehler ([Die Administration gestalten](../../../../bc/6.0/admin4-gestaltung.md#die-regeln)).
2. **B.**
3. Sie listet Templates, die noch migriert werden; sie zu ergänzen würde neues Inline-CSS verstecken, statt es zu
   verschieben.
4. Nein; sie liest die Quellen von Templates und Stylesheets.
5. `--packer` (`exp:velocity deploy --packer`), und kein Versionsparameter an einem Link.

</details>

## Weiterführende Literatur

- [Die Administration gestalten](../../../../bc/6.0/admin4-gestaltung.md#checkliste-für-das-code-review): die vollständige
  Checkliste.
- [Standards zur Barrierefreiheit](../../../../../eng-us/standards/accessibility.md): welche Design-Tests welche WCAG-Kriterien
  absichern.
- [Visuelle Regressionsprüfungen](../../../../../eng-us/help/designers/lifecycle/visual-regression-checks.md): Screenshots im Zeitverlauf.
- [Standards für Tests und CI](../../../../../eng-us/standards/testing-and-ci.md): wo die Design-Suite läuft.

## Verwandte Seiten

- [Modul 4: Die Administration gestalten](README.md)
- [Theme-Design: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
