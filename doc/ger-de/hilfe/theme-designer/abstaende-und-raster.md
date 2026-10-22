---
title: "Abstände und Raster"
audience: theme-designer
category: theme-design
order: 14
summary: Wie Theme-Designer Abstände und ein Layoutraster für ein Exponential-Theme festlegen - eine Abstandsskala als Tokens, Abstände innerhalb und zwischen Komponenten, ein Seitenraster, das auch in schmalen Layoutzonen funktioniert, logische Eigenschaften und was Sie übergeben.
keywords: Theme-Designer, Abstände, Abstandsskala, Raster, Layoutraster, Spaltenabstände, Spalten, Container, Rhythmus, Tokens, logische Eigenschaften, Layoutzonen, theme designer, spacing, spacing scale, grid, layout grid, gutters, columns, container, rhythm, logical properties, layout zones
type: help
---
# Abstände und Raster

Diese Seite ist für Theme-Designer, die den Raum zwischen Dingen und die Spalten festlegen, in denen Dinge stehen. In
einem Exponential-Theme wird beides zu Tokens und einer kleinen Zahl von Layoutklassen. Die Breakpoints und
Containerbreiten selbst behandelt [Breakpoints und Containerbreiten](../designer/theme-design/breakpoints-und-containerbreiten.md),
die Abstände zwischen Layoutblöcken [Layout-Blöcke gestalten](../designer/theme-design/layout-bloecke-gestalten.md).

## Inhalt

1. [Eine Abstandsskala](#eine-abstandsskala)
2. [Abstand innen und Abstand zwischen](#abstand-innen-und-abstand-zwischen)
3. [Das Seitenraster](#das-seitenraster)
4. [Raster innerhalb von Zonen](#raster-innerhalb-von-zonen)
5. [Logische Eigenschaften](#logische-eigenschaften)
6. [Die Skala als Tokens](#die-skala-als-tokens)
7. [Was Sie übergeben](#was-sie-übergeben)

## Eine Abstandsskala

Wählen Sie fünf bis sieben Stufen und verwenden Sie nichts anderes. Eine Skala, bei der jede Stufe etwa das 1,5- bis
2-Fache der vorigen ist (zum Beispiel 4, 8, 16, 24, 40 Pixel, in `rem` geschrieben), ergibt klare Unterschiede zwischen
"zusammengehörig" und "getrennt". Werte dazwischen sind auch im Mockup nicht erlaubt: In den meisten Designtools können
Sie die Skala als Abstandsvariablen festlegen, sodass ein Verschieben um 3 Pixel als Fehler sichtbar wird.

## Abstand innen und Abstand zwischen

Legen Sie zwei Regeln fest und schreiben Sie sie neben die Skala:

- **Innerhalb** einer Komponente (Innenabstand einer Karte, Abstand zwischen einer Beschriftung und ihrem Feld): kleine
  Stufen.
- **Zwischen** Komponenten und Abschnitten: größere Stufen, und noch größere zwischen Abschnitten, die nichts miteinander
  zu tun haben.

Der Abstand zwischen zwei Elementen gehört zu ihrem Container, nicht zu einem der beiden Elemente. Geben Sie Stapeln
einen `gap` (in Flex oder Grid) statt jedem Kind einen unteren Außenabstand; die Inhalte der Redakteure erzeugen dann nie
einen doppelten Abstand oder einen fehlenden am Ende einer Liste.

Formatierter Text ist die Ausnahme: Absätze, Listen und Überschriften in einem Textfeld brauchen vertikale Außenabstände,
weil es keinen Container pro Element gibt. Legen Sie sie einmal für den Bereich des formatierten Texts fest, in Stufen
der Skala.

## Das Seitenraster

Eine Inhaltswebsite braucht meist nur:

- einen **Container** mit einer maximalen Breite und einem seitlichen Innenabstand, der bei größeren Breiten wächst;
- eine **Inhaltsspalte**, begrenzt auf eine lesbare Zeilenlänge ([Typografische Skala](typografische-skala.md#zeilenhöhe-und-zeilenlänge));
- ein **zweispaltiges** Layout (Inhalt und Seitenleiste), das auf kleinen Bildschirmen einspaltig wird.

Ein Zwölf-Spalten-Raster im Designtool ist eine gute Zeichenhilfe, aber Sie brauchen keine zwölf Spaltenklassen im CSS.
Beschreiben Sie die Layouts nach ihrer Absicht ("Inhalt und Seitenleiste, 2 zu 1, unter 60em gestapelt"), und lassen
Sie CSS Grid sie ausdrücken.

## Raster innerhalb von Zonen

Mit Exponential Layouts setzen Redakteure Blöcke in Zonen, und eine Zone kann die volle Breite haben, eine Spalte eines
zweispaltigen Container-Blocks oder eine schmale Seitenleiste sein. Ein Kartenraster, das für 1200 Pixel gestaltet ist,
erscheint auch in einer 300 Pixel breiten Zone. Deshalb:

- gestalten Sie jeden Ansichtstyp eines Blocks in voller Breite und in Seitenleistenbreite ([Layoutblöcke entwerfen](layoutbloecke-gestalten.md));
- bemessen Sie Raster im CSS nach dem verfügbaren Platz (`repeat( auto-fill, minmax( 16rem, 1fr ) )`) oder mit
  Container-Queries, nicht nach dem Viewport ([Breakpoints und Containerbreiten](../designer/theme-design/breakpoints-und-containerbreiten.md#container-queries)).

## Logische Eigenschaften

Schreiben Sie Abstände mit logischen Eigenschaften (`margin-block`, `padding-inline-start`, `inset-inline-end`) statt
mit left/right/top/bottom. Sie folgen der Schreibrichtung, sodass eine Sprache, die von rechts nach links läuft, ein
gespiegeltes Layout ohne zweites Stylesheet erhält ([Logische CSS-Eigenschaften](../designer/theme-design/logische-css-eigenschaften.md)).
Kennzeichnen Sie im Mockup, welche Abstände von der Richtung abhängen (ein Symbol vor einer Beschriftung) und welche
nicht.

## Die Skala als Tokens

Der Startsatz an Tokens des Projekts legt die Skala als `--site-space-1` bis `--site-space-5` fest
([Design-Tokens](../designer/theme-design/design-tokens.md#ein-startsatz)). Verwenden Sie dieselben Namen in Ihrem
Designtool, damit ein Wert, der aus einem Mockup abgelesen wird, schon der Name ist, den der Entwickler tippt:

```css
.mytheme-stack       { display: flex; flex-direction: column; gap: var(--site-space-3); }
.mytheme-section + .mytheme-section { margin-block-start: var(--site-space-5); }
.mytheme-card        { padding: var(--site-space-4); }
```

Abstands-Tokens haben hell und dunkel denselben Wert und werden einmal definiert.

## Was Sie übergeben

- Die Abstandsskala mit Namen und Werten.
- Die Regeln für innen und zwischen, in zwei Sätzen.
- Die Seitenlayouts nach Absicht, mit der Breite, bei der sich jedes ändert.
- Jeden Ansichtstyp eines Blocks in voller Breite und in Seitenleistenbreite.
- Einen Hinweis zu richtungsabhängigen Abständen für Sprachen, die von rechts nach links laufen.

## Verwandte Seiten

- [Responsives Design](../designer/theme-design/responsive-design.md)
- [Layouttypen und Zonen](../designer/theme-design/layouttypen-und-zonen.md)
- [Komponenteninventar](komponenteninventar.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
