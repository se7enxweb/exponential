---
title: "Gesprächspunkte: Barrierefreiheit"
audience: advocate
category: advocacy
order: 17
summary: Belegte Gesprächspunkte zur Barrierefreiheit der Administration von Exponential (Tastatur, Landmarks, Tests für Kontrast und Zielgröße, Dunkelmodus, erzwungene Farben, reduzierte Bewegung, von rechts nach links, Alternativtext), mit dem, was die Quellen über öffentliche Websites behaupten und was nicht.
keywords: Gesprächspunkte, Barrierefreiheit, a11y, WCAG, Tastatur, Screenreader, Kontrast, Zielgröße, Dunkelmodus, hoher Kontrast, erzwungene Farben, reduzierte Bewegung, Alternativtext, von rechts nach links, talking points, accessibility, keyboard, screen reader, contrast, target size, dark mode, high contrast, forced colors, reduced motion, alternative text, right to left
type: help
---
# Gesprächspunkte: Barrierefreiheit

Diese Seite ist für Fürsprecher, die mit Menschen sprechen, denen Barrierefreiheit wichtig ist: Menschen mit
Behinderungen, Designer, öffentliche Stellen, Lehrende. Aussagen zur Barrierefreiheit werden leicht übertrieben. Sagen
Sie genau, was die Quellen sagen, und machen Sie klar, dass sie die **Administration** beschreiben.

## Inhalt

