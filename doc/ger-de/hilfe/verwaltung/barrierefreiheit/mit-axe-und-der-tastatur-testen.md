---
title: Mit axe und der Tastatur testen
audience: government, developer, designer, project-manager
category: public-sector
order: 250
summary: Für Teams, die eine öffentliche Website von Release zu Release barrierefrei halten - eine wiederholbare Routine aus automatischen Prüfungen mit einem Werkzeug auf Basis von axe und manuellen Durchgängen mit Tastatur und Screenreader, wie das Exponential-Projekt selbst testet und wie Sie Ergebnisse festhalten.
keywords: axe, automatisierte Tests, Test der Barrierefreiheit, Tastaturtest, Screenreader, Regression, Release-Routine, Designtests, CI, manuelle Prüfung, Testprotokoll, WCAG, automated testing, accessibility testing, keyboard test, screen reader, regression, release routine, design tests, phpunit, manual review, test log
type: help
---
# Mit axe und der Tastatur testen

Diese Seite richtet sich an Entwicklung, Design und Projektverantwortliche, die Tests der Barrierefreiheit zur Routine
statt zum Ereignis machen wollen. Automatische Werkzeuge finden einen Teil der Probleme; ein Durchgang mit Tastatur und
Screenreader findet einen Großteil des Rests. Exponential liefert kein Testwerkzeug für die Barrierefreiheit
öffentlicher Websites mit; die folgende Routine verwendet verbreitete externe Werkzeuge und die Praxis, der das Projekt
für seine eigene Administration folgt.

## Inhalt

1. [Wie das Projekt testet](#wie-das-projekt-testet)
2. [Automatische Prüfungen auf Ihrer Website](#automatische-prüfungen-auf-ihrer-website)
3. [Die manuellen Durchgänge](#die-manuellen-durchgänge)
4. [Wann Sie testen](#wann-sie-testen)
5. [Ergebnisse festhalten](#ergebnisse-festhalten)
6. [Verwandte Seiten](#verwandte-seiten)

## Wie das Projekt testet

- **Quelltests** in `tests/tests/design/` (die Suite `design` von `phpunit.xml`) prüfen Templates und Stylesheets der
  Administration: Beschriftungen, Landmarks, Tastatur-Einstiegspunkte, Kontrast-Tokens, Fehlerübersichten. Sie stellen
  keine Seiten dar.
- **axe läuft von Hand** bei Reviews, nicht in der CI; Befunde und Korrekturen werden im
  [Changelog von 6.0.15](../../../../changelogs/6.0/6.0.15.md) festgehalten.
- Seiten werden im hellen und dunklen Modus geprüft, bei 1440 px und bei 960 px mit 200 % Zoom, und allein mit der
  Tastatur ([Standards zur Barrierefreiheit: Für Template-Autoren](../../../../eng-us/standards/accessibility.md#for-template-authors)).

Wenn Sie die Administration erweitern, führen Sie nach Template-Änderungen die Suite des Projekts aus:

```bash
php vendor/bin/phpunit --testsuite design
```

## Automatische Prüfungen auf Ihrer Website

1. Führen Sie eine Liste von URLs: eine je Seiten-Template, Formular und Sprache (siehe „Ihr öffentliches Design mit
   WCAG-EM prüfen“).
2. Lassen Sie auf jeder URL ein Prüfwerkzeug auf Basis von axe laufen: eine Browsererweiterung für spontane Prüfungen,
   ein Kommandozeilenwerkzeug für die Liste. Halten Sie Werkzeug und Version fest.
3. Lassen Sie es in Telefonbreite und in jedem Farbschema laufen.
4. Behandeln Sie jede „violation“ als Befund; lesen Sie jeden Punkt „needs review“ von Hand.

Automatische Werkzeuge können nicht beurteilen, ob ein Alternativtext sinnvoll ist, ob die Lesereihenfolge Sinn ergibt
oder ob ein Linktext klar ist.

## Die manuellen Durchgänge

Folgen Sie [Die Barrierefreiheit Ihrer Website testen](../../../../eng-us/help/creators/website-creators/testing-accessibility.md):

- **Tastatur**: Sprunglink, Reihenfolge, sichtbarer Fokus, Menüs, keine Fallen.
- **Screenreader**: Seitentitel, Landmarks und Überschriften, Bilder, Formularbeschriftungen und Fehler,
  Sprachwechsel.
- **Zoom, Farbe und Bewegung**: 200 % und 320 px, Kontrast, Farbe nicht allein, reduzierte Bewegung.

## Wann Sie testen

| Ereignis | Test |
|---|---|
| Vor dem Start | vollständige Bewertung nach WCAG-EM |
| Jedes Release, das Templates, Stylesheets oder Skripte ändert | automatische Liste + Tastaturdurchgang auf den geänderten Templates |
| Upgrade von Exponential oder einer Erweiterung, die öffentliche Seiten darstellt | automatische Liste + Tastaturdurchgang auf den betroffenen Seiten |
| Monatlich | Stichprobe neuer Inhalte (siehe „Überwachung und Rückmeldungen“) |
| Nach der Meldung einer Barriere | die gemeldete Seite und ihr Template |

## Ergebnisse festhalten

Verwenden Sie eine Tabelle für alle Tests, damit Entwicklungen sichtbar werden:

| Datum | URL oder Aufgabe | Werkzeug oder Durchgang | Kriterium | Befund | Quelle (Template / Inhalt / Erweiterung) | Behoben in |
|---|---|---|---|---|---|---|

Bewahren Sie die Tabelle bei den Nachweisen für die Erklärung zur Barrierefreiheit auf.

## Verwandte Seiten

- [Die Barrierefreiheit Ihrer Website testen](../../../../eng-us/help/creators/website-creators/testing-accessibility.md)
- [Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
