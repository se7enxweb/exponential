---
title: Bild-Performance
audience: designer
category: theme-design
order: 660
summary: Schnelle Bilder in einem Design: Aliase in der angezeigten Größe, srcset und sizes, Lazy Loading unterhalb des ersten Bildschirms, sofortiges Laden für das Hauptbild, Breite und Höhe gegen Layoutverschiebung, moderne Formate und SVG für Grafiken.
keywords: Bild-Performance, Lazy Loading, loading lazy, fetchpriority, srcset, sizes, Bildalias, WebP, AVIF, responsive Bilder, LCP-Bild, decoding async, image performance, lazy loading, image alias, webp, avif, responsive images, LCP image
type: help
---
# Bild-Performance

Diese Seite ist für Designer, die entscheiden, wie Bilder ausgeliefert werden. Bilder sind meist der größte Teil einer Seite; ein paar
Attribute und die richtigen Aliase bewirken den größten Unterschied.

## Inhalt

1. [Die richtige Größe](#die-richtige-größe)
2. [Lazy und eager](#lazy-und-eager)
3. [Den Platz reservieren](#den-platz-reservieren)
4. [Formate](#formate)
5. [Ein vollständiges Beispiel](#ein-vollständiges-beispiel)

## Die richtige Größe

Schicken Sie nie ein Bild mit 1600 px in eine Karte mit 300 px. Definieren Sie Aliase für die Größen, die Ihr Design zeigt
([Bildaliase](bildaliase.md)), und bieten Sie zwei oder drei davon mit `srcset` und `sizes` an, damit der Browser wählt.

## Lazy und eager

- Bilder unterhalb des ersten Bildschirms: `loading="lazy"`. Der Browser holt sie, wenn sie in die Nähe kommen.
- Das Hauptbild des ersten Bildschirms (ein Hero, das Aufmacherbild des Artikels): **nicht** lazy. Es ist meist das Element des Largest
  Contentful Paint; `fetchpriority="high"` sagt dem Browser, es zuerst zu holen.
- `decoding="async"` lässt den Browser Bilder außerhalb des Hauptthreads dekodieren.

## Den Platz reservieren

Die Attribute `width` und `height` (der Alias liefert beide) oder ein CSS-`aspect-ratio` lassen den Browser den Kasten reservieren,
bevor das Bild ankommt, sodass Text nicht springt ([CLS und LCP messen](../../../../eng-us/help/designers/theme-design/measuring-cls-and-lcp.md)).

## Formate

- Fotos: JPEG oder WebP und AVIF, wo Ihr Bildkonverter sie erzeugt; `settings/image.ini [ImageUploadSettings] AllowedMIMETypes[]`
  führt `image/webp` und `image/avif` als hochladbare Typen auf.
- Logos, Symbole, Diagramme: SVG ([SVG-Bilder](../../../../eng-us/help/designers/theme-design/svg-images.md)).
- Keine Bilder von Text: Verwenden Sie echten Text mit einem Webfont.

## Ein vollständiges Beispiel

```tpl
{def $img = $node.data_map.image.content}
{if $node.data_map.image.has_content}
<img src={$img['mytheme_card'].url|ezroot}
     srcset="{$img['mytheme_card'].url|ezroot( 'no' )} {$img['mytheme_card'].width}w,
             {$img['mytheme_hero'].url|ezroot( 'no' )} {$img['mytheme_hero'].width}w"
     sizes="(min-width: 64em) 33vw, 100vw"
     width="{$img['mytheme_card'].width}" height="{$img['mytheme_card'].height}"
     alt="{$img.alternative_text|wash}" loading="lazy" decoding="async">
{/if}
{undef $img}
```

Lassen Sie beim ersten Bild einer Vollansicht `loading="lazy"` weg und fügen Sie `fetchpriority="high"` hinzu.

## Verwandte Seiten

- [Bildaliase](bildaliase.md)
- [Bilder in einem Design](bilder-in-einem-design.md)
- [Performance von Themes](../../../../eng-us/help/designers/theme-design/theme-performance.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
