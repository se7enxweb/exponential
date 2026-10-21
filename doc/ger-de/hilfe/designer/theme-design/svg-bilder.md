---
title: SVG-Bilder
audience: designer
category: theme-design
order: 300
summary: SVG in Designs und in Inhalten: Design-SVGs, die Sie selbst ausliefern, SVG-Uploads von Redakteuren (seit Oktober 2026 angenommen und bereinigt, nie skaliert), was das für Templates bedeutet und wie SVGs barrierefrei bleiben und dem Theme folgen.
keywords: SVG, SVG-Bilder, Vektorbilder, Logo als SVG, SVG-Upload, SanitizeSVG, AllowedMIMETypes, expSvgSanitizer, SVG in Inhalten, currentColor, barrierefreies SVG, Bildaliase SVG, svg, svg images, vector images, logo svg, svg upload, svg in content, svg accessibility, image aliases svg
type: help
---
# SVG-Bilder

Diese Seite ist für Designer, die SVG für Logos, Symbole und Illustrationen verwenden. Sie beantwortet: wie sich Design-SVGs und
hochgeladene SVGs unterscheiden, was der Kernel mit hochgeladenen SVGs tut und wie Templates sie behandeln müssen.

## Inhalt

1. [Design-SVGs](#design-svgs)
2. [Von Redakteuren hochgeladene SVGs](#von-redakteuren-hochgeladene-svgs)
3. [Was das für Ihre Templates bedeutet](#was-das-für-ihre-templates-bedeutet)
4. [SVG und dunkler Modus](#svg-und-dunkler-modus)
5. [Barrierefreies SVG](#barrierefreies-svg)

## Design-SVGs

SVG-Dateien im `images/` Ihres Designs sind Ihre eigenen Dateien: Sie haben sie erstellt, Sie vertrauen ihnen. Verlinken Sie sie wie jedes
Bild mit `ezimage` oder verwenden Sie sie als CSS-Hintergründe und -Masken. Entfernen Sie vor dem Ausliefern Editor-Metadaten und
versteckte Ebenen (ein SVG-Optimierer erledigt das) und geben Sie der Datei eine `viewBox`, damit sie skaliert.

## Von Redakteuren hochgeladene SVGs

Seit dem 15. Oktober 2026 nimmt der Bild-Datentyp SVG-Uploads (`.svg`, `.svgz`) an (`settings/image.ini [ImageUploadSettings]`):

```ini
AllowedMIMETypes[]=image/svg+xml
# enabled (default) or disabled
SanitizeSVG=enabled
```

Ein SVG kann Skript enthalten, daher speichert der Kernel einen Upload nie so, wie er kam: `expSvgSanitizer` behält nur Elemente, Attribute
und CSS aus einer Positivliste, und die gespeicherte Datei ist seine Ausgabe. Hochgeladene SVGs werden mit Headern ausgeliefert, die
verhindern, dass sie beim direkten Öffnen etwas ausführen. `SanitizeSVG=disabled` lehnt SVG-Uploads ab; es gibt keinen Weg, eines
unbereinigt zu speichern. Einzelheiten: [SVG-Bilder (Verhaltensänderung)](../../../../eng-us/bc/6.0/svg-images.md).

## Was das für Ihre Templates bedeutet

- **Ein SVG wird nie skaliert.** Jeder Alias eines SVG-Bildes ist die bereinigte Datei selbst (Kommentar in `settings/image.ini`). Seine
  angezeigte Größe kommt aus Ihrem CSS oder aus den Attributen `width` und `height`, nicht aus dem Alias.
- Geben Sie SVG-Bildern in Inhalten eine Größe in CSS, zum Beispiel `max-width: 100%; height: auto;` und eine Breite aus dem Zusammenhang,
  damit eine riesige `viewBox` nicht die ganze Seite einnimmt.
- Zeigen Sie Inhalts-SVGs mit `<img>` (wie es das Datentyp-Template tut), nie indem Sie ihr Markup in die Seite einfügen: In einem `<img>`
  kann ein SVG kein Skript ausführen und die Seite nicht lesen.

## SVG und dunkler Modus

- Ein Design-SVG, das als CSS-Maske gezeichnet oder inline mit `fill="currentColor"` ins Markup geschrieben ist, folgt der Textfarbe und
  damit dem Theme.
- Ein SVG in einem `<img>` sieht die Farben der Seite nicht. Liefern Sie für Logos eine helle und eine dunkle Datei aus und schalten Sie mit
  `<picture>` und `prefers-color-scheme` um, oder wählen Sie Farben, die auf beiden Hintergründen funktionieren.

## Barrierefreies SVG

- In `<img>`: ein `alt` wie bei jedem Bild.
- Dekoratives Inline-SVG: `aria-hidden="true"` und `focusable="false"`.
- Bedeutungstragendes Inline-SVG: `role="img"` und ein `<title>` als erstes Kind oder `aria-label`.

## Verwandte Seiten

- [Bilder in einem Design](bilder-in-einem-design.md)
- [Symbole in einem Design](icons-in-einem-design.md)
- [Dunkler Modus im Überblick](dunkelmodus-ueberblick.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
