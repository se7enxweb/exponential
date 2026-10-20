---
title: What can I change in the Design tab?
audience: editor, admin
category: manual
order: 200
summary: The Design tab holds Look and feel (the site name, logo and footer of the classic site designs), the toolbars, the menus of the classic designs, the template editor and the images without alt text; editors usually get Look and feel and alt text only.
keywords: Design tab, look and feel, toolbar management, menus classic, template editor, missing alt text, visual/*, site logo, site name
type: help
---
# What can I change in the Design tab?

The **Design** tab collects what changes the look of the site rather than its content. What you see in it depends on
your role: most editors see only **Look and feel** and **Missing alt text**.

## Who can do this

| Entry in the left menu | Needs |
|---|---|
| **Look and feel** | `content/edit` on the Look and feel object of the site |
| **Toolbar management**, **Menus (classic)**, **Template Editor** | the policy `visual/*` (administrators and designers) |
| **Missing alt text** | the Exponential Intelligence policy for alt text; see the extension's manual |

## Steps

1. Click **Design** in the top menu. If you do not see it, look under **More**.
2. Choose an entry in the **Design** menu on the left:
   - **Look and feel**: edit the site name, logo, footer text and similar fields of the classic site designs. Click
     **Send for publishing** to save.
   - **Toolbar management**: choose which tools the toolbars of a siteaccess show.
   - **Menus (classic)**: choose the menu arrangement of a site that uses a classic design.
   - **Template Editor**: look at and override templates.
   - **Missing alt text**: the images that have no alternative text, with suggestions to review.
3. Check the site in a private window after a change: visitors may see a cached page for a short time.

![The Design tab with its left menu: Look and feel, Toolbar management, Menus (classic), Template Editor and Missing alt text](images/design-tab.webp)

## What can go wrong

- **You change Look and feel but the site does not change.** The site is built with Exponential Layouts, where the
  header and footer are blocks of a layout. Change them in the layout editor instead.
- **Menus (classic) says "None of the siteaccesses listed here reads these settings".** The same reason: the menus of a
  Layouts site are blocks. Nothing needs to be done on this page.
- **A template change broke a page.** Undo the override in the Template Editor, or ask the person who made it.
  Templates are developer work: test them on a copy of the site first.

## Related pages

- [Templates and design](../../guides/templates-and-design.md)
- [Classic menu settings](../../guides/classic-menu-settings.md)
- [The admin4 design](../../features/6.0/admin4-design.md)
- [How do I build a page with Layouts?](how-do-i-build-a-page-with-layouts.md)
- [User manual](README.md)

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
