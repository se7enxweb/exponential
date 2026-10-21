---
title: Template-Funktionen und Includes
audience: app-creator, developer
category: app-creation
order: 180
summary: Die Funktionen der Template-Sprache, die eine Anwendung am häufigsten nutzt - include, die *_view_gui-Funktionen, def/set/undef, foreach, set-block, cache-block mit Schlüsseln und Ablaufzeit und wie Sie die Templates einer Erweiterung strukturieren.
keywords: Template-Funktionen, Includes, Cache-Blöcke, Schlüssel, Ablaufzeit, Templates, Anwendungsentwickler, template functions, include, node_view_gui, attribute_view_gui, content_view_gui, def, set, undef, foreach, set-block, cache-block, keys, expiry, subtree_expiry, templates, app creator
type: help
---
# Template-Funktionen und Includes

Diese Seite richtet sich an Anwendungsentwickler, die die Templates ihrer Erweiterung schreiben. Die Template-Sprache ist
in [6. Die Template-Sprache in zehn Minuten](../../../anleitungen/templates-und-design.md#6-die-template-sprache-in-zehn-minuten)
beschrieben; diese Seite sammelt, was Anwendungen am häufigsten verwenden.

## Inhalt

1. [Variablen](#variablen)
2. [Includes und Template-Teile](#includes-und-template-teile)
3. [Die Ansichtsfunktionen](#die-ansichtsfunktionen)
4. [Schleifen und Bedingungen](#schleifen-und-bedingungen)
5. [Cache-Blöcke](#cache-blöcke)
6. [Die Templates einer Erweiterung strukturieren](#die-templates-einer-erweiterung-strukturieren)

## Variablen

| Funktion | Verwendung |
|---|---|
| `{def $a = ...}` | deklarieren (einmal je Template) |
| `{set $a = ...}` | ändern |
| `{undef $a}` | freigeben; geben Sie am Ende eines Templates frei, was Sie definiert haben |
| `{set-block variable=$html}...{/set-block}` | Ausgabe in einer Variablen auffangen |

## Includes und Template-Teile

```text
{include uri='design:expdemo/parts/item_card.tpl' item=$item show_image=true()}
```

Parameter werden zu Variablen des eingebundenen Templates; es wird über die Design-Kette gefunden, sodass Websites es
überschreiben können. Verwenden Sie Teile für Markup, das sich wiederholt.

## Die Ansichtsfunktionen

| Funktion | Rendert |
|---|---|
| `{node_view_gui content_node=$node view='line'}` | einen Knoten mit `node/view/line.tpl` oder dessen Override |
| `{content_view_gui content_object=$object view='embed'}` | ein Objekt |
| `{attribute_view_gui attribute=$node.data_map.body}` | ein Attribut mit dem Template seines Datentyps |
| `{attribute_edit_gui attribute=$attribute}` | das Bearbeitungsfeld eines Attributs |

## Schleifen und Bedingungen

```text
{foreach $items as $index => $item}
  {if $index|eq( 0 )}<h2>{$item.title|wash}</h2>{else}<p>{$item.title|wash}</p>{/if}
  {delimiter}<hr>{/delimiter}
{/foreach}
```

`{foreach $list as $item max 5 offset 2}` begrenzt die Schleife; `{break}` und `{continue}` funktionieren wie in PHP.

## Cache-Blöcke

```text
{cache-block keys=array( $item.id, $current_user.contentobject_id ) expiry=600}
  ... expensive output ...
{/cache-block}
```

| Parameter | Bedeutung |
|---|---|
| `keys` | wovon die Ausgabe abhängt |
| `expiry` | Sekunden; Standard ist 7200 (zwei Stunden, `eZTemplateCacheFunction::DEFAULT_TTL`); `0` läuft nie zeitbasiert ab |
| `subtree_expiry` | wird geleert, wenn unterhalb dieses Knotens oder dieser Adresse Inhalt veröffentlicht wird |
| `ignore_content_expiry` | wird durch Veröffentlichen nicht geleert |

Daten aus Ihren eigenen Tabellen sind nicht an das Veröffentlichen gebunden: Setzen Sie eine `expiry`, die dazu passt,
wie veraltet die Daten sein dürfen, oder leeren Sie den Cache `template-block`, wenn sich Ihre Daten ändern
([Caching in Anwendungen](caching-in-anwendungen.md)).

## Die Templates einer Erweiterung strukturieren

```text
extension/expdemo/design/standard/templates/expdemo/
├── list.tpl            one per view
├── item.tpl
└── parts/item_card.tpl reusable parts
```

- Ein Template je Ansicht, nach ihr benannt.
- Teile in `parts/`; Override-Templates (je Klasse) in `override/templates/`.
- Kein Inline-`<script>` oder -`<style>`, kein `style=""`: CSS und JS laufen über die Listen in `design.ini`
  ([Stylesheets und Skripte](../website-ersteller/stylesheets-und-skripte.md)).
- `|wash` auf jedem ausgegebenen Wert; `|i18n( 'extension/expdemo' )` auf jedem Text.
- Prüfen Sie jedes Template mit `php bin/php/eztemplatecheck.php`.

## Verwandte Seiten

- [Ergebnisse von Ansichten und das Pagelayout](ergebnisse-von-ansichten-und-das-pagelayout.md)
- [Muster für Template-Funktionen](../../../../eng-us/standards/patterns/template-function.md)
- [View-Cache und Cache-Blöcke](../website-ersteller/view-cache-und-cache-bloecke.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
