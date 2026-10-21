---
title: Bilder in einem Design
audience: designer
category: theme-design
order: 260
summary: Die zwei Arten von Bildern, mit denen ein Designer umgeht: Design-Bilder (Logos, Symbole, Hintergründe), gefunden mit ezimage und ezdesign, und Inhaltsbilder, die Redakteure hochladen und in Aliasen gezeigt werden; wie Sie jede verlinken, Anführungszeichen und Alternativtext.
keywords: Bilder, Design-Bilder, ezimage, ezdesign, Logo, Hintergrundbild, Inhaltsbilder, Bildalias, Alternativtext, img-Tag, Anführungszeichen, URL in CSS, images, design images, background image, content images, image alias, alt text, img tag, quotes, url in css
type: help
---
# Bilder in einem Design

Diese Seite ist für Designer, die Bilder auf Seiten setzen. Sie beantwortet: wohin Design-Bilder gehören und wie Sie sie verlinken, wie
sich Inhaltsbilder unterscheiden und welche Attribute jedes Bild braucht.

## Inhalt

1. [Zwei Arten von Bildern](#zwei-arten-von-bildern)
2. [Design-Bilder mit ezimage](#design-bilder-mit-ezimage)
3. [Jede Design-Datei mit ezdesign](#jede-design-datei-mit-ezdesign)
4. [Bilder in CSS](#bilder-in-css)
5. [Inhaltsbilder](#inhaltsbilder)
6. [Alternativtext und Größe](#alternativtext-und-größe)

## Zwei Arten von Bildern

| | Design-Bilder | Inhaltsbilder |
|---|---|---|
| Wer sie hinzufügt | der Designer, im Design | Redakteure, in der Administration |
| Wo | `design/<design>/images/` | der Inhaltsspeicher (`var/<site>/storage/images/`) |
| Wie verlinkt | Operatoren `ezimage` / `ezdesign` | das Bildfeld, in einem Alias gezeichnet |
| Beispiele | Logo, Symbole, Hintergrundmuster | Artikelfotos, Galeriebilder, Banner |

Ein Logo, das Redakteure ändern können müssen, ist Inhalt (ein Bildfeld eines Objekts mit Website-Einstellungen), kein Design-Bild.

## Design-Bilder mit ezimage

`ezimage` sucht die Datei unter `images/` in jeder Design-Basis der Kaskade und gibt ihre URL in doppelten Anführungszeichen zurück:

```tpl
<img src={'logo.svg'|ezimage} alt="{$site.title|wash}" width="160" height="40">
```

Beachten Sie: keine Anführungszeichen um `{...}` in `src=`; der Operator fügt sie hinzu. Die Art der Anführungszeichen ist sein erster
Parameter:

| Aufruf | Ausgabe |
|---|---|
| `'logo.svg'\|ezimage` | `"/extension/mytheme/design/mytheme/images/logo.svg"` |
| `'logo.svg'\|ezimage( 'single' )` | dasselbe in einfachen Anführungszeichen |
| `'logo.svg'\|ezimage( 'no' )` | keine Anführungszeichen, zur Verwendung in einer Zeichenkette oder einem `data-`-Wert, den Sie selbst in Anführungszeichen setzen |

Eine Datei, die nicht gefunden wird, wird als Template-Warnung gemeldet („Image '...' does not exist in any design“).

## Jede Design-Datei mit ezdesign

`ezdesign` funktioniert genauso für jeden Pfad innerhalb einer Design-Basis:

```tpl
<link rel="icon" href={'images/favicon/favicon.svg'|ezdesign} type="image/svg+xml">
```

Ziehen Sie für Stylesheets `ezcss_load` vor; `ezdesign` ist für einzelne Dateien, die nicht gepackt werden (Symbole, Manifeste,
Downloads, die mit dem Design ausgeliefert werden).

## Bilder in CSS

Verwenden Sie relative Pfade vom Stylesheet zum Bild:

```css
/* design/mytheme/stylesheets/mytheme.css */
.site-header { background-image: url(../images/header-pattern.svg); }
```

Der Packer schreibt den relativen Pfad um, wenn er das Stylesheet in den Cache verschiebt, sodass das Bild weiter gefunden wird. Das
Bild muss in Ihrem Design liegen (relative Pfade werden nicht über die Kaskade gesucht).

## Inhaltsbilder

Ein Bildfeld wird über sein Datentyp-Template gezeichnet, in einem **Alias** (einer in `image.ini` definierten Größe):

```tpl
{attribute_view_gui attribute=$node.data_map.image image_class='medium'}
```

oder, wenn Sie die URL selbst brauchen:

```tpl
{def $image = $node.data_map.image.content['large']}
<img src={$image.url|ezroot} width="{$image.width}" height="{$image.height}" alt="{$image.alternative_text|wash}">
{undef $image}
```

Siehe [Bildaliase](bildaliase.md).

## Alternativtext und Größe

- Jedes `<img>` hat ein `alt`. Dekorative Bilder bekommen `alt=""`; bedeutungstragende Bilder eine kurze Beschreibung. Bei
  Inhaltsbildern ist der Alternativtext des Bildfelds Aufgabe des Redakteurs; geben Sie ihn mit `|wash` aus.
- Geben Sie `width` und `height` an (oder ein CSS-`aspect-ratio`), damit die Seite nicht springt, während Bilder laden
  ([CLS und LCP messen](cls-und-lcp-messen.md)).
- Verwenden Sie SVG für Logos und Symbole, moderne Formate für Fotos ([SVG-Bilder](svg-bilder.md), [Bild-Performance](bildleistung.md)).

## Verwandte Seiten

- [Bildaliase](bildaliase.md)
- [Symbole in einem Design](icons-in-einem-design.md)
- [URL-Operatoren](../templates/url-operatoren.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
