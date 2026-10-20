---
title: Project managers: start here
audience: project-manager
category: leadership
order: 10
summary: The landing page for project managers: what the leadership pages of this help offer, a plan for your first week with Exponential, and links to every leadership page and the help pages you will use most.
keywords: project manager, project management, start here, leadership, onboarding, first week, plan, overview, roles, planning, Exponential
featured: true
type: help
---
# Project managers: start here

This page is for project managers who run a website or application project built on Exponential, whether you are
new to the product or new to the role. It answers three questions: what this part of the help offers you, what to do
in your first week, and where to find each answer later. Everything here is written so that the help alone can be
your information source: every product fact on these pages points to the file it comes from.

> **Note**
> Exponential is the open source content management system, framework and shop that runs in this administration. It
> continues the code base of eZ Publish, a product its creator discontinued after almost 18 years, and is led by the
> company **7x** together with the nonprofit **Exponential Foundation**
> ([Who is 7x?](../who-is-7x.md), [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md)).

## Contents

1. [What this section gives you](#what-this-section-gives-you)
2. [Your first week](#your-first-week)
3. [Every leadership page](#every-leadership-page)
4. [The existing help pages you will use most](#the-existing-help-pages-you-will-use-most)
5. [How the help itself works](#how-the-help-itself-works)
6. [Ground rules these pages follow](#ground-rules-these-pages-follow)
7. [Questions to answer before your kick-off](#questions-to-answer-before-your-kick-off)

## What this section gives you

The leadership pages turn the technical documentation of Exponential into what a project manager needs to plan,
estimate, decide, coordinate and hand over. They do not replace the guides for editors, administrators and
developers; they tell you which of those guides answers which question, and in what order to read them.

| You need to | Read |
|---|---|
| Explain the product and its family to a sponsor or a new team member | [What Exponential is: the product family](what-exponential-is.md) |
| Plan the phases of a project and map people to roles in the administration | [Planning an Exponential project](planning-an-exponential-project.md) |
| Decide where the site runs: hosting, database, operating system, web server | [Choosing hosting, database and operating system](choosing-hosting-database-and-os.md) |
| Scope the work and find out what already exists as an extension | [Estimating and scoping with extensions](estimating-and-scoping-with-extensions.md) |
| Design the content structure with editors and developers | [Running content modeling workshops](content-modeling-workshops.md) |
| Plan several sites or several languages from one installation | [Planning multisite and multilingual projects](multisite-and-multilingual-planning.md) |
| Prepare accessibility, security and data protection reviews | [Accessibility, security and compliance checklists](accessibility-security-and-compliance-checklists.md) |
| Plan releases and upgrades over the life of the site | [Release and upgrade planning](release-and-upgrade-planning.md) |
| Agree on what "done" means and how it is checked | [Quality gates and acceptance](quality-gates-and-acceptance.md) |
| Keep a risk register | [Risk management for Exponential projects](risk-management.md) |
| Copy a charter, a RACI chart or a launch checklist | [Project templates](project-templates.md) |
| Report progress from the product's own logs | [Measuring a project: KPIs from logs and audit](kpis-from-logs-and-audit.md) |

## Your first week

The plan below assumes you have an account in the administration with at least the Editor role, and a demonstration
or test installation that you are allowed to change. If you have no installation yet, ask an administrator or a
developer for one: a complete demonstration site is installed with one command,
`./console exp:install`, on SQLite and without a database server
([Installing Exponential in one command](../../features/6.0/install-in-one-command.md)).

### Day 1: know the product and the people behind it

- [ ] Read [What Exponential is: the product family](what-exponential-is.md) (30 minutes).
- [ ] Skim [Who is 7x?](../who-is-7x.md) and [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md),
      at least the sections "In short" and "The support commitment".
- [ ] Write down, in one sentence each, which product of the family your project uses and why. If you cannot, the
      table "Which product is for you" on the Foundation page helps.

### Day 2: walk the administration as an editor

- [ ] Follow [Getting started](../getting-started.md): sign in, find an item, make a first change, sign out.
- [ ] Follow [A tour of the administration](../admin-tour.md), including "In action: editing a folder" and
      "In action: the layouts editor".
- [ ] Read [Publishing, versions and the trash](../publishing-and-versions.md): drafts, versions and going back are
      what editors will ask you about first.
- [ ] Note every place where your role shows a button grayed out or not at all. That is your first lesson in roles
      and policies.

### Day 3: understand roles, permissions and the content model

- [ ] Read sections 1, 2 and 5 of [Roles, policies and unactivated users](../../guides/roles-and-policies.md).
- [ ] Read section 1 of [The content model and editing content](../../guides/content-model-and-editing.md): classes,
      attributes, datatypes, objects and nodes.
- [ ] Read "In short" of [Sections](../../guides/sections.md) and of [Object states](../../guides/object-states.md).
- [ ] Draft a first list of the people in your project and the role each one will need
      ([Planning an Exponential project](planning-an-exponential-project.md) has a table to start from).

### Day 4: understand where the site runs and how it changes

- [ ] Read [Choosing hosting, database and operating system](choosing-hosting-database-and-os.md).
- [ ] Ask your administrator which database, which web server and which install method your project uses, and
      write it into your charter ([Project templates](project-templates.md)).
- [ ] Read "In short" of [The audit trail](../../features/6.0/audit-trail.md): it is your record of who did what.
- [ ] Read [When something goes wrong](../when-something-goes-wrong.md): it is the procedure your team uses to report
      errors.

### Day 5: plan

- [ ] Fill in the scoping worksheet of [Estimating and scoping with extensions](estimating-and-scoping-with-extensions.md).
- [ ] Schedule the first content modeling workshop ([Running content modeling workshops](content-modeling-workshops.md)).
- [ ] Start a risk register from [Risk management for Exponential projects](risk-management.md).
- [ ] Agree on the quality gates with the developers ([Quality gates and acceptance](quality-gates-and-acceptance.md)).
- [ ] Read [Working together](working-together.md) and share it with the team.

> **Tip**
> Keep a project notebook from day 1: every decision with its date, who made it and the help page or file it is
> based on. Most of the templates on [Project templates](project-templates.md) assume one.

## Every leadership page

| Order | Page | For |
|---|---|---|
| 10 | Project managers: start here (this page) | project managers |
| 20 | [What Exponential is: the product family](what-exponential-is.md) | project managers, sales and marketing |
| 30 | [Planning an Exponential project](planning-an-exponential-project.md) | project managers |
| 40 | [Choosing hosting, database and operating system](choosing-hosting-database-and-os.md) | project managers, administrators |
| 50 | [Estimating and scoping with extensions](estimating-and-scoping-with-extensions.md) | project managers, developers |
| 60 | [Running content modeling workshops](content-modeling-workshops.md) | project managers, editors, developers |
| 70 | [Planning multisite and multilingual projects](multisite-and-multilingual-planning.md) | project managers, administrators |
| 80 | [Accessibility, security and compliance checklists](accessibility-security-and-compliance-checklists.md) | project managers, administrators |
| 90 | [Release and upgrade planning](release-and-upgrade-planning.md) | project managers, administrators |
| 100 | [Quality gates and acceptance](quality-gates-and-acceptance.md) | project managers, developers |
| 110 | [Risk management for Exponential projects](risk-management.md) | project managers |
| 120 | [Working with the community and contributing back](working-with-the-community.md) | project managers, developers |
| 130 | [Running a team on Git](running-a-team-on-git.md) | project managers, developers |
| 140 | [Project templates: charter, RACI, checklists, launch and handover](project-templates.md) | project managers |
| 150 | [Measuring a project: KPIs from the product's own logs and audit trail](kpis-from-logs-and-audit.md) | project managers, administrators |
| 200 | [Working together](working-together.md) | everyone |
| 210 | [Becoming a leader in the Exponential community](becoming-a-community-leader.md) | everyone |
| 220 | [The leadership reading path](leadership-reading-path.md) | project managers, sales and marketing |

Sales and marketing colleagues have their own section, which starts at
[Sales and marketing: start here](../business/sales-and-marketing-start-here.md). Two of its pages are useful to
project managers too: [Who Exponential is for](../business/who-exponential-is-for.md) and
[Licensing explained for buyers](../business/licensing-for-buyers.md).

## The existing help pages you will use most

### For working with editors

| Page | Why a project manager reads it |
|---|---|
| [Getting started](../getting-started.md) | What every new editor does in the first ten minutes; use it for onboarding |
| [A tour of the administration](../admin-tour.md) | The words your editors use: header, tabs, columns, dashboard |
| [Creating and editing content](../editing-content.md) | What editing a page involves, for estimating content entry |
| [Publishing, versions and the trash](../publishing-and-versions.md) | Drafts, versions, hiding and restoring |
| [Languages](../languages.md) | How translation works for editors |
| [The layouts editor](../layouts-editor.md) | What editors can arrange on a page without a developer |
| [Images, media and uploads](../media-and-uploads.md) | Uploads, many at once, and the media library |

### For working with administrators

| Page | Why a project manager reads it |
|---|---|
| [Roles, policies and unactivated users](../../guides/roles-and-policies.md) | Who may do what; how to ask for a role |
| [Database support](../database-support.md) and [Operating system support](../os-support.md) | The facts behind the hosting decision |
| [Security hardening](../security-hardening.md) | The checklist administrators work through before launch |
| [Operating a site](../../guides/operating-a-site.md) | Caches, cronjobs, backups and the weekly checklist |
| [The upgrade check](../../guides/upgrade-check.md) | How an upgrade is checked before and after |

### For working with developers

| Page | Why a project manager reads it |
|---|---|
| [Develop with Git](../develop-with-git.md) | The recommended way to build websites with Exponential: Git clones with full history |
| [Extensions](../../guides/extensions.md) | How extensions are found, installed, built and released |
| [Templates and design](../../guides/templates-and-design.md) | How the look of a site is built |
| [REST API v1 and v2](../../guides/rest-api-v1-and-v2.md) | What integrations can use |
| [The glossary](../../glossary.md) | Every term in one place |

## How the help itself works

The help you are reading is the git-tracked documentation of the repository, shown in the administration
([The help system](../../features/6.0/help-system.md)):

- The **help dashboard** (`/exphelp/dashboard`) has a search field, the pages for each audience, the categories,
  featured pages and the latest changes.
- The **Help** button in the header opens the page that belongs to the screen you are on.
- Every page can be printed and downloaded as a PDF (**Print this page**, **Download as PDF**), which is useful for
  steering committee packs.
- **Report an error** (`/exphelp/report`) explains which tracker to use and prepares an issue with the diagnostics,
  never with passwords, keys or personal data.
- Reading the help needs the policy `exphelp/read`. The Administrator role has it, and a new installation gives it to
  the Editor role ([The help system](../../features/6.0/help-system.md)). If a team member cannot open the help, that
  is the policy to ask for.

## Ground rules these pages follow

- **Every product fact has a source.** A link to a page in `doc/`, or a file path. If a page says "the sources do
  not state", treat that as a question to ask your team or 7x, not as a gap to fill with assumptions.
- **No prices, hours or service levels are invented.** Where an estimate is needed, the pages give you questions and
  worksheets, not numbers. Services and prices of 7x are on the 7x website, not in this help
  ([Who is 7x?](../who-is-7x.md#services-and-hosting)).
- **Templates are fill-in forms.** Placeholders look like `<project name>`.
- **The documentation is free.** You may copy and adapt these pages for your project under the GNU Free
  Documentation License 1.3 ([Free documentation](../free-documentation.md)).

## Questions to answer before your kick-off

Copy this list into your project notebook and fill it in with your sponsor and your technical lead.

| Question | Your answer | Where the help explains the options |
|---|---|---|
| Which product of the family do we build on? | `<answer>` | [What Exponential is](what-exponential-is.md) |
| Is this a new site, or a move from an older installation? | `<answer>` | [Upgrading](../../guides/upgrading.md), [Planning an Exponential project](planning-an-exponential-project.md) |
| How many sites and how many languages? | `<answer>` | [Planning multisite and multilingual projects](multisite-and-multilingual-planning.md) |
| Which database, which web server, which operating system? | `<answer>` | [Choosing hosting, database and operating system](choosing-hosting-database-and-os.md) |
| Which extensions do we need, and which do we build? | `<answer>` | [Estimating and scoping with extensions](estimating-and-scoping-with-extensions.md) |
| Who are the editors, and who approves before publishing? | `<answer>` | [Workflows](../../guides/workflows.md), [Roles and policies](../../guides/roles-and-policies.md) |
| Which accessibility, security and data protection rules apply to us? | `<answer>` | [Checklists](accessibility-security-and-compliance-checklists.md) |
| Who looks after the site after launch, and how are updates applied? | `<answer>` | [Release and upgrade planning](release-and-upgrade-planning.md) |

## Related pages

- [The leadership reading path](leadership-reading-path.md)
- [Working together](working-together.md)
- [Who is 7x?](../who-is-7x.md)
- [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md)
- [The help system](../../features/6.0/help-system.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
