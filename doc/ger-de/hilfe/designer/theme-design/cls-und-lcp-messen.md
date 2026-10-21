---
title: CLS und LCP messen
audience: designer
category: theme-design
order: 670
summary: Zwei Zahlen, die sagen, wie sich eine Seite anfühlt: Cumulative Layout Shift (Ziel 0,05 oder weniger) und Largest Contentful Paint; was in Exponential-Designs schlechte Werte verursacht und wie Sie sie im Browser messen.
keywords: CLS, Cumulative Layout Shift, LCP, Largest Contentful Paint, Core Web Vitals, Layoutverschiebung, Web Vitals, Performance-Messung, Lighthouse, Ruckeln, cumulative layout shift, largest contentful paint, layout shift, web vitals, performance measurement, lighthouse, jank
type: help
---
# CLS und LCP messen

Diese Seite ist für Designer, die prüfen, wie eine Seite lädt. **Cumulative Layout Shift (CLS)** misst, wie stark Inhalte springen,
während die Seite lädt; **Largest Contentful Paint (LCP)** misst, wann das größte sichtbare Element (meist das Hauptbild oder die
Überschrift) gezeichnet wird.

## Inhalt

1. [Ziele](#ziele)
2. [Was Layoutverschiebung verursacht](#was-layoutverschiebung-verursacht)
3. [Was das größte Zeichnen verzögert](#was-das-größte-zeichnen-verzögert)
4. [Wie Sie messen](#wie-sie-messen)

## Ziele

- CLS höchstens **0,05**: das Ziel, das das Projekt für seine eigenen Seiten verwendet (strenger als die oft genannten 0,1).
- LCP so früh wie möglich; ein übliches Ziel sind unter 2,5 Sekunden auf einem Telefon der Mittelklasse.

## Was Layoutverschiebung verursacht

| Ursache | Abhilfe |
|---|---|
| Bilder ohne Breite und Höhe | geben Sie `width` und `height` des Alias aus oder setzen Sie `aspect-ratio` |
| Webfonts, die mit sehr anderen Maßen tauschen | ein Ersatzstapel nahe am Webfont; wenige Schnitte |
| Banner und Einwilligungsleisten, die über den Inhalt geschoben werden | legen Sie sie darüber (feste Position) oder reservieren Sie ihren Platz |
| Einbettungen (Videos, Karten) ohne Kasten | ein Platzhalter mit dem endgültigen Seitenverhältnis, wie es der Platzhalter für die Video-Einwilligung des Designs media tut |
| ein Theme, das nach dem ersten Zeichnen gesetzt wird | setzen Sie es im Kopf vor den Stylesheets ([Der Theme-Umschalter und der Schutz vor dem ersten Zeichnen](theme-umschalter-und-pre-paint-guard.md)) |
| Seitenspalten, die sich öffnen, nachdem Skripte laufen | zeichnen Sie den gemerkten Zustand ab dem ersten Zeichnen (admin4 verwendet eine Klasse `a4-preload`, bis sein Spaltenskript gelaufen ist) |

## Was das größte Zeichnen verzögert

- das Hauptbild, lazy geladen oder in einer weit größeren Größe als angezeigt;
- Skripte im Kopf, die das Rendern blockieren;
- viele Stylesheets oder Schrift-CSS von Drittanbietern, bevor irgendein Text gezeichnet werden kann;
- eine langsame Serverantwort: eine nicht zwischengespeicherte Vollansicht mit vielen fetch-Aufrufen ([Performance von fetch](../vorlagen/fetch-leistung.md)).

## Wie Sie messen

1. Öffnen Sie die Seite in einem privaten Fenster, abgemeldet.
2. Entwicklerwerkzeuge, Bereich Performance oder Lighthouse: Zeichnen Sie einen Seitenaufruf auf; lesen Sie CLS und LCP und die
   Elemente dahinter.
3. Wiederholen Sie das bei 375 px (Telefon-Emulation, gedrosseltes Netz) und bei 1440 px.
4. Wiederholen Sie es nach einmaligem Leeren der Caches: Der erste Besucher nach einer Veröffentlichung bekommt den langsamen Weg.

## Verwandte Seiten

- [Performance von Themes](theme-leistung.md)
- [Bild-Performance](bildleistung.md)
- [Checkliste: Performance](checkliste-leistung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
