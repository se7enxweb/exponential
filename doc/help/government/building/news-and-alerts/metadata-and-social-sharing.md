---
title: Metadata and social sharing
audience: government, website-creator, editor
category: public-sector
order: 90
summary: How to give news and notices good titles, descriptions and sharing images with the xrowmetadata datatype in Exponential, show them in the page head, and offer sharing without third-party trackers on a public-sector site.
keywords: metadata, xrowmetadata, page title, meta description, Open Graph, sharing image, social sharing, share buttons, privacy, sitemaps, canonical
type: help
---
# Metadata and social sharing

This page is for website creators and editors who want news and notices to look right in search results and when
people share links. The `xrowmetadata` extension, activated by the default installation, adds a Metadata attribute
with a page title, keywords, description, canonical link and (since 1.3.6) an Open Graph image. Sources:
[xrowmetadata](../../../../features/6.0/extensions/xrowmetadata.md) and
[Metadata and social sharing](../../../creators/website-creators/metadata-and-social-sharing.md).

## Contents

1. [The Metadata attribute](#the-metadata-attribute)
2. [In the page head](#in-the-page-head)
3. [Sharing without trackers](#sharing-without-trackers)
4. [Checklist for editors](#checklist-for-editors)

## The Metadata attribute

| Field | Use |
|---|---|
| Title | the title search results and browser tabs show; shorter than the headline when needed |
| Description | one or two sentences; the snippet in results and link previews |
| Keywords | rarely used by search engines today; optional |
| Canonical | when the same content is reachable at several addresses |
| Open Graph image | the image link previews show |

Many shipped classes already have a Meta Data attribute (`metadata`): Event, Folder, Frontpage, Feedback form and the
`ng_*` classes among them. On a new installation the base-data Article class does not; add one in **Setup > Classes**
with the Metadata datatype ([sevenx_classes](../../../site-packages/sevenx-multisite/sevenx-classes.md)).

## In the page head

Your pagelayout prints the metadata of the current node, for example:

```text
{def $meta = metadata( $module_result.node_id )}
{if $meta}
  {if $meta.title}<title>{$meta.title|wash}</title>{/if}
  {if $meta.description}<meta name="description" content="{$meta.description|wash}">{/if}
{/if}
```

Add Open Graph tags (`og:title`, `og:description`, `og:image`, `og:url`) the same way; the full example is in
[xrowmetadata](../../../../features/6.0/extensions/xrowmetadata.md). When the Metadata is empty, fall back to the
object's name and its intro.

## Sharing without trackers

| Option | Privacy |
|---|---|
| Plain share links (a link to the platform's share address with the page URL) | nothing is loaded until the visitor clicks |
| A "Copy link" button | nothing leaves the site |
| Embedded share widgets of social platforms | they load third-party scripts and may track visitors on every page |

Exponential's administration loads nothing from third parties and the product ships no cookie banner
([Privacy standards](../../../../standards/privacy.md)); keeping public pages free of third-party widgets avoids
consent questions.

## Checklist for editors

- [ ] The title says what the item is about in the first words.
- [ ] The description is a full sentence, not a copy of the title.
- [ ] The sharing image has a meaningful alternative text in its Image object and shows no personal data
  inadvertently.
- [ ] Notices that will be hidden soon need no sharing image.

## Related pages

- [News and alerts](README.md)
- [SEO basics](../../../creators/website-creators/seo-basics.md)
- [Sitemaps](../../../creators/website-creators/sitemaps.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
