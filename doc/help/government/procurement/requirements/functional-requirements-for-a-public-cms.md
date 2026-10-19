---
title: Functional requirements for a public-sector CMS
audience: government, project-manager, editor, admin
category: public-sector
order: 30
summary: A catalog of functional requirements a public body usually needs from a web content management system - content model, editing, approval, versions, permissions, search, forms, feeds, multisite - each phrased to be verifiable, with the Exponential documentation that answers it.
keywords: functional requirements, CMS, content model, editing, approval workflow, versions, roles, permissions, sections, search, forms, RSS, PDF, multisite, tender, specification
type: help
---
# Functional requirements for a public-sector CMS

This page is for project managers and procurement teams who list what the CMS must do for editors, approvers and
visitors. Each requirement is written so that a bidder can answer it and an evaluator can check it. The last column
names the Exponential page that documents the feature, so you can see what an answer backed by evidence looks like.
Adapt the wording and the priorities to your own needs; see
[How to write verifiable requirements](how-to-write-verifiable-requirements.md).

## Contents
1. [Content and editing](#content-and-editing)
2. [Approval, versions and history](#approval-versions-and-history)
3. [Permissions and organization](#permissions-and-organization)
4. [Search, navigation and addresses](#search-navigation-and-addresses)
5. [Forms, feeds and outputs](#forms-feeds-and-outputs)
6. [Several sites](#several-sites)
7. [What to leave out or ask separately](#what-to-leave-out-or-ask-separately)

## Content and editing

| Id | Requirement | Exponential evidence |
|---|---|---|
| FUN-01 | Content types (for example news item, service page, event, document) can be defined by the customer with their own fields, without programming. | [The content model and editing content](../../../../guides/content-model-and-editing.md), [Class groups](../../../../guides/class-groups.md) |
| FUN-02 | Editors write rich text with headings, lists, links, tables and embedded images in a browser editor. | [Online editor TinyMCE 8](../../../../features/6.0/online-editor-tinymce8.md), [Editing content](../../../editing-content.md) |
| FUN-03 | Images and files are stored once and reused on several pages. | [Media and uploads](../../../media-and-uploads.md) |
| FUN-04 | Editors can place one item in several places of the site structure. | [The content model and editing content](../../../../guides/content-model-and-editing.md) |
| FUN-05 | Deleted content goes to a trash from which it can be restored, with a record of who removed it. | [Trash: who and where](../../../../features/6.0/trash-who-and-where.md) |
| FUN-06 | Editors find their unfinished drafts and the items waiting for them. | [My drafts and my pending items](../../../../guides/drafts-and-pending.md) |

## Approval, versions and history

| Id | Requirement | Exponential evidence |
|---|---|---|
| FUN-10 | Content can require approval by a named group before it is published. | [Workflows: approvals, triggers](../../../../guides/workflows.md) |
| FUN-11 | Every published change is kept as a version; earlier versions can be compared and restored. | [Versions of an object](../../../../guides/content-history.md), [Publishing and versions](../../../publishing-and-versions.md) |
| FUN-12 | Content can pass through stages of the customer's own definition (for example draft, legal review, ready) with different permissions per stage. | [Object states](../../../../guides/object-states.md) |
| FUN-13 | A record shows who published, moved or removed content and when. | [The audit trail](../../../../features/6.0/audit-trail.md) |

## Permissions and organization

| Id | Requirement | Exponential evidence |
|---|---|---|
| FUN-20 | Access is granted through roles with fine-grained policies (by module, by content type, by part of the site). | [Roles and policies](../../../../guides/roles-and-policies.md) |
| FUN-21 | Content can be grouped into areas (for example per department) that carry their own permissions. | [Sections](../../../../guides/sections.md) |
| FUN-22 | Administrators can find accounts that have not been used for a period and disable them. | [Check user accounts](../../../../features/6.0/user-hygiene.md) |
| FUN-23 | The administration shows editors only the links they are allowed to use. | [Admin links follow permissions](../../../../features/6.0/admin-links-follow-permissions.md) |

## Search, navigation and addresses

| Id | Requirement | Exponential evidence |
|---|---|---|
| FUN-30 | Visitors can search the site; the search can be replaced by an enterprise search engine. | [Site search](../../../creators/website-creators/site-search.md), [Search with ezfind and Solr](../../../creators/website-creators/search-with-ezfind-and-solr.md) |
| FUN-31 | Every page has a readable address derived from its title; old addresses keep working after a move. | [Links, URL aliases, wildcards](../../../../guides/urls-and-aliases.md), [Redirects and moved content](../../../creators/website-creators/redirects-and-moved-content.md) |
| FUN-32 | Content can be tagged with a controlled vocabulary. | [Tagging with eztags](../../../creators/website-creators/tagging-with-eztags.md) |
| FUN-33 | The site publishes an XML sitemap and a robots.txt. | [Sitemaps](../../../creators/website-creators/sitemaps.md), [robots.txt](../../../../features/6.0/robots-txt.md) |

## Forms, feeds and outputs

| Id | Requirement | Exponential evidence |
|---|---|---|
| FUN-40 | Editors can build simple forms (contact, feedback, poll) and read the submissions in the administration. | [Collected information](../../../../guides/collected-information.md), [Forms with the information collector](../../../creators/website-creators/forms-with-the-information-collector.md) |
| FUN-41 | News and other lists are published as RSS feeds; external feeds can be imported. | [RSS feeds](../../../../guides/rss-feeds.md) |
| FUN-42 | A part of the site can be exported as one PDF document. | [PDF exports](../../../../guides/pdf-exports.md) |
| FUN-43 | Subscribers can receive newsletters, with consent records and one-click unsubscribe. | [Newsletters with cjw_newsletter](../../../creators/website-creators/newsletters-with-cjw-newsletter.md), [E-mail preferences](../../../../features/6.0/mail-preferences.md) |
| FUN-44 | The site can be taken offline for maintenance with a message for visitors. | [Maintenance mode](../../../../guides/maintenance-mode.md) |

## Several sites

| Id | Requirement | Exponential evidence |
|---|---|---|
| FUN-50 | Several sites (for example the main site and campaign or department sites) run from one installation, with their own addresses, designs and languages. | [Multisite setup](../../../creators/website-creators/multisite-setup.md), [Multi-site INI overrides](../../../../features/6.0/multi-site-ini-overrides.md) |
| FUN-51 | Editors can link between those sites without hard-coding addresses. | [Multisite cross-site links](../../../../features/6.0/multisite-cross-site-links.md) |

## What to leave out or ask separately

- **Features that the documentation does not show.** Scheduled publishing at a future date, for example, has
  no guide of its own; the workflows guide lists a *Wait until date* event type among the workflow events
  ([Workflows](../../../../guides/workflows.md)). If you need scheduled publishing, ask the bidder to state how it is
  delivered and to demonstrate it.
- **Maps and geographic information.** Exponential has no GIS features; a map field from an extension relies on a
  third-party map service. Ask separately and treat the service as a processor.
- **Analytics.** The kernel ships none; an extension that adds a third-party analytics service needs consent
  ([Privacy standards](../../../../standards/privacy.md)).
- **Accessibility, security, privacy and languages** have their own requirement pages in this folder.

## Related pages
- [Requirements for a public-sector CMS](README.md)
- [Integration and API requirements](integration-and-api-requirements.md)
- [Language and multilingual requirements](language-and-multilingual-requirements.md)
- [What you can build](../../../creators/website-creators/what-you-can-build.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
