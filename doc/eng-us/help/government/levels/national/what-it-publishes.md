---
title: What a national government publishes
audience: government, project-manager, website-creator
category: public-sector
order: 20
summary: The kinds of content ministries and central departments typically publish, who reads them, and what each asks of a content management system, mapped to the Exponential feature that answers it.
keywords: ministry website, policy pages, press release, publications, consultation, speeches, statistics, corporate information, national government content, scope
type: help
---
# What a national government publishes

This page is for project managers and website creators at a ministry or central department who define the scope of a
site. It lists the content national governments typically publish (general public-sector practice) and, for each,
what it asks of the content management system and where Exponential provides it.

## Contents

1. [The content](#the-content)
2. [What the CMS must do](#what-the-cms-must-do)
3. [Volume and rhythm](#volume-and-rhythm)
4. [Questions to settle before building](#questions-to-settle-before-building)

## The content

| Content | Typical audience | Typical rhythm |
|---|---|---|
| Policy and topic pages: what the ministry does about an issue | citizens, journalists, stakeholders | changed when policy changes |
| News and press releases | journalists, citizens | daily to weekly, sometimes urgent |
| Speeches and statements by office holders | journalists, researchers | on the day |
| Publications: reports, strategies, white papers, guidance | professionals, researchers | weekly to monthly, often as PDF |
| Consultations: a proposal, the questions, the response form and later the outcome | stakeholders, citizens | a few a year, with fixed opening and closing dates |
| Services: how to apply, who is eligible, links to the online transaction | citizens and businesses | stable, reviewed regularly |
| Corporate information: structure, leadership, budget, annual report, contact | everyone, oversight bodies | yearly and on change |
| Transparency material: disclosure logs, spending, contracts | journalists, watchdogs | on a fixed schedule |
| Jobs and procurement notices | job seekers, suppliers | continuous, with deadlines |
| Events and campaigns | target groups | seasonal |

## What the CMS must do

| Need | Exponential feature | Read |
|---|---|---|
| Many editors, each limited to their unit | Sections and roles with section limitations | [Sections](../../../../guides/sections.md), [Roles and policies](../../../../guides/roles-and-policies.md) |
| A second pair of eyes before publishing | An *Approve* event in a workflow on publishing | [Workflows](../../../../guides/workflows.md) |
| History of every published text | Versions with compare and new draft from an old version; the audit trail | [Versions of an object](../../../../guides/content-history.md), [Audit trail](../../../../features/6.0/audit-trail.md) |
| Publications as documents | The File class (`ezbinaryfile`), relations from pages to files, PDF exports of a subtree | [PDF exports](../../../../guides/pdf-exports.md) |
| Several official languages | Content languages and translations per object | [Content languages](../../../../guides/content-languages.md) |
| Consultations with responses | The information collector and **Setup > Collected information** | [Collected information](../../../../guides/collected-information.md) |
| News feeds for journalists and other sites | RSS exports | [RSS feeds](../../../../guides/rss-feeds.md) |
| Notifications to subscribers | The notification system and e-mail preferences | [Notifications](../../../../guides/notifications-administrator.md) |
| Topics that cut across units | Tags with eztags | [Tagging with eztags](../../../creators/website-creators/tagging-with-eztags.md) |
| Search across a large site | Built-in search, or eZ Find with Solr | [Search with eZ Find and Solr](../../../creators/website-creators/search-with-ezfind-and-solr.md) |
| Load peaks | View cache, static cache, Velocity's response cache | [Performance and caching](../../../creators/website-creators/performance-and-caching.md) |

## Volume and rhythm

National sites tend to grow without anyone deciding it. Each unit adds pages; few are removed. Plan for:

- **Ownership**: every page has an owning unit. Sections record the unit; the owner of an object is recorded too
  ([Changing owners](../../../../guides/changing-owners.md)).
- **Review dates**: Exponential has no built-in "review by" reminder. A common pattern is a date attribute
  (`ezdate`) on the class and a list of pages past their date for each unit, made with a template fetch or a saved
  search, and checked in a monthly editorial meeting.
- **Archiving rather than deleting**: superseded publications usually stay available with a clear "withdrawn" or
  "superseded" label. Object states can carry that label and roles can respect it
  ([Object states](../../../../guides/object-states.md)).
- **Urgent publishing**: a minister's statement may have to go out within minutes. Decide in advance who may publish
  without approval, and give that small group a role whose publishing is outside the approval workflow (the *Approve*
  event can exclude user groups).

## Questions to settle before building

- [ ] Which units publish, and who approves for each?
- [ ] Which languages are official, and must every page exist in each before it is published?
- [ ] Which content must be kept, for how long, and by whom (records officer)?
- [ ] Which services are transactional elsewhere, and only linked from the site?
- [ ] Who answers for accessibility of documents uploaded by units, not only of web pages?
- [ ] What is the expected traffic on a normal day and on the busiest day of the last years?

The answers feed the content model, the section and role plan, and the requirements in the next pages.

## Related pages

- [National government](README.md)
- [Content model for national government](content-model.md)
- [Planning your website](../../../creators/website-creators/planning-your-website.md)
- [Site map and content inventory](../../../creators/website-creators/site-map-and-content-inventory.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
