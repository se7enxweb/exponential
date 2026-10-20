---
title: Proactive disclosure
audience: government, project-manager, editor
category: public-sector
order: 30
summary: How a public body publishes information before anyone asks for it, using Exponential: deciding what to publish, a structure for it, regular publishing with roles and approval, and keeping it current.
keywords: proactive disclosure, publish routinely, publication scheme, transparency, freedom of information, FOIA reading room, routine publication, information we publish
type: help
---
# Proactive disclosure

This page is for managers and editors who publish information routinely, without waiting for requests. Many
freedom of information laws require or encourage this; in the United States, for example, [FOIA.gov](https://www.foia.gov/)
explains the federal law and its proactive disclosure duties. Exponential provides the publishing side; deciding
what must be published is a matter of law and policy.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [What is commonly published](#what-is-commonly-published)
2. [A structure in the content tree](#a-structure-in-the-content-tree)
3. [A routine for each category](#a-routine-for-each-category)
4. [Keeping it current](#keeping-it-current)
5. [Checklist](#checklist)

## What is commonly published

Categories that transparency programs often publish proactively:

| Category | Typical content type in Exponential |
|---|---|
| Who we are and what we do | pages, organization chart as an image with a text alternative |
| What we spend | spending reports as files, a spending register (see "Spending and contract publishing") |
| Our priorities and performance | plans and reports as files |
| How we make decisions | agendas, minutes, decision register (see "Meeting records" and "Decision registers") |
| Our policies and procedures | pages or files |
| Lists and registers | registers kept as content, exported as open data |
| Services we offer | service pages |
| Frequently requested information | released FOI responses (see "Disclosure logs") |

## A structure in the content tree

Create one folder per category below a "Transparency" folder, and put each category in a section if different
teams publish them ([Sections](../../../guides/sections.md)). The folder names become the addresses
(`/Transparency/What-we-spend`), which stay valid after renames because old aliases redirect
([Redirects and moved content](../../creators/website-creators/redirects-and-moved-content.md)).

## A routine for each category

For each category write down: the owner, the frequency, the format, the approval step and the retention. Then set
it up in Exponential:

1. A role for the owning team limited to the category's section ([Roles and policies](../../../guides/roles-and-policies.md)).
2. An approval workflow for the section if a second person must check (see "Approval before publishing").
3. A *Wait until date* workflow event if publications must appear at a set time (see "Scheduled publication and
   embargoes").
4. An RSS export of the folder so subscribers see new items ([RSS feeds](../../../guides/rss-feeds.md)).

## Keeping it current

- Add a "Last reviewed" date field to the classes you use for routine publications, and print it.
- Use **Setup > Link management** to find broken links in published pages ([Links](../../../guides/urls-and-aliases.md#1-the-link-list)).
- Use object states (for example a group "review" with Current and Needs review) to mark items for review, and a
  role limitation so only reviewers set them ([Object states](../../../guides/object-states.md)).
- Do not remove superseded documents silently; mark them superseded and link the new one.

## Checklist

- [ ] Categories, owners and frequencies decided with legal counsel.
- [ ] One folder and section per category; roles per owning team.
- [ ] Approval and scheduling where required; workflow cronjob running.
- [ ] Feeds of new publications.
- [ ] A yearly review of every category.

## Related pages

- [Publication schemes](publication-schemes.md)
- [Disclosure logs](disclosure-logs.md)
- [Structuring the content tree](../../creators/website-creators/structuring-the-content-tree.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
