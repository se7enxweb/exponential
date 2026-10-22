---
title: Ansichtsmodi
audience: cms-developer
category: cms-development
order: 24
summary: Für CMS-Entwickler, wie ein Inhalt in vielen Zusammenhängen gezeigt wird. Die Seite behandelt full, line, embed und die anderen mitgelieferten Ansichtsmodi, wie ein Modus gewählt wird, ein Template je Klasse und Modus, eigene Ansichtsmodi für Karten und Teaser, Ansichtsmodi in Layouts und Einbettungen und welche Modi zwischengespeichert werden.
keywords: Ansichtsmodi, View-Modi, Full View, Line View, Embed View, embed-inline, Karte, Teaser, node_view_gui, content_view_gui, node/view, eigener Ansichtsmodus, CachedViewModes, Override je Ansichtsmodus, view modes, full view, line view, embed view, card, custom view mode
type: help
---
# Ansichtsmodi

Diese Seite richtet sich an Entwickler, bei denen derselbe Artikel wie eine Seite, ein Listeneintrag, eine Karte und ein
eingebetteter Kasten aussehen muss. Exponential trennt, **was** gezeigt wird (das Objekt), von **wie** (dem
Ansichtsmodus): Jeder Zusammenhang fragt nach einem Ansichtsmodus, und jede Klasse kann je Modus ein eigenes Template
haben. Templates, die Inhalte auflisten, müssen nie wissen, welche Klassen sie auflisten.

## Inhalt

