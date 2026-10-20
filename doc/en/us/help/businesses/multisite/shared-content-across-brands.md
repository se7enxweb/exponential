---
title: Sharing content across brands
audience: businesses, editor, project-manager
category: business-solutions
order: 30
summary: How several brands on one Exponential installation share content without copying it - separate folders, several locations, relations and embeds, shared header and footer layouts, shared media and users, and how editors are kept to their brand.
keywords: shared content, reuse, multisite, brands, locations, relations, embed, shared layouts, media library, users, roles, subtree, business
type: help
---
# Sharing content across brands

This page is for brand owners, content managers and project leads who run several brands on one Exponential
installation and want to publish some things once for all of them: product information, company news, a legal
notice, a press kit, a store locator. It explains the ways content is shared without copying, what stays separate,
and how to keep each editorial team in its own area.

## Contents

1. [Why share instead of copy](#why-share-instead-of-copy)
2. [The ways to share](#the-ways-to-share)
3. [Shared header, footer and page parts](#shared-header-footer-and-page-parts)
4. [Media, users and tags](#media-users-and-tags)
5. [Keeping teams in their brand](#keeping-teams-in-their-brand)
6. [Deciding what to share](#deciding-what-to-share)

## Why share instead of copy

A copied text has to be corrected in every copy. On one installation, a shared item is one object: correct it once,
publish, and every brand that shows it shows the correction. That matters most for content with legal or financial
weight, such as prices, safety information, terms and contact details.

## The ways to share

All brands on one installation work on the same content tree. The website creators' documentation names three ways
to reuse an object ([Relations and content reuse](../../creators/website-creators/relations-and-reuse.md#three-ways-to-reuse)):

| Way | Use when | Result |
|---|---|---|
| A relation field | the relation is part of the content model ("related products", "author", "venue") | a typed field editors fill with a picker |
| An embed in rich text | an editor wants an image or box inside a text | the object is shown at that place in the body |
| Several locations | the item belongs in two places of the menu | the same object at two addresses |

For brands, **several locations** is the most direct form: one product object placed in the product folder of brand A
and of brand B appears on both sites, with each site's design. **Relations and embeds** suit content that one brand
quotes or teases from another.

The other shape is a **shared root with different designs**: two siteaccesses show the same content tree, each with its
own design ([Setting up several sites in one installation](../../creators/website-creators/multisite-setup.md#shapes-of-a-multisite)).
That fits a trade site and a consumer site that present the same catalog differently.

> **Warning**
> When one brand's page links to content that lives in another brand's folder, the link must point to the other
> brand's address, or visitors land on a 404. The `ezurlmultisite` extension handles this; see
> [Links between your sites](cross-site-links.md).

## Shared header, footer and page parts

With Exponential Layouts, a page layout can link its zones to the zones of a **shared layout**: the footer is defined
once, and "a new phone number means editing" one place instead of every layout
([Shared layouts: header, footer and prefooter](../../creators/website-creators/shared-header-and-footer.md)). Rules worth knowing:

- A zone links to a zone of a **published** shared layout, never to a draft.
- Blocks of a published shared layout cannot be changed from a page that links to it; the shared layout is opened,
  drafted and edited there.
- A list of shared layouts shows how many zones link to each one, so you see the reach of a change before you make it.

For several brands, a shared layout can carry what every brand has in common (legal links, the group's contact
block), while each brand keeps its own header with its logo.

## Media, users and tags

| Shared item | Why it helps | Note |
|---|---|---|
| Media library (images, files) | One logo set and one product photo per item | Organize it by brand and by use ([Organizing the media library](../../creators/website-creators/organizing-the-media-library.md)) |
| Users and groups | One account per person, one place to disable a leaver | Roles decide what each person may do on which brand |
| Tags (`eztags`) | One vocabulary for products, topics or regions across brands | [Tagging content with eztags](../../creators/website-creators/tagging-with-eztags.md) |

If your brands also publish in several languages, mark shared folders, users and media **always available**, so they
show on a language siteaccess even without a translation
([Main language and always available](../../../guides/content-languages.md#6-main-language-and-always-available)).

## Keeping teams in their brand

- One administration can serve every brand; roles **limited by subtree** keep each team in its brand's folder
  ([Roles and policies](../../../guides/roles-and-policies.md)).
- A shared folder (group news, legal) can be editable by a central team only and readable by all.
- Several administration siteaccesses are possible when teams should not see each other's brands at all
  ([Editors and the administration](../../creators/website-creators/multisite-setup.md#editors-and-the-administration)).
- An approval workflow can hold changes to shared content until the content owner approves them
  ([Workflows](../../../guides/workflows.md)).

## Deciding what to share

Use this table in a workshop with the brand owners:

| Content | Shared, one object | Separate per brand | Owner |
|---|---|---|---|
| Company news | | | |
| Product data and prices | | | |
| Legal notice, privacy, terms | | | |
| Contact details, store locator | | | |
| Campaigns and landing pages | | | |
| Photos and logos | | | |

Rules of thumb: share what must be identical everywhere (facts, legal texts, product data), keep separate what each
brand speaks about in its own voice (campaigns, stories, landing pages).

## Related pages

- [A design per brand](per-brand-designs.md)
- [Links between your sites](cross-site-links.md)
- [Relations and content reuse](../../creators/website-creators/relations-and-reuse.md)
- [Shared layouts: header, footer and prefooter](../../creators/website-creators/shared-header-and-footer.md)
- [Structuring the content tree](../../creators/website-creators/structuring-the-content-tree.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
