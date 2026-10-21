---
title: "Tutorial: eine Landingpage mit Layouts bauen"
audience: designer, editor
category: theme-design
order: 790
summary: Schritt für Schritt vom Design bis zur veröffentlichten Seite: das Pagelayout zum Zeichnen von Layouts vorbereiten, die Blöcke einer Landingpage gestalten, das Layout im Layout-Editor bauen, es mit einer Regel der Seite zuordnen und es auf Telefon, Desktop und mit Zoom prüfen.
keywords: Tutorial, Landingpage, Layout-Editor, Page Builder, Kampagnenseite, Hero-Block, Regel, Zuordnung, Layout veröffentlichen, Blöcke gestalten, resolve_layout, tutorial, landing page, layouts editor, page builder, campaign page, hero block, rule, mapping, publish layout, design blocks
type: help
---
# Tutorial: eine Landingpage mit Layouts bauen

Dieses Tutorial ist für einen Designer, der mit einem Redakteur zusammen eine aus Blöcken zusammengestellte Landingpage möchte: ein Hero,
drei Vorteile, eine Liste von Artikeln, ein Handlungsaufruf. Planen Sie ein bis zwei Stunden ein. Es setzt voraus, dass Ihr Design Layouts
zeichnet ([Layouts für Designer](layouts-fuer-designer.md)); die Designs media und starter tun das bereits.

## Inhalt

1. [Schritt 1: Das Pagelayout zeichnet Layouts](#schritt-1-das-pagelayout-zeichnet-layouts)
2. [Schritt 2: die Blöcke festlegen](#schritt-2-die-blöcke-festlegen)
3. [Schritt 3: die Blöcke gestalten](#schritt-3-die-blöcke-gestalten)
4. [Schritt 4: das Layout bauen](#schritt-4-das-layout-bauen)
5. [Schritt 5: es der Seite zuordnen](#schritt-5-es-der-seite-zuordnen)
6. [Schritt 6: veröffentlichen und prüfen](#schritt-6-veröffentlichen-und-prüfen)

## Schritt 1: Das Pagelayout zeichnet Layouts

In Ihrem `pagelayout.tpl`, dort, wo vorher `{$module_result.content}` stand:

```tpl
{def $page_layout = fetch( 'explayouts', 'resolve_layout', hash() )}
{if $page_layout}
    {include uri='design:explayouts/layout.tpl' layout=$page_layout module_result=$module_result}
{else}
    {$module_result.content}
{/if}
{undef $page_layout}
```

Seiten ohne passende Regel sehen genau aus wie vorher.

## Schritt 2: die Blöcke festlegen

| Abschnitt der Seite | Block (Definition) | View-Typ |
|---|---|---|
| Großes Bild mit Überschrift und Knopf | `hero` | Standard |
| Drei Verkaufsargumente | `three_columns` mit Kindern `rich_text` | Standard |
| Neueste Artikel | `list` mit einer Abfrage-Sammlung | `grid` |
| Handlungsaufruf | `button` | Standard |

Wählen Sie aus den Blöcken, die Ihre Installation anbietet ([Blockdefinitionen](blockdefinitionen.md)); ein neues Aussehen ist meist ein neuer
View-Typ, kein neuer Block.

## Schritt 3: die Blöcke gestalten

Öffnen Sie das Template jedes Blocks in Ihrem Design (kopieren Sie es aus
`extension/explayouts/design/standard/templates/explayouts/block/`, wenn Ihr Design keines hat) und geben Sie ihm das gewünschte Markup;
gestalten Sie dann über Klassen in einem Stylesheet, das das Pagelayout lädt:

```css
.slb-hero { padding-block: var(--site-space-5); background: var(--site-card); }
.slb-hero h1 { font-size: var(--site-text-xl); }
.slb-zone > * + * { margin-block-start: var(--site-space-5); }
```

Verwenden Sie Token, damit die Seite im dunklen Modus funktioniert ([Layout-Blöcke gestalten](layout-bloecke-gestalten.md)).

## Schritt 4: das Layout bauen

Mit dem Redakteur im Layout-Editor der Administration:

1. **Neues Layout**, Layouttyp mit einer Kopf-, Haupt- und Fußzone (oder der Typ, den Ihr Design verwendet), nennen Sie es
   „Frühjahrskampagne“.
2. Verknüpfen Sie die Kopf- und Fußzone mit dem gemeinsamen Layout für Kopf- und Fußbereich, damit die Seite den Rahmen der Website behält
   ([Gemeinsame Layouts und verknüpfte Zonen](gemeinsame-layouts-und-verknuepfte-zonen.md)).
3. Fügen Sie die Blöcke der Reihe nach der Zone `main` hinzu; füllen Sie ihre Felder; geben Sie dem Listenblock seine Sammlung (zum
   Beispiel die Kinder des Ordners News, Limit 6).
4. Vorschau.

## Schritt 5: es der Seite zuordnen

Legen Sie eine Regel an: das Layout „Frühjahrskampagne“, Ziel **node** (die Landingpage) oder **subtree**, optional eine Bedingung **time**
für den Kampagnenzeitraum, eine Priorität über den allgemeinen Regeln ([Layout-Regeln](layout-regeln.md)).

## Schritt 6: veröffentlichen und prüfen

1. Veröffentlichen Sie das Layout (die Website liest nur veröffentlichte Layouts).
2. Öffnen Sie die Landingpage abgemeldet.
3. Prüfen Sie bei 1440 px, bei 960 px mit Skalierung 2 und bei 375 px; im hellen und im dunklen Modus; mit der Tastatur.
4. Prüfen Sie ein einziges `h1` (das Hero) und Überschriften in der richtigen Reihenfolge ([Landmarks und Überschriften](landmarks-und-ueberschriften.md)).
5. Prüfen Sie die Layoutverschiebung: Bilder im Hero und im Raster haben Größen ([CLS und LCP messen](cls-und-lcp-messen.md)).

Zeigt die Seite ein anderes Layout, passt zuerst eine Regel mit höherer Priorität; zeigt sie kein Layout, ist die Regel deaktiviert oder das
Layout nicht veröffentlicht.

## Verwandte Seiten

- [Layouts für Designer](layouts-fuer-designer.md)
- [Block-Ansichtstemplates](block-ansichtstemplates.md)
- [Der Layout-Editor](../../layout-editor.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
