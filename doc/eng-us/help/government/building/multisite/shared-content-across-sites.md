---
title: Shared content across sites
audience: government, editor, website-creator
category: public-sector
order: 60
summary: How one piece of content (a contact page, a policy, a news item) appears on several department sites of one Exponential installation without copying: several locations, object relations and embeds, shared folders, and what to watch with languages and permissions.
keywords: shared content, reuse, multiple locations, Add locations, object relation, embed, shared folder, always available, multisite, no copies
type: help
---
# Shared content across sites

This page is for editors and site builders of a multisite installation. Because all sites share one content tree,
an object can appear on several sites without being copied; corrections then reach every site at once
([Relations and reuse](../../../creators/website-creators/relations-and-reuse.md)).

## Contents

1. [Three ways to share](#three-ways-to-share)
2. [Several locations](#several-locations)
3. [Relations and embeds](#relations-and-embeds)
4. [A shared folder](#a-shared-folder)
5. [Watch out](#watch-out)

## Three ways to share

| Way | Use it when |
|---|---|
| Several locations | the item belongs in the menu of two sites; the same object at two addresses |
| Object relation or embed | a page of one site shows or links an item that lives elsewhere |
| A shared folder read by templates | many items (common news, a central A to Z) appear on every site |

## Several locations

An object can have more than one location (node) in the tree; each location can be in another site's folder. In lists
of items, **Add location...** places selected items in a further location as well (in the list of uploaded files), and the
item's **Locations** tab has **Add locations**; the tab shows
all its locations and which one is the main one. Editing the object changes it everywhere.

## Relations and embeds

Relation fields and embeds in rich text point to the object, not to an address, so they follow it when it moves.
With the `ezurlmultisite` extension, a link to content in another site's folder points to that site's host (see "Links
between department sites").

## A shared folder

Keep central content (for example "News from the whole administration") in a folder outside the department folders,
and let each site's templates fetch from it. Each site then shows the same items in its own design.

## Watch out

- **Permissions**: an item in a shared folder is visible on every site whose visitors may read its section.
- **Languages**: a site shows the item only in languages its `SiteLanguageList[]` contains, unless always available.
- **Removal**: removing one location of an item with several locations keeps the others; removing the object removes
  it everywhere.
- **Addresses**: each location has its own address; the main location decides the canonical one.

## Related pages

- [Relations and reuse](../../../creators/website-creators/relations-and-reuse.md)
- [Links between department sites](links-between-department-sites.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
