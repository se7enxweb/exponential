---
title: Layouttypen, Zonen und Blöcke
audience: website-creator, developer
category: website-creation
order: 290
summary: Die zwölf Layouttypen und ihre Zonen, die 46 Blocktypen nach Zweck gruppiert, Ansichtstypen, Blockparameter, Container und Collections mit Offset, Limit und Anheften - eine Referenz für die Planung von Layouts.
keywords: Layouttyp, Zonen, Blöcke, Blocktypen, Ansichtstyp, Listenblock, Hero, Container, Collection, manuell, dynamisch, Abfrage, Anheften, Website-Ersteller, layout type, zones, blocks, block types, view type, list block, hero, full_view, tpl_block, container, collection, manual, dynamic, query, pinning, offset, limit, website creator
type: help
---
# Layouttypen, Zonen und Blöcke

Diese Seite richtet sich an Website-Ersteller, die Layouts planen. Sie listet auf, was mitgeliefert wird, damit Sie einen
Layouttyp und Blöcke wählen können, bevor Sie den Editor öffnen. Quelle: [Exponential Layouts](../../../bc/6.0/layouts.md);
die Layouttypen sind in den Blöcken `[LayoutType_*]` von `extension/explayouts/settings/explayouts.ini.append.php`
definiert.

## Inhalt

1. [Layouttypen](#layouttypen)
2. [Blöcke nach Zweck](#blöcke-nach-zweck)
3. [Ansichtstypen und Parameter](#ansichtstypen-und-parameter)
4. [Container](#container)
5. [Collections](#collections)
6. [Planungsblatt](#planungsblatt)

## Layouttypen

Zwölf werden standardmäßig mitgeliefert. Der Typ wird beim Anlegen gewählt und lässt sich nicht ändern, weil Blöcke zu
Zonen gehören.

| Bezeichner | Name | Zonen |
|---|---|---|
| `1_column` | 1 column | `main` |
| `2_column` | 2 columns | `left`, `right` |
| `3_column` | 3 columns | `left`, `main`, `right` |
| `4_column` | 4 columns | `col1`, `col2`, `col3`, `col4` |
| `hero` | Hero + 3 columns | `top`, `left`, `main`, `right` |
| `sidebar_left` | Sidebar left | `sidebar`, `main` |
| `sidebar_right` | Sidebar right | `main`, `sidebar` |
| `featured` | Featured | `hero`, `feature1`, `feature2`, `feature3`, `bottom` |
| `mosaic` | Mosaic | `a`, `b`, `c`, `d`, `e` |
| `layout_1` | Single zone | `main` |
| `layout_2` | Header / Main / Footer | `header`, `post_header`, `main`, `pre_footer`, `footer` |
| `layout_4` | Header / Left / Right / Footer | `header`, `post_header`, `left`, `right`, `pre_footer`, `footer` |

Verwenden Sie `layout_1`, `layout_2` oder `layout_4` für den Rahmen einer ganzen Seite (sie enthalten Kopf- und
Fußzonen) und die Spaltentypen für einen Inhaltsbereich.

## Blöcke nach Zweck

Sechsundvierzig Blocktypen werden mitgeliefert, hier wie in der Referenz gruppiert:

| Zweck | Blocktypen |
|---|---|
| Text und Markup | `text`, `title`, `rich_text`, `markdown`, `html`, `quote` |
| Medien | `image`, `video`, `gallery`, `thumb_gallery`, `grid_gallery`, `slider`, `carousel`, `map` |
| Inhaltslisten | `list`, `grid`, `list_zigzag`, `list_accordion`, `sushi_bar`, `single`, `full_view` |
| Layout und Struktur | `two_columns`, `three_columns`, `four_columns`, `column`, `spacer`, `divider` |
| Oberflächenelemente | `button`, `card`, `accordion`, `tabs`, `alert`, `badge`, `progress` |
| Seitenabschnitte | `hero`, `about`, `features`, `lead`, `logos` |
| Gestaltete Komponenten | die Komponentenfamilie für hero, features, about, logos, quote und lead, jeweils mit mehreren Stilen |
| Templates | `tpl_block` |

Wichtige Blöcke:

- `list` ist das Arbeitspferd: sechs Ansichtstypen (`list`, `grid`, `list_numbered`, `list_zigzag`, `list_accordion`,
  `grid_featured`). Ändern Sie den Ansichtstyp, bevor Sie zu einem anderen Block greifen.
- `full_view` rendert das aktuelle Inhaltselement; die meisten Layouts für den Seitenrahmen brauchen genau einen.
- `html` gibt rohes Markup aus: Geben Sie ihn nur vertrauenswürdigen Autoren, und denken Sie daran, dass eine Content
  Security Policy Inline-Skripte darin blockiert.
- `tpl_block` rendert ein benanntes Template, das ein Entwickler geschrieben hat; der Notausgang für alles Besondere.

## Ansichtstypen und Parameter

Der **Ansichtstyp** eines Blocks ist eine von mehreren Formen, in denen er gerendert wird; seine **Parameter**
konfigurieren ihn (Titel, Anzahl der Elemente, CSS-Klasse, Link). Der Parameter **CSS-Klasse** erlaubt es, einen Block
mit einer Klasse Ihres Themes zu gestalten, ohne ein Template zu ändern.

## Container

`two_columns`, `three_columns`, `four_columns` und `column` enthalten andere Blöcke, sodass eine Zone weiter aufgeteilt
werden kann. Halten Sie die Verschachtelung flach: Zwei Ebenen lassen sich leicht bearbeiten, vier nicht.

## Collections

Listenblöcke besitzen eine Collection:

| Art | Verwendung |
|---|---|
| Manuell | von Hand ausgewählte Elemente über den Content-Browser |
| Dynamisch | eine Abfrage (Unterelemente eines Knotens, eine Inhaltssuche, verschlagwortete Inhalte ...), ausgewertet beim Rendern der Seite |

**Offset** und **Limit** beschneiden das Ergebnis: Ein Block „Hervorgehoben“ mit Limit 1 und ein Block „Mehr“ mit
Offset 1 und Limit 6 ergeben aus einer Abfrage einen Aufmacher plus Liste. **Anheften** setzt ein von Hand ausgewähltes
Element an eine feste Position einer dynamischen Collection
([Anheften](../../../bc/6.0/layouts.md#anheften-ausgewählte-und-dynamische-inhalte-mischen)).

## Planungsblatt

| Seite | Layouttyp | Zone | Blöcke (Typ / Ansichtstyp) | Collection |
|---|---|---|---|---|
| `<front page>` | `layout_2` | `header` | verknüpft mit dem gemeinsamen Kopfbereich | |
| | | `main` | `hero`; `list` / `grid_featured` | dynamisch: neueste Artikel, Limit 7 |
| | | `footer` | verknüpft mit dem gemeinsamen Fußbereich | |

## Verwandte Seiten

- [Landingpages mit dem Layouts-Editor](landingpages-mit-dem-layouts-editor.md)
- [Layout-Regeln](layout-regeln.md)
- [explayouts_ui_api](../../../../eng-us/features/6.0/extensions/explayouts_ui_api.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
