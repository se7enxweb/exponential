---
title: "Inhaltslisten und Karten gestalten"
audience: theme-designer
category: theme-design
order: 10
summary: Wie Theme-Designer Listen, Raster und Karten für Exponential-Inhalte gestalten - Ordnerlisten, gemischte Klassen in einer Liste, von Redakteuren festgelegte Sortierung, Seitennavigation, Karten mit und ohne Bild und die Barrierefreiheit von Karten, die ganz verlinkt sind.
keywords: Theme-Designer, Liste, Listenansicht, Raster, Karten, Teaser, Ordner, Seitennavigation, Sortierung, gemischte Klassen, Kartenlink, Zeilenansicht, theme designer, listing, list view, grid, cards, folder, pagination, sorting, mixed classes, card link, line view
type: help
---
# Inhaltslisten und Karten gestalten

Diese Seite ist für Theme-Designer, die die Listen einer Website gestalten: die Unterelemente eines Ordners,
Nachrichtenarchive, verwandte Einträge, Raster aus Karten. In Listen finden die meisten Besucher Inhalte, und dort
trifft ein Design auf die größte Vielfalt an Objekten. Die Template-Seite steht in
[Knoten und Listen abrufen](../designer/vorlagen/knoten-und-listen-abrufen.md),
[Zeilenansicht](../designer/vorlagen/zeilenansicht.md) und [Seitennavigation](../designer/vorlagen/seitenumbruch.md).

## Inhalt