1. [Was die Quellen abdecken](#was-die-quellen-abdecken)
2. [Die Punkte](#die-punkte)
3. [In einer Minute zeigen](#in-einer-minute-zeigen)
4. [Sagen Sie nicht](#sagen-sie-nicht)
5. [Fragen, die Sie hören werden](#fragen-die-sie-hören-werden)

## Was die Quellen abdecken

Die dokumentierte Arbeit an der Barrierefreiheit steckt in der **Administration** (dem Design admin4) und der Hilfe.
Die Quellen sagen nicht, dass irgendein Design einer öffentlichen Website einen bestimmten Standard erfüllt. Die
Barrierefreiheit einer öffentlichen Website hängt von ihren eigenen Templates und Inhalten ab
([Checklisten zur Barrierefreiheit](../fuehrung/checklisten-barrierefreiheit-sicherheit-und-compliance.md#barrierefreiheit)).

## Die Punkte

| Sagen Sie | Quelle |
|---|---|
| „Das Design admin4 folgt WCAG-Regeln, die durch Tests geprüft werden: ein Kontrasttest der Farb-Tokens im hellen und dunklen Modus.“ | [Die Administration gestalten](../../../eng-us/bc/6.0/admin4-styling.md), [Sprachen und Barrierefreiheit](../wer-ist-die-exponential-foundation.md#sprachen-und-barrierefreiheit) |
| „Bedienziele sind mindestens 24 mal 24 Pixel groß (WCAG 2.5.8), und jedes Formularfeld hat einen Namen.“ | [Checklisten zur Barrierefreiheit](../fuehrung/checklisten-barrierefreiheit-sicherheit-und-compliance.md#barrierefreiheit) |
| „Ein Sprunglink ist der erste Tab-Stopp, mit Landmarks für Kopfbereich, Menüs, Brotkrumenpfad und Hauptinhalt.“ | [Das Design admin4, Landmarks und Namen](../../../eng-us/features/6.0/admin4-design.md#landmarks-and-names) |
| „Die Administration und die Hilfe lassen sich allein mit der Tastatur bedienen.“ | [Tastenkürzel](../tastenkuerzel.md), [Das Design admin4, Tastatur](../../../eng-us/features/6.0/admin4-design.md#keyboard) |
| „Helles und dunkles Theme, der hohe Kontrast von Windows (erzwungene Farben) und reduzierte Bewegung werden unterstützt.“ | [Checklisten zur Barrierefreiheit](../fuehrung/checklisten-barrierefreiheit-sicherheit-und-compliance.md#barrierefreiheit) |
| „Sprachen mit Schreibrichtung von rechts nach links drehen die Administration um, und darin geschriebene Inhalte bekommen die richtige Richtung.“ | [Von rechts nach links](../../../eng-us/features/6.0/right-to-left.md) |
| „Bildfelder können pro Klasse einen Alternativtext verlangen; das ist aus, bis es eingeschaltet wird.“ | [Checklisten zur Barrierefreiheit](../fuehrung/checklisten-barrierefreiheit-sicherheit-und-compliance.md#barrierefreiheit) |
| „Exponential Intelligence hat einen Bericht ‚Fehlender Alternativtext‘, der Vorschläge für einen Teilbaum in die Warteschlange stellt.“ | [expai, Für Redakteure](../../../eng-us/features/6.0/extensions/expai.md#for-editors) |
| „Sofortsuchergebnisse in der Hilfe werden Screenreadern angesagt.“ | [Das Hilfesystem, Sprachen und Barrierefreiheit](../../../eng-us/features/6.0/help-system.md#languages-and-accessibility) |
| „Beitragende müssen denselben Regeln folgen: keine Inline-Styles, gemeinsame Komponenten, Tokens mit Werten für den Dunkelmodus; ein Test setzt das durch.“ | [CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md) |

## In einer Minute zeigen

1. Öffnen Sie eine beliebige Seite der Administration und drücken Sie einmal `Tab`: Der Sprunglink erscheint.
2. Drücken Sie die Sonnen-Schaltfläche in der Kopfzeile: Dunkelmodus.
3. Verwenden Sie im Inhaltsbaum die Pfeiltasten.
4. Öffnen Sie die Hilfe mit dem „?“ und drücken Sie `/`, um zu suchen.

## Sagen Sie nicht

- „Exponential ist WCAG-konform“ oder „vollständig barrierefrei“. Die Quellen beschreiben Tests und Funktionen der
  Administration, keine Konformitätsaussage für jede Seite oder Website.
- „Ihre Website wird barrierefrei sein.“ Darüber entscheiden das eigene Design und die Inhalte der Website.
- „Screenreader funktionieren perfekt.“ Sagen Sie, was gebaut ist: Landmarks, Namen, Ansagen; laden Sie zum Testen ein.

## Fragen, die Sie hören werden

| Frage | Antwort |
|---|---|
| „Gibt es eine Erklärung zur Barrierefreiheit?“ | Die Quellen enthalten keine für das Produkt. Jede Website veröffentlicht ihre eigene; die Checklisten helfen bei der Vorbereitung ([Checklisten zur Barrierefreiheit](../fuehrung/checklisten-barrierefreiheit-sicherheit-und-compliance.md)). |
| „Können Redakteure barrierefreie Inhalte erstellen?“ | Sie können Alternativtext hinzufügen; eine Klasse kann ihn verlangen; Überschriften und Listen im Rich-Text-Editor helfen bei der Struktur. Am wichtigsten ist die Schulung der Redaktion. |
| „Wie melde ich ein Problem mit der Barrierefreiheit?“ | Als Fehler im richtigen Tracker, mit der Seite, der assistiven Technologie und dem, was geschah ([Fehler gut melden](fehler-gut-melden.md)). |
| „Kann ich helfen?“ | Ja: Testen Sie mit Ihrer assistiven Technologie und melden Sie; prüfen Sie ein Theme ([Für Barrierefreiheit eintreten](fuer-barrierefreiheit-eintreten.md)). |

## Verwandte Seiten

- [Für Barrierefreiheit eintreten](fuer-barrierefreiheit-eintreten.md)
- [Barrierefreie Folien und Grafiken](barrierefreie-folien-und-grafiken.md)
- [Funktions-Highlights, Barrierefreiheit der Administration](../geschaeft/funktionshighlights-fuer-kunden.md#barrierefreiheit-der-administration)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
