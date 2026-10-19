---
title: What a parliament publishes
audience: government, project-manager, website-creator
category: public-sector
order: 20
summary: The content parliaments and legislative assemblies typically publish - members, agendas, bills, votes, transcripts, committees, petitions, visiting and education information - and what each needs from Exponential.
keywords: parliament website, members, order paper, agenda, bills, legislation stages, votes, divisions, transcripts, hansard, committees, petitions, scope
type: help
---
# What a parliament publishes

This page is for project managers and website creators at a parliament or legislative assembly who define the scope
of a site. It lists what parliaments typically publish (general public-sector practice; procedures differ) and what
each kind of content needs.

## Contents

1. [The content](#the-content)
2. [What the CMS must do](#what-the-cms-must-do)
3. [Where the data comes from](#where-the-data-comes-from)
4. [Questions to settle](#questions-to-settle)

## The content

| Content | Audience | Rhythm |
|---|---|---|
| Members: public role, party or group, constituency, committees, contact | citizens, press | with elections and appointments |
| Agendas (order papers) for sittings and committee meetings | press, members, public | daily during sittings |
| Bills: text of each version, stage, amendments, explanatory notes | lawyers, press, stakeholders | as bills move |
| Votes: result per vote, and how each member voted where recorded | press, researchers, citizens | within hours of a vote |
| Transcripts of debates | researchers, press | the day after or later |
| Committees: members, inquiries, evidence, reports | stakeholders | continuous |
| Petitions, questions, written answers | citizens | continuous |
| Live and recorded video of sittings | public | during sittings |
| Visiting, education, how parliament works | schools, public | stable |
| Administration: budget, members' expenses as required, jobs | oversight, press | yearly |

## What the CMS must do

| Need | Exponential | Read |
|---|---|---|
| Bills linked to their versions, votes and committees | relations between objects | [Relations and reuse](../../../creators/website-creators/relations-and-reuse.md) |
| Member pages reused everywhere a member appears | a Member class, related from votes and committees | [Content model for parliaments](content-model.md) |
| Official documents unchanged once published | versions, the audit trail, a restricted role for corrections | [Versions of an object](../../../../guides/content-history.md) |
| Lists by date (agendas, votes) | date attributes and fetches; RSS exports | [RSS feeds](../../../../guides/rss-feeds.md) |
| Load during important votes | caches and Velocity | [Performance and caching](../../../creators/website-creators/performance-and-caching.md) |
| Data for reuse | data files as File objects; REST API | [REST API v1 and v2](../../../../guides/rest-api-v1-and-v2.md) |
| Several official languages | translations per object | [Content languages](../../../../guides/content-languages.md) |

## Where the data comes from

| Content | Usual source | The website's part |
|---|---|---|
| Bills and their texts | a legislative drafting or bill tracking system | publish the documents; link to the system's own record |
| Votes | an electronic voting system or the clerks' record | publish the result and the record as a document; per-member votes as a table or data file |
| Transcripts | the reporting service | publish documents or HTML produced by it |
| Video | a streaming platform | link or embed; captions from the provider |

Exponential imports content through its REST API, packages or scripts; connecting a parliamentary system is a
development task. When a source system has its own public interface, link to it rather than copying everything.

## Questions to settle

- [ ] Which system is authoritative for each kind of record, and is the website a copy or the official publication?
- [ ] How soon after a vote must the result be online?
- [ ] Which data is published for reuse, in which formats, under which license?
- [ ] How are members' pages updated after an election?

## Related pages

- [Parliaments](README.md)
- [Agendas, bills and voting records](agendas-bills-and-voting-records.md)
- [Planning your website](../../../creators/website-creators/planning-your-website.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
