---
title: "Thema: Barrierefreiheit"
audience: press
category: press-media
order: 63
summary: Ein Themenbeitrag über die Arbeit an der Barrierefreiheit in der Administration von Exponential - durch Tests geprüfte WCAG-Regeln, Bedienung mit der Tastatur, Sprunglinks und Landmarks, dunkler Modus und erzwungene Farben, Alternativtext für Bilder - mit druckbaren Fakten, Quellen und den Konformitätsaussagen, die Sie nicht machen dürfen.
keywords: Thema, Barrierefreiheit, WCAG, admin4, Tastatur, Screenreader, Kontrast, dunkler Modus, erzwungene Farben, Alternativtext, Presse, story, accessibility, keyboard, screen readers, contrast, dark mode, forced colors, alternative text, press
type: help
---
# Thema: Barrierefreiheit

Diese Seite ist für Journalisten, die über digitale Barrierefreiheit berichten, und für Creator mit Behinderungen, die
Software rezensieren. Aussagen zur Barrierefreiheit sind leicht übertrieben; die dokumentierte Arbeit betrifft die
**Administration** von Exponential und ihre Hilfe, nicht jede öffentliche Website. Diese Seite sagt Ihnen, was
dokumentiert ist und wie Sie es formulieren.

## Inhalt

