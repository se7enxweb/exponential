---
title: Life events and topics
audience: government, website-creator, project-manager, editor
category: public-sector
order: 40
summary: How to organize services by topic and by life event with eztags in Exponential - a tag tree, synonyms, closed vocabularies, tag pages and lists - so one service appears wherever people look for it.
keywords: life events, topics, taxonomy, eztags, tag tree, synonyms, closed vocabulary, tag pages, tags view, service catalog, navigation
type: help
---
# Life events and topics

This page is for website creators, project managers and editors who organize services for people who do not know
which department provides them. People think in topics ("housing", "transport") and in life events ("having a baby",
"moving house", "starting a business", "when someone dies"). With tags, one service appears under every topic and
life event it belongs to, while living in one place in the tree. The tags extension is documented in
[eztags](../../../../features/6.0/extensions/eztags.md) and [Tagging with eztags](../../../creators/website-creators/tagging-with-eztags.md).

## Contents

1. [Two tag trees](#two-tag-trees)
2. [Building the trees](#building-the-trees)
3. [Tagging services](#tagging-services)
4. [Showing topics and life events](#showing-topics-and-life-events)
5. [Governing the vocabulary](#governing-the-vocabulary)

## Two tag trees

| Tree | Example structure | Purpose |
|---|---|---|
| Topics | Housing > Social housing, Housing > Building permits; Transport > Parking | what the service is about |
| Life events | Having a baby; Moving house; Starting school; Starting a business; Retiring; When someone dies | the situation the person is in |

Keep life events to a short list (often ten to twenty); each is a page that gathers every service needed in that
situation, in a sensible order.

## Building the trees

1. Open **Tags** (`/tags/dashboard`) in the administration.
2. Create a top tag "Topics" and one "Life events"; add child tags below each.
3. Add **synonyms** for words people use: "rubbish", "garbage" and "trash" as synonyms of the topic "Waste".
   Synonyms point to the main tag, so search and tag pages treat them as one.
4. Translate tags if the site is multilingual: tags have their own translations.

The **Tags** dashboard and the add, edit, move and merge functions are part of eztags; the policies are in the
`tags` module ([Tagging with eztags](../../../creators/website-creators/tagging-with-eztags.md#permissions)).

## Tagging services

1. In the Service class, the **Topics and life events** attribute uses the Tags datatype. In its class settings,
   choose the **Edit view** (Default, Select or Tree) and set **Limit by tags subtree** to one root tag; a single attribute takes one subtree, so either put "Topics" and "Life events" below one root tag, or use two Tags attributes, one per tree,
   if you want a closed vocabulary.
2. Editors type in the field; autocomplete suggests existing tags.
3. Decide whether editors may create new tags (open vocabulary) or only choose (closed). For a service catalog, closed
   is usually better: the web team adds tags on request.

## Showing topics and life events

| Page | How |
|---|---|
| A topic page | the tag page `/tags/view/<tag>` lists everything tagged, with your template |
| A life event page | an Article or Folder written for the event (an introduction, the order of steps), plus a list of services tagged with it |
| On each service page | the tags as links (`attribute_view_gui` on the tags attribute) |
| "Popular topics" on the front page | a fixed list of tag links, chosen by the web team |

eztags adds an extended attribute filter to `content/list` and `content/tree` fetches, so a template can list "the
services tagged Moving house"; see the extension's own documentation in `extension/eztags/doc/` for the filter syntax.

## Governing the vocabulary

| Rule | Why |
|---|---|
| One owner of the tag trees | consistency |
| Merge duplicates rather than keep both | eztags has a merge function in the Tags admin |
| Use the residents' words for main tags, official terms as synonyms | findability |
| Review tags with no content yearly | a tag page without services is a dead end |

## Related pages

- [Building a service catalog](README.md)
- [Finding services](finding-services.md)
- [Structuring the content tree](../../../creators/website-creators/structuring-the-content-tree.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
