---
title: Logische CSS-Eigenschaften
audience: designer
category: theme-design
order: 590
summary: Eine Umrechnungstabelle von physischen CSS-Eigenschaften (left, right, margin-left, text-align: left) zu logischen (inline-start, inline-end), was keine logische Form hat und wie Sie ein bestehendes Stylesheet sicher umstellen.
keywords: logische Eigenschaften, margin-inline-start, padding-inline, inset-inline, text-align start, border-inline, logischer Radius, RTL-CSS, Schreibrichtung, CSS umstellen, logical properties, logical radius, rtl css, writing direction, convert css
type: help
---
# Logische CSS-Eigenschaften

Diese Seite ist eine Referenz für Designer, die CSS schreiben, das in beiden Textrichtungen funktioniert. Logische Eigenschaften
benennen Seiten nach dem Textfluss (Anfang und Ende) statt nach dem Bildschirm (links und rechts).

## Inhalt

1. [Umrechnungstabelle](#umrechnungstabelle)
2. [Was keine logische Form hat](#was-keine-logische-form-hat)
3. [Ein Stylesheet umstellen](#ein-stylesheet-umstellen)

## Umrechnungstabelle

| Physisch | Logisch |
|---|---|
| `margin-left` / `margin-right` | `margin-inline-start` / `margin-inline-end` |
| `margin-left` + `margin-right` | `margin-inline` |
| `padding-left` / `padding-right` | `padding-inline-start` / `padding-inline-end` |
| `left` / `right` (positioniert) | `inset-inline-start` / `inset-inline-end` |
| `top` / `bottom` | `inset-block-start` / `inset-block-end` |
| `border-left` / `border-right` | `border-inline-start` / `border-inline-end` |
| `border-top-left-radius` | `border-start-start-radius` |
| `border-top-right-radius` | `border-start-end-radius` |
| `border-bottom-left-radius` | `border-end-start-radius` |
| `border-bottom-right-radius` | `border-end-end-radius` |
| `text-align: left` / `right` | `text-align: start` / `end` |
| `width` / `height` | `inline-size` / `block-size` (optional; zählt nur bei vertikaler Schrift) |

Von links nach rechts ergibt jede logische Eigenschaft denselben Wert wie die physische; die Umstellung ändert für Seiten von links nach
rechts also nichts. So wurden die Stylesheets der Administration umgestellt (`components.css`, `admin4.css`, `pagelayout.css`,
`core.css` und die Modul-Stylesheets).

## Was keine logische Form hat

| Eigenschaft | Was zu tun ist |
|---|---|
| `float: left` / `right` | `float: inline-start` / `inline-end`, wo unterstützt, oder eine Spiegelregel `:where([dir="rtl"])` |
| `margin`/`padding` mit vier Werten und verschiedenem links und rechts | in `margin-block` und `margin-inline` aufteilen oder eine Spiegelregel hinzufügen |
| `background-position: left ...` | eine Spiegelregel |
| `box-shadow` / `text-shadow` mit waagrechtem Versatz | eine Spiegelregel |
| `transform: translateX()` | eine Spiegelregel (Wert negieren) |
| Grid und Flex | nichts: Sie folgen der Richtung bereits |

Eine Spiegelregel folgt der Regel, die sie spiegelt:

```css
.teaser { background-position: left center; }
:where([dir="rtl"]) .teaser { background-position: right center; }
```

## Ein Stylesheet umstellen

1. Suchen Sie nach `left` und `right`: `grep -n -E "left|right" stylesheets/*.css`.
2. Stellen Sie jedes Vorkommen nach der Tabelle auf seine logische Form um; lassen Sie die von Redakteuren gewählte Ausrichtung
   (Klassen `.text-left` aus dem Rich Text) in Ruhe.
3. Fügen Sie Spiegelregeln für das hinzu, was keine logische Form hat.
4. Vergleichen Sie Screenshots von Seiten von links nach rechts vorher und nachher: Sie müssen identisch sein.
5. Prüfen Sie eine Seite von rechts nach links.

## Verwandte Seiten

- [Designs für rechts nach links](rechts-nach-links-designs.md)
- [Responsives Design](responsive-design.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
