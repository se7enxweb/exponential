---
title: "Bildzuschnitte und Art Direction gestalten"
audience: theme-designer
category: theme-design
order: 27
summary: Wie Theme-Designer die Bildgrößen und Zuschnitte eines Exponential-Themes planen - ein Inventar der Bildplätze, Bild-Aliase und ihre Filter, was die Konverter zuschneiden können und was nicht, CSS object-fit, responsive Quellen, der Bildeditor für Redakteure und was Sie dem Entwickler übergeben.
keywords: Theme-Designer, Bilder, Bild-Aliase, Zuschnitt, Art Direction, Seitenverhältnis, object-fit, srcset, picture, image.ini, Geometriefilter, ezie, Bildeditor, responsive Bilder, theme designer, images, image aliases, crop, aspect ratio, geometry filters, image editor, responsive images
type: help
---
# Bildzuschnitte und Art Direction gestalten

Diese Seite ist für Theme-Designer, die entscheiden, wie Bilder erscheinen: in welchen Größen, in welchen Formen und was
mit einem Hochformatfoto in einem Querformatplatz geschieht. In Exponential laden Redakteure ein Original hoch, und das
Theme fordert **Bild-Aliase** an: benannte, erzeugte Varianten, die Filter wie "auf 480 mal 480 verkleinern" herstellen.
Die Einstellungen und der Template-Code stehen in [Bildaliase](../designer/theme-design/bildaliase.md), die
Performance in [Performance von Bildern](../designer/theme-design/bildleistung.md). Diese Seite ist die Seite des
Designs.

## Inhalt

