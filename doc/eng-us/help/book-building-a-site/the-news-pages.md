---
title: "The news pages: overview, full article and archive"
audience: builder, designer
category: books
order: 130
summary: Building the first dynamic pages: a News folder with articles, the overview of the latest seven, the full view of one article, and an archive list with paging, using fetch, overrides and the navigator.
keywords: news, folder, article, fetch list, sort_by, published, limit, offset, paging, navigator, override, line view, full view, archive, Building book
type: help
---
# The news pages: overview, full article and archive

The news section is the first real dynamic page: three views of one kind of content.

1. an **overview** of the seven newest articles, reached from the main menu,
2. the **full view** of one article, from its title, its image or a "Read more" link,
3. an **archive**: every article, newest first.

## The content

Use two built-in classes: **Folder** for a container and **Article** for the items. Create a folder named **News**
("This folder contains all the news articles.") at the root, then create a few articles inside it. Sorting by the
publishing date is the folder's own sort setting, as the book says: the newest on top (see **Sorting** in the folder's
edit form). Put a couple of articles in so you have something to see.

## The overview: a template for the News folder

The News folder shows a list, so it needs its own full view. Override the folder's full view for this one node (the
node ID of the News folder is, say, 45):

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

Each item is drawn in the **line** view. That view is another template you provide, an override of `node/view/line.tpl` for the
class `article`:

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

The book's version wraps the title in a table with a thumbnail. Add the image with
`{if $node.data_map.image.has_content}...{/if}` and `attribute_view_gui` with `image_class='small'` on the related image
object (`$node.data_map.image.content`), as the plain content model of the Article class says. `fetch` with `sort_by` and `limit`
is explained in [fetching content](../book-learning/fetching-content-in-templates.md).

## The full article

An article's full view is the standard one until you override it (`Match[class_identifier]=article`, source
`node/view/full.tpl`). Show the title, the date and author, the intro and the body, and the image on the side. The book
also adds a link back to the news overview and to the archive.

## The archive

The archive lists every article, with paging. The simplest design is a second view of the folder, reached with a view
parameter: the address `/news/(archive)/1` (as in the overview link above) is the same node with `$view_parameters.archive`
set. In the folder template:

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

Nothing has to be declared: any `(name)/value` pair at the end of the address becomes `$view_parameters.name` in the full view
(`offset` is read by the kernel itself, so the paging links work as they do on the standard folder page).
See [View parameters](../template-language/view-parameters.md). The view cache keeps a separate copy per parameter set. A simpler
alternative that needs no parameter is to make the archive its own page and link to it.

Next: [the members page](the-members-page.md).

## Source and license

Adapted from the welcome page and news chapters of of the tutorial *Building an eZ publish site* (written for eZ publish 3.2, 2003), originally by
ez.no and released again under the GNU Free Documentation License by 7x in 2024,
[Book-Building-An-eZPublish-Site](https://github.com/se7enxweb/Book-Building-An-eZPublish-Site). Copyright (c)
1999-2024 7x and Graham Brookins. Licensed under the GNU Free Documentation License, version 1.3 or any later
version, with no Invariant Sections, no Front-Cover Texts and no Back-Cover Texts ([the license](../../LICENSE-GFDL-1.3.txt)).
Changes: rewritten for Exponential 6: override rules in an extension, current fetch and view parameters.
