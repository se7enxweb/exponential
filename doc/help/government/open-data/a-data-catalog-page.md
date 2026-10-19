---
title: A data catalog page
audience: government, website-creator, editor
category: public-sector
order: 90
summary: How to build the page that lists all datasets of a public body in Exponential: a catalog folder, a list view with license and format, filters by theme with tags, a search box limited to the catalog, and the feed link.
keywords: data catalog, catalog page, dataset list, folder, line view, template override, eztags, filter, theme, search, open data portal
type: help
---
# A data catalog page

This page is for website creators and editors who build the entry page of a public body's open data. In
Exponential a catalog is a folder whose children are dataset pages; your design decides how the list looks. The
template techniques come from the website creators' pages linked in each step.

## Contents

1. [What the catalog page should show](#what-the-catalog-page-should-show)
2. [The catalog folder](#the-catalog-folder)
3. [The list of datasets](#the-list-of-datasets)
4. [Filtering by theme](#filtering-by-theme)
5. [Searching the catalog](#searching-the-catalog)
6. [The feed link](#the-feed-link)
7. [Checklist](#checklist)

## What the catalog page should show

- A short introduction: what data the body publishes, under which licenses, how to ask for more.
- The list of datasets, newest or alphabetical, each with its title, a one-line description, its themes, its
  formats, its license and the date it last changed.
- Ways to narrow the list: themes, a search box.
- A feed of new datasets and the contact mailbox.

## The catalog folder

1. In **Content structure**, create a **Folder** named "Open data" where it fits in the site's navigation.
2. Write the introduction in the folder's description.
3. Set the folder's sorting to the order you want (for example by modification, newest first).
4. Publish datasets as children (see "Publishing dataset files").

## The list of datasets

The folder's full view lists its children. Give the dataset class its own **line** view, so each row shows what
re-users need. Override `node/view/line.tpl` for the class `dataset` in your design
([Template overrides](../../creators/website-creators/template-overrides.md)):

```text
<article class="dataset-line">
  <h3><a href={$node.url_alias|ezurl}>{$node.name|wash}</a></h3>
  {attribute_view_gui attribute=$node.data_map.description}
  <dl>
    <dt>{'License'|i18n( 'design/mysite' )}</dt><dd>{attribute_view_gui attribute=$node.data_map.license}</dd>
    <dt>{'Last changed'|i18n( 'design/mysite' )}</dt><dd>{attribute_view_gui attribute=$node.data_map.modified}</dd>
  </dl>
</article>
```

Use `|wash` on every printed value and translate the labels with `|i18n` so the catalog works in each language of
the site ([Building a multilingual site](../../creators/website-creators/multilingual-sites.md)). Keep the list
accessible: a heading per dataset, real links, no information in color only
([Accessibility for site builders](../../creators/website-creators/accessibility-for-site-builders.md)).

## Filtering by theme

With the `eztags` extension, a Tags field on the dataset class gives each tag its own page that lists everything
tagged with it, and synonyms let people find a theme under another word
([Tagging with eztags](../../creators/website-creators/tagging-with-eztags.md)). Link the theme tags from the catalog
page. Without `eztags`, a Selection field and one folder per theme are a simpler alternative.

## Searching the catalog

A search box that sends `SearchText` to `content/search` searches the whole site. To search only the catalog, add a hidden field `SubTreeArray` with the node ID of the catalog
folder to the form: the `content/search` view reads it and limits the results to that subtree
(`kernel/private/classes/views/content/search.php`) ([Site search](../../creators/website-creators/site-search.md)).
With Solr (`ezfind`), facets by class, tag or date are available
([Search with ezfind and Solr](../../creators/website-creators/search-with-ezfind-and-solr.md)).

Mark the dataset fields people search for as **Searchable** in the class: title, description and keywords.

## The feed link

Create an RSS export of the catalog folder (see "Feeds for new and changed datasets") and link it from the catalog
page with a visible link and a `<link rel="alternate" type="application/rss+xml">` element in the page head.

## Checklist

- [ ] The introduction says what is published, under which licenses, and how to ask for more.
- [ ] Each row shows title, description, license, formats and last change.
- [ ] Themes are tags or selections from one controlled list.
- [ ] The catalog works signed out, on a phone and in every language of the site.
- [ ] The feed link and the contact mailbox are on the page.

## Related pages

- [Template overrides](../../creators/website-creators/template-overrides.md)
- [Menus and navigation](../../creators/website-creators/menus-and-navigation.md)
- [Structuring the content tree](../../creators/website-creators/structuring-the-content-tree.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
