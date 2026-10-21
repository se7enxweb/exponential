---
title: Family sites
audience: individual, website-creator, customer
category: website-creation
order: 1700
summary: The section for people who run a private site for their family on Exponential - photos, news, recipes and family history behind a sign-in - with planning, private areas through sections and roles, inviting relatives, dates of birth, and privacy, and the honest limits.
keywords: family site, private site, family photos, family news, family history, genealogy, private area, sign-in, sections, roles, invite, relatives, birthday, privacy, children
type: help
---
# Family sites

This section is for individuals who want a place on the web for their family: news, photos, recipes, a family
history, the dates that matter. Unlike a personal site, a family site is mostly **private**: it is for relatives who
sign in, not for the public and not for search engines.

## Contents

1. [What a family site usually holds](#what-a-family-site-usually-holds)
2. [The pages of this section](#the-pages-of-this-section)
3. [What carries it in Exponential](#what-carries-it-in-exponential)
4. [What does not exist](#what-does-not-exist)

## What a family site usually holds

| Part | Typical class | Who sees it |
|---|---|---|
| A public front page, or none at all | *Frontpage* or *Folder* | Everyone, or nobody |
| Family news | *Blog* with *Blog post* | Family |
| Photo albums | *Gallery* with *Image* | Family |
| Recipes, stories, family history | *Article* in folders | Family |
| Family events (reunions, celebrations) | *Event* in an *Event calendar* | Family |
| A list of relatives with dates of birth | A class with an `ezbirthday` field, or user profiles | Family, or only you |

## The pages of this section

| Page | Read it when |
|---|---|
| [Planning a family site](planning-a-family-site.md) | Before you install anything |
| [Private areas with roles and sections](private-areas-with-roles-and-sections.md) | You make the site, or part of it, private |
| [Inviting family members](inviting-family-members.md) | You give relatives their accounts |
| [Birthdays and family dates](birthdays-and-family-dates.md) | You want to record dates of birth and anniversaries |
| [Privacy for your family](privacy-for-your-family.md) | Before the first photo of a child goes up |

The [family privacy checklist](../checklists/family-privacy-checklist.md) sums it up for launch day.

## What carries it in Exponential

- **Sections and roles** decide who may read what. The default site package already creates a section called
  *Restricted* that anonymous visitors cannot read (`sevenx-multi-site-installer.php`).
- **A whole siteaccess can require sign-in** with `[SiteAccessSettings] RequireUserLogin=true`
  ([Member registration and areas](../../creators/website-creators/member-registration-and-areas.md#a-members-only-area)).
- **Accounts** are created by you, or by relatives through registration if you allow it.
- **Two-step sign-in** protects accounts ([Two-factor sign-in](../running-it/two-factor-sign-in.md)).
- **Galleries, blogs, events** come from the same classes as on public sites.

## What does not exist

- **No birthday reminders**: the `birthday` extension stores a date of birth and nothing more
  ([Birthdays and family dates](birthdays-and-family-dates.md)).
- **No family-tree or genealogy module.** A family history is articles and relations you build yourself.
- **No private messaging by default**; `ezpm` is only suggested in `composer.json`.
- **No shared family calendar sync** with phones; events are pages on the site.

## Related pages

- [Individuals: start here](../individuals-start-here.md)
- [Club sites](../club-sites/README.md)
- [Member registration and members-only areas](../../creators/website-creators/member-registration-and-areas.md)
- [Non-technical people: start here](../../non-technical-people/non-technical-people-start-here.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
