---
title: "Die Nachrichtenseiten: Übersicht, vollständiger Artikel und Archiv"
audience: builder, designer
category: books
order: 130
summary: Die ersten dynamischen Seiten bauen: ein Ordner News mit Artikeln, die Übersicht der sieben neuesten, die vollständige Ansicht eines Artikels und eine Archivliste mit Blätterfunktion, mit fetch, Overrides und dem Navigator.
keywords: Neuigkeiten, Ordner, Artikel, Blättern, Seitenaufteilung, Navigator, Override, Zeilenansicht, Vollansicht, Archiv, news, folder, article, fetch list, sort_by, published, limit, offset, paging, navigator, override, line view, full view, archive, Building book
type: help
---
# Die Nachrichtenseiten: Übersicht, vollständiger Artikel und Archiv

Der Nachrichtenbereich ist die erste wirklich dynamische Seite: drei Ansichten einer Art von Inhalt.

1. eine **Übersicht** der sieben neuesten Artikel, erreichbar über das Hauptmenü,
2. die **Vollansicht** eines Artikels, über seinen Titel, sein Bild oder einen Link „Weiterlesen“,
3. ein **Archiv**: alle Artikel, die neuesten zuerst.

## Der Inhalt

Verwenden Sie zwei mitgelieferte Klassen: **Folder** als Container und **Article** für die Einträge. Legen Sie an der
Wurzel einen Ordner namens **News** an („This folder contains all the news articles.“) und darin dann einige Artikel. Die
Sortierung nach dem Veröffentlichungsdatum ist die eigene Sortiereinstellung des Ordners, wie das Buch sagt: die neuesten
oben (siehe **Sortierung** im Bearbeitungsformular des Ordners). Legen Sie ein paar Artikel an, damit Sie etwas sehen.

## Die Übersicht: ein Template für den Ordner News

Der Ordner News zeigt eine Liste, er braucht also seine eigene Vollansicht. Überschreiben Sie die Vollansicht des Ordners für
diesen einen Knoten (die Knoten-ID des Ordners News sei zum Beispiel 45):

```ini
[full_news_folder]
Source=node/view/full.tpl
MatchFile=full/news_folder.tpl
Subdir=templates
Match[node]=45
```

`design/tscm/override/templates/full/news_folder.tpl`:

```
<h1>{$node.name|wash}</h1>
{def $items=fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id,
                                            'class_filter_type', 'include',
                                            'class_filter_array', array( 'article' ),
                                            'sort_by', array( 'published', false() ),
                                            'limit', 7 ) )}
{foreach $items as $item}
    {node_view_gui content_node=$item view='line'}
{/foreach}
<p><a href={concat( $node.url_alias, '/(archive)/1' )|ezurl}>News archive</a></p>
```

Jeder Eintrag wird in der Ansicht **line** gezeichnet. Diese Ansicht ist ein weiteres Template, das Sie bereitstellen, ein
Override von `node/view/line.tpl` für die Klasse `article`:

```ini
[line_article]
Source=node/view/line.tpl
MatchFile=line/article.tpl
Subdir=templates
Match[class_identifier]=article
```

```
{* design/tscm/override/templates/line/article.tpl *}
<article class="news-item">
    <h2><a href={$node.url_alias|ezurl}>{$node.name|wash}</a></h2>
    <p class="date">{$node.object.published|l10n( 'shortdate' )}</p>
    {attribute_view_gui attribute=$node.data_map.intro}
    <a href={$node.url_alias|ezurl}>Read more</a>
</article>
```

Die Fassung des Buches setzt den Titel mit einem Vorschaubild in eine Tabelle. Fügen Sie das Bild mit
`{if $node.data_map.image.has_content}...{/if}` und `attribute_view_gui` mit `image_class='small'` auf dem verknüpften
Bildobjekt (`$node.data_map.image.content`) hinzu, wie es das schlichte Inhaltsmodell der Klasse Article vorsieht. `fetch` mit
`sort_by` und `limit` erklärt [Inhalte holen](../buch-lernen/inhalte-in-templates-abrufen.md).

## Der vollständige Artikel

Die Vollansicht eines Artikels ist die Standardansicht, bis Sie sie überschreiben (`Match[class_identifier]=article`, Quelle
`node/view/full.tpl`). Zeigen Sie den Titel, das Datum und den Autor, die Einleitung und den Text und das Bild an der Seite.
Das Buch fügt außerdem einen Link zurück zur Nachrichtenübersicht und zum Archiv hinzu.

## Das Archiv

Das Archiv listet alle Artikel mit Blätterfunktion auf. Der einfachste Entwurf ist eine zweite Ansicht des Ordners, erreichbar
über einen Ansichtsparameter: Die Adresse `/news/(archive)/1` (wie im Übersichtslink oben) ist derselbe Knoten mit gesetztem
`$view_parameters.archive`. Im Ordner-Template:

```
{if $view_parameters.archive}
    {def $page_limit=20
         $count=fetch( 'content', 'list_count', hash( 'parent_node_id', $node.node_id ) )
         $all=fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id,
                                              'sort_by', array( 'published', false() ),
                                              'limit', $page_limit,
                                              'offset', $view_parameters.offset ) )}
    {foreach $all as $item}
        <p>{$item.object.published|l10n( 'shortdate' )} <a href={$item.url_alias|ezurl}>{$item.name|wash}</a></p>
    {/foreach}
    {include name=navigator uri='design:navigator/google.tpl'
             page_uri=$node.url_alias item_count=$count
             view_parameters=$view_parameters item_limit=$page_limit}
{else}
    ...the overview above...
{/if}
```

Nichts muss deklariert werden: Jedes Paar `(name)/wert` am Ende der Adresse wird in der Vollansicht zu
`$view_parameters.name` (`offset` liest der Kernel selbst, die Blätterlinks funktionieren also wie auf der Standardseite eines
Ordners). Siehe [Ansichtsparameter](../template-sprache/ansichtsparameter.md). Der View-Cache hält für jede Parameterkombination
eine eigene Kopie. Eine einfachere Alternative ohne Parameter ist, das Archiv zu einer eigenen Seite zu machen und darauf zu
verlinken.

Weiter: [die Mitgliederseite](die-mitgliederseite.md).

## Quelle und Lizenz

Bearbeitet nach den Kapiteln über die Startseite und die Neuigkeiten des Tutorials *Building an eZ publish site* (geschrieben
für eZ publish 3.2, 2003), ursprünglich von ez.no, 2024 von 7x erneut unter der GNU Free Documentation License veröffentlicht,
[Book-Building-An-eZPublish-Site](https://github.com/se7enxweb/Book-Building-An-eZPublish-Site). Copyright (c)
1999-2024 7x und Graham Brookins. Lizenziert unter der GNU Free Documentation License, Version 1.3 oder einer späteren
Version, ohne unveränderliche Abschnitte, ohne vordere und ohne hintere Umschlagtexte ([die Lizenz](../../../eng-us/LICENSE-GFDL-1.3.txt)).
Änderungen: für Exponential 6 neu geschrieben: Override-Regeln in einer Erweiterung, aktuelle Fetch- und Ansichtsparameter.
