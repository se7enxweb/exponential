---
title: Hide a page temporarily
audience: government, editor
category: public-sector
order: 180
summary: For editors - take a page off the site for a while without removing it, with Hide / unhide in the tree menu or Hide selected in a list, know what hiding affects (everything below, menus, search, downloads) and bring it back.
keywords: hide, unhide, Hide / unhide, Hide selected, Unhide selected, hidden, invisible, temporarily, reverse relations, take offline, page
type: help
---
# Hide a page temporarily

This page is for editors who need a page off the site for a while: information under review, a seasonal page, a
service that is suspended. A hidden item stays in the administration but is not shown on the site, and nor is
anything below it ([Publishing, versions and the trash](../../../publishing-and-versions.md#hide-an-item)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [What hiding affects](#what-hiding-affects)
4. [Check the result](#check-the-result)
5. [Pitfalls](#pitfalls)

## Before you start

- Your role has `content/hide` for the item.
- Decide whether hiding is right: for a page that should go for good, see
  [Remove an outdated page and restore it](remove-an-outdated-page-and-restore-it.md); for a page with a known end
  date, an automatic hide date may be set up ([Publish a public notice with an expiry date](publish-a-public-notice-with-an-expiry-date.md)).

## Steps

1. In the content tree, open the item's menu (click the icon in front of its name) and choose **Advanced** >
   **Hide / unhide**.
2. For several items: in the parent's **Sub items** list, tick them, **More actions** > **Hide selected**.
3. One location only: on the item's **Locations** tab, tick the location and press **Hide**; other locations stay
   visible.
4. If other content points to the item, a warning lists it (the reverse relation check): those pages will point to
   something visitors cannot see ([Reverse relation check](../../../../features/6.0/reverse-relation-check.md)).

To bring it back: **Hide / unhide** again, or **Unhide selected**.

## What hiding affects

| Thing | While hidden |
|---|---|
| The page and everything below it | not shown to visitors |
| Menus and lists | left out (fetches show what visitors see) |
| Search | hidden content is not found by visitors; with eZ Find, the index is told |
| Downloads of files in it | refused to visitors (a visible location is needed) |
| The administration | shown, marked hidden; editors can still edit |

## Check the result

- [ ] Signed out, the page's address no longer shows the page.
- [ ] The parent's menu no longer lists it.
- [ ] Pages that linked to it: fix or remove the links while it is hidden.

## Pitfalls

| Pitfall | Avoid it |
|---|---|
| Hiding a folder hides all its children | check what is below before hiding |
| Forgetting to unhide | note the date in your calendar |
| Expecting search engines to forget at once | copies elsewhere remain for a while |
| A location made visible by an edit | in the edit form's **Locations** window existing locations start at **Unchanged**; choosing **Visible** shows that location |

## Related pages

- [Publishing tasks](README.md)
- [Remove an outdated page and restore it](remove-an-outdated-page-and-restore-it.md)
- [Mark content under review with object states](mark-content-under-review-with-object-states.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
