---
title: Layout-Blöcke gestalten
audience: designer
category: theme-design
order: 760
summary: CSS für Seiten, die mit dem Layout-Editor gebaut sind: die Klassennamen, die die mitgelieferten Templates ausgeben (slb, slb-zone, ngl-block, ngl-vt-*), das Raster-Stylesheet, Abstände zwischen Blöcken, Blöcke, die sich an schmale Zonen anpassen, und dunkler Modus für Blöcke.
keywords: Blöcke gestalten, Layout-CSS, slb, slb-zone, ngl-block, Blockklassen, grid.css, explayouts-CSS, Container-Queries, Abstände zwischen Blöcken, CSS für Landingpages, style blocks, layouts css, block classes, explayouts css, container queries, block spacing, landing page css
type: help
---
# Layout-Blöcke gestalten

Diese Seite ist für Designer, die das CSS von Layout-Seiten schreiben. Sie beantwortet: welche Klassen Ihnen die Templates geben, wie Sie
Blöcke anordnen und dimensionieren und wie ein Block in einer breiten und einer schmalen Zone richtig aussieht.

## Inhalt

1. [Die Klassen, die Sie bekommen](#die-klassen-die-sie-bekommen)
2. [Das Raster-Stylesheet](#das-raster-stylesheet)
3. [Abstände zwischen Blöcken](#abstände-zwischen-blöcken)
4. [Blöcke in schmalen Zonen](#blöcke-in-schmalen-zonen)
5. [Hell, dunkel und rechts nach links](#hell-dunkel-und-rechts-nach-links)

## Die Klassen, die Sie bekommen

Die mitgelieferten Templates von `extension/explayouts/design/standard/templates/explayouts/` geben aus:

| Element | Klassen und Attribute |
|---|---|
| Layout | `slb` (plus `slb-2-column` und ähnliche in Layouttyp-Templates), `data-layout-id` |
| Zone | `slb-zone`, `data-zone-identifier`; in Layouttyp-Templates auch `sevenx-zone sevenx-zone-<name>` |
| Block | `slb slb-<definition>` (`slb-title`, `slb-list`), `data-block-id` |
| Containerblöcke | `ngl-block ngl-two-columns`, mit Spalten `container`, `row` und `col-md-*` |

Designs, die die Templates überschreiben, wählen eigene Klassen; die Designs media und starter behalten die Klassennamen des Themes, aus
dem sie portiert wurden. Sehen Sie sich die Kommentare mit Template-Pfaden einer Seite an, um zu sehen, welches Template einen Block
gezeichnet hat ([Ein Design debuggen](ein-design-debuggen.md)).

## Das Raster-Stylesheet

Das Design media lädt `explayouts_standard/grid.css` (aus `design/standard/stylesheets/` der Layouts-Erweiterung) vor seinem eigenen CSS,
für die Klassen `container`, `row` und `col-*`. Wenn Ihr Design Container mit einem eigenen Raster (CSS-Grid oder Flex) zeichnet, brauchen
Sie es nicht.

## Abstände zwischen Blöcken

Geben Sie Blöcken an einer Stelle einen vertikalen Rhythmus:

```css
.slb-zone > * + * { margin-block-start: var(--site-space-5); }
```

statt Außenabständen in jedem Block-Template. Redakteure können dann beliebige Blöcke stapeln, ohne seltsame Lücken.

## Blöcke in schmalen Zonen

Derselbe Listenblock kann in einer breiten Zone `main` und in einer schmalen `sidebar` stehen. Verwenden Sie Container-Queries an der Zone:

```css
.slb-zone { container-type: inline-size; }
.slb-list .block-list { display: grid; gap: var(--site-space-3); }
@container (min-width: 40rem) {
  .slb-list .block-list { grid-template-columns: repeat(3, minmax(0, 1fr)); }
}
```

## Hell, dunkel und rechts nach links

- Farben nur aus Token, damit Blöcke dem dunklen Modus folgen ([Design-Token](design-tokens.md)).
- Logische Eigenschaften, damit zweispaltige Blöcke und Blöcke mit Bild und Text in Sprachen von rechts nach links spiegeln
  ([Logische CSS-Eigenschaften](logische-css-eigenschaften.md)).
- Hero-Blöcke mit Text auf Bildern: Prüfen Sie den Kontrast in beiden Themes ([Farbkontrast](farbkontrast.md)).

## Verwandte Seiten

- [Block-Ansichtstemplates](block-ansichtstemplates.md)
- [Breakpoints und Containerbreiten](breakpoints-und-containerbreiten.md)
- [Tutorial: eine Landingpage mit Layouts bauen](tutorial-eine-landingpage-mit-layouts-bauen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
