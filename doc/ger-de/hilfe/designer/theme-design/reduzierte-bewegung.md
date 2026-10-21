---
title: Reduzierte Bewegung
audience: designer
category: theme-design
order: 490
summary: Besucher respektieren, die um weniger Bewegung bitten: die Media Query prefers-reduced-motion, welche Animationen abzuschalten sind, Slider und automatisches Abspielen, weiches Scrollen und wie admin4 es macht.
keywords: reduzierte Bewegung, prefers-reduced-motion, Animation, Übergänge, automatisches Abspielen, Karussell, Slider, weiches Scrollen, vestibulär, Bewegungsempfindlichkeit, reduced motion, animation, transitions, autoplay, carousel, slider, smooth scroll, vestibular, motion sensitivity
type: help
---
# Reduzierte Bewegung

Diese Seite ist für Designer, die Übergänge, Animationen, Slider oder Videos hinzufügen. Manchen Besuchern wird von Bewegung schwindlig oder
übel, und sie schalten in ihrem Betriebssystem „Bewegung reduzieren“ ein. Browser melden das mit der Media Query `prefers-reduced-motion`.

## Inhalt

1. [Das Muster](#das-muster)
2. [Was abzuschalten ist](#was-abzuschalten-ist)
3. [Slider, Karussells und Videos](#slider-karussells-und-videos)
4. [In Skripten](#in-skripten)
5. [Wie admin4 es macht](#wie-admin4-es-macht)

## Das Muster

Schreiben Sie Bewegung als Verbesserung, nur für Besucher, die nicht um weniger gebeten haben:

```css
@media (prefers-reduced-motion: no-preference) {
  .card { transition: transform 150ms ease-out; }
  .card:hover { transform: translateY(-2px); }
  html { scroll-behavior: smooth; }
}
```

oder schalten Sie sie für diejenigen ab, die darum gebeten haben:

```css
@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }
}
```

Die erste Form ist sicherer: Bewegung ist standardmäßig nur auf Wunsch aktiv.

## Was abzuschalten ist

- Parallaxe, zoomende Hintergründe, große Bewegungen über den Bildschirm.
- Weiches Scrollen für Links innerhalb der Seite.
- Animierte Seitenübergänge.

Kleine Überblendungen von Farbe oder Deckkraft sind meist in Ordnung; Probleme verursacht Bewegung durch den Raum.

## Slider, Karussells und Videos

- Kein automatisches Abspielen oder ein sichtbarer Pause-Knopf, der mit der Tastatur funktioniert.
- Zeigen Sie bei reduzierter Bewegung die erste Folie und lassen Sie Besucher von Hand weiterblättern.
- Die Blöcke Slider, Karussell und Galerie des Layout-Editors rendern mit den Templates und Skripten des Designs: Prüfen Sie sie mit
  eingeschalteter reduzierter Bewegung.

## In Skripten

```js
const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
if (!reduce) { startAutoplay(); }
```

## Wie admin4 es macht

Die Stylesheets von admin4 haben Blöcke `@media (prefers-reduced-motion: reduce)`, die Übergänge abschalten (zum Beispiel die Drehung des
Symbols im Menü „Mehr“ in `admin4.css`), und Hover-Effekte laufen nur unter
`(hover: hover) and (pointer: fine) and (prefers-reduced-motion: no-preference)`.

## Verwandte Seiten

- [Barrierefreiheit: Grundlagen für Themes](barrierefreiheit-grundlagen-fuer-themes.md)
- [Tastatur und Fokus](tastatur-und-fokus.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