1. [Das Thema in einem Absatz](#das-thema-in-einem-absatz)
2. [Was neu ist, und seit wann](#was-neu-ist-und-seit-wann)
3. [Fakten, die Sie drucken können](#fakten-die-sie-drucken-können)
4. [Was Sie nicht behaupten sollten](#was-sie-nicht-behaupten-sollten)
5. [Selbst testen](#selbst-testen)
6. [Blickwinkel und Ideen für Überschriften](#blickwinkel-und-ideen-für-überschriften)
7. [Wen Sie fragen](#wen-sie-fragen)
8. [Verwandte Seiten](#verwandte-seiten)

## Das Thema in einem Absatz

Im Oktober 2026 durchlief das neue Administrationsdesign von Exponential, admin4, eine Serie von Arbeiten zur
Barrierefreiheit: Landmarks und Namen, Bedienung der Reiter und des Inhaltsbaums mit der Tastatur, ein Name für jedes
Formularfeld, Kontrast der Farben, Fokusanzeigen und die Modi, die ein Browser anfordert
([Changelog von 6.0.15](../../../changelogs/6.0/6.0.15.md#14-october-2026-the-admin4-accessibility-series-merged-and-refined)).
„Das Design admin4 folgt WCAG-Regeln, die Tests prüfen: ein Kontrasttest der Farbtokens im hellen und dunklen Modus
und Bedienflächen von mindestens 24 mal 24 Pixeln“ ([Sprachen und Barrierefreiheit](../wer-ist-die-exponential-foundation.md#sprachen-und-barrierefreiheit)).
Die Regeln binden auch Mitwirkende: keine Inline-Styles, gemeinsame Komponenten, Farbtokens mit Werten für den dunklen
Modus und ein Test, der das durchsetzt ([CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md)).

## Was neu ist, und seit wann

Alles in der Entwicklungslinie 6.0.15, die nicht erschienen ist.

| Was | Wann | Quelle |
|---|---|---|
| Landmarks und Namen in admin4 | 14. Oktober 2026 | [Changelog von 6.0.15](../../../changelogs/6.0/6.0.15.md#14-october-2026-landmarks-and-names-in-admin4) |
| Der Inhaltsbaum ist ein Baum für Screenreader und Tastaturen | 14. Oktober 2026 | [Changelog von 6.0.15](../../../changelogs/6.0/6.0.15.md#14-october-2026-the-content-tree-is-a-tree-for-screen-readers-and-keyboards) |
| Fokusanzeigen in admin4 | 14. Oktober 2026 | [Changelog von 6.0.15](../../../changelogs/6.0/6.0.15.md#14-october-2026-admin4-focus-indicators) |
| Die verbleibenden schwerwiegenden Befunde zur Barrierefreiheit der Administrationsseiten | 17. Oktober 2026 | [Changelog von 6.0.15](../../../changelogs/6.0/6.0.15.md#17-october-2026-the-remaining-serious-accessibility-findings-of-the-admin-pages) |
| Die Seite des Layout-Editors und ihr Zoom | 17. Oktober 2026 | [Changelog von 6.0.15](../../../changelogs/6.0/6.0.15.md#17-october-2026-the-layout-editor-page-can-be-zoomed) |

## Fakten, die Sie drucken können

| Fakt | Quelle |
|---|---|
| Bedienflächen sind mindestens 24 mal 24 Pixel groß (WCAG 2.5.8), und jedes Formularfeld hat einen Namen. | [Gesprächspunkte: Barrierefreiheit](../fuersprecher/gespraechspunkte-barrierefreiheit.md#die-punkte) |
| Ein Sprunglink ist der erste Tab-Halt, mit Landmarks für Kopfbereich, Menüs, Brotkrümelnavigation und Hauptinhalt. | [Das Design admin4, Landmarks und Namen](../../../eng-us/features/6.0/admin4-design.md#landmarks-and-names) |
| Die Administration und die Hilfe lassen sich allein mit der Tastatur bedienen. | [Tastenkürzel](../tastenkuerzel.md) |
| Helles und dunkles Theme, der Hochkontrastmodus von Windows (erzwungene Farben) und reduzierte Bewegung werden unterstützt. | [Gesprächspunkte: Barrierefreiheit](../fuersprecher/gespraechspunkte-barrierefreiheit.md#die-punkte) |
| Auf dem Dashboard wird eine Veränderung als Zahl, Pfeil und Worte gezeigt, nie allein durch Farbe. | [Das Design admin4, Was Sie bekommen](../../../eng-us/features/6.0/admin4-design.md#what-you-get) |
| Bildfelder können pro Klasse einen Alternativtext verlangen; das ist ausgeschaltet, bis es eingeschaltet wird. | [Gesprächspunkte: Barrierefreiheit](../fuersprecher/gespraechspunkte-barrierefreiheit.md#die-punkte) |
| Exponential Intelligence kann Alternativtext vorschlagen und Bilder melden, denen er fehlt. | [expai, Für Redakteure](../../../eng-us/features/6.0/extensions/expai.md#for-editors) |
| Sprachen, die von rechts nach links geschrieben werden, drehen die Administration um. | [Von rechts nach links](../../../eng-us/features/6.0/right-to-left.md) |

## Was Sie nicht behaupten sollten

- „Exponential ist WCAG-konform“ oder „vollständig barrierefrei“. Die Quellen beschreiben Tests und Funktionen der
  Administration, keine Konformitätsaussage für jede Seite oder Website.
- „Mit Exponential gebaute Websites sind barrierefrei.“ Darüber entscheiden die eigenen Templates und Inhalte jeder
  Website.
- „Screenreader funktionieren perfekt.“ Sagen Sie, was gebaut ist, und laden Sie zum Testen ein.
- Die Quellen enthalten keine Erklärung zur Barrierefreiheit für das Produkt; sagen Sie nicht, es gebe eine.

## Selbst testen

Auf einer Installation, die Sie kontrollieren: Drücken Sie auf einer beliebigen Administrationsseite einmal `Tab` (der
Sprunglink erscheint), drücken Sie die Sonnen-Schaltfläche (dunkler Modus), verwenden Sie die Pfeiltasten im
Inhaltsbaum, öffnen Sie die Hilfe mit „?“ und drücken Sie `/`
([Gesprächspunkte: Barrierefreiheit](../fuersprecher/gespraechspunkte-barrierefreiheit.md#in-einer-minute-zeigen)). Rezensenten,
die assistive Technik verwenden, sind die besten Tester; melden Sie Probleme als Fehler im öffentlichen Tracker.

## Blickwinkel und Ideen für Überschriften

```text
"Accessibility as a test that fails: how Exponential guards its admin"
"No state by color alone: inside a CMS administration redesign"
"Contributors must follow the same rules: accessibility written into CONTRIBUTING.md"
```

Auf Deutsch zum Beispiel: „Barrierefreiheit als Test, der fehlschlägt: wie Exponential seine Administration schützt“,
„Kein Zustand allein durch Farbe: ein Blick in die Neugestaltung einer CMS-Administration“, „Auch Mitwirkende müssen
die Regeln befolgen: Barrierefreiheit in CONTRIBUTING.md festgeschrieben“.

## Wen Sie fragen

| Frage | Fragen Sie | Wo |
|---|---|---|
| Die Arbeit an der Barrierefreiheit und ihre Tests | 7x | info@se7enx.com oder <https://exponential.earth/about/contact-us> |
| Einen Fehler bei der Barrierefreiheit | den öffentlichen Tracker | <https://github.com/se7enxweb/exponential/issues> |

## Verwandte Seiten

- [Gesprächspunkte: Barrierefreiheit](../fuersprecher/gespraechspunkte-barrierefreiheit.md)
- [Thema: admin4, das Design der Administration](thema-admin4.md)
- [Thema: Sprachen und Schreibrichtung von rechts nach links](thema-sprachen-und-rechts-nach-links.md)
- [Funktionsthemen: Übersicht](themenbeitraege-uebersicht.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
