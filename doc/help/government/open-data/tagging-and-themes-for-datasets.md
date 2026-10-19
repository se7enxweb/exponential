---
title: Tagging and themes for datasets
audience: government, editor, website-creator
category: public-sector
order: 130
summary: How to classify datasets by theme so people and portals can find them: a controlled theme list with eztags (tag tree, synonyms, tag pages) or a simple selection field, and rules for keeping the list small and stable.
keywords: tags, eztags, themes, taxonomy, classification, synonyms, controlled vocabulary, keywords, dcat:theme, data catalog, open data
type: help
---
# Tagging and themes for datasets

This page is for editors and site builders who classify datasets. A controlled list of themes makes a catalog
browsable and lets you map your themes to those of a national or European portal later. Exponential offers two
ways: the `eztags` extension ([Tagging with eztags](../../creators/website-creators/tagging-with-eztags.md)) and the
kernel's Selection datatype.

## Contents

1. [Themes and keywords are different](#themes-and-keywords-are-different)
2. [Option 1: eztags](#option-1-eztags)
3. [Option 2: a selection field](#option-2-a-selection-field)
4. [Rules for a theme list](#rules-for-a-theme-list)
5. [Mapping to a portal's themes](#mapping-to-a-portals-themes)

## Themes and keywords are different

| | Themes | Keywords |
|---|---|---|
| Who chooses the words | the data team, once | the editor, per dataset |
| How many | a short, fixed list | as many as useful |
| Purpose | browsing and filtering | search |
| DCAT term | `dcat:theme` | `dcat:keyword` |
| Datatype | `eztags` or `ezselection` | `ezkeyword` |

## Option 1: eztags

`eztags` keeps a tag tree with synonyms, gives each tag a page that lists everything tagged with it, and offers
autocomplete in the edit form.

1. Open **Tags** (`/tags/dashboard`) and create a top-level tag "Data themes" with one child per theme.
2. Add synonyms where people use other words ("Transport" with the synonym "Mobility").
3. Add a Tags field (`theme`) to the dataset class and limit it to the "Data themes" subtree in its datatype options.
4. Tag each dataset; link the theme tag pages from the catalog page.

With Solr, tag clouds can be built from search (`eztags` `TagCloudOverSolr`)
([Search with ezfind and Solr](../../creators/website-creators/search-with-ezfind-and-solr.md)).

## Option 2: a selection field

When `eztags` is not active, add a Selection field (`ezselection`) named Theme with one option per theme, and tick
multiple selection if a dataset can have several. It is simple and fixed; it has no synonyms and no tag pages,
so you list datasets per theme with a template or a folder per theme.

## Rules for a theme list

- Keep it short: a dozen to twenty themes is easy to scan.
- Name themes in plain language and translate them for each content language.
- Never rename a theme silently; announce changes, because re-users filter by them.
- Give every dataset at least one theme.
- Review the list once a year against **Setup > Search statistics** to see what people look for.

## Mapping to a portal's themes

National and European portals often have their own theme lists. Keep a small mapping table (your theme, portal
theme) in the description of the "Data themes" tag or in a page for the data team. If you later write a DCAT
export template, it uses this table. Exponential does not ship any portal's vocabulary.

## Related pages

- [Tagging with eztags](../../creators/website-creators/tagging-with-eztags.md)
- [eztags](../../../features/6.0/extensions/eztags.md)
- [Choosing datatypes](../../creators/website-creators/choosing-datatypes.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
