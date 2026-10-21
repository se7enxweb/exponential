---
title: Testen bei Zoomstufen
audience: designer
category: theme-design
order: 420
summary: Warum ein Design bei 200 % Browser-Zoom funktionieren muss, was Zoom mit dem Viewport macht, wie Sie ihn bei 960 px mit Geräteskalierung 2 nachbilden, was meistens bricht und wie das Design media seine Zoom-Probleme behoben hat.
keywords: Zoom, 200 % Zoom, Browser-Zoom, Textvergrößerung, 960px, Geräteskalierung, Reflow, WCAG-Reflow, Zoom-Unterstützung, versteckte Bedienelemente, Zoom testen, zoom, browser zoom, text resize, device scale factor, reflow, zoom support, hidden controls, testing zoom
type: help
---
# Testen bei Zoomstufen

Diese Seite ist für Designer, die ein Design testen. Viele Besucher lesen mit Browser-Zoom, manche ständig mit 200 %. Die Seite beantwortet:
was Zoom tut, wie Sie ihn schnell testen und was meistens bricht.

## Inhalt

1. [Was Zoom tut](#was-zoom-tut)
2. [Wie Sie testen](#wie-sie-testen)
3. [Was meistens bricht](#was-meistens-bricht)
4. [Ein ausgearbeitetes Beispiel: das Design media](#ein-ausgearbeitetes-beispiel-das-design-media)
5. [Checkliste](#checkliste)

## Was Zoom tut

Browser-Zoom macht jedes CSS-Pixel größer, daher **schrumpft der Viewport in CSS-Pixeln**: Ein 1920 px breites Fenster ist bei 200 % Zoom
ein Viewport von 960 px, bei 150 % einer von 1280 px. Media Queries sehen die kleinere Breite. Ein Desktop-Besucher mit Zoom bekommt also Ihr
Tablet- oder Telefon-Layout, mit Maus und Tastatur.

Die Web Content Accessibility Guidelines verlangen, dass sich Inhalte ohne Verlust von Inhalt oder Funktion auf 200 % vergrößern lassen
und bei 320 CSS-Pixeln Breite ohne waagrechtes Scrollen umbrechen.

## Wie Sie testen

- **Echter Zoom:** Drücken Sie Strg und + (Cmd und + auf einem Mac) bis 200 % in einem 1920 px breiten Fenster.
- **Geräte-Emulation:** In den Entwicklerwerkzeugen des Browsers sieht ein responsiver Viewport von 960 x 1080 mit Pixelverhältnis 2 aus
  wie ein 1920-px-Bildschirm bei 200 %.
- **Automatisch:** Die Browserprüfungen des Projekts verwenden 1440 px mit Skalierung 1, 960 px mit Skalierung 2 und 375 px mit Skalierung 2.

![Die Seite eines Eintrags bei 960 Pixeln Breite, wie bei 200 Prozent Zoom auf einem Laptop: Die Spalten klappen weg, und nichts läuft über](../../../../eng-us/help/images/designers/edit-form-960.webp)

## Was meistens bricht

- **„Für Telefone“ versteckte Bedienelemente.** Eine Regel, die unterhalb eines Breakpoints eine Suchvorschau oder Slider-Pfeile ausblendet,
  blendet sie auch für Desktop-Benutzer mit Zoom aus, die eine Maus haben und sie erwarten.
- **Feste Höhen.** Text wächst, Kästen nicht: Text läuft über oder wird abgeschnitten.
- **Fixierte Kopfbereiche.** Ein Kopfbereich, der bei 100 % 100 px einnimmt, beansprucht einen großen Teil eines vergrößerten Viewports;
  machen Sie ihn kürzer oder unterhalb einer Höhe nicht fixiert (`@media (max-height: ...)`).
- **Waagrechtes Scrollen** durch feste Breiten, breite Tabellen oder lange Wörter; verwenden Sie `max-width: 100%`, Scroll-Container für
  Tabellen und `overflow-wrap: anywhere` für lange Zeichenketten.

## Ein ausgearbeitetes Beispiel: das Design media

Die `stylesheets/zoom-support.css` des Designs media beginnt mit dieser Erklärung: Der Browser-Zoom verkleinert den Viewport in CSS-Pixeln,
das ursprüngliche Theme behandelte jeden schmalen Viewport als Touch-Telefon und versteckte Suchvorschauen und Slider-Steuerungen, und die
Datei gibt sie zurück. Seine `exp-mobile-layout.css` fügt Korrekturen für Tippziele, Eingabegrößen und Bilder für Telefone, Tablets und
200 % Zoom hinzu. Beide werden nach der Build-Ausgabe des Themes geladen und gewinnen daher, ohne sie zu bearbeiten.

## Checkliste

- [ ] 960 px mit Skalierung 2: Jede Funktion der 1440-px-Seite ist da und mit der Maus bedienbar.
- [ ] Kein waagrechtes Scrollen der Seite bei 960 px und 375 px.
- [ ] Kein abgeschnittener Text in Knöpfen, Karten, Reitern.
- [ ] Fixierte Elemente lassen genug Platz für den Inhalt.
- [ ] Heller und dunkler Modus beide bei 200 % geprüft.

## Verwandte Seiten

- [Responsives Design](responsive-design.md)
- [Breakpoints und Containerbreiten](breakpoints-und-containerbreiten.md)
- [Checkliste: Barrierefreiheit](checkliste-barrierefreiheit.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
