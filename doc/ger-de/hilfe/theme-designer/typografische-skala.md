---
title: "Typografische Skala"
audience: theme-designer
category: theme-design
order: 13
summary: Wie Theme-Designer eine typografische Skala für ein Exponential-Theme wählen und in Tokens verwandeln - wenige Stufen, fließende Größen mit clamp(), Zeilenhöhe und Zeilenlänge, Überschriften, die Redakteure in formatiertem Text anlegen, andere Schriften und Zoom.
keywords: Theme-Designer, typografische Skala, Typografie, Schriftgröße, modulare Skala, clamp, fließende Typografie, Zeilenhöhe, Zeilenlänge, Überschriften, Tokens, rem, theme designer, type scale, typography, font size, modular scale, fluid type, line height, measure, headings
type: help
---
# Typografische Skala

Diese Seite ist für Theme-Designer, die die Textgrößen eines Themes festlegen. Eine typografische Skala ist eine kurze
Liste von Schriftgrößen, jede mit einer Rolle, aus der jeder Text im Theme seine Größe nimmt. In einem Exponential-Theme
wird die Skala zu einer Reihe von CSS Custom Properties neben den Farb-Tokens ([Design-Tokens](../designer/theme-design/design-tokens.md)).
Die Schriften selbst, ihre Dateien und Lizenzen behandelt [Schriften in einem Design](../designer/theme-design/schriften-in-einem-design.md).

## Inhalt