1. [Woher Listen kommen](#woher-listen-kommen)
2. [Gemischte Klassen in einer Liste](#gemischte-klassen-in-einer-liste)
3. [Die Reihenfolge wählen die Redakteure](#die-reihenfolge-wählen-die-redakteure)
4. [Die Karte gestalten](#die-karte-gestalten)
5. [Eine ganze Karte klickbar machen](#eine-ganze-karte-klickbar-machen)
6. [Seitennavigation](#seitennavigation)
7. [Raster, die ihre Form behalten](#raster-die-ihre-form-behalten)
8. [Eine Checkliste für Listen](#eine-checkliste-für-listen)

## Woher Listen kommen

| Liste | Übliche Quelle | Hinweis zum Design |
|---|---|---|
| Unterelemente eines Ordners | die Vollansicht des Ordners ruft seine Unterelemente ab | kann jede Klasse enthalten, die Redakteure dort anlegen dürfen |
| Ein Nachrichtenarchiv | ein Abruf, nach Klasse gefiltert und nach Datum sortiert | Daten zählen; nach Monat oder Jahr gruppieren? |
| Verwandte Inhalte | Verknüpfungsfelder des Objekts | meist kurz: 1 bis 5 Einträge |
| Eine Liste in einem Layoutblock | die Sammlung eines Blocks, von Hand oder per Abfrage ausgewählt | Redakteure wählen den Ansichtstyp ([Layoutblöcke entwerfen](layoutbloecke-gestalten.md)) |
| Suchergebnisse | das Suchmodul | siehe [Suchergebnisse gestalten](suchergebnisse-gestalten.md) |

## Gemischte Klassen in einer Liste

Ein Ordner kann gleichzeitig Artikel, Dateien, Links, weitere Ordner und Bilder enthalten. Jeder Eintrag wird im
Zeilen- (oder Karten-)Template seiner Klasse gezeichnet, eine Liste mischt also Formen. Gestalten Sie die Zeile oder
Karte jeder Klasse, die in diesem Ordner erscheinen kann, und geben Sie ihnen ein gemeinsames Gerüst: denselben
Titelstil, dieselbe Zeile für Metadaten, dieselben Abstände. Besucher sollen eine Liste sehen, nicht fünf.

## Die Reihenfolge wählen die Redakteure

Jeder Knoten speichert eine Sortierung für seine Unterelemente (zum Beispiel nach Veröffentlichungsdatum, nach Name oder
manuell nach Priorität), die Redakteure in der Administration festlegen. Ordner-Templates folgen ihr meist. Gestalten
Sie keine Liste, die nur in einer Reihenfolge Sinn ergibt, etwa eine Zeitleiste, es sei denn, das Template legt diese
Reihenfolge fest und die Redakteure wissen es.

## Die Karte gestalten

Zeichnen Sie die Karte in mindestens vier Zuständen: mit Bild und langem Titel, mit Bild und kurzem Titel, ohne Bild und
mit allen optionalen Feldern leer. Entscheiden Sie:

- das **Seitenverhältnis des Bildes**, und ob Bilder darauf zugeschnitten werden ([Bildzuschnitte und Art Direction gestalten](bildzuschnitte-und-art-direction-gestalten.md));
- was den **Link** trägt: mindestens den Titel;
- was bei **Hover und Fokus** geschieht ([Interaktive Zustände gestalten](interaktive-zustaende-gestalten.md));
- die **Überschriftenebene** des Titels, die davon abhängt, wo die Liste auf der Seite steht
  ([Landmarks und Überschriften](../designer/theme-design/landmarks-und-ueberschriften.md)).

Karten verwenden die Tokens für Fläche, Linie, Radius und Abstand; geben Sie Karten keine eigene Farbe außerhalb der
Palette ([Design-Tokens](../designer/theme-design/design-tokens.md)).

## Eine ganze Karte klickbar machen

Ein häufiger Wunsch lautet "die ganze Karte ist ein Link". Wenn die ganze Karte in ein einziges `<a>` gehüllt wird,
lesen Screenreader Bild, Titel, Text und Datum als einen langen Link vor. Das barrierefreie Muster behält einen echten
Link auf dem Titel und dehnt seine klickbare Fläche mit CSS über die Karte:

```css
/* design/mytheme/stylesheets/components.css */
.mytheme-card { position: relative; }
.mytheme-card__link::after { content: ""; position: absolute; inset: 0; }
.mytheme-card__link:focus-visible { outline: none; }
.mytheme-card:focus-within { outline: 2px solid var(--site-focus); outline-offset: 2px; }
```

Andere Links in der Karte (ein Tag, ein Autor) brauchen dann `position: relative` und eine Stapelreihenfolge über der
gedehnten Fläche. Zeigen Sie das im Mockup, damit der Entwickler weiß, welche Elemente einzeln klickbar bleiben.

## Seitennavigation

Lange Listen werden mit den mitgelieferten Navigator-Templates (`navigator/google.tpl` und weitere in
`design/standard/templates/navigator/`) auf Seiten aufgeteilt, die ein Design überschreiben kann. Gestalten Sie:

- die aktuelle Seite, zurück und weiter sowie die erste und die letzte Seite;
- den Zustand auf Seite eins (kein "zurück") und auf der letzten Seite;
- die Version für kleine Bildschirme, meist zurück, "Seite 3 von 12", weiter.

## Raster, die ihre Form behalten

- Bevorzugen Sie CSS Grid mit `repeat( auto-fill, minmax( <min>, 1fr ) )` gegenüber festen Spaltenzahlen, damit sich das
  Raster an den Container anpasst, auch an schmale Layoutzonen ([Breakpoints und Containerbreiten](../designer/theme-design/breakpoints-und-containerbreiten.md)).
- Ein Raster mit einem Eintrag soll nicht wie ein kaputtes Dreierraster aussehen; entscheiden Sie, ob eine einzelne
  Karte die ganze Zeile einnimmt.
- Gleich hohe Karten kommen aus dem Raster, nicht aus festen Höhen.

## Eine Checkliste für Listen

- [ ] Ein Zeilen- oder Kartendesign für jede Klasse, die in der Liste erscheinen kann.
- [ ] Karten mit und ohne Bild, mit langem und kurzem Titel.
- [ ] Sichtbarer Fokus auf jeder Karte; ein Link pro Karte für Screenreader.
- [ ] Seitennavigation auf der ersten, einer mittleren und der letzten Seite; auf kleinen Bildschirmen.
- [ ] Die leere Liste ([Leere Zustände und Fehlerzustände gestalten](leere-zustaende-und-fehlerzustaende-gestalten.md)).
- [ ] Hell und dunkel; 200 % Zoom; von rechts nach links, wenn die Website solche Sprachen hat.

## Verwandte Seiten

- [Ansichtsmodi gestalten](ansichtsmodi-gestalten.md)
- [Abruffilter und Sortierung](../designer/vorlagen/fetch-filter-und-sortierung.md)
- [Layout-Blöcke gestalten](../designer/theme-design/layout-bloecke-gestalten.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
