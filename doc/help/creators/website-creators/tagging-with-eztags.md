---
title: Tagging content with eztags
audience: website-creator, editor
category: website-creation
order: 230
summary: Add a tag taxonomy to a site with the eztags extension - build the tag tree, add a tags field to classes, tag content, show tag pages and lists of tagged content, and the settings that matter.
keywords: tags, eztags, taxonomy, keywords, tag tree, synonyms, tag cloud, tag view, autocomplete, classification, ezkeyword, website creator
type: help
---
# Tagging content with eztags

This page is for website creators who want visitors to find content by topic. The `eztags` extension (shown in the
administration as **Tags**) adds a tag tree you manage, a tags field for classes with autocomplete, and a page per tag
that lists everything tagged with it. It does more than the kernel's `ezkeyword` datatype: a hierarchy, synonyms,
and closed or open vocabularies ([eztags](../../../features/6.0/extensions/eztags.md)).

## Contents

1. [When to use tags](#when-to-use-tags)
2. [Set it up](#set-it-up)
3. [Showing tags on a site](#showing-tags-on-a-site)
4. [Settings that matter](#settings-that-matter)
5. [Permissions](#permissions)
6. [Good tagging practice](#good-tagging-practice)

## When to use tags

| Need | Use |
|---|---|
| Topics that cut across the folder structure | tags |
| A fixed list of categories editors must choose from | tags with a closed (predefined) vocabulary |
| Free keywords anybody may add | tags with an open vocabulary, or `ezkeyword` |
| A single place for an item in the menu | the content tree, not tags |

## Set it up

1. Make sure `eztags` is active (**Setup > Extensions**; [Extensions](../../../guides/extensions.md)).
2. Open **Tags** (`/tags/dashboard`) and build the tag tree: top-level topics, subtopics, synonyms.
3. In **Setup > Classes**, add an attribute of the tags datatype to each class that should be tagged, and choose its
   edit view (Default, Select or Tree).
4. Edit an object and type in the tags field: autocomplete suggests existing tags.
5. Open `/tags/view/<tag>` on the site: it lists everything tagged with it.

## Showing tags on a site

- **Tags of an article**: render the field with `{attribute_view_gui attribute=$node.data_map.tags}`, which links each
  tag to its tag page.
- **The tag page**: `/tags/view/...` works like a content page, with the current tag in `$tag`; override its template
  in your design like any other.
- **Lists by tag**: the extension adds an extended attribute filter to `content/list` and `content/tree` fetches, so a
  folder or a block can show "the latest five items tagged X".
- **Tag cloud**: built from Solr when it is available (`TagCloudOverSolr`).

## Settings that matter

From `eztags.ini` ([eztags, Settings](../../../features/6.0/extensions/eztags.md#settings)):

| Block | Key | Default | Meaning |
|---|---|---|---|
| `GeneralSettings` | `URLPrefix` | `tags/view` | the address prefix of tag pages |
| `GeneralSettings` | `MaxResults` | `24` | autocomplete results |
| `GeneralSettings` | `AutoCompleteType` | `start` | match the start or any part of a tag |
| `GeneralSettings` | `DefaultAlwaysAvailable` | `false` | new tags available in every language |
| `SearchSettings` | `IndexSynonyms`, `IndexParentTags` | `enabled`, `disabled` | what goes into the search index |
| `EditSettings` | `AvailableViews[]` | Default, Select, Tree | edit views of the field |

## Permissions

The module `tags` has its own policies: `read`, `dashboard`, `view`, `add` (limited by tag), `edit`, `delete`,
synonym functions and more. Give visitors `tags/read` and `tags/view` so tag pages work for them; give editors `add`
only below the tags they curate ([Roles and policies](../../../guides/roles-and-policies.md)).

## Good tagging practice

- [ ] Decide who owns the vocabulary, and whether editors may add tags.
- [ ] Merge duplicates and use synonyms rather than near-identical tags.
- [ ] Keep the tree shallow; three levels are plenty for most sites.
- [ ] Translate tags for multilingual sites.
- [ ] Give each tag page an introduction, so it is a useful landing page for search engines.

## Related pages

- [Choosing datatypes for your fields](choosing-datatypes.md)
- [Site search](site-search.md)
- [SEO basics for Exponential sites](seo-basics.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
