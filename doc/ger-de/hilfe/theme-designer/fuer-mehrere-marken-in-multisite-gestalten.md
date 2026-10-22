---
title: "Für mehrere Marken im Multisite-Betrieb gestalten"
audience: theme-designer
category: theme-design
order: 25
summary: Wie Theme-Designer ein Designsystem für mehrere Marken planen, die aus einer Exponential-Installation ausgeliefert werden - was pro Marke variiert und was gemeinsam bleibt, eine Markenmatrix, Marken-Tokens, Logos und wie das Design auf ein gemeinsames Design plus ein kleines Marken-Design pro Website abgebildet wird.
keywords: Theme-Designer, Multisite, Mehrmarken, Marken, Markenschicht, gemeinsames Design, Marken-Tokens, White Label, Siteaccess, Design-Kaskade, Markenmatrix, theme designer, multisite, multi-brand, brands, brand layer, shared design, brand tokens, white label, siteaccess, design cascade, brand matrix
type: help
---
# Für mehrere Marken im Multisite-Betrieb gestalten

Diese Seite ist für Theme-Designer, die für eine Installation gestalten, die mehrere Websites ausliefert, jede mit
eigener Marke: die Websites einer Unternehmensgruppe, regionale Websites, eine Produktfamilie. Exponential liefert jede
Website über einen eigenen Siteaccess aus, und jeder Siteaccess hat eine eigene Liste von Designs. Das übliche
technische Muster ist ein **gemeinsames Design** mit allen Templates plus ein **kleines Marken-Design** pro Website, das
nur enthält, was sich unterscheidet; es wird samt Einstellungen in
[Designs in einer Multisite-Installation](../designer/theme-design/designs-in-einer-multisite-installation.md#muster-a-gemeinsames-design-plus-eine-markenschicht)
und [Tutorial: zwei Websites, zwei Designs](../designer/theme-design/tutorial-zwei-websites-zwei-designs.md) erklärt. Diese
Seite handelt davon, für dieses Muster zu gestalten.

## Inhalt

1. [Entscheiden, was variieren darf](#entscheiden-was-variieren-darf)
2. [Die Markenmatrix](#die-markenmatrix)
3. [Marken-Tokens](#marken-tokens)
4. [Logos und Markenbilder](#logos-und-markenbilder)
5. [Wenn Templates sich unterscheiden müssen](#wenn-templates-sich-unterscheiden-müssen)
6. [Mehrere Marken prüfen](#mehrere-marken-prüfen)
7. [Stolperfallen](#stolperfallen)

## Entscheiden, was variieren darf

Je günstiger ein Markenunterschied zu bauen und zu pflegen ist, desto freier darf er variieren:

| Unterschied | Kosten | Wo er liegt |
|---|---|---|
| Farben, Akzent, Schriften, Radius | gering | das Token-Stylesheet des Marken-Designs |
| Logo, Favicon, Markenbilder | gering | die Bilder des Marken-Designs |
| Text im Fußbereich, Kontaktangaben | gering bis mittel | Inhalte oder ein kleines Template im Marken-Design |
| Struktur der Navigation | mittel | der Inhaltsbaum jeder Website |
| Markup von Komponenten, Seitenlayouts | hoch | eigene Templates pro Marke |

Vereinbaren Sie schriftlich mit dem Kunden, welche Zeilen sich unterscheiden dürfen. "Alles darf sich unterscheiden"
bedeutet mehrere Themes, nicht ein Theme mit mehreren Marken.

## Die Markenmatrix

Listen Sie die Marken in Spalten und die veränderlichen Dinge in Zeilen auf:

| | Marke Nord | Marke Süd | Gemeinsam |
|---|---|---|---|
| Akzent | blau | grün | |
| Schrift | Markenschrift A | Systemstapel | |
| Radius | | | ein Radius für alle |
| Logo | north.svg, dunkle Version | south.svg, dunkle Version | |
| Layout des Kopfbereichs | | | gemeinsam |
| Links im Fußbereich | eigener Ordner | eigener Ordner | Template gemeinsam |

Aus der Matrix werden die Marken-Designs: eine Spalte, ein kleines Marken-Design.

## Marken-Tokens

Das gemeinsame Design definiert jeden Token mit einem neutralen Standardwert; jedes Marken-Design überschreibt nur die
Tokens in seiner Spalte. Weil die Kaskade zuerst im Marken-Design sucht, ersetzt ein Marken-Stylesheet mit demselben
Dateinamen für diese Website das gemeinsame ([Die Design-Kaskade](../designer/theme-design/die-design-kaskade.md)).
Regeln:

- Marken überschreiben **Rollen**-Tokens (`--site-accent`), nie Stile von Komponenten;
- jede Farbe, die eine Marke überschreibt, erhält ihren dunklen Wert in derselben Markendatei;
- der Akzent jeder Marke erreicht die Kontrastziele auf ihren eigenen Flächen, hell und dunkel
  ([Farbpaletten und Farbabstufungen](farbpaletten-und-farbabstufungen.md#vor-dem-programmieren-prüfen)).

In Ihrem Designtool sind Marken meist ein Modus oder ein Satz Variablen über den gemeinsamen; behalten Sie dieselben
Token-Namen ([Von Designtool-Variablen zu CSS Custom Properties](von-designtool-variablen-zu-css-custom-properties.md)).

## Logos und Markenbilder

Logos haben verschiedene Formen: eine breite Wortmarke, ein quadratisches Symbol. Gestalten Sie den Platz im Kopfbereich
für die Extreme aller Marken, mit fester Höhe und maximaler Breite, und bitten Sie jede Marke um helle und dunkle
Versionen ihres Logos als SVG ([SVG-Bilder](../designer/theme-design/svg-bilder.md)). Favicons und Touch-Icons gibt es
ebenfalls pro Marke ([Favicons und Touch-Icons](../designer/theme-design/favicons-und-touch-icons.md)).

## Wenn Templates sich unterscheiden müssen

Manchmal braucht eine Marke ein anderes Template: einen anderen Fußbereich, eine besondere Startseite. Legen Sie dieses
eine Template in das Marken-Design, wo die Kaskade es zuerst findet. Halten Sie die Zahl solcher Templates klein und
führen Sie sie in der Markenmatrix auf; jedes ist eine Kopie, die bei jeder Änderung mit dem gemeinsamen Design im
Gleichschritt gehalten werden muss.

## Mehrere Marken prüfen

- Prüfen Sie jeden wichtigen Bildschirm in jeder Marke, hell und dunkel; ein Markenakzent, der hell funktioniert, kann
  dunkel versagen.
- Stellen Sie die Marken auf der [Styleguide-Seite](eine-styleguide-seite-bauen.md) nebeneinander, eine pro Siteaccess.
- Änderungen am gemeinsamen Design werden vor dem Release in allen Marken geprüft.

## Stolperfallen

- **Markenwildwuchs:** Kleine einmalige Unterschiede summieren sich zu getrennten Themes. Prüfen Sie neue Wünsche gegen
  die Matrix.
- **Anpassungen an Komponenten pro Marke:** Eine Markendatei, die Komponenten umgestaltet, statt Tokens zu
  überschreiben, lässt sich schwer im Gleichschritt halten.
- **Gemeinsame Caches:** Der Rahmen jeder Website wird pro Siteaccess gecacht; ein Entwickler prüft, dass Markeninhalte
  nicht zwischen Websites durchsickern ([Caching für Designer](../designer/theme-design/caching-fuer-designer.md)).

## Verwandte Seiten

- [Designs in einer Multisite-Installation](../designer/theme-design/designs-in-einer-multisite-installation.md)
- [Designs und Siteaccesses](../designer/theme-design/designs-und-siteaccesses.md)
- [Anpassung und White-Label-Arbeit](../designer/verkauf/anpassung-und-white-label.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
