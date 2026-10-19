---
title: Agendas, bills and voting records
audience: government, project-manager, website-creator, editor
category: public-sector
order: 70
summary: How a parliament publishes its order paper, follows a bill through its stages, and publishes votes as documents and pages in Exponential, with the Sitting, Bill and Vote record classes, relations, RSS and careful corrections.
keywords: agenda, order paper, sitting, bill stages, readings, amendments, voting records, divisions, roll call, vote record, publication, correction, RSS
type: help
---
# Agendas, bills and voting records

This page is for clerks, editors, website creators and project managers at a parliament or legislative assembly. It
describes how to publish the three core parts of the record with the classes of
[Content model for parliaments](content-model.md) and the roles of
[Structure, sections and roles for parliaments](structure-sections-and-roles.md). Every product feature named is
documented on the page linked next to it.

## Contents

1. [Agendas](#agendas)
2. [A bill through its stages](#a-bill-through-its-stages)
3. [Votes](#votes)
4. [Corrections](#corrections)
5. [Feeds and reuse](#feeds-and-reuse)

## Agendas

| Step | Do |
|---|---|
| Plan | create Sitting objects for the session's sitting days, below `Business > <year> > <month>`; status *scheduled* |
| Publish the agenda | edit the sitting: agenda items in the Agenda field with links to bills and papers; relate the agenda document (File); status *agenda published*; **Send for publishing** |
| Change during the day | edit and publish again; write the change at the top of the agenda ("Item 4 withdrawn") |
| After the sitting | relate the transcript File and the Vote records; status *record complete* |

Agenda items link to Bill objects through the **Bills on the agenda** relation field, so each bill page can list the
sittings it was on (a fetch of reverse related objects in the template, `fetch( 'content', 'reverse_related_objects', ... )`).

## A bill through its stages

1. **Introduction.** Create the Bill object: number, title, sponsor (a relation to Member objects), summary, the first
   printed text as a File object related in **Versions of the text**. Stage *introduced*.
2. **Each stage.** Edit the bill: set **Stage**, relate the new printed text and amendments as File objects. Each
   publish makes a new version of the Bill page, so the page's history shows when the stage changed
   ([Versions of an object](../../../../guides/content-history.md)).
3. **Committee.** Relate the committee folder; the committee's report is a File object related to the bill.
4. **Outcome.** Stage *adopted* (or *lapsed*, *withdrawn*), with a link to the published law in the official journal
   where one exists.

Do not replace an earlier printed text with a later one in the same File object: each printed version is its own
object with its own address, because each is cited.

## Votes

| Field | Fill with |
|---|---|
| Title | "Vote on <bill number>, <stage>" or the question's short form |
| Date and time | when the vote closed |
| Bill | the Bill object |
| Question | the question put |
| Result, counts | as announced |
| Members' votes | the Matrix with member and vote, where votes are recorded per member |
| Official record | the clerks' record as a File (PDF or CSV) |

Publish votes from the official record only, never from notes taken in the chamber. If per-member votes come from a
voting system, an import that fills the Matrix and attaches the system's export avoids typing errors; that import is a
development task ([REST API v1 and v2](../../../../guides/rest-api-v1-and-v2.md)).

A Vote record relates to the bill; templates on the bill page list its votes, and on a member's page the votes that
mention the member (reverse relations work when members are related, not when they are only names in a Matrix:
decide which you need).

## Corrections

| Situation | Do |
|---|---|
| A wrong count or name in a vote record | correct it from the official record; add a correction note with date; the earlier version stays |
| A wrong document related | relate the right one, publish; keep the wrong File object hidden, not removed, unless it must go |
| Personal data published unlawfully | remove it and the versions that hold it under the Record corrections procedure ([Versions of an object](../../../../guides/content-history.md)) |

Every change is in the audit trail ([Audit trail](../../../../features/6.0/audit-trail.md)).

## Feeds and reuse

| Feed | How |
|---|---|
| Agendas, bills, votes | an RSS export per folder in **Setup > RSS** ([RSS feeds](../../../../guides/rss-feeds.md)) |
| Notifications | people with an account follow a folder or a bill with **Notify me** ([Notifications: the user's guide](../../../../features/6.0/notifications.md)) |
| Data | the official record files; the REST API |

## Related pages

- [Parliaments](README.md)
- [Content model for parliaments](content-model.md)
- [Council meetings and local services](../municipal-and-local/council-meetings-and-local-services.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
