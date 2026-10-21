---
title: Run-once
audience: designer, developer
category: templates
order: 140
summary: Die Funktion run-once gibt ihren Inhalt nur beim ersten Erreichen während einer Anfrage aus, was verhindert, dass Markup, das mehrere eingebundene Templates brauchen (ein Sprite, ein Dialog, ein Datenblock), doppelt erscheint.
keywords: run-once, einmal ausführen, einmal ausgeben, Duplikate vermeiden, eingebundene Templates, SVG-Sprite, Dialog-Markup, einmal pro Seite, run once, print once, avoid duplicates, included templates, svg sprite, dialog markup, once per page
type: help
---
# Run-once

Diese Seite ist für Designer, deren Teile auf einer Seite mehrmals eingebunden werden können, die aber manches Markup nur einmal
brauchen.

## Inhalt

1. [Syntax](#syntax)
2. [Wann Sie es verwenden](#wann-sie-es-verwenden)
3. [Vorsicht](#vorsicht)

## Syntax

Aus `lib/eztemplate/classes/eztemplateblockfunction.php`:

```tpl
{run-once}
<p>This appears only one time</p>
{/run-once}
```

Wird der Block in einer Anfrage zum ersten Mal erreicht, wird sein Inhalt ausgegeben; jedes weitere Mal wird er übersprungen.
Der Schlüssel ist der Platz des Blocks im Template, sodass zwei verschiedene `{run-once}`-Blöcke voneinander unabhängig sind.

## Wann Sie es verwenden

Ein Galerie-Teil, der für jede Galerie auf einer Seite eingebunden wird, braucht einen gemeinsamen Dialog für die große
Bildansicht:

```tpl
{* design/mytheme/templates/parts/gallery.tpl *}
{run-once}
<dialog class="lightbox" id="lightbox">
    <button type="button" class="lightbox-close">{'Close'|i18n( 'design/mytheme' )}</button>
    <img alt="">
</dialog>
{ezscript_require( array( 'lightbox.js' ) )}
{/run-once}
<ul class="gallery">...</ul>
```

Für Stylesheets und Skripte allein vermeiden `ezcss_require` und `ezscript_require` schon Duplikate.

## Vorsicht

- In einem Template im View-Cache bedeutet „einmal“ einmal beim Schreiben des Caches; das gecachte HTML enthält es dann immer.
- Innerhalb eines Cache-Blocks dasselbe: Die gespeicherte Ausgabe entscheidet.
- Legen Sie gemeinsames Markup lieber in das Pagelayout, wenn jede Seite es braucht.

## Verwandte Seiten

- [Cache-Block](cache-block.md)
- [Include](include.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