1. [Wie viele Stufen](#wie-viele-stufen)
2. [Die Größen wählen](#die-größen-wählen)
3. [Fließende Größen mit clamp](#fließende-größen-mit-clamp)
4. [Zeilenhöhe und Zeilenlänge](#zeilenhöhe-und-zeilenlänge)
5. [Überschriften, die Redakteure anlegen](#überschriften-die-redakteure-anlegen)
6. [Andere Schriften und Sprachen](#andere-schriften-und-sprachen)
7. [Die Skala als Tokens](#die-skala-als-tokens)
8. [Prüfungen](#prüfungen)

## Wie viele Stufen

Fünf bis sieben Stufen decken eine Inhaltswebsite ab: klein (Metadaten, Bildunterschriften), Fließtext, großer
Fließtext (Vorspann) und drei oder vier Überschriftengrößen. Jede weitere Stufe ist eine Größe, die jemand einmal
verwendet und an die sich niemand erinnert. Wenn zwei Stufen auf dem Bildschirm kaum zu unterscheiden sind, führen Sie
sie zusammen.

## Die Größen wählen

1. Legen Sie zuerst die Größe des **Fließtexts** fest: meist `1rem` (16 px bei den Standardeinstellungen des Browsers),
   für langen Text nie kleiner.
2. Wählen Sie ein **Verhältnis** zwischen den Stufen, zum Beispiel 1,2 für eine ruhige Skala oder 1,25 für mehr Kontrast
   zwischen den Ebenen, und multiplizieren Sie vom Fließtext aus nach oben für Überschriften und einmal nach unten für
   kleinen Text.
3. Runden Sie auf Werte, die richtig aussehen; eine Skala ist ein Ausgangspunkt, kein Gesetz.
4. Verwenden Sie `rem` für Größen, damit sie der Schrifteinstellung im Browser des Besuchers folgen. Pixelgrößen
   ignorieren sie.

## Fließende Größen mit clamp

Große Überschriften, die auf dem Desktop in eine Zeile passen, sind auf dem Telefon zu groß.
`clamp( <min>, <preferred>, <max> )` lässt eine Größe zwischen zwei Grenzen mit dem Viewport wachsen. Das eigene
Token-Beispiel des Projekts verwendet das für seine größte Stufe:

```css
--site-text-xl: clamp(1.5rem, 1.2rem + 1.5vw, 2.25rem);
```

Behalten Sie einen `rem`-Anteil im bevorzugten Wert (`1.2rem + 1.5vw`, nicht `4vw` allein), sonst reagiert der Text nicht
mehr auf Zoom und Schrifteinstellungen des Besuchers. Fließtext bleibt fest.

## Zeilenhöhe und Zeilenlänge

- **Zeilenhöhe** ohne Einheit (`1.5` für Fließtext, `1.2` bis `1.3` für Überschriften), damit sie mit der Größe
  skaliert.
- **Zeilenlänge** von 60 bis 75 Zeichen für Fließtext: Begrenzen Sie die Textspalte mit `max-inline-size: 70ch`, nicht
  mit einer Pixelbreite. In schmalen Layoutzonen begrenzt die Zone sie ohnehin.
- Machen Sie Zeilenhöhe und den Abstand zwischen Absätzen zum Teil der Skala, damit der Rhythmus aus den Tokens folgt
  ([Abstände und Raster](abstaende-und-raster.md)).

## Überschriften, die Redakteure anlegen

Redakteure fügen Überschriften in Felder mit formatiertem Text ein, und die gewählten Ebenen betreffen die Struktur,
nicht das Aussehen. Zwei Folgen:

- Gestalten Sie `h2` bis `h6` innerhalb des Bereichs für formatierten Text in einer sinnvollen absteigenden Reihenfolge;
  Redakteure werden alle verwenden.
- Geben Sie derselben Überschriftenebene an verschiedenen Stellen nicht verschiedene Größen, nur damit ein Mockup
  funktioniert. Muss ein Kartentitel klein aussehen, aber ein `h3` sein, verwenden Sie eine Klasse für das Aussehen und
  behalten Sie die Ebene für die Struktur ([Landmarks und Überschriften](../designer/theme-design/landmarks-und-ueberschriften.md)).

## Andere Schriften und Sprachen

- Schriften wie Arabisch, Devanagari oder Thai brauchen oft eine größere Schrift oder eine höhere Zeile, um bei gleicher
  Größe so gut lesbar zu sein wie lateinischer Text. Planen Sie einen eigenen Schriftstapel und, falls nötig, eine
  Größenanpassung pro Sprache über eine `:lang()`-Regel ([Für viele Sprachen gestalten](fuer-viele-sprachen-gestalten.md)).
- Deutsch, Finnisch und andere Sprachen haben lange Wörter: Eine große Überschrift auf dem Telefon muss umbrechen oder
  trennen (`hyphens: auto` funktioniert, wenn das `lang`-Attribut der Seite stimmt).

## Die Skala als Tokens

```css
/* design/mytheme/stylesheets/tokens.css */
:root {
  --site-text-s:  0.875rem;
  --site-text-m:  1rem;
  --site-text-l:  1.25rem;
  --site-text-xl: clamp(1.5rem, 1.2rem + 1.5vw, 2.25rem);
  --site-text-2xl: clamp(1.875rem, 1.4rem + 2.2vw, 3rem);
  --site-leading-body: 1.5;
  --site-leading-tight: 1.2;
}
```

Tokens für Größe und Zeilenhöhe sind hell und dunkel gleich und werden einmal definiert. Benennen Sie die Stufen nach
Größe (`s`, `m`, `l`) oder nach Rolle (`--site-text-caption`), aber nicht beides und nicht nach Pixelwert.

## Prüfungen

- [ ] Fließtext mindestens `1rem`; kleiner Text nur für Nebeninformationen.
- [ ] Jede Größe in den Stylesheets kommt aus einem Token.
- [ ] Bei 200 % Browser-Zoom überlappt kein Text und wird nicht abgeschnitten ([Bei verschiedenen Zoomstufen testen](../designer/theme-design/testen-bei-zoomstufen.md)).
- [ ] Überschriften `h2` bis `h6` im formatierten Text gestaltet.
- [ ] Lange Wörter brechen auf einem 375 px breiten Bildschirm um.

## Verwandte Seiten

- [Abstände und Raster](abstaende-und-raster.md)
- [Von Designtool-Variablen zu CSS Custom Properties](von-designtool-variablen-zu-css-custom-properties.md)
- [Schriften in einem Design](../designer/theme-design/schriften-in-einem-design.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
