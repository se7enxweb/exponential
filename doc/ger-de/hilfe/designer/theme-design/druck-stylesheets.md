---
title: Druck-Stylesheets
audience: designer
category: theme-design
order: 330
summary: Seiten gut druckbar machen: Druckregeln in Ihrem Stylesheet oder einer eigenen Druckdatei, was auszublenden ist, Link-URLs, Seitenumbrüche, Farben und der View-Modus print, den der Kernel anbietet.
keywords: Druck, Druck-Stylesheet, media print, @media print, Druck-CSS, Seitenumbruch, Druckansicht, druckbare Seite, Navigation beim Drucken ausblenden, print, print stylesheet, print css, page break, print view, printable page, hide navigation when printing
type: help
---
# Druck-Stylesheets

Diese Seite ist für Designer, die möchten, dass Artikel, Rezepte und Formulare sauber gedruckt werden. Sie beantwortet: wohin Druckregeln
gehören und was sie meist enthalten.

## Inhalt

1. [Wohin Druckregeln gehören](#wohin-druckregeln-gehören)
2. [Ein Satz Regeln für den Anfang](#ein-satz-regeln-für-den-anfang)
3. [Der View-Modus print](#der-view-modus-print)
4. [Testen](#testen)

## Wohin Druckregeln gehören

Entweder in Ihr Haupt-Stylesheet:

```css
@media print {
  /* rules below */
}
```

was keine zusätzliche Anfrage kostet, oder in eine eigene Datei, die nur für den Druck geladen wird:

```tpl
{ezcss( array( 'print.css' ), 'print' )}
```

(der zweite Parameter von `ezcss` ist das Attribut `media`). Das Design ezwebin verlinkt sein `stylesheets/print.css` mit `media="print"` in
`page_head_style.tpl`.

## Ein Satz Regeln für den Anfang

```css
@media print {
  .site-header nav, .site-footer, .skip-link, .cookie-banner, .share-buttons { display: none; }
  body { color: #000; background: #fff; font-size: 11pt; }
  a[href^="http"]::after { content: " (" attr(href) ")"; font-size: 0.9em; }
  img, figure, table, pre { break-inside: avoid; }
  h2, h3 { break-after: avoid; }
}
```

- Blenden Sie Navigation, Banner und interaktive Bedienelemente aus.
- Schwarz auf Weiß, gleich welches Bildschirm-Theme: Der Druck braucht Ihren dunklen Modus nicht.
- Geben Sie nach externen Links deren Ziel aus.
- Vermeiden Sie Umbrüche in Bildern, Tabellen und Code.

## Der View-Modus print

Der Kernel hat ein Knoten-View-Template `print` (`design/standard/templates/node/view/print.tpl`), erreichbar als
`/content/view/print/<node id>`. Ein Design kann es für eine reduzierte Seite überschreiben. Für die meisten Websites ist `@media print`
auf der normalen Seite einfacher und braucht keine zusätzliche URL.

## Testen

Verwenden Sie die Druckvorschau des Browsers und „Druckmedien emulieren“ der Entwicklerwerkzeuge, um Druckregeln zu untersuchen.

## Verwandte Seiten

- [Stylesheets laden](stylesheets-laden.md)
- [View-Modi](../templates/ansichtsmodi.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
