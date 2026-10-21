---
title: Einen Blocktyp hinzufügen
audience: designer, developer
category: build-two-ways
order: 530
summary: Einen neuen Blocktyp anlegen, den Redakteure in jedes Layout ziehen können; eine Blockdefinition in der explayouts.ini, eine vorhandene oder neue Handler-Klasse und ein Template, mit der Stelle, an der AvailableBlocks[] gelesen wird, und einem getesteten Beispiel, dem Notice-Block.
keywords: Blocktyp, Blockdefinition, BlockDefinition, AvailableBlocks, Handler, expLayoutsTextBlockHandler, getParameters, explayouts.ini, neuer Block, Notice, Palette, mysite, block type, block definition, handler, new block, palette
type: help
---
# Einen Blocktyp hinzufügen

Ein Blocktyp hat drei Teile: eine **Definition** in der `explayouts.ini`, eine **Handler**-Klasse, die seine Parameter
deklariert, und ein **Template** pro Design. Das Beispiel ist ein *Notice*-Block: ein kurzer Text in einem hervorgehobenen
Kasten, in zwei Ansichtstypen, `info` und `warning`. Er verwendet einen vorhandenen Handler wieder, es wird also kein PHP geschrieben.

## Inhalt

1. [Die Definition](#1-die-definition)
2. [Der Handler](#2-der-handler)
3. [Das Template](#3-das-template)
4. [Aktivieren, leeren, in der Palette finden](#4-aktivieren-leeren-in-der-palette-finden)
5. [Verwenden](#5-verwenden)
6. [Ein eigener Handler](#ein-eigener-handler)

## 1. Die Definition

`extension/mysite/settings/explayouts.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[BlockSettings]
AvailableBlocks[]=notice

# A new block type: a highlighted notice with a text an editor types
[BlockDefinition_notice]
Name=Notice
Handler=expLayoutsTextBlockHandler
ViewTypes[]=info
ViewTypes[]=warning
Category=basic

*/ ?>
```

| Schlüssel | Bedeutet |
|---|---|
| `AvailableBlocks[]` | die Blocktypen, die der Editor anbietet |
| `Name` | der Name in der Palette und am Block |
| `Handler` | die PHP-Klasse, die die Parameter deklariert |
| `ViewTypes[]` | die Ansichten, die ein Redakteur im Reiter Design wählen kann |
| `Category` | die Gruppe der Palette: `basic`, `components`, `listing`, `gallery`, `containers`, `placeholders` |
| `HasCollection=1` | der Block hat Einträge (wie List) |
| `IsContainer=1` und `Placeholders[]` | der Block enthält andere Blöcke (wie Column) |

**Wo die Zeile zählt.** Seit explayouts 1.4.42 beginnt die eigene `explayouts.ini.append.php` von explayouts
`AvailableBlocks[]` (ebenso `AvailableQueries[]`, `SiteAccessMatch[]` und `AllowedTemplateRoots[]`) nicht mehr mit einer
Rücksetzzeile. Das `AvailableBlocks[]=notice` Ihrer Erweiterung bleibt erhalten, wo auch immer die Erweiterung in
`ActiveExtensions[]` steht: Für Blöcke spielt die Position keine Rolle mehr, und ein doppelt aufgeführter Block wird
einmal angeboten (`./console exp:ini where explayouts.ini/BlockSettings/AvailableBlocks admin` zeigt die wirksame
Liste). Um die ganze Liste zu ersetzen, schreiben Sie die Rücksetzzeile dorthin, wo sie nach allen Erweiterungen
gelesen wird: in `settings/override/explayouts.ini.append.php` oder einen Siteaccess
(`./console exp:ini clear explayouts.ini/BlockSettings/AvailableBlocks[] <scope>`). Für Template-Overrides und
`DesignExtensions[]` zählt die Reihenfolge der Erweiterungen weiterhin
([Ihre eigene Design-Erweiterung](eigene-design-erweiterung.md)).

## 2. Der Handler

`expLayoutsTextBlockHandler` deklariert einen Parameter:

```php
'content' => array( 'name' => 'Content', 'type' => 'textarea', 'default' => '' ),
```

Der Editor baut das Formular des Blocks aus `getParameters()` des Handlers; das Feld erscheint im Reiter **Design**.

## 3. Das Template

Das Media-Design bindet für jeden Block `explayouts/block/<definition>.tpl` ein, das Template ist also
`extension/mysite/design/media/templates/explayouts/block/notice.tpl`:

```smarty
{if and( is_set( $block.parameters.content ), $block.parameters.content|ne( '' ) )}
<aside class="notice notice-{$block.view_type|wash}" role="note">
    <p>{$block.parameters.content|wash|nl2br}</p>
</aside>
{/if}
```

Das `block.tpl` des Designs umschließt es mit `<div class="ngl-block ngl-notice ngl-vt-<view type> ...">` und wendet die
gemeinsamen Optionen an (Abstände, CSS-Klasse, Container).

## 4. Aktivieren, leeren, in der Palette finden

```text
./console exp:cache clear --tag=template,content,ini
```

Öffnen Sie ein Layout im Editor, und die Gruppe **Basic** der Palette hat **Notice**:

![Die Blockpalette: die Gruppe Basic endet mit Notice](../../../eng-us/help/build-two-ways/images/nb-01-palette-with-notice.webp)

## 5. Verwenden

![Der Notice-Block im Prefooter abgelegt, noch ohne Inhalt](../../../eng-us/help/build-two-ways/images/nb-02-notice-dropped.webp)

Ziehen Sie **Notice** in eine Zone. Er kommt mit dem Ansichtstyp `default` an; wählen Sie ihn aus, öffnen Sie **Design**,
wählen Sie `warning`, tippen Sie den Text in **Content** ein und klicken Sie auf **Layout veröffentlichen**.

![Der Notice-Block im Reiter Design: Ansichtstyp warning und der Text in Content](../../../eng-us/help/build-two-ways/images/nb-04-notice-design-tab.webp)

Die veröffentlichte Seite stellt dar:

```html
<div class="ngl-block ngl-notice ngl-vt-warning whitespace-top-none whitespace-bottom-none">
  <div class="container">
    <aside class="notice notice-warning" role="note"><p>Prices of the training plans change on 1 November.</p></aside>
  </div>
</div>
```

![Der Hinweistext unter dem Band Most popular articles, noch ohne Gestaltung](../../../eng-us/help/build-two-ways/images/nb-05-notice-on-site.webp)

Er bleibt schlicht, bis das Design `.notice` gestaltet ([Ihre Blöcke gestalten](bloecke-gestalten.md)).

## Ein eigener Handler

Für andere Felder schreiben Sie eine Klasse, die `expLayoutsBlockHandlerInterface` implementiert, mit `getParameters()` (die
Felder: `name`, `type`, `default`) und `getValues( $block )` (was das Template in `$block.values` bekommt), legen sie in
`extension/mysite/classes/` ab, führen `php bin/php/ezpgenerateautoloads.php -e` aus und nennen sie in `Handler=`. Die
Handler in `extension/explayouts/classes/` (zum Beispiel `explayoutstextblockhandler.php`, `explayoutstplblockhandler.php`)
sind kurze Beispiele.

## Verwandte Seiten

- [Einen Ansichtstyp hinzufügen](ansichtstyp-hinzufuegen.md)
- [Wie ein Block seinen Templates zugeordnet wird](zuordnung-block-zu-template.md)
- [Eigene Template-Blöcke](eigene-template-bloecke.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
