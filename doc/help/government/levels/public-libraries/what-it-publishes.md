---
title: What a public library publishes
audience: government, project-manager, website-creator
category: public-sector
order: 20
summary: The content public libraries typically publish - branches and opening hours, events, services, digital resources, reading recommendations, news - and what each needs from Exponential.
keywords: library website, branches, opening hours, events, story time, reading group, library card, digital resources, e-books, recommendations, scope
type: help
---
# What a public library publishes

This page is for project managers and website creators at a public library or library network who define the scope
of a site. It lists what libraries typically publish (general public-sector practice) and what each needs.

## Contents

1. [The content](#the-content)
2. [What the CMS must do](#what-the-cms-must-do)
3. [What the website should not do](#what-the-website-should-not-do)
4. [Questions to settle](#questions-to-settle)

## The content

| Content | Audience | Rhythm |
|---|---|---|
| Branches: address, opening hours, holiday closures, facilities, accessibility | everyone | changes a few times a year; closures at short notice |
| Events: story times, reading groups, author talks, courses, exhibitions | families, readers, learners | weekly; many small items |
| Services: joining, borrowing rules, fees, printing, room booking, home delivery | members | stable |
| Digital resources: e-books, databases, streaming, with how to sign in | members | links to providers |
| Recommendations: reading lists, staff picks, themes | readers | monthly |
| Learning: help with computers, language cafes, homework help | learners | per term |
| News: new services, building works | everyone | occasional |
| About the library: governance, plans, annual report, volunteering, jobs | stakeholders | yearly |

## What the CMS must do

| Need | Exponential | Read |
|---|---|---|
| An events calendar with a month view and a program list | the Event and Event calendar classes; calendar and program views in the ezwebin design | [Events and catalogs](events-and-linked-catalogs.md) |
| Branch staff add their own events | a role limited to the branch's subtree or section | [Structure, sections and roles](structure-sections-and-roles.md) |
| Opening hours in one place | a Branch object per branch, related from other pages | [Content model](content-model.md) |
| Closures at short notice | a notice on the branch page and the front page | [The layouts editor](../../../layouts-editor.md) |
| Events by theme or audience | tags | [Tagging with eztags](../../../creators/website-creators/tagging-with-eztags.md) |
| Event sign-up | the information collector, or the event system you use | [Forms with the information collector](../../../creators/website-creators/forms-with-the-information-collector.md) |
| A newsletter of events | cjw_newsletter | [Newsletters with CJW Newsletter](../../../creators/website-creators/newsletters-with-cjw-newsletter.md) |
| Several languages | translations per object | [Multilingual sites](../../../creators/website-creators/multilingual-sites.md) |

## What the website should not do

| Do not | Instead |
|---|---|
| Rebuild the catalog as content objects | link to the catalog's search; at most embed a search box that sends the query to it |
| Store loan or reader data in the CMS | the library management system holds it |
| Ask for a library card number in a website form | send people to the catalog's own sign-in |
| Track what visitors read with third-party analytics | use privacy-friendly statistics ([Statistics and analytics](../../../creators/website-creators/statistics-and-analytics.md)) |

## Questions to settle

- [ ] Which system holds the catalog, and what search address does it accept?
- [ ] Does the events calendar stay on the website, or does an event system feed it?
- [ ] Who publishes an unexpected closure on a Sunday?
- [ ] Which languages do the library's communities speak?

## Related pages

- [Public libraries](README.md)
- [Content model for public libraries](content-model.md)
- [Planning your website](../../../creators/website-creators/planning-your-website.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
