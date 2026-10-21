---
title: Campaigns and appeals
audience: non-profit, businesses, editor, sales-marketing
category: business-solutions
order: 50
summary: Running a fundraising appeal or awareness campaign on a non-profit's Exponential site - planning one goal and one action, a campaign layout in Exponential Layouts, a layout rule with a time condition that switches it on and off, the warning about mistyped conditions, connecting to donations, volunteers and the newsletter, measuring, and what to do after the campaign.
keywords: campaign, appeal, fundraising appeal, awareness campaign, Exponential Layouts, layout rule, time condition, landing page, call to action, donate button, newsletter, measuring, archive
type: help
---
# Campaigns and appeals

This page is for fundraisers and communications staff. A campaign is a time-limited push: a winter appeal, a matched
giving week, an awareness day, a volunteer drive. Exponential Layouts lets editors build campaign pages without a
developer, and a layout rule can switch a campaign on and off by date. The compact version is
[Campaigns and landing pages](../../businesses/nonprofits/campaigns-and-landing-pages.md).

## Contents

1. [Plan first](#plan-first)
2. [Build the campaign page](#build-the-campaign-page)
3. [Switch it on and off by date](#switch-it-on-and-off-by-date)
4. [Connect the campaign](#connect-the-campaign)
5. [Measure](#measure)
6. [After the campaign](#after-the-campaign)
7. [Campaign checklist](#campaign-checklist)

## Plan first

| Question | Your answer |
|---|---|
| The one goal (money, volunteers, sign-ups, awareness) | |
| The one action you ask for | |
| Start and end dates | |
| Who builds, who approves, who publishes | |
| How you will know it worked | |

Put the dates in the [content calendar](../../customers/day-to-day/content-calendar.md).

## Build the campaign page

1. Create a campaign layout in the layouts editor, starting from a draft
   ([The layouts editor](../../layouts-editor.md), [Landing pages with Layouts](../../creators/website-creators/landing-pages-with-layouts.md)).
2. Use blocks: a headline and image, a short text, the **Button / Link** block for the action, perhaps a list of
   recent news ([Layout types, zones and blocks](../../creators/website-creators/layout-types-zones-and-blocks.md)).
3. Keep the shared header and footer, so the page stays part of your site
   ([Shared header and footer](../../creators/website-creators/shared-header-and-footer.md)).
4. Preview, then publish by the agreed person. "Drafts are per layout, not per user: two people editing the same
   layout overwrite each other" ([Landing pages with Layouts](../../creators/website-creators/landing-pages-with-layouts.md#drafts-and-publishing)).

Accessibility: [Accessible campaigns and documents](../accessibility/accessible-campaigns-and-documents.md#campaign-pages).

## Switch it on and off by date

A layout rule can carry a condition `time`, with `from` and/or `to`; "it is designed for campaign layouts that switch
themselves on and off" ([Layouts reference, Conditions](../../../bc/6.0/LAYOUTS.md#conditions)). To show an appeal on
the home page for four weeks, map the campaign layout to the home page with a time condition and a higher priority
than the normal layout; when the period ends, the normal layout applies again
([Layout rules](../../creators/website-creators/layout-rules.md)).

> **Warning**
> "An unrecognised condition type **matches** rather than failing. A typo in a condition therefore silently widens the
> rule instead of disabling it" (same reference). Check the rule on the site after you save it, and again on the day it
> should end.

## Connect the campaign

| Action | Connect to |
|---|---|
| Give | The giving page or donation platform ([Donations](../donations/README.md)) |
| Volunteer | The volunteer form ([Volunteers](../volunteers/README.md)) |
| Stay informed | The newsletter subscribe page ([Supporter newsletter](supporter-newsletter-with-cjw-newsletter.md)) |
| Come to an event | The event page ([Events and calendars](events-and-calendars-for-non-profits.md)) |
| Share | Metadata for social sharing ([Metadata and social sharing](../../creators/website-creators/metadata-and-social-sharing.md)) |

One main action per campaign. A page with five equal buttons asks visitors to choose instead of act.

## Measure

| Goal | Measure | Where |
|---|---|---|
| Money | Gifts during the period | The platform, or shop orders |
| Volunteers | Applications during the period | Collected information |
| Sign-ups | New confirmed subscribers | `cjw_newsletter` |
| Awareness | Page views | View counts or an analytics service |

See [Measuring outcomes with site data](../grants/measuring-outcomes-with-site-data.md). Write the starting figure down
before the campaign begins.

## After the campaign

- Confirm the time condition ended the campaign layout.
- Thank supporters: a news item and a newsletter edition with the result.
- Keep the campaign page as an archive with its results, or move it; do not delete pages others linked to. Moved
  content can be redirected ([Redirects and moved content](../../creators/website-creators/redirects-and-moved-content.md)).
- Write down what worked for the next campaign.

## Campaign checklist

- [ ] One goal, one action, dates agreed.
- [ ] Layout built from a draft, previewed, published by the agreed person.
- [ ] Rule mapped with a time condition, checked on the site.
- [ ] Buttons tested logged out; donation path tested.
- [ ] Accessibility pre-launch test passed.
- [ ] Starting figures recorded; result published afterward.

See also the [Campaign landing page checklist](../../customers/growth/campaign-landing-page-checklist.md) and
[Before a campaign](../../customers/value/checklists/before-a-campaign.md).

## Related pages

- [Communications for non-profits](README.md)
- [The giving page](../donations/the-giving-page.md)
- [Landing pages with Layouts (owners)](../../customers/growth/landing-pages-with-layouts.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
