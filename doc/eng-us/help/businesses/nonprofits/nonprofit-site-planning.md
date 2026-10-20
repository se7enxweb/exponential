---
title: Non-profit site planning
audience: businesses, project-manager, editor
category: business-solutions
order: 10
summary: The decisions a charity, foundation or community organization takes before building its site on Exponential - goals, audiences, the pages a non-profit needs, who edits, who approves, which features to use, and a one-page planning worksheet.
keywords: nonprofit website plan, charity website, site structure, audiences, donors, volunteers, beneficiaries, editors, approval, planning worksheet
type: help
---
# Non-profit site planning

This page is for the director, board member or coordinator who is responsible for a non-profit's website. It turns the
general planning advice for Exponential sites into the decisions a non-profit takes, and it names the feature behind
each one. Read it before you talk to a developer or partner; it makes their estimate faster and more accurate.

## Contents

1. [Start from goals](#start-from-goals)
2. [Audiences](#audiences)
3. [The pages most non-profits need](#the-pages-most-non-profits-need)
4. [Who edits and who approves](#who-edits-and-who-approves)
5. [Choosing the features](#choosing-the-features)
6. [Languages and accessibility](#languages-and-accessibility)
7. [Hosting and budget](#hosting-and-budget)
8. [A planning worksheet](#a-planning-worksheet)

## Start from goals

Write down three to five goals the site serves, each with something you can observe:

| Goal | Observable sign |
|---|---|
| Explain the mission | Visitors reach the "About" and "Our work" pages |
| Raise money | Clicks on the donate button, or shop orders if you sell donation items |
| Recruit volunteers | Volunteer forms received ([Volunteers and sign-up forms](volunteers-and-sign-up-forms.md)) |
| Keep supporters informed | Newsletter subscribers ([Newsletters](../newsletters/README.md)) |
| Run campaigns | Visits to campaign pages during the campaign ([Campaigns and landing pages](campaigns-and-landing-pages.md)) |

Exponential does not set or report these targets for you; the measuring tools are listed on
[Statistics and analytics](../../creators/website-creators/statistics-and-analytics.md).

## Audiences

List each audience and the one thing they come to do: donors (give, see impact), volunteers (sign up), people you serve
(find help, contact you), partners and funders (reports, contacts), press (facts, images). The top tasks become the
main menu; everything else sits below. For the method, see
[Planning your website](../../creators/website-creators/planning-your-website.md) and
[Site map and content inventory](../../creators/website-creators/site-map-and-content-inventory.md).

## The pages most non-profits need

- [ ] Home: mission in one sentence, the current campaign, the donate and volunteer buttons.
- [ ] About: mission, history, board and staff, legal information your law requires.
- [ ] Our work: programs or projects, each a page with its own story and results.
- [ ] Get help: for the people you serve, in plain language.
- [ ] Donate ([Donations and fundraising pages](donations-and-fundraising-pages.md)).
- [ ] Volunteer ([Volunteers and sign-up forms](volunteers-and-sign-up-forms.md)).
- [ ] News and events.
- [ ] Reports and documents: annual reports and accounts as downloads
      ([Files and downloads](../../creators/website-creators/files-and-downloads.md)).
- [ ] Contact, privacy notice, accessibility statement.

The `ezwebin` site interface, required by `composer.json`, ships class packages such as `article`, `event`,
`event_calendar`, `feedback_form`, `file`, `folder`, `frontpage` and `blog` (`extension/ezwebin/packages/`), which
cover most of this list without new classes.

## Who edits and who approves

Small organizations often have one staff member and several volunteers. Decide:

| Role | Who | Permission idea |
|---|---|---|
| Site owner | Director or coordinator | Approves structure and new sections |
| Editors | Staff, trusted volunteers | Edit in their own sections only |
| Form readers | Whoever answers volunteers and contacts | `infocollector/read`, nothing more |
| Administrator | A partner or a skilled volunteer | Settings, updates, backups |

Roles and policies are explained in [Roles, policies and unactivated users](../../../guides/roles-and-policies.md);
approval before publishing in [Workflows](../../../guides/workflows.md). Two-factor login protects accounts that can
change the site ([Two-factor login](../../../guides/two-factor-login.md)). The
[Associations and small groups](../associations/README.md) section covers volunteer editors in more depth.

## Choosing the features

| Feature | Use it if |
|---|---|
| Exponential Layouts | Editors change the home page and campaign pages often |
| Information collector forms | You need contact, volunteer or feedback forms |
| `cjw_newsletter` | You send regular updates by mail |
| The shop with `ezpaypal` | You sell items (including fixed donation items) and accept PayPal |
| `eztags` | Content needs topics that cut across sections |
| Multilingual content | You serve people in several languages |

Each is part of a standard installation (`composer.json`). Start with the fewest features that meet your goals; each
one adds training and upkeep.

## Languages and accessibility

If you serve people in several languages, plan the languages from the start
([Multilingual sites](../../creators/website-creators/multilingual-sites.md), [Multilingual](../multilingual/README.md)).
Plan accessibility from the start too: it is cheaper than repairing a finished site
([Accessibility for non-profits](accessibility-for-nonprofits.md)).

## Hosting and budget

See [Low-budget operations](low-budget-operations.md) for the choices that keep running costs low, and
[Costs](../costs/README.md) for a cost model to fill with your own figures.

## A planning worksheet

```text
Mission in one sentence:       ...
Goals (with observable sign):  1. ...  2. ...  3. ...
Audiences and top tasks:       ...
Main menu:                     ...
Donations:                     external page / shop items / none
Volunteer forms:               ... (who reads them: ...)
Newsletter:                    yes / no (owner: ...)
Campaign pages:                expected per year: ...
Languages:                     ...
Editors and their sections:    ...
Approval before publishing:    yes / no
Administrator:                 ...
Hosting:                       ... (database: SQLite / other)
Launch date:                   ...
```

## Related pages

- [Non-profits](README.md)
- [Planning your website](../../creators/website-creators/planning-your-website.md)
- [Planning an Exponential project](../../leadership/planning-an-exponential-project.md)
- [Project brief template](../templates/project-brief-template.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
