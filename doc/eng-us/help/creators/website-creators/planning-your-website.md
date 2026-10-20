---
title: Planning your website
audience: website-creator, project-manager
category: website-creation
order: 40
summary: Plan a website on Exponential in seven steps: goals, audiences, content model, sites and languages, design, features and operations, with a one-page planning sheet to fill in.
keywords: plan, planning, website plan, requirements, goals, audiences, content model, siteaccess, languages, design, launch, operations, planning sheet, website creator
type: help
---
# Planning your website

This page is for website creators at the start of a new site. It turns the decisions Exponential asks of you into
seven planning steps, in the order the product needs the answers, and ends with a planning sheet to fill in. Project
managers who run larger projects will find the same steps, with roles and budgets, in
[Planning an Exponential project](../../leadership/planning-an-exponential-project.md).

## Contents

1. [Why the order matters](#why-the-order-matters)
2. [Step 1: goals and audiences](#step-1-goals-and-audiences)
3. [Step 2: content inventory and site map](#step-2-content-inventory-and-site-map)
4. [Step 3: the content model](#step-3-the-content-model)
5. [Step 4: sites, addresses and languages](#step-4-sites-addresses-and-languages)
6. [Step 5: design](#step-5-design)
7. [Step 6: features](#step-6-features)
8. [Step 7: hosting and operations](#step-7-hosting-and-operations)
9. [The planning sheet](#the-planning-sheet)

## Why the order matters

Some decisions are cheap to change later and some are not:

| Decision | Change later | Why |
|---|---|---|
| Design, templates, stylesheets | Easy | Templates are files; an override can be removed ([Template overrides](template-overrides.md)) |
| Menus, layouts of landing pages | Easy | Settings and the layouts editor |
| Content classes (adding fields) | Easy | A new attribute is added to every object of the class |
| Content classes (changing a field's datatype) | Hard | The stored data belongs to the datatype; a change means migrating content |
| Siteaccess layout (URL, host, port) | Medium | Settings, but links, certificates and search engines follow it |
| Languages | Medium | Adding is easy; removing a translation deletes content ([Content languages](../../../guides/content-languages.md)) |
| Database engine | Hard | A move means an export and import |

So plan the content model, the languages and the database with care, and keep the design flexible.

## Step 1: goals and audiences

Write down, in one sentence each: what the site is for, who visits it, what they should do there (read, contact,
buy, subscribe, log in) and how you will know it works (see [Visitor statistics and analytics](statistics-and-analytics.md)).

## Step 2: content inventory and site map

List what content exists and what will be written, and draw the tree. The method is on
[Site map and content inventory](site-map-and-content-inventory.md). The result decides the folders of the content
tree ([Structuring the content tree](structuring-the-content-tree.md)).

## Step 3: the content model

Group the inventory into kinds of content and give each a list of fields. Prefer the classes a site package already
ships (Article, Folder, Image, File, Product ...) and add only what is missing. Method and examples:
[Designing your content model](designing-your-content-model.md), [Content classes for websites](content-classes-for-websites.md),
[Choosing datatypes for your fields](choosing-datatypes.md).

## Step 4: sites, addresses and languages

Decide:

- **How many public sites** (brands, regions) from one installation. Each is one or more siteaccesses
  ([Setting up several sites in one installation](multisite-setup.md)).
- **How visitors reach each**: by path (`/de`), by host (`www.example.de`) or by port; the installer offers all three
  ([10.2 Siteaccesses and site addresses](../../../install/10-after-installing.md#102-siteaccesses-and-site-addresses)).
- **Which languages**, and which language each site shows first ([Building a multilingual site](multilingual-sites.md)).
- **Right to left?** Arabic or Hebrew content needs an RTL-ready design ([Right-to-left sites](right-to-left-sites.md)).

## Step 5: design

Choose a starting point: a shipped theme, a theme you adapt, or your own design in an extension
([Choosing a design or theme](choosing-a-design-or-theme.md)). Plan accessibility from the start
([Accessibility for site builders](accessibility-for-site-builders.md)).

## Step 6: features

Tick what the site needs; each line is a page of this section:

| Feature | Page |
|---|---|
| Landing pages edited visually | [Landing pages with the layouts editor](landing-pages-with-layouts.md) |
| Contact or feedback forms | [Forms with the information collector](forms-with-the-information-collector.md) |
| Newsletter | [Newsletters with cjw_newsletter](newsletters-with-cjw-newsletter.md) |
| Shop | [Running a shop: overview](shop-overview.md) |
| Search | [Site search](site-search.md) |
| Members area | [Member registration and members-only areas](member-registration-and-areas.md) |
| RSS | [RSS feeds for your site](rss-feeds-for-your-site.md) |
| Tags | [Tagging content with eztags](tagging-with-eztags.md) |
| Custom logic | [Application creators: start here](../app-creators/app-creators-start-here.md) |

## Step 7: hosting and operations

Decide where it runs and who looks after it: web server (Velocity, Apache or nginx), database, backups, cronjobs,
updates ([Hosting requirements for a website](hosting-for-a-website.md), [Maintaining your website](maintaining-your-website.md)).

## The planning sheet

Copy this into your project notes and fill it in.

| Question | Answer |
|---|---|
| Purpose of the site, in one sentence | `<answer>` |
| Visitors and what they do | `<answer>` |
| Kinds of content (classes) | `<answer>` |
| Number of public sites, and their addresses | `<answer>` |
| Languages, first language per site | `<answer>` |
| Design: shipped theme, adapted theme or own | `<answer>` |
| Features (forms, newsletter, shop, search, members, layouts) | `<answer>` |
| Database and web server | `<answer>` |
| Who edits, who approves, who administers | `<answer>` |
| Launch date and who signs it off | `<answer>` |

> **Tip**
> Install a demo site first and click through it with the people who will edit the site. Seeing the administration
> answers half the questions above ([Your first site, step by step](your-first-site-step-by-step.md)).

## Related pages

- [Site map and content inventory](site-map-and-content-inventory.md)
- [Planning an Exponential project](../../leadership/planning-an-exponential-project.md)
- [Running content modeling workshops](../../leadership/content-modeling-workshops.md)
- [The launch checklist](launch-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
