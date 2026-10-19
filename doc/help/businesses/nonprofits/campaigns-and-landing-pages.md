---
title: Campaigns and landing pages
audience: businesses, editor, sales-marketing
category: business-solutions
order: 40
summary: How a non-profit builds campaign pages with Exponential Layouts - the five ideas of the layouts editor, a campaign page from scratch, switching it on and off by date with a time condition, shared header and footer, handing it to editors, and connecting the campaign to donations, forms and the newsletter.
keywords: campaign page, landing page, Exponential Layouts, layouts editor, time condition, appeal, fundraising campaign, awareness campaign, blocks, zones, rules
type: help
---
# Campaigns and landing pages

This page is for campaign leads, communication staff and editors of non-profits. It shows how a campaign page is built
in Exponential Layouts without a developer, how it can switch itself on and off by date, and how it connects to the
donate button, the volunteer form and the newsletter.

## Contents

1. [Why Layouts for campaigns](#why-layouts-for-campaigns)
2. [The five ideas](#the-five-ideas)
3. [Building a campaign page](#building-a-campaign-page)
4. [Switching on and off by date](#switching-on-and-off-by-date)
5. [Drafts and who publishes](#drafts-and-who-publishes)
6. [Connecting the campaign](#connecting-the-campaign)
7. [After the campaign](#after-the-campaign)
8. [Campaign checklist](#campaign-checklist)

## Why Layouts for campaigns

The README calls Exponential Layouts a visual page builder that ships with the product, and the website creator's guide
recommends it for "front pages, landing and campaign pages that change often" and "pages editors must change without a
developer" ([Landing pages with layouts](../../creators/website-creators/landing-pages-with-layouts.md#when-to-use-layouts-and-when-templates)).
The `se7enxweb/explayouts*` packages are in the `require` block of `composer.json`.

## The five ideas

| Idea | Meaning |
|---|---|
| Layout | A named arrangement based on a layout type, which fixes its zones |
| Zone | A named region of a layout (`main`, `sidebar`, `footer`) |
| Block | One item in a zone: text, image, a list of articles, a hero, a button |
| Collection | The content a listing block shows: picked by hand, or a query |
| Rule | Decides which requests get which layout, by targets and conditions |

Source: [Landing pages with layouts, The five ideas](../../creators/website-creators/landing-pages-with-layouts.md#the-five-ideas).
Editors find the editor at `/explayouts_ui_api/app` in the administration; the help page
[The layouts editor](../../layouts-editor.md) walks through it.

## Building a campaign page

Adapted from [A campaign page from scratch](../../creators/website-creators/landing-pages-with-layouts.md#a-campaign-page-from-scratch):

1. **Create the content page** where the campaign lives, for example `/Winter-appeal`.
2. **Create the layout**: a layout type with a top zone and columns; name it after the campaign.
3. **Fill the zones**:
   - top: a `hero` block with the campaign's headline, one sentence and a button;
   - main: a `rich_text` block with the story, and a `list` block with a dynamic collection of the campaign's news;
   - side: a `button` block ("Button / Link") to the donation page and one to the volunteer form.
4. **Map it**: a rule with target `node` (the campaign page).
5. **Preview**, then **Publish**.
6. **Check** the page on the site, logged out, and on a phone.

The `button` block type is listed as "Button / Link" in the [Layouts reference](../../../bc/6.0/LAYOUTS.md).

## Switching on and off by date

A rule can carry a condition `time`, with `from` and/or `to`; "it is designed for campaign layouts that switch
themselves on and off" ([Layouts reference, Conditions](../../../bc/6.0/LAYOUTS.md#conditions)). For a home page
that shows the appeal for four weeks, map a campaign layout to the home page with a time condition and a higher
priority than the normal layout; when the period ends, the normal layout applies again.

> **Warning**
> The reference warns: "An unrecognised condition type **matches** rather than failing. A typo in a condition therefore
> silently widens the rule instead of disabling it." Check a time-limited rule on the site after you save it, and
> again on the day it should end.

Details of rules: [Layout rules](../../creators/website-creators/layout-rules.md).

## Drafts and who publishes

"The editor always works on a draft; visitors only see published layouts." Drafts are per layout, not per user: "two
people editing the same layout overwrite each other"
([Landing pages with layouts, Drafts and publishing](../../creators/website-creators/landing-pages-with-layouts.md#drafts-and-publishing)).
Agree who publishes, and that one person edits a layout at a time. Give editors the `explayouts` policies and only the
layouts they own (same page, Handing over to editors).

The shared header and footer of a site can come from a shared layout, so a campaign page keeps the site's navigation
([Shared header and footer](../../creators/website-creators/shared-header-and-footer.md)).

## Connecting the campaign

| Campaign action | Connect to |
|---|---|
| Give | The donation page or platform ([Donations and fundraising pages](donations-and-fundraising-pages.md)) |
| Volunteer | A form object ([Volunteers and sign-up forms](volunteers-and-sign-up-forms.md)) |
| Stay informed | The newsletter subscribe page ([Newsletters](../newsletters/README.md)) |
| Share | Metadata for social sharing ([Metadata and social sharing](../../creators/website-creators/metadata-and-social-sharing.md)) |
| Measure | Your analytics tool ([Statistics and analytics](../../creators/website-creators/statistics-and-analytics.md)) |

Use one clear call to action per campaign; a page with five equal buttons asks visitors to choose instead of act.

## After the campaign

- Let the time condition end the campaign layout, then confirm it did.
- Keep the campaign page as an archive with its results, or move it to an archive section; do not delete pages others
  have linked to. Moved content can be redirected ([Redirects and moved content](../../creators/website-creators/redirects-and-moved-content.md)).
- Write down what worked for the next campaign.

## Campaign checklist

- [ ] One goal and one main call to action.
- [ ] Layout built from a draft, previewed, published by the agreed person.
- [ ] Rule mapped to the right page; time condition checked on the site.
- [ ] Buttons lead to the donation page, the form and the newsletter; all tested logged out.
- [ ] Accessible: headings in order, alternative texts, contrast of the hero text
      ([Accessibility for non-profits](accessibility-for-nonprofits.md)).
- [ ] Social sharing metadata set; analytics in place.
- [ ] End date confirmed; archive plan agreed.

## Related pages

- [Non-profits](README.md)
- [Landing pages with layouts](../../creators/website-creators/landing-pages-with-layouts.md)
- [The layouts editor](../../layouts-editor.md)
- [Promoting your store](../stores/promoting-your-store.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
