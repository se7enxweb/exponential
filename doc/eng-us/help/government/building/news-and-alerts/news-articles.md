---
title: News articles
audience: government, website-creator, editor
category: public-sector
order: 20
summary: How to set up news on a public-sector Exponential site - the shipped Article class and its fields, a news folder organized by year, lists of the latest news with a fetch, publication dates, images with alternative text, and topics with tags.
keywords: news, article class, news folder, latest news, fetch, sort by published, publication date, images, alternative text, tags, archive by year
type: help
---
# News articles

This page is for website creators and editors who set up and run the news section of a public-sector site. It uses the
shipped Article class and shows how to organize, list and maintain news. Class facts are from
[sevenx_classes](../../../site-packages/sevenx-multisite/sevenx-classes.md) and the class view of the demonstration
server.

## Contents

1. [The Article class](#the-article-class)
2. [Organizing news](#organizing-news)
3. [Lists of the latest news](#lists-of-the-latest-news)
4. [Dates](#dates)
5. [Images and accessibility](#images-and-accessibility)

## The Article class

On a new installation, `article` has the definition of the base data:

| Attribute | Identifier | Datatype | Required |
|---|---|---|---|
| Title | `title` | Text line | yes |
| Short title | `short_title` | Text line | no |
| Author | `author` | Authors (`ezauthor`) | no |
| Intro | `intro` | XML block | yes |
| Body | `body` | XML block | no |
| Enable comments | `enable_comments` | Checkbox | no |
| Image | `image` | Object relation (to an Image object) | no |

Useful additions for public-sector news (add them in **Setup > Classes**): Tags (`eztags`) for topics, and Metadata
(`xrowmetadata`) for descriptions and sharing images ([Metadata and social sharing](metadata-and-social-sharing.md)).
Turn off **Enable comments** in the template or remove it if you do not moderate comments.

## Organizing news

```text
News              a folder with Display sub items on, sorted by publication date, newest first
├── 2026          a folder per year, created at the start of the year
│   └── <articles>
└── 2025
```

Year folders keep lists short in the administration and give stable archive addresses. Set the sort order of the
News folder on its edit form (**Sorting of sub items**).

## Lists of the latest news

On the front page or a topic page, a fetch lists the newest articles below the News folder:

```text
{def $news = fetch( 'content', 'tree', hash(
        'parent_node_id', $news_node_id,
        'class_filter_type', 'include',
        'class_filter_array', array( 'article' ),
        'sort_by', array( 'published', false() ),
        'limit', 5 ) )}
{foreach $news as $item}
  <article>
    <h3><a href={$item.url_alias|ezurl}>{$item.name|wash}</a></h3>
    <p><time>{$item.object.published|l10n( 'shortdate' )}</time></p>
    {attribute_view_gui attribute=$item.data_map.intro}
  </article>
{/foreach}
{undef $news}
```

The layouts editor can also show lists of content in blocks without template work ([The layouts editor](../../../layouts-editor.md)).
Lists in cache blocks need an expiry so new items appear ([View cache and cache blocks](../../../creators/website-creators/view-cache-and-cache-blocks.md)).

## Dates

| Date | Where it comes from |
|---|---|
| Published | set at the first publication; shown as the news date |
| Modified | each new version |
| A corrected item | show "Updated on <date>" from the modified time when you correct a published item |

To publish at a later time, see [Press releases](press-releases.md#embargo).

## Images and accessibility

- Switch on **Alternative text is required** for the image attribute of the Image class
  ([Content model and editing, 9](../../../../guides/content-model-and-editing.md#9-required-alternative-text-trimmed-text-lines-and-names-made-by-php)).
- Write the alternative text for what the image shows in the article's context; a decorative image needs none, but
  then it should not be in the Image field.
- Image sizes come from aliases ([Images and image variations](../../../creators/website-creators/images-and-image-variations.md)).
- Photos of people need their consent where your rules require it.

## Related pages

- [News and alerts](README.md)
- [Press releases](press-releases.md)
- [Creating and editing content](../../../editing-content.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
