---
title: Hiding content
audience: government, editor, admin
category: public-sector
order: 90
summary: For editors and administrators - what hiding a node does in Exponential (visitors no longer see it or anything below it, it stays in the tree and can be revealed), how the administration shows hidden items, scheduled hiding, and when hiding is the right tool for records.
keywords: hide, reveal, hidden, hidden by a parent, visibility, ShowHiddenNodes, hide.php, HideSettings, is_hidden, is_invisible, content.node.hide, search index, records, withdraw, public sector
type: help
---
# Hiding content

This page is for editors and administrators. Hiding takes an item off the public site without removing it. It is often
the right tool when a notice must be withdrawn but kept, or when a page must disappear until it is reviewed.

## Contents

1. [What hiding does](#what-hiding-does)
2. [How to hide and reveal](#how-to-hide-and-reveal)
3. [Seeing what is hidden](#seeing-what-is-hidden)
4. [Scheduled hiding](#scheduled-hiding)
5. [Hiding and records](#hiding-and-records)
6. [Related pages](#related-pages)

## What hiding does

- The node and everything below it become invisible to the public siteaccesses (`ShowHiddenNodes=false` there).
- The item stays in the tree, with its versions, relations and URL alias.
- The search index is updated so the hidden item is not found by visitors.
- Revealing brings it back as it was.
- Hide and reveal are recorded in the audit as `content.node.hide` and `content.node.reveal` (on by default).

## How to hide and reveal

In the administration, on the item: the action **Hide** (or **Hide / unhide** in the context menu), and **Reveal** to
undo. You need the right to hide content in your role.

## Seeing what is hidden

admin4 marks visibility at a glance ([The admin4 design: Visibility and account status](../../../features/6.0/admin4-design.md#visibility-and-account-status)):

| Mark | Meaning |
|---|---|
| filled disc | visible |
| ring | hidden |
| ring with a bar | hidden because an item above it is hidden |

The node view header shows a badge (Visible, Hidden, Hidden by a parent); the sub-items table shows the mark before
each name; the content tree marks hidden nodes.

## Scheduled hiding

The cronjob script `hide.php` hides objects of chosen classes when a date attribute has passed, once
`content.ini [HideSettings] RootNodeList[]` and `HideDateAttributeList[<class>]=<attribute>` are set. See "Archiving
old notices".

## Hiding and records

- Hiding keeps the record intact; removal does not.
- A hidden item is still reachable by staff in the administration and by anyone with a role that shows hidden nodes;
  hiding is not access control for confidential content. Use sections or states with read policies for that.
- Note the reason for hiding (a field in the class, or your records system), because the audit shows who and when, not
  why.

## Related pages

- [Archiving old notices](archiving-old-notices.md)
- [The admin4 design](../../../features/6.0/admin4-design.md)
- [Editing content](../../editing-content.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