1. [Das Inventar der Bildplätze](#das-inventar-der-bildplätze)
2. [Was ein Alias kann](#was-ein-alias-kann)
3. [Feste Seitenverhältnisse: im Alias oder im CSS zuschneiden](#feste-seitenverhältnisse-im-alias-oder-im-css-zuschneiden)
4. [Art Direction](#art-direction)
5. [Was Redakteure tun können](#was-redakteure-tun-können)
6. [Größen für scharfe und leichte Bilder](#größen-für-scharfe-und-leichte-bilder)
7. [Das Bilderblatt](#das-bilderblatt)

## Das Inventar der Bildplätze

Listen Sie jede Stelle auf, an der ein Bild erscheint, mit seiner angezeigten Größe bei jeder Breite:

| Platz | Verhältnis | Größte Anzeigebreite | Kleinste | Kann leer sein? |
|---|---|---|---|---|
| Hero eines Artikels | 16:9 | 1200 px | 375 px | ja |
| Karte | 3:2 | 400 px | 300 px | ja |
| Vorschaubild eines Zeileneintrags | 1:1 | 96 px | 64 px | ja |
| Einbettung in formatiertem Text | wie hochgeladen | Textspalte | 375 px | nein |
| Open-Graph-Bild | wie hochgeladen | 1200 px | | ja ([Bilder zum Teilen und für Metadaten](bilder-fuer-social-sharing-und-metadaten.md)) |

Halten Sie die Zahl der Seitenverhältnisse klein: Jedes ist ein Alias (oder mehrere) und ein Testfall.

## Was ein Alias kann

Ein Alias wird in `image.ini` definiert (in `settings/image.ini.append.php` Ihrer Design-Erweiterung) als eine Liste
von Filtern, die auf das Original oder auf einen anderen Alias angewendet werden. Die eigenen Aliase des Kernels
(`reference`, `small`, `medium`, `large` und andere) verwenden `geometry/scaledownonly`, das das Bild in einen Kasten
einpasst, ohne es zu vergrößern, und seine Proportionen behält. Die Filter, die beide Bildkonverter unterstützen, sind
als Kommentare in `settings/image.ini` aufgeführt; darunter:

| Filter | Wirkung |
|---|---|
| `geometry/scaledownonly=<w>;<h>` | in einen Kasten einpassen, nie vergrößern, Proportionen behalten |
| `geometry/scalewidthdownonly=<w>` | auf eine Breite einpassen, nie vergrößern |
| `geometry/scaleexact=<w>;<h>` | genau diese Größe erzwingen (verzerrt, wenn das Verhältnis abweicht) |
| `geometry/crop=...` | ein Rechteck an einer festen Position ausschneiden |

Ein Alias kann ein Bild also verkleinern und ein festes Rechteck ausschneiden, aber er weiß nicht, wo sich das Motiv
eines Fotos befindet. In den mitgelieferten Filtern gibt es keinen Zuschnitt nach Fokuspunkt. Ein fester Zuschnitt aus
der oberen linken Ecke ergibt bei einem Quer- und einem Hochformat sehr unterschiedliche Ergebnisse; prüfen Sie das mit
echten Uploads.

## Feste Seitenverhältnisse: im Alias oder im CSS zuschneiden

Für Plätze mit festem Seitenverhältnis gibt es zwei Wege:

| Weg | Wie | Abwägung |
|---|---|---|
| CSS | der Alias verkleinert; der Platz hat `aspect-ratio: 3 / 2` und das Bild `object-fit: cover` | der sichtbare Ausschnitt ist zentriert (oder mit `object-position` gesetzt); etwas mehr wird heruntergeladen |
| Alias | verkleinern, dann im Alias auf das Verhältnis zuschneiden | kleinere Dateien; die Position des Zuschnitts ist für jedes Bild fest |

Für die meisten Inhaltswebsites ist CSS die sicherere Wahl: Das vollständige Bild bleibt erhalten, der Zuschnitt passt
sich dem Platz an, und `object-position` kann den sichtbaren Teil für einen ganzen Platztyp verschieben (zum Beispiel
nach oben für Porträts von Menschen). Setzen Sie nie ein `object-position` pro Bild in ein `style=""`-Attribut;
verwenden Sie eine Klasse oder nehmen Sie den zentrierten Zuschnitt hin.

## Art Direction

Art Direction heißt, bei verschiedenen Breiten verschiedene Zuschnitte zu zeigen: ein breites Panorama auf dem Desktop,
einen engeren Ausschnitt auf dem Telefon. Mit einem Original und Filtern mit fester Position ist echte Art Direction pro
Bild begrenzt. Möglichkeiten, nach Aufwand geordnet:

1. **Nur CSS:** dasselbe Bild mit anderem `aspect-ratio` und `object-position` pro Breakpoint.
2. **Zwei Aliase in einem `picture`-Element:** zum Beispiel ein breiter Alias für große Bildschirme und ein
   quadratischer für kleine, mit `media`-Bedingungen gewählt ([Bildaliase](../designer/theme-design/bildaliase.md#responsive-bilder)
   zeigt `srcset`; dieselben Aliase funktionieren in `<source>`-Elementen).
3. **Zwei Bildfelder:** Braucht der Kunde Kontrolle, erhält die Inhaltsklasse ein zweites, optionales Bild für kleine
   Bildschirme. Das ist eine Änderung am Inhaltsmodell ([Ein Design auf Inhaltsklassen abbilden](ein-design-auf-inhaltsklassen-abbilden.md)).

## Was Redakteure tun können

Wo die Erweiterung ezie (der Online-Bildeditor) installiert ist, können Redakteure ein Bild beim Bearbeiten seines
Objekts zuschneiden, drehen und spiegeln. Die Änderung betrifft das gespeicherte Bild, also folgt ihr jeder Alias. Das ist
ein gutes Werkzeug für "dieses Foto braucht einen engeren Ausschnitt" und ein Grund, CSS-Zuschnitte zu wählen, die den
größten Teil des Bildes behalten.

## Größen für scharfe und leichte Bilder

- Der Alias sollte etwa der größten angezeigten Breite entsprechen, bei kleinen Plätzen, die auf Bildschirmen mit hoher
  Pixeldichte scharf sein müssen, dem Doppelten, angeboten über `srcset`.
- Jeder Alias wird bei der ersten Verwendung erzeugt und aufbewahrt, ein Template, das viele Aliase nennt, erzeugt also
  viele Dateien pro Bild. Bevorzugen Sie wenige Aliase, jeder in mehreren Plätzen verwendet.
- Benennen Sie Aliase mit dem Präfix Ihres Themes (`mytheme_card`), damit sie nie mit denen einer anderen Erweiterung
  kollidieren.
- Bilder im ersten Bildschirm werden nicht verzögert geladen, die übrigen schon ([CLS und LCP messen](../designer/theme-design/cls-und-lcp-messen.md)).

## Das Bilderblatt

Übergeben Sie eine Zeile pro Platz:

| Platz | Name des Alias | Filter | Verhältnis und Zuschnitt | Partner in srcset | Design ohne Bild |
|---|---|---|---|---|---|
| Karte | `mytheme_card` | auf 480 mal 480 verkleinern | 3:2, CSS cover, zentriert | `mytheme_card_2x` | Karte nur mit Text |

## Verwandte Seiten

- [Bildaliase](../designer/theme-design/bildaliase.md)
- [Bilder und Bild-Aliase](../cms-entwickler/bilder-und-bildvarianten.md)
- [SVG-Bilder](../designer/theme-design/svg-bilder.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
