---
title: Tags and categories
audience: individual, website-creator, editor
category: website-creation
order: 1624
summary: How one person organizes blog posts and portfolio items by topic on Exponential - folders as categories, the keyword field (ezkeyword) the shipped classes use for tags, the eztags extension for a controlled vocabulary, and the habits that keep a tag list useful for years.
keywords: tags, categories, keywords, ezkeyword, eztags, folders, topics, taxonomy, vocabulary, blog, portfolio, archive, navigation
type: help
---
# Tags and categories

This page is for individuals who want readers to find older posts or works by topic. Exponential gives you three
tools; most personal sites need one or two of them.

## Contents

1. [Three tools](#three-tools)
2. [Folders as categories](#folders-as-categories)
3. [The keyword field of the shipped classes](#the-keyword-field-of-the-shipped-classes)
4. [eztags for a controlled list](#eztags-for-a-controlled-list)
5. [Which to use](#which-to-use)
6. [Habits that keep tags useful](#habits-that-keep-tags-useful)
7. [Checklist](#checklist)

## Three tools

| Tool | What it is | Shipped where |
|---|---|---|
| **Folders** | The content tree: each item lives in one place | kernel |
| **Keywords** (`ezkeyword`) | Free words typed into a field, comma-separated | the `tags` field of *Blog*, *Blog post*, *Image* and *Gallery* in the `sevenx_classes` package |
| **eztags** | A managed tag tree with synonyms, translations and tag pages | the `eztags` extension, in the root `composer.json` |

Note that the field called "Tags" on the shipped blog and image classes is a **keyword** field
(`datatype="ezkeyword"` in `class-blog_post.xml` and `class-image.xml`), not an `eztags` field.

## Folders as categories

A folder gives each item exactly one home and becomes part of the address and the menu. Good for a few broad,
lasting categories:

- a travel blog with one folder per country;
- a portfolio with one gallery per series.

Folders are poor at topics that overlap. A post about "cooking on a sailing trip" belongs in two places; that is
what tags are for. An item can be given extra locations in the tree, but use that sparingly
([Structuring the content tree](../../creators/website-creators/structuring-the-content-tree.md)).

## The keyword field of the shipped classes

- Type words separated by commas: `sailing, cooking, Norway`.
- Nothing stops you typing `Sailing`, `sailing` and `sailling`; consistency is up to you.
- Keywords are stored with the content, indexed for search, and templates can list related content by keyword.

It is the quickest start and fine for a blog of a few hundred posts if you keep a written list of your keywords.

## eztags for a controlled list

`eztags` keeps tags as their own tree (**Tags** in the administration): each tag exists once, can have synonyms and
translations, and has its own page (`tags/view/...`) that lists everything tagged with it. Editors pick from the
list with autocomplete instead of typing freely. Using it means adding an `eztags` field to your classes (or
replacing the keyword field), which is a class change
([Tagging with eZ Tags](../../creators/website-creators/tagging-with-eztags.md),
[eztags](../../../features/6.0/extensions/eztags.md)).

## Which to use

| Your site | Use |
|---|---|
| A small blog, one author | Keywords, with a written list |
| A blog you plan to keep for many years, or two authors | `eztags` |
| A portfolio with series | Galleries as folders, plus keywords or `eztags` for subjects |
| A multilingual site | `eztags` (tags can be translated) |
| You want a page per topic without template work | `eztags` (tag pages ship) |

## Habits that keep tags useful

- **Ten to thirty tags** for a personal blog is plenty. A tag used once is noise.
- **Decide spelling and number** once: singular or plural, lower case.
- **Write the list down** (or let `eztags` hold it) and reuse rather than invent.
- **Review twice a year**: merge near-duplicates, retire tags nobody needs.
- **Tags are not keywords for search engines.** They are for your readers.

## Checklist

- [ ] Broad categories decided as folders, if any.
- [ ] Keywords or `eztags` chosen.
- [ ] A written tag list with spellings.
- [ ] Each new post or work tagged from the list.
- [ ] A review date in your calendar.

## Related pages

- [Blogs](README.md)
- [Tagging with eZ Tags](../../creators/website-creators/tagging-with-eztags.md)
- [Building an image gallery](../portfolios/building-an-image-gallery.md)
- [Content audits](../../customers/day-to-day/content-audits.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
