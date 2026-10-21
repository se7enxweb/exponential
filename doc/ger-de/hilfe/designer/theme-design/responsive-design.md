---
title: Responsives Design
audience: designer
category: theme-design
order: 400
summary: Ein Exponential-Design vom Telefon bis zum breiten Bildschirm und bei starkem Zoom funktionsfähig machen: das viewport-Tag, fließendes Layout, Breakpoints, responsive Bilder über Aliase, Tippziele und die Breiten, bei denen das Projekt testet.
keywords: responsiv, mobil, Telefon, Tablet, Viewport, Breakpoints, fließendes Layout, Media Queries, Container-Queries, Tippziele, pointer coarse, Zoom, 375px, 960px, 1440px, responsive, mobile, phone, tablet, viewport, breakpoints, fluid layout, media queries, container queries, touch targets, zoom
type: help
---
# Responsives Design

Diese Seite ist für Designer, die ein Design auf jedem Bildschirm funktionsfähig machen. Sie beantwortet: was das Pagelayout braucht, wie
Sie Breakpoints planen und welche Breiten und Zoomstufen Sie testen.

![Die Seite eines Eintrags in der Administration bei 375 Pixeln Breite, der Breite eines Telefons](../../../../eng-us/help/images/designers/item-page-375.webp)

## Inhalt

1. [Die Grundlagen](#die-grundlagen)
2. [Ein Layout, das fließt](#ein-layout-das-fließt)
3. [Breakpoints](#breakpoints)
4. [Bilder](#bilder)
5. [Berührung](#berührung)
6. [Was zu testen ist](#was-zu-testen-ist)

## Die Grundlagen

- Das viewport-Tag im Kopf des Pagelayouts (jedes mitgelieferte Design hat es):

  ```html
  <meta name="viewport" content="width=device-width, initial-scale=1">
  ```

  Fügen Sie nie `maximum-scale=1` oder `user-scalable=no` hinzu: Besucher müssen zoomen können.
- Ein HTML für jedes Gerät; CSS entscheidet über das Layout. Exponential hat eine Erkennung mobiler Geräte
  (`$module_result.is_mobile_device`), aber Templates, die pro Gerät anderes Markup senden, erschweren Caching und Testen. Ziehen Sie CSS
  vor.

## Ein Layout, das fließt

```css
.site-main {
  max-width: 72rem;
  margin-inline: auto;
  padding-inline: clamp(1rem, 3vw, 2rem);
}
.card-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(16rem, 1fr));
  gap: 1.5rem;
}
```

Raster mit `auto-fill`/`minmax`, `flex-wrap`, `clamp()` für Größen und logische Eigenschaften (`margin-inline`) decken die meisten
Layouts mit wenigen Media Queries ab und bleiben in Sprachen von rechts nach links korrekt.

## Breakpoints

Siehe [Breakpoints und Containerbreiten](breakpoints-und-containerbreiten.md) für die Werte, die die mitgelieferten Designs verwenden,
und wie Sie Ihre eigenen wählen. Schreiben Sie sie wo möglich in `rem` oder `em`, damit sie der Schriftgröße des Besuchers folgen.

## Bilder

- `max-width: 100%; height: auto;` an Inhaltsbildern.
- Mehrere Aliase mit `srcset`, damit Telefone kleine Dateien laden ([Bildaliase](bildaliase.md)).
- Attribute `width` und `height`, damit das Layout nicht springt ([CLS und LCP messen](cls-und-lcp-messen.md)).

## Berührung

- Tippziele mindestens 24 x 24 CSS-Pixel, besser 44 x 44 für die wichtigsten Bedienelemente. Die `exp-mobile-layout.css` des Designs media
  sammelt solche Korrekturen.
- `@media (pointer: coarse)` kann Bedienelemente nur für Berührung vergrößern; admin4 verwendet es.
- Verlassen Sie sich nicht auf Hover, um Inhalte zu zeigen: Touchscreens haben kein Hover.

## Was zu testen ist

Das Projekt prüft seine Seiten bei diesen Größen:

| Breite | Skalierung | Steht für |
|---|---|---|
| 1440 px | 1 | einen Desktop |
| 960 px | 2 | einen Desktop bei 200 % Browser-Zoom |
| 375 px | 2 | ein Telefon |

Bei jeder Größe: kein waagrechtes Scrollen der Seite, kein überlappender Text, jedes Bedienelement erreichbar, Text lesbar. Siehe
[Testen bei Zoomstufen](testen-bei-zoomstufen.md).

## Verwandte Seiten

- [Breakpoints und Containerbreiten](breakpoints-und-containerbreiten.md)
- [Barrierefreiheit: Grundlagen für Themes](barrierefreiheit-grundlagen-fuer-themes.md)
- [Logische CSS-Eigenschaften](logische-css-eigenschaften.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
