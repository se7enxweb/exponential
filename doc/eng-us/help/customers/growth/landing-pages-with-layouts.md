---
title: Landing pages with layouts
audience: customer, editor, businesses
category: customer-success
order: 4
summary: The owner's view of Exponential Layouts for front pages, landing pages and campaigns - what editors can change without a developer, who owns which layout, campaign periods by rule, shared header and footer, drafts and the one-person-per-layout rule - and what still needs your partner.
keywords: landing pages, layouts, Exponential Layouts, page builder, campaign, blocks, zones, rules, shared layouts, front page, site owner
type: help
---
# Landing pages with layouts

This page is for owners and marketing leads who want to change front pages and campaign pages without waiting for a
developer. Exponential Layouts is a visual page builder: a **layout** is a saved arrangement of **blocks** in
**zones**, and a **rule** decides which pages get it, so editors rearrange pages without touching templates
(`README.md`, "Exponential Layouts"). The editor's clicks are on [The layouts editor](../../layouts-editor.md).

## Contents

1. [What editors can do on their own](#what-editors-can-do-on-their-own)
2. [Ownership](#ownership)
3. [Campaign periods](#campaign-periods)
4. [Shared parts: header, footer, prefooter](#shared-parts-header-footer-prefooter)
5. [Drafts and publishing](#drafts-and-publishing)
6. [When you need your partner](#when-you-need-your-partner)

## What editors can do on their own

| Task | How | Source |
|---|---|---|
| Rearrange a page | add, move and change blocks in the zones of its layout | [The layouts editor](../../layouts-editor.md) |
| Show the newest articles automatically | a list or grid block with a dynamic collection (children, subtree, latest ...) | `README.md`, "Exponential Layouts" |
| Pin a chosen item among dynamic ones | a hand-picked item in the collection | same |
| Use text, image, button, card, gallery, carousel, tabs, accordion, map or video blocks | the standard blocks of `explayouts_standard` | same |
| Give a part of the site its own layout | a rule by subtree, class, siteaccess or time | [Layout rules](../../creators/website-creators/layout-rules.md) |
| See which layout a page uses | the **Layouts** tab on a node's view | `README.md`, "Exponential Layouts" |

The editor is at `/explayouts_ui_api/app` in the administration; access needs the `explayouts` module's policies, at
least `explayouts/read` ([Landing pages with the layouts editor](../../creators/website-creators/landing-pages-with-layouts.md)).

## Ownership

Layouts are shared work, so give each one an owner:

| Layout | Typical owner |
|---|---|
| Front page | editor-in-chief |
| Section front pages | the section's lead editor |
| Campaign layouts | the campaign owner, until the campaign ends |
| Shared header, footer, prefooter | one person only, often the owner or the web lead |

Name blocks clearly and note in a block's name why an item is pinned; your successor will thank you
([Landing pages with the layouts editor](../../creators/website-creators/landing-pages-with-layouts.md)).

## Campaign periods

A campaign landing page is a layout mapped to the page by a rule with a `time` condition (`from`, `to`), at a higher
priority than the page's normal rule. Before and after the period the normal layout shows
([Layout rules](../../creators/website-creators/layout-rules.md)). Plan it in the
[Content calendar](../day-to-day/content-calendar.md) and check it with the
[Campaign landing page checklist](campaign-landing-page-checklist.md).

## Shared parts: header, footer, prefooter

A shared layout holds the header, footer or prefooter once; the zones of every page layout link to it. Change it once
and every page follows (`README.md`, "Exponential Layouts";
[Shared layouts](../../creators/website-creators/shared-header-and-footer.md)). That is powerful and therefore
risky: a mistake there is on every page. Keep it with one owner and publish changes to it at a quiet time.

## Drafts and publishing

> **Warning**
> The editor always works on a draft and visitors only see published layouts. Drafts are **per layout, not per
> user**: two people editing the same layout overwrite each other
> ([Landing pages with the layouts editor](../../creators/website-creators/landing-pages-with-layouts.md)). Agree
> that one person edits a layout at a time.

- **Publish** makes the draft live; **Discard draft** throws it away.
- Check the page on the site, signed out, after publishing.

## When you need your partner

| Need | Why |
|---|---|
| A new layout type (a new arrangement of zones) | layout types are defined in settings (`explayouts.ini`); the type of a layout cannot be changed later |
| A new kind of block | a block type is a handler class and a template in an extension |
| A block that shows a form or shop steps | logic belongs in templates ([When to use layouts and when templates](../../creators/website-creators/landing-pages-with-layouts.md#when-to-use-layouts-and-when-templates)) |
| Moving layouts to another installation | export and import (`README.md`, "Exponential Layouts") |

## Related pages

- [The layouts editor](../../layouts-editor.md)
- [Campaign landing page checklist](campaign-landing-page-checklist.md)
- [Campaigns and landing pages for non-profits](../../businesses/nonprofits/campaigns-and-landing-pages.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