1. [Die Idee](#die-idee)
2. [Die mitgelieferten Ansichtsmodi](#die-mitgelieferten-ansichtsmodi)
3. [Wie ein Modus gewählt wird](#wie-ein-modus-gewählt-wird)
4. [Ein Template je Klasse und Modus](#ein-template-je-klasse-und-modus)
5. [Eigene Ansichtsmodi](#eigene-ansichtsmodi)
6. [Ansichtsmodi in Einbettungen und Layouts](#ansichtsmodi-in-einbettungen-und-layouts)
7. [Caching](#caching)
8. [Die Modi einer Website planen](#die-modi-einer-website-planen)

## Die Idee

```text
article "Opening of the new hall"
  full   -> the article page                 (node/view/full.tpl -> full/article.tpl)
  line   -> one row in the news list          (node/view/line.tpl -> line/article.tpl)
  card   -> a card on the front page          (node/view/card.tpl -> card/article.tpl)
  embed  -> a box inside another article       (content/view/embed.tpl -> embed/article.tpl)
```

Ein Listen-Template zeichnet jedes Unterelement mit `node_view_gui` und einem Modusnamen; das richtige Template je
Klasse finden die Override-Regeln. Kommt später eine Klasse hinzu, braucht sie Templates für diese Klasse, keine
Änderungen an jeder Liste.

## Die mitgelieferten Ansichtsmodi

| Modus | Verwendet für |
|---|---|
| `full` | die Seite des Inhalts selbst |
| `line` | einen Eintrag in einer Liste |
| `embed` | Inhalte, die als Block in Rich Text eingebettet sind |
| `embed-inline` | Inhalte, die in eine Textzeile eingebettet sind |
| `text_linked`, `text`, `tiny`, `plain` | Kurzformen in Listen, Relationen und der Administration |
| `listitem`, `thumb`, `sitemap`, `print`, `pdf` | weitere mitgelieferte Modi |

Templates: [Ansichtsmodi](../designer/vorlagen/ansichtsmodi.md#die-üblichen-ansichtsmodi), [Full View](../designer/vorlagen/vollansicht.md),
[Line View](../designer/vorlagen/zeilenansicht.md), [Embed View](../designer/vorlagen/embed-ansicht.md).

## Wie ein Modus gewählt wird

| Zusammenhang | Wie |
|---|---|
| Eine URL | `/content/view/<mode>/<node id>`; ein URL-Alias öffnet `full` |
| Ein Template | `{node_view_gui content_node=$child view='line'}`, `{content_view_gui content_object=$object view='embed'}` |
| Rich Text | der Redakteur wählt `embed` oder einen anderen Modus, den `content.ini [embed] AvailableViewModes[]` erlaubt |
| Ein Block von Layouts | der View-Typ des Blocks und der View-Typ der Elemente, im Editor gewählt |

## Ein Template je Klasse und Modus

`node/view/<mode>.tpl` ist der Standard für jede Klasse. Eine Regel gibt einer Klasse ihr eigenes Template für einen
Modus:

```ini
[exp_mysite_line_event]
Source=node/view/line.tpl
MatchFile=line/exp_event.tpl
Subdir=templates
Match[class_identifier]=exp_event
```

```smarty
{* extension/exp_mysite/design/exp_mysite/override/templates/line/exp_event.tpl *}
<article class="exp-line exp-line--event">
    <h3><a href={$node.url_alias|ezurl}>{$node.name|wash()}</a></h3>
    <p>{$node.data_map.start_date.content.timestamp|l10n( 'shortdate' )}</p>
</article>
```

Regeln und ihre Reihenfolge:
[Templates und Overrides für CMS-Entwickler](templates-und-overrides-fuer-cms-entwickler.md#override-regeln).

## Eigene Ansichtsmodi

Ein Modus ist nur ein Name, für den es Templates gibt. So fügen Sie `card` hinzu:

1. Legen Sie den Standard `design/exp_mysite/templates/node/view/card.tpl` für jede Klasse an; ein Modus ohne
   Standard-Template irgendwo in der Design-Kette schlägt fehl.
2. Fügen Sie Regeln und Templates für die Klassen hinzu, die eine eigene Karte brauchen.
3. Verwenden Sie ihn: `{node_view_gui content_node=$child view='card'}`.
4. Leeren Sie den Template-Override-Cache (`php bin/php/ezcache.php --clear-id=template-override --allow-root-user`).

Vermeiden Sie die Namen, die der Kernel bereits verwendet. Versehen Sie Ihre eigenen mit einem Präfix, wenn sie mit
einem Theme kollidieren könnten, auf dem Sie aufbauen (`exp_card`). Das Rezept der Designer:
[Eigene Ansichtsmodi](../designer/vorlagen/eigene-ansichtsmodi.md).

## Ansichtsmodi in Einbettungen und Layouts

Redakteure wählen unter den Embed-Ansichtsmodi, die `content.ini [embed] AvailableViewModes[]` listet; fügen Sie Ihre
eigenen dort hinzu, in `content.ini.append.php` Ihrer Erweiterung, mit einem Template je Klasse
([Für die Einbettungen der Redakteure](../designer/vorlagen/eigene-ansichtsmodi.md#für-das-einbetten-durch-redakteure)). Exponential
Layouts zeichnet die Elemente eines Blocks in einem View-Typ der Blockdefinition; Block-Templates können Elemente
ebenfalls mit Ihren Ansichtsmodi zeichnen ([Einen View-Typ hinzufügen](../zwei-wege-zum-bauen/ansichtstyp-hinzufuegen.md),
[View-Templates für Blöcke](../designer/theme-design/block-ansichtstemplates.md)).

## Caching

`site.ini [ContentSettings] CachedViewModes=full;sitemap;pdf`: Diese Modi werden, als Seiten geöffnet, im View-Cache
gespeichert. Modi, die innerhalb eines anderen Templates gezeichnet werden (`line`, `card`, `embed`), werden als Teil
der Seite zwischengespeichert, die sie zeichnet; eine Änderung an einem Artikel erreicht also eine Karte auf der
Startseite, wenn der Cache dieser Seite geleert wird ([Caching für CMS-Entwickler](caching-fuer-cms-entwickler.md)).

## Die Modi einer Website planen

Schreiben Sie je Klasse eine Tabelle: welche Modi sie braucht, mit einer Skizze von jedem. Eine typische Website braucht
`full`, `line`, einen Karten- oder Teaser-Modus und `embed` für die Klassen, die Redakteure einbetten. Wenige Modi, die
überall verwendet werden, lassen sich leichter einheitlich halten als ein Modus je Seite
([Die Ansichtsmodi eines Designs planen](../designer/vorlagen/ansichtsmodi.md#die-ansichtsmodi-eines-designs-planen)).

## Verwandte Seiten

- [Inhalte in Templates abrufen](inhalte-in-templates-abrufen.md)
- [Ansichtsmodi für Website-Bauer](../erweiterungsentwicklung/teil-2-builder/view-modi-fuer-builder.md)
- [Knoten-Templates nach Klasse](../designer/vorlagen/knoten-templates-nach-klasse.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
